<?php
/**
 * Description of PolestarController
 *
 * @author ramon
 */
class PolestarController  extends Controller {

    public function getValidUsers()
    {
        $result = array();
        $logins = Login::model()->findAll("LoginRoleId=:role OR LoginRoleId=:rolesa", array(":role"=>LoginRole::ADMINISTRATOR, ":rolesa" => LoginRole::SUPER_ADMIN));
        foreach ($logins as $login) {
            $result[] = $login->UserName;
        }
        return $result;
    }

    public function  getActionPermissions(){
        return array(
            'activity'          => Permission::PERM__NAV__POLESTAR__ROUTES,
            'activitylog'       => Permission::PERM__NAV__POLESTAR__ROUTES,
            'routeview'         => Permission::PERM__NAV__POLESTAR__ROUTES,
            'suppliers'         => Permission::PERM__NAV__POLESTAR__SUPPLIERS,
            'deliverypoints'    => Permission::PERM__NAV__POLESTAR__DELIVERY_POINTS,
            'routeclone'        => Permission::PERM__FUN__POLESTAR__ROUTE_CLONE,
        );
    }

    // OLD MOCKUPS BEGIN
    public function actionRoutes() {
        $this->render('routes');
    }

    public function actionRoutecontrol() {
        $this->render('routecontrol');
    }
    // OLD MOCKUPS END

    public function actionRouteview($id = NULL) {
        $model = new PolestarRouteViewForm();

        if (isset($_GET['PolestarRouteViewForm'])) {
            $model->setAttributes($_GET['PolestarRouteViewForm'],false);
            if ($model->validate())
                $model->populateJobs();
        } else {
            $model->printCentreId = $id;
            $model->planningDate = date('d/m/Y');
            $model->populateJobs();
        }

        $this->render('routeview', array('model' => $model));
    }

    public function actionRouteview_job($id) {
        $model = new PolestarRouteViewForm();
        $jobInfo = $model->getSingleJob($id);
        $this->renderPartial('routeview_job', array(
                            'model' => $model,
                            'jobInfo' => $jobInfo,
                                ));
    }

    public function actionInfoentry($id, $ui = NULL) {
        $this->layout = (isset($ui)) ? '//layouts/'.$ui : $this->layout;
        $model = new PolestarInfoEntryForm();
        $model->jobId = $id;
        $model->populate();

        if (isset($_POST['PolestarInfoEntryForm'])) {
            // save
            $model->setAttributes($_POST['PolestarInfoEntryForm'], false);
            if ($model->validate() && $model->save()) {
                $this->renderPartial('_close');
                Yii::app()->end();
            }
        }

        $this->render('infoentry', array('model' => $model));
    }

    public function actionRoute($id = NULL, $ui = NULL, $dt = NULL, $pc = NULL) {
        $this->layout = (isset($ui)) ? '//layouts/'.$ui : $this->layout;

        $model = new PolestarJobForm();
        if (!isset($_POST['PolestarJobForm'])) {
            if (isset($id)) {
                // job edit
                $model->Id = $id;
                $model->populate();
            } else {
                // new job
                $model->DeliveryDate = $dt;
                $model->PrintCentreId = $pc;
                $model->initialize();
            }
        } else {
            // save
            $model->setAttributes($_POST['PolestarJobForm'], false);
            //var_dump($model->CollPostcode);die;
            if ($model->validate() && $model->save()) {
                if (isset($_POST['save_and_load'])) {
                    $this->redirect(array('polestar/load', 'ui'=>'popUp','jid' => $model->Id ));
                } else {
                    $this->renderPartial('_close');
                    Yii::app()->end();
                }
            }
        }

        $this->render('route', array(
            'model' => $model
        ));
    }
    
    public function actionRoutenewref($pcid, $dt) {
        echo PolestarJob::getValidReference($pcid, $dt);
        Yii::app()->end();
    }

    public function actionRoute_reorder() {
        $jid = $_POST['jid'];
        $items = $_POST['items'];
        foreach ($items as  $item) {
            $l = PolestarLoad::model()->findByAttributes(array(
                'Id' => $item['id'],
                'JobId' =>$jid,
            ));
            $l->Sequence = $item['seq'];
            $l->save();
        }
        PolestarJobMap::clearMileageInfo($jid);
    }
    
