<?php
/**
 * Description of PolestarStatusUpdater
 *
 * @author ramon
 */
class PolestarStatusUpdater {

    const LOAD_STATUS_CHANGED   = "LoadStatus";
    const LOAD_DEF_CHANGED      = "Load"; 
    
    const UPDATE_STATUS_ALLOWANCE = 900; // allow 15 mins before changing from new to amended
    
    public static function saveJobDetails($job, $deliveryDate, $additionalRevisionChanges = array()) {
        $status = $job->StatusId;
        $pc = PolestarPrintCentre::model()->findByPk($job->PrintCentreId);
        
        $today = date('d/m/Y');
        $tomorrow = new DateTime();
        $tomorrow = $tomorrow->modify('+1 day');
        $tomorrow = $tomorrow->format('d/m/Y');
        $deadlineDates = array($today, $tomorrow);
        // #218 begin
        // if friday, deadline dates are fri/sat/sun/mon -- add sunday/monday to deadline dates
        $sunday = NULL;
        $monday = NULL;
        if (date('N') == '5') {
            $sunday = new DateTime();
            $sunday = $sunday->modify('+2 day');
            $sunday = $sunday->format('d/m/Y');
            $deadlineDates[] = $sunday;
            
            $monday = new DateTime();
            $monday = $monday->modify('+3 day');
            $monday = $monday->format('d/m/Y');
            $deadlineDates[] = $monday;
        }
        // #218 end
        
        $cutoff = intval(str_replace(':', '', $pc->LateAdviseCutoff));
        $now = intval(date('His'));
        $afterDeadline = ($now >= $cutoff);
        
        if ($job->isNewRecord) {
            $status = PolestarStatus::NEWLY_ADDED_ID; // newly added - initial status
            
            // initial status
            if ($today == $deliveryDate) { // if job to be COLLECTED today
                $status = ($afterDeadline) ? PolestarStatus::SAME_DAY_ID : $status;
            }
            else {
                if (($tomorrow == $deliveryDate) || 
                    ($sunday == $deliveryDate) || 
                    ($monday == $deliveryDate)) { // #218
                    $status = ($afterDeadline) ? PolestarStatus::LATE_ADVICE_ID : $status;
                }
            }
        }
        else {
            $original = PolestarJob::model()->findByPk($job->Id);
            if (isset($original)) {
                
                $diff = $original->compare($job);
                if (!empty($diff) && !is_array($diff))
                    $diff = array($diff);
                $diff = array_merge($diff, $additionalRevisionChanges); // for load related changes
                
                if (!empty($diff)) { // changes posted
                    // store revision
                    $history = new PolestarJobHistory();
                    $history->setAttributes($original->attributes, FALSE);
                    $history->save();

                    // update status if applicable
                    if (in_array($deliveryDate, $deadlineDates)) { // if job to be COLLECTED today/tomorrow
                        
                        $elapsed = time() - intval($original->formatDate('CreatedDate', 'U'));
                        $afterAllowance = ($elapsed > PolestarStatusUpdater::UPDATE_STATUS_ALLOWANCE);
                        
                        $changes = array_keys($diff);
                        
                        if (!$afterDeadline) {
                            // If any changes to SAME DAY ADVICE or NEWLY ADDED or BOOKED or CONFIRMED 
                            // are completed before 1PM ( all sites apart Bicester) and before 2PM for 
                            // Bicester - the system has to show AMENDED status.
                            $statusToCheck = array( 
                                        PolestarStatus::SAME_DAY_ID,
                                        PolestarStatus::NEWLY_ADDED_ID,
                                        PolestarStatus::BOOKED_ID,                                
                                        PolestarStatus::CONFIRMED_ID,
                                        PolestarStatus::ALLOCATED_ID
                                );
                            if (in_array($job->StatusId, $statusToCheck) && $afterAllowance)
                                $status = PolestarStatus::AMENDED_ID;
                            
                            if (in_array('DeliveryDate', $changes, TRUE) && $afterAllowance ) { // #210
                                //$status = PolestarStatus::SAME_DAY_ID; // #225 commented out
                                /*
                                 * If date changed to tomorrow
                                 * before cutoff: amended
                                 * after cutoff: late advice
                                 * If date changed to today
                                 * before cutoff: same day 
                                 * after cutoff: same day
                                 */
                                if ($deliveryDate == $today)
                                    $status = PolestarStatus::SAME_DAY_ID;
                                else 
                                    $status = PolestarStatus::AMENDED_ID;
                            }
                        }
                        else { // after deadline

                            // If the changes take place after deadline, then the system has to mark it
                            // as AMENDED if
                            // @ the number of pallets has changed .
                            // after deadline any changes to pallets or number of publications (on existing load) status should be AMENDED 
                            if ((PolestarStatusUpdater::hasLoadFieldChange($diff, 'PalletsTotal') ||
                                    PolestarStatusUpdater::hasLoadFieldChange($diff, 'Quantity'))
                                && $afterAllowance)
                                $status = PolestarStatus::AMENDED_ID;
                            
                            // as LATE ADVICE if:
                            // @ vehicle size is changed
                            // @ additional drop is added
                            // @ collection time has changed
                            // @ new route added after deadline
                            // after deadline if they change either collection or delivery time on an existing load status should be LATEADVICE 
                            $newLoad = PolestarStatusUpdater::hasLoadStatusChange($diff, PolestarStatus::NEWLY_ADDED_ID);
                            if ((in_array('VehicleId', $changes, TRUE) || 
                                    in_array('CollScheduledTime', $changes, TRUE) ||
                                    PolestarStatusUpdater::hasLoadFieldChange($diff, 'DelScheduledTime') ||
                                    ($newLoad !== FALSE))
                                && $afterAllowance)
                                $status = PolestarStatus::LATE_ADVICE_ID;
                            
                            if (in_array('DeliveryDate', $changes, TRUE) && $afterAllowance ) { // #225
                                /*
                                 * If date changed to tomorrow
                                 * before cutoff: amended
                                 * after cutoff: late advice
                                 * If date changed to today
                                 * before cutoff: same day 
                                 * after cutoff: same day
                                 */
                                if ($deliveryDate == $today)
                                    $status = PolestarStatus::SAME_DAY_ID;
                                else 
                                    $status = PolestarStatus::LATE_ADVICE_ID;
                            }
                            
                            //after deadline if a load is removed from a job its still AMENDED, not LATE ADVICE 
                            // --> this is only valid when we have more than one loads going to the same 
                            //      delivery address.
                            // if we have multiple loads going to different addresses then this affect the mileage 
                            // and should be marked as late advise.
                            $cancelledLoad = PolestarStatusUpdater::hasLoadStatusChange($diff, PolestarStatus::CANCELLED_ID);
                            if ($cancelledLoad !== FALSE) {
                                if ($cancelledLoad !== TRUE) { // load id returned
                                    $cload = PolestarLoad::model()->findByPk($cancelledLoad);
                                    $oloads = PolestarLoad::model()->findAll(array(
                                        'condition' => "JobId = :jid and Id != :lid and ifnull(StatusId,'') != :csid",
                                        'params' => array(
                                            ':jid' => intval($cload->JobId),
                                            ':lid' => intval($cload->Id),
                                            ':csid' => PolestarStatus::CANCELLED_ID
                                        )
                                    ));
                                    $sameAddress = FALSE;
                                    $cloadPostcode = PolestarJobMap::doSanitizePostcode($cload->DelPostcode);
                                    foreach ($oloads as $oload) {
                                        $oloadPostcode = PolestarJobMap::doSanitizePostcode($oload->DelPostcode);
                                        if ($oloadPostcode == $cloadPostcode)
                                            $sameAddress = TRUE; // there's another load with same address as cancelled one
                                    }
                                    if ($sameAddress === TRUE)
                                        $status = PolestarStatus::AMENDED_ID;
                                    else
                                        $status = PolestarStatus::LATE_ADVICE_ID;
                                }
                                else // no reference to load, default to amended (shouldnt reach this point)
                                    $status = PolestarStatus::AMENDED_ID;
                            }
                        }
                    }

                    // append status diff
                    if ($status != $original->StatusId) {
                        $diff['StatusId'] = array(
                            'old' => $original->StatusId,
                            'new' => $status);
                    }
                    
                    // store revision changes
                    $job->RevisionChanges = json_encode($diff);
                    $job->RevisionNo = $job->RevisionNo + 1;
                    if (!empty($diff)) {
                        $job->EditedDate = new CDbExpression('now()');
                        $job->EditedBy = Yii::app()->user->loginId;
                    }
                }
            }
        }
        
        $job->StatusId = $status;        
        $job->save();
        
        return $job;
    }
    
