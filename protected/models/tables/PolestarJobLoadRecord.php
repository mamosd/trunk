<?php
/**
 * Description of PolestarJobLoadRecord
 *
 * @author ramon
 */
class PolestarJobLoadRecord  extends CActiveRecord{
    
    public $fieldsChanged;
    
    public function formatDate($field, $format, $empty = NULL) {
        if (empty($this->$field)) return $empty;
        $dt = new DateTime($this->$field);
        return $dt->format($format);
    }

    public function formatTime($field, $format, $empty = NULL) {
        if (empty($this->$field)) return $empty;
        $t = strtotime($this->$field);
        return date($format,$t);
    }
    
    public function getRevisionHistory() {
        $historyModel = (($this instanceof PolestarJob) || ($this instanceof PolestarJobHistory))
                ? PolestarJobHistory::model() 
                : PolestarLoadHistory::model();
        return $historyModel->with('EditedByLogin')->findAll(array(
            'condition' => 'Id = :jid AND RevisionNo < :rno',
            'params' => array(
                            ':jid' => $this->Id,
                            ':rno' => $this->RevisionNo
                ),
            'order' => 'RevisionNo desc'
        ));
    }
    
    public function getRevisionChangesArray() {
        $result = array();
        if (!empty($this->RevisionChanges)) {
            $cs = json_decode($this->RevisionChanges);
            if (!is_array($cs)) {
                $csa = array();
                $partial = array();
                foreach ($cs as $field => $c)
                    if ($c instanceof stdClass) {
                        $csa[$field] = $c;
                        if (!empty($partial)) {
                            $csa[] = (object)$partial;
                            $partial = array();
                        }
                    }
                    else {
                        $partial[$field] = $c;
                    }
                $cs = $csa;
            }
            $result = $cs;
        }
        return $result;
    }
    
    public function getFieldsChanged() {
        if (!isset($this->fieldsChanged)) {
            $goodStatuses = array(
                PolestarStatus::ALLOCATED_ID,
                PolestarStatus::BOOKED_ID,
                PolestarStatus::CONFIRMED_ID,
                PolestarStatus::DATA_COMPLETED_ID,
                PolestarStatus::NEWLY_ADDED_ID
            );
            $allFields = array();
            $all = $this->getAllRevisionChangesArray();
//            var_dump($all);
            // if querying on a load, get last good status timestamp from job
            $lastGoodStatusTs = NULL;
            if (($this instanceof PolestarLoad) || ($this instanceof PolestarLoadHistory)) {
                $crit = new CDbCriteria();
                $crit->addCondition('Id = :jid');
                $crit->params = array(':jid' => $this->JobId);
                $crit->addInCondition('StatusId', $goodStatuses);
                $crit->order = 'RevisionNo DESC';
                $last = PolestarJobHistory::model()->find($crit);
                if (isset($last))
                    $lastGoodStatusTs = $last->formatDate('EditedDate', 'U');
            }
            foreach ($all as $chg) {
                $toStatus = isset($chg['toStatus']) ? $chg['toStatus'] : '';
                // stop gathering changes when a "good status" is found
                if (in_array($toStatus, $goodStatuses)) 
                    break;
                // stop gathering if change beyond last good status timestamp
                if (isset($lastGoodStatusTs) && ($chg['whenTs'] <= $lastGoodStatusTs)) 
                    break;
                
                foreach ($chg['what'] as $field => $data) {
                    $changed = $field;
                    if (is_numeric($field)) {
                        $changed = 'load:'.$data->type;
                    }
                    if (!in_array($changed, $allFields))
                        $allFields[] = $changed;
                }
            }
            $this->fieldsChanged = $allFields;
        }
        return $this->fieldsChanged;
    }
    
    public function getAllRevisionChangesArray() {
        $result = array();
        $current = $this->getRevisionChangesArray();
        if (!empty($current)) {
            $result[] = array(
                'who'       => $this->EditedByLogin->FriendlyName,
                'when'      => $this->formatDate('EditedDate', 'd/m H:i'),
                'what'      => $current,
                'whenTs'    => $this->formatDate('EditedDate', 'U'),
                'toStatus'  => $this->StatusId,
                'revisionNo' => $this->RevisionNo
            );
            $history = $this->getRevisionHistory();
            foreach ($history as $rev) {
                $change = $rev->getRevisionChangesArray();
                if (!empty($change)) {
                    $result[] = array(
                        'who'       => isset($rev->EditedByLogin) ? $rev->EditedByLogin->FriendlyName : 'unknown',
                        'when'      => $rev->formatDate('EditedDate', 'd/m H:i'),
                        'what'      => $change,
                        'whenTs'    => $rev->formatDate('EditedDate', 'U'),
                        'toStatus'  => $rev->StatusId,
                        'revisionNo' => $rev->RevisionNo
                    );
                }
            }
        }
        return $result;
    }
    
    public static function getRevisionFieldDescription($field, $value) {
        $result = $value;
        switch($field) {
            case "VehicleId":
                $i = PolestarVehicle::model()->findByPk($value);
                $result = (isset($i)) ? $i->Name : $result; 
                break;
            case "StatusId":
                $i = PolestarStatus::model()->findByPk($value);
                $result = (isset($i)) ? $i->Name : $result; 
                $result = (empty($result)) ? 'Active' : $result;
                break;
            case "SupplierId":
            case "ProviderId":
                $i = PolestarSupplier::model()->findByPk($value);
                $result = (isset($i)) ? $i->Name : $result; 
                break;
        }
        return $result;
    }
    
    private $jobData;
    private function getJob() {
        if (($this instanceof PolestarLoad) || ($this instanceof PolestarLoadHistory)) {
            if (!isset($this->jobData))
                $this->jobData = PolestarJob::model()->findByPk($this->JobId);
            return $this->jobData;
        }
        else
            return $this;
    }
    
    public function getUiTag($field, $value = NULL, $tag = 'span') {
        $statusToHighlight = array(
            PolestarStatus::AMENDED_ID,
            PolestarStatus::LATE_ADVICE_ID
        );
        $fieldValue = (isset($value)) ? $value : NULL;
        if (!isset($fieldValue) && $this->hasAttribute($field))
            $fieldValue = $this->$field;
        $options = array();
        if (in_array($this->getJob()->StatusId, $statusToHighlight) && ($this->getJob()->ClearHighlighting == 'N')){
            $changedFields = $this->getFieldsChanged();
            if (in_array($field, $changedFields))
                $options['class'] = 'highlight';
        }
        return CHtml::tag($tag, $options, $fieldValue);
    }
}