    public function actionRoute_reorder_cps() {
        $jid = $_POST['jid'];
        $items = $_POST['items'];
        
        $sortedCP = 0;

        foreach ($items as  $item) {
            // element got first
            if (empty($item['seq'])) {
                $oldJob = PolestarJob::model()->findByPk($jid);
                $job = PolestarJob::model()->findByPk($jid);
                $jobcp = PolestarJobCollectionPoint::model()->findByPk($item['id']);
                
                $job->CollPostcode        = $jobcp->CollPostcode;
                $job->CollAddress         = $jobcp->CollAddress;
                $job->CollCompany         = $jobcp->CollCompany;
                $job->CollScheduledTime   = $jobcp->CollScheduledTime;
                $job->SpecialInstructions = $jobcp->SpecialInstructions;
                $job->save();
                
                $jobcp->CollPostcode        = $oldJob->CollPostcode;
                $jobcp->CollAddress         = $oldJob->CollAddress;
                $jobcp->CollCompany         = $oldJob->CollCompany;
                $jobcp->CollScheduledTime   = $oldJob->CollScheduledTime;
                $jobcp->SpecialInstructions = $oldJob->SpecialInstructions;
                $jobcp->save();
            }
            
            // the job
            if (empty($item['jid'])) {
                $jobcp->Sequence = $item['seq'];
                $jobcp->save();
            }
            
            // rest of elements
            else {
                $cp = PolestarJobCollectionPoint::model()->findByPk($item['id']);
                $cp->Sequence = $item['seq'];
                $cp->save();
            }
        }
        PolestarJobMap::clearMileageInfo($jid);
    }

    public function actionRoute_drop() {
        $jid = $_POST['id'];
        $model = PolestarJob::model()->findByPk($jid);
        if (isset($model)) {
            $model->StatusId = PolestarStatus::CANCELLED_ID;
            //$model->save();
            PolestarStatusUpdater::saveJobDetails($model, $model->formatDate('DeliveryDate', 'd/m/Y'));
        }
        
        //add comment if > 24 hour
        $criteria=new CDbCriteria;
        $criteria->condition = "Id=$jid";
        $getdata = PolestarJob::model()->findAll($criteria);
        //print_r($getdata[0]->CollScheduledTime);

        $to_time = $getdata[0]->DeliveryDate." ".$getdata[0]->CollScheduledTime;        
        $to_time = strtotime($to_time);
        $from_time = strtotime(date("Y-m-d H:i:s"));
        $diff = round(($from_time - $to_time) / 60 / 60,2);

        if ($diff > 24)
        {
            $c = new PolestarJobComment();
            $c->JobId       = $jid;
            $c->Comment     = "DO NOT CHARGE THE CLIENT";
            $c->CreatedBy   = Yii::app()->user->loginId;
            $c->CreatedDate = new CDbExpression('NOW()');
            $c->save();
        }
        
        Yii::app()->end();
    }

    public function actionLoad_drop() {
        $id = $_POST['id'];
        $load = PolestarStatusUpdater::updateLoadToStatus($id, PolestarStatus::CANCELLED_ID);
        PolestarJobMap::clearMileageInfo($load->JobId);
        Yii::app()->end();
    }
    
    public function actionCollection_point_drop() {
        $id = $_POST['id'];
        $jid = $_POST['jid'];
        if (!empty($_POST['jid'])) {
            // just delete the load
            PolestarJobCollectionPoint::model()->deleteByPk($id);
            PolestarJobMap::clearMileageInfo($jid);
        } else {
            $job = PolestarJob::model()->findByPk($id);
            $cp = PolestarJobCollectionPoint::model()->findByAttributes(array(
                "JobId" => $id
            ));
            
            $job->CollPostcode        = $cp->CollPostcode;
            $job->CollAddress         = $cp->CollAddress;
            $job->CollCompany         = $cp->CollCompany;
            $job->CollScheduledTime   = $cp->CollScheduledTime;
            $job->SpecialInstructions = $cp->SpecialInstructions;
            $job->save();
            
            $cp->delete();
            PolestarJobMap::clearMileageInfo($id);
        }
        
        Yii::app()->end();
    }

    public function actionLoad_activate() {
        $id = $_POST['id'];
        $load = PolestarStatusUpdater::updateLoadToStatus($id, NULL);
        PolestarJobMap::clearMileageInfo($load->JobId);
        Yii::app()->end();
    }