    public static function hasLoadStatusChange($revision, $status = '') {
        if (isset($revision['type'])) // single update
            $revision = array($revision);
        foreach ($revision as $key => $change){
            if (array_key_exists('type', $change) && ($change['type'] == PolestarStatusUpdater::LOAD_STATUS_CHANGED)) {
                if (empty($status)) // any status change
                    return isset($change['id']) ? $change['id'] : TRUE; // return load id if available
                else if ($change['new'] == $status)
                    return isset($change['id']) ? $change['id'] : TRUE; // return load id if available
            }
        }
        return FALSE;
    }
    
    public static function hasLoadFieldChange($revision, $field = '') {
        if (isset($revision['type'])) // single update
            $revision = array($revision);
        foreach ($revision as $key => $change){
            if (array_key_exists('type', $change) && ($change['type'] == PolestarStatusUpdater::LOAD_DEF_CHANGED)) {
                if (empty($field)) // any field change
                    return TRUE;
                else if ($change['field'] == $field)
                    return TRUE;
            }
        }
        return FALSE;
    }
    
    public static function getLoadStatusChangeRevision($load, $newStatus) {
        return array(
            'type' => PolestarStatusUpdater::LOAD_STATUS_CHANGED,
            'id' => $load->Id,
            'ref' => $load->Ref,
            'field' => NULL,
            'old' => $load->StatusId,
            'new' => $newStatus
        );
    }
    