    public function actionLoad($id = NULL, $ui = NULL, $jid = NULL) {
        $this->layout = (isset($ui)) ? '//layouts/'.$ui : $this->layout;

        $model = new PolestarLoadForm();
        if (!isset($_POST['PolestarLoadForm'])) {
            if (isset($id)) {
                // load edit
                $model->Id = $id;
                $model->populate();
                // TODO: populate
            } else {
                // new job
                $model->JobId = $jid;
                $model->initialize();
            }
        } else {
            // save
            $model->setAttributes($_POST['PolestarLoadForm'], false);
            if ($model->validate() && $model->save()) {
                if (isset($_POST['save_and_load'])) {
                    $this->redirect(array('polestar/load', 'ui'=>'popUp','jid' => $model->JobId ));
                } else {
                    $this->renderPartial('_close');
                    Yii::app()->end();
                }
            }
        }

        $this->render('load', array('model' => $model));
    }

    public function actionUpload($pcid, $dt, $ui = NULL) {
        $this->layout = (isset($ui)) ? '//layouts/'.$ui : $this->layout;
        $model = new PolestarUploadForm();
        $model->printCentreId = $pcid;
        $model->planningDate = $dt;
        $result = NULL;

        if(isset($_POST['PolestarUploadForm']))
        {
            //$model->attributes = $_POST['SecondaryRoutingForm'];
            $model->setAttributes($_POST['PolestarUploadForm'], false);
            $model->spreadsheet = CUploadedFile::getInstance($model, 'spreadSheet');

            if($model->validate()) // validates file type
            {
                $fileFolder = '_uploads/polestar/jobs';
                $finalFolder = Yii::getPathOfAlias('webroot')."/$fileFolder";
                @mkdir($finalFolder, 0755, TRUE);

                $model->uploadedFileName = "$fileFolder/".date('YmdHi').'-'.$model->spreadsheet->getName();
                $model->spreadsheet->saveAs($model->uploadedFileName);
                // redirect to success page
                if ($model->validateSheetContents()) {
                    $result = $model->importFile();
                }
            }
        }

        $this->render('upload', array('model' => $model, 'uploadResult' => $result));
    }

    public function actionSuppliers() {
        $suppliers = PolestarSupplier::model()->findAll(array(
            'order' => 'Name ASC'
        ));

        $this->render('suppliers', array(
            'suppliers' => $suppliers,
        ));
    }

    public function actionSupplier_fullview($id, $ui = NULL) {
        $this->layout = (isset($ui)) ? '//layouts/'.$ui : $this->layout;
        $model = PolestarSupplier::model()->with('Contacts')->findByPk($id);
        $this->render('supplier_fullview', array('model' => $model));
    }