    public static function getLoadFieldChangeRevision($load, $field, $oldValue, $newValue) {
        return array(
            'type' => PolestarStatusUpdater::LOAD_DEF_CHANGED,
            'id' => $load->Id,
            'ref' => $load->Ref,
            'field' => $field,
            'old' => $oldValue,
            'new' => $newValue
        );
    }
    
    public static function updateJobToStatus($jobId, $statusId) {
        $job = PolestarJob::model()->findByPk($jobId);
        // update rules
        switch ($statusId) {
            case PolestarStatus::BOOKED_ID:
                // do not revert to booked if already confirmed or data completed
                if (in_array($job->StatusId, array(PolestarStatus::CONFIRMED_ID, PolestarStatus::DATA_COMPLETED_ID)))
                    return;
                break;
        }
        
        $job->StatusId = $statusId;
        $job->EditedBy    = Yii::app()->user->loginId;
        $job->EditedDate  = new CDbExpression('NOW()');
        
        PolestarStatusUpdater::saveJobDetails($job, $job->DeliveryDate);
    }
    
    public static function updateLoadToStatus($loadId, $statusId) {
        $load = PolestarLoad::model()->findByPk($loadId);
        $job = PolestarJob::model()->findByPk($load->JobId);
        
        $load->StatusId = $statusId;
        
        $revision = PolestarStatusUpdater::getLoadStatusChangeRevision($load, $statusId);
        PolestarStatusUpdater::saveJobDetails($job, 
                $job->formatDate('DeliveryDate', 'd/m/Y'), 
                $revision);
        
        $load->save();
        
        return $load;
    }
    
    public static function getAllJobRevisionChangesHtml($jobInfo) {
        
        $html = '';
        
        $allChanges = $jobInfo->getAllRevisionChangesArray();
        $first = TRUE;
        foreach ($allChanges as $change) {
            $dt = $change['when'];
            $by = $change['who'];
            $by = (!empty($by)) ? " by $by" : '';
            $dt = !empty($dt) ? "($dt)" : '';
            $prefix = ($first) ? "<strong>Latest Changes </strong>" : '<hr/>';
            $changeHtml = PolestarStatusUpdater::getRevisionChangesHtml($change['what'], $change['revisionNo']);
            $html .= "$prefix<strong>$dt</strong>$by<br/>".$changeHtml;
            
            $first = FALSE;
        }
        
        return $html;
    }
    
    public static function getRevisionChangesHtml($cs, $revId = NULL) {
        $changes = '';
        if (!empty($cs)) {
            foreach ($cs as $field => $c) {
                if (isset($c->type)) {
                    switch($c->type) {
                        case PolestarStatusUpdater::LOAD_DEF_CHANGED: // load field
                            $field = $c->field;
                            $old = PolestarJobLoadRecord::getRevisionFieldDescription($field, $c->old);
                            $new = PolestarJobLoadRecord::getRevisionFieldDescription($field, $c->new);
                            $changes .= "<strong>load:{$c->ref}:{$c->field}</strong> from '$old' to '$new'<br/>";
                            break;
                        case PolestarStatusUpdater::LOAD_STATUS_CHANGED: // load status
                            $field = 'StatusId';
                            $new = PolestarJobLoadRecord::getRevisionFieldDescription($field, $c->new);
                            $changes .= "<strong>load:{$c->ref}</strong> status '$new'<br/>";
                            break;
                    }
                }
                else {
                    $old = PolestarJobLoadRecord::getRevisionFieldDescription($field, $c->old);
                    $new = PolestarJobLoadRecord::getRevisionFieldDescription($field, $c->new);
                    $changes .= "<strong>$field</strong> from '$old' to '$new'<br/>";
                }
            }
        }
        return $changes;
    }
}