    public function actionSupplier($id = NULL) {
        $model = PolestarSupplier::model()->findByPk($id);
        if (!isset($model)) {
            $model = new PolestarSupplier();
        }

        $NewContacts = (!empty($_POST['NewContacts']) && is_array($_POST['NewContacts'])) ? $_POST['NewContacts'] : array();
        if (isset($_POST['Contacts']) && is_array($_POST['Contacts'])) {
            $Contacts = $_POST['Contacts'];
        } else {
            $Contacts = array();
            if (!empty($model->Id)) {
                $ContactsDB = PolestarSupplierContact::model()->findAllByAttributes(array(
                    "SupplierId" => $model->Id,
                ));
                foreach ($ContactsDB as $cdb) {
                    $Contacts[] = $cdb->Id;
                }
            }
        }

        if (isset($_POST['PolestarSupplier'])) {

            if (empty($_POST['PolestarSupplier']['Id'])) // new suppliers
                unset($_POST['PolestarSupplier']['Id']);

            $model->setAttributes($_POST['PolestarSupplier'],false);

            if ($model->IsNewRecord) {
                $model->CreatedBy = Yii::app()->user->loginId;
                $model->CreatedDate = new CDbExpression('NOW()');
            } else {
                $model->EditedBy = Yii::app()->user->loginId;
                $model->EditedDate = new CDbExpression('NOW()');
            }

            if ($model->validate() && $model->save()) {

                // delete contacts
                $crit = new CDbCriteria();
                $crit->AddColumnCondition(array(
                    "SupplierId" => $model->Id
                ));

                //$crit->AddNotInCondition("Id", $Contacts);
                $crit->AddNotInCondition("Id", (isset($_POST['Contacts']) ? $_POST['Contacts'] : array()));
                PolestarSupplierContact::model()->deleteAll($crit);

                foreach ($NewContacts as $data) {
                    $s = new PolestarSupplierContact();
                    $s->setAttributes($data,false);
                    $s->Email = trim($s->Email);
                    $s->SupplierId = $model->Id;
                    $s->CreatedBy = Yii::app()->user->loginId;
                    $s->CreatedDate = new CDbExpression('NOW()');
                    $s->save();
                }

                // document cleaning
                if (!empty($_POST['Documents']) && is_array($_POST['Documents'])) {
                    $crit = new CDbCriteria();
                    $crit->AddColumnCondition(array(
                        "SupplierId" => $model->Id
                    ));
                    $ids = (isset($_POST['Documents']) && is_array($_POST['Documents'])) ? $_POST['Documents'] : array();
                    $crit->AddNotInCondition("Id", $ids);
                    $docs = PolestarSupplierDocument::model()->findAll($crit);
                    foreach($docs as $doc){
                       @ unlink( Yii::getPathOfAlias('webroot').$doc->FileName );
                       $doc->delete();
                    }
                }

                // print centres
                PolestarSupplierPrintCentre::model()->deleteAllByAttributes (array( 'SupplierId' => $model->Id ));
                if (!empty($_POST['PrintCentre']) && is_array($_POST['PrintCentre'])) {
                    foreach ($_POST['PrintCentre'] as $pid => $v) {
                        if (!$v) continue;
                        $p = new PolestarSupplierPrintCentre();
                        $p->PrintcentreId = $pid;
                        $p->SupplierId = $model->Id;
                        $p->insert();
                    }
                }

                if (isset($_POST['saveandexit'])) {
                    $this->redirect(array('polestar/suppliers'));
                } else {
                    $this->redirect(array('polestar/supplier', 'id' => $model->Id));
                }
            }
        }

        $crit = new CDbCriteria();
        $crit->addColumnCondition(array(
            'Live' => 1,
        ));
        $crit->order = 'Name ASC';
        $printCentres = PolestarPrintCentre::model()->findAll($crit);

        $supplierPrintCentreIds = array();
        if (!empty($model->Id)) {
            $supplierPrintCentres = PolestarSupplierPrintCentre::model()->findAllByAttributes(array(
                'SupplierId' => $model->Id,
            ));
            foreach ($supplierPrintCentres as $spc) {
                $supplierPrintCentreIds[] = $spc->PrintcentreId;
            }
        }

        $this->render('supplier', array(
            'model' => $model,
            'NewContacts' => $NewContacts,
            'Contacts' => $Contacts,
            'PrintCentres' => $printCentres,
            'SupplierPrintCentreIds' => $supplierPrintCentreIds,
        ));
    }

    public function actionSupplierdocumentupload($id) {
        $chunksFolder = Yii::getPathOfAlias('webroot').'/_uploads/temp/chunks';
        $fileFolder = '/_uploads/polestar/supplier/'.$id;
        $finalFolder = Yii::getPathOfAlias('webroot').$fileFolder;

        @mkdir($finalFolder, 0755, TRUE);
        @mkdir($chunksFolder, 0755, TRUE);

        Yii::import("ext.EFineUploader.qqFileUploader");

        $uploader = new qqFileUploader();
        $uploader->allowedExtensions = array('pdf','tif', 'tiff', 'jpg');
        $uploader->sizeLimit = 10 * 1024 * 1024;//maximum file size in bytes
        $uploader->chunksFolder = $chunksFolder;

        $result = $uploader->handleUpload($finalFolder);
        $result['filename'] = $uploader->getUploadName();
        $result['folder'] = ''; //$webFolder;

        if (!isset($result['error'])) {
            $uploadedFile = $fileFolder.'/'.$result['filename'];

            $newFile = new PolestarSupplierDocument();
            $newFile->SupplierId = $id;
            $newFile->FileName = $uploadedFile;
            $newFile->UploadedBy = Yii::app()->user->loginId;
            $newFile->UploadedDate = new CDbExpression('NOW()');
            $newFile->save();
        }

        //header("Content-Type: text/plain");
        $result = htmlspecialchars(json_encode($result), ENT_NOQUOTES);
        echo $result;
        Yii::app()->end();
    }

    public function actionSupplierdocumentdownload($id) {
        $f = PolestarSupplierDocument::model()->findByPk($id);
        if (!isset($f))
            throw new CHttpException(404);
        $filename = basename($f->FileName);
        Yii::app()->getRequest()->sendFile($filename, @file_get_contents(Yii::getPathOfAlias('webroot').$f->FileName));
    }

    public function actionAdvice_sheet($id) {
        $m = new PolestarAdviceSheet();
        $m->id = $id;
        $m->generatePdf();
    }

    public function actionSend_advice_sheet($id) {
        $m = new PolestarAdviceSheet();
        $m->id = $id;


        $result = array ('success' => true, 'reason' => '');

        $sent = $m->sendPdf($_GET['contacts']);
        if (!isset($sent) || ($sent < 1))
            $result = array ('success' => false, 'reason' => 'No emails sent, please check supplier configuration or notify the system administrator.');
        else
            PolestarStatusUpdater::updateJobToStatus ($id, PolestarStatus::BOOKED_ID);

        echo json_encode($result);
        Yii::app()->end();
    }

    public function actionDeliveryPoints() {
        $criteria = new CDbCriteria();
        $criteria->order = 'Name ASC';
        $dps = PolestarDeliveryPoint::model()->findAll($criteria);
        $this->render('deliverypoints', array('dps'=>$dps));
    }

    public function actionDeliveryPoint($id = NULL) {
        $ui = isset($_GET['ui']) ? $_GET['ui'] : NULL;
        $this->layout = (isset($ui)) ? '//layouts/'.$ui : $this->layout;

        $model = new PolestarDeliveryPointForm();
        $contacts= array();

        // collect user input data
        if(isset($_POST['PolestarDeliveryPointForm']))
        {
            $model->setAttributes($_POST['PolestarDeliveryPointForm'], false);

            // validate user input and redirect to the titles page if valid
            if($model->validate() && $model->save()) {
                if("popUp" === $ui)
                {
                    echo "<script>parent.$.colorbox.close()</script>";
                    Yii::app()->end();
                }
                else
                {
                    if ( isset( $_POST['save'] ) )
                    {
                        if ( !isset ( $id ) )
                        {
                            $insert_id = Yii::app()->session['getLastIdPolestarDeliveryPoint'];
                            unset(Yii::app()->session['getLastIdPolestarDeliveryPoint']);
                            $this->redirect(array("polestar/deliverypoint", 'id'=>$insert_id));
                        }
                    }
                    else if ( isset( $_POST['saveandexit'] ) )
                    {
                        $this->redirect(array('polestar/deliverypoints'));
                    }
                }
            }
        }
        if (isset($id))
        {
            $model->populate($id);
            $criteria = new CDbCriteria();
            $criteria->condition = 'DeliveryPointId = '.$id;
            $criteria->order = 'Name ASC';
            $contacts = PolestarDeliverypointContact::model()->findAll($criteria);
        }
        $this->render('deliverypoint', array('model' => $model, 'ui'=> $ui, 'contacts' => $contacts));
    }

    public function actionDeliveryPointContact($id = NULL)
    {
        $ui = isset($_GET['ui']) ? $_GET['ui'] : null;
        $this->layout = (isset($ui)) ? '//layouts/'.$ui : $this->layout;

        $model = new PolestarDeliveryPointContactForm();

        // collect user input data
        if(isset($_POST['PolestarDeliveryPointContactForm']))
        {
            $model->setAttributes($_POST['PolestarDeliveryPointContactForm'], false);
            // validate user input and redirect to the titles page if valid
            if($model->validate() && $model->save()) {
                if("popUp" === $ui)
                {
                    echo "<script>parent.$.colorbox.close()</script>";
                    Yii::app()->end();
                    //echo "<script>location.reload();</script>";
                    //$this->redirect(array('admin/titles'));
                    $redirectURL = Yii::app()->createUrl("polestar/deliverypoints");
                    echo "<script>parent.window.location='$redirectURL';</script>";
                }
                else
                {
                    $this->redirect(array('polestar/deliverypoints'));
                }
            }
        }
        else
        {
            if (isset($id))
            {
                //$model->populate($id);
            }
        }
        $this->render('deliverypoint_contact', array('model' => $model, 'ui'=>$ui, 'DeliveryPointId'=>$_GET['DeliveryPointId']));
    }

    public function actionDeliveryPointContactdelete($contactid,$deliverypointid)
    {
        PolestarDeliverypointContact::model()->deleteByPk($contactid);
        $this->redirect(array('polestar/deliverypoint','id'=>$deliverypointid));
    }

    public function actionPostcode_lookup($postcode){

        $sanitizedpc = strtolower($postcode);
        $sanitizedpc = preg_replace("/[^a-z0-9]/", '', $sanitizedpc);

        $dp = PolestarDeliveryPoint::model()->findByAttributes(array(
            'SanitizedPostcode' => $sanitizedpc,
        ));

        if (!isset($dp)) {
            throw new CHttpException(404);
        }

        echo json_encode(array(
            'postcode' => $dp->PostalCode,
            'address' => $dp->Address,
            'company' => $dp->Company,
        ));

        Yii::app()->end();
    }

    public function actionJob_suppliers($id) {
        $model = PolestarJob::model()->with('Supplier')->findByPk($id);
        if (!isset($model) || !isset($model->Supplier)) throw new CHttpException(404);
        $contacts = $model->Supplier->Contacts;

        $result = array();
        foreach ($contacts as $contact) {
            $telephone = $contact->Telephone;
            if (!empty($contact->ExtensionNo))
                $telephone .= " ext ".$contact->ExtensionNo;
            $result[] = array(
                'id' => $contact->Id,
                'name' => trim($contact->Name.' '.$contact->Surname),
                'receiveAdviceEmails' => $contact->ReceiveAdviceEmails?1:0,
                'department' => $contact->Department,
                'landline' => $telephone,
                'mobile' => $contact->Mobile,
                'email' => $contact->Email,
                'type' => $contact->Type,
                'sent' => PolestarSupplierNotification::model()->exists(array(
                    'condition' => "ContactId = :cid AND JobId = :jid AND Type = 'advice'",
                    'params' => array(':cid' => $contact->Id, ':jid' => $id)
                ))
            );
        }

        echo json_encode($result);
        Yii::app()->end();
    }
    
    public function actionExport($date, $pc, $filter = NULL) {
        $model = new PolestarExportForm();
        $model->planningDate = $date;
        $model->printCentreId = $pc;
        $model->statusToList = $filter;
        
        $model->outputXls();
        Yii::app()->end();
    }
    
    /**
     * DEPRECATED - replaced by activitylog
     * @param type $id
     * @param type $ajax
     * @return type
     */
    public function actionActivity($id = NULL, $ajax = NULL) {
        $model = new PolestarActivityForm();
        $model->printCentreId = $id;
        if (isset($ajax) && isset($model->printCentreId)) {
            $this->renderPartial('activity_pc', array('model' => $model));
            Yii::app()->end();
            return;
        }
        $allPcs = PolestarPrintCentre::getAllForLoginAsOptions();
        $printCentres = array('0' => 'All') + $allPcs;
        if (!isset($model->printCentreId)) {
            $ids = array_keys($printCentres);
            $model->printCentreId = $ids[0];
        }
        $this->render('activity', array('model' => $model, 'printCentres' => $printCentres));
    }
    
    public function actionJobmap($id, $ui = NULL) {
        $this->layout = (isset($ui)) ? '//layouts/'.$ui : $this->layout;
        
        $model = new PolestarJobMap();
        $model->jobId = $id;
        if ($model->isMileageOutdated())
            $model->updateMileage();
        
        $this->render('jobmap', array('model' => $model));
    }
    
    public function actionActivitylog($ajax = NULL) {
        $model = new PolestarActivityLogForm();
        
        if(isset($_POST['alForm']))
            $model->setAttributes($_POST['alForm'], false);
        else {
            $criteria = Yii::app()->session['activitylog-criteria'];
            if (isset($criteria))
                $model->setAttributes($criteria, false);
            else { // defaults
                $model->dateFrom = date('d/m/Y');
                $model->dateTo = $model->dateFrom;
            }
        }
        $model->fullBlown = TRUE;
        if (isset($ajax)) {
            $model->fullBlown = FALSE;
            $this->renderPartial('activitylog', array('model' => $model, 'ajax' => TRUE));
            Yii::app()->end();
            return;
        }
        $this->render('activitylog', array('model' => $model, 'ajax' => FALSE));
    }
    
    public function actionRouteclone($id, $ui = NULL) {
        $this->layout = (isset($ui)) ? '//layouts/'.$ui : $this->layout;
        
        $model = new PolestarRouteCloneForm();
        $model->sourceJobId = $id;
        $model->populateFromSource();
        $originalDate = $model->getJob()->DeliveryDate;
        
        if (isset($_POST['PolestarRouteCloneForm'])) {
            $model->setAttributes($_POST['PolestarRouteCloneForm'], false);
            if ($model->validate() && $model->save()) {
                $job = $model->newJobInfo;
                $url = $job->getPermalink($originalDate != $job->DeliveryDate); // avoid hash when cloning for same day
                $this->renderPartial('_close', array('redirectTo' => $url));
                
                Yii::app()->end();
            }
        }
        
        $this->render('routeclone', array('model' => $model));
    }
}
