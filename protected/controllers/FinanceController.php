<?php
/**
 * Description of FinanceController
 *
 * @author ramon
 */
class FinanceController extends Controller {
    
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
                'control'   => Permission::PERM__NAV__LSC__CONTROL_SCREEN,
                'baserouting' => Permission::PERM__NAV__LSC__BASE_ROUTING,
                'contractors' => Permission::PERM__NAV__LSC__CONTRACTORS,
                'accountingreport' => Permission::PERM__FUN__LSC__PO,
                'invoices' => Permission::PERM__FUN__LSC__INVOICES,
        );
    }
    
    public function actionControl($base = NULL, $c = NULL)
    {
        $model = new FinanceControl();
        if(isset($base))
            $model->setBaseEdit($c);
        
        if (isset($_GET['FinanceControl']))
        {
            $model->setAttributes($_GET['FinanceControl'], false);
            if ($model->validate())
                $model->populate();
        }
        else
        {
            if ($model->baseEdit)
                $model->populate ();
        }
        $model->doInitialize = 0;
        $this->render('control', array('model' => $model));
    }
    
    public function actionBaserouting($c)
    {
        $this->actionControl(1, $c);
    }
    
    public function actionRoute($date, $ui = NULL, $base = 0, $filter = NULL)
    {
        $this->layout = (isset($ui)) ? '//layouts/'.$ui : $this->layout;
        $model = new FinanceRouteForm();
        $model->editingCategory = $filter;
        $model->setBaseEdit($base);
        
        if (isset($_POST['FinanceRouteForm']))
        {
            $model->setAttributes($_POST['FinanceRouteForm'], false);
            $model->setFees($_POST['Fee']);
            if ($model->validate())
            {
                $model->save();
                if("popUp" === $ui) {
                    echo "<script>parent.$.colorbox.close()</script>";
                    Yii::app()->end();
                }
            }
        }
        else
            $model->weekStarting = $date;
        
        $this->render('route', array('model' => $model));
    }
    
    public function actionRouteinstance($id, $date, $ui = NULL, $scenario = NULL, $base = 0, $instanceid = NULL, $filter = NULL)    
    {
        //$ui = isset($_GET['ui']) ? $_GET['ui'] : null;
        $this->layout = (isset($ui)) ? '//layouts/'.$ui : $this->layout;
        //$id = $_GET['id'];
        //$dateInfo = $_GET['date'];
        $entryType = substr($date, 11);
        $date = substr($date, 0, 10);
        
        //echo "ui $ui, id $id, date $date, entrytype $entryType";
        
        $model = new FinanceRouteInstanceForm($scenario);
        $model->editingCategory = $filter;
        $model->setBaseEdit($base);
        
        if (isset($_POST['FinanceRouteInstanceForm']))
        {
            $model->setAttributes($_POST['FinanceRouteInstanceForm'], false);
            if ($model->validate())
            {
                $model->save();
                if("popUp" === $ui) {
                    echo "<script>parent.$.colorbox.close()</script>";
                    Yii::app()->end();
                }
            }
            else
            {
                $model->populate();
            }
        }
        else
        {
            $model->date = $date;
            $model->entryType = $entryType;
            $model->routeId = $id;
            if (($instanceid !== NULL) && ($instanceid != -1))
                $model->instanceId = $instanceid;

            $model->populate();
        }
        
        $this->render('routeinstance', array('model' => $model));
    }
        
    public function actionCommentvisibility($id) {
        $model = new FinanceRouteInstanceForm();
        $result = $model->toggleCommentVisibility($id);
        echo json_encode($result);
        Yii::app()->end();
    }
    
    public function actionAccountingReport()
    {
        //$model = new FinanceControl();
        $model = new FinanceInvoicing();
        if (isset($_POST['Report']))
        {
            $model->setAttributes($_POST['Report'], false);
            if ($model->validate())
            {
                $model->outputAccountingCsv();
                Yii::app()->end();
            }
        }
    }
    
    public function actionContractors()
    {
        $model = new FinanceContractorForm();
        $model->getContractorList();
        $this->render('contractors', array('model' => $model));
    }
    
    public function actionContractor($id = NULL)
    {
        $model = new FinanceContractorForm();
        $docsModel = new FinanceContractorDocumentsForm();
       
        if (isset($_POST['FinanceContractorForm']))
        {
            $model->setAttributes($_POST['FinanceContractorForm'], false);
            
            
	    if($model->validate() && $model->save()) 
            {
                if ( isset( $_POST['save'] ) )
                {
                    if ( !isset ( $id ) )
                    {
                        $insert_id = Yii::app()->db->getLastInsertID();
                        $this->redirect(array("finance/contractor", 'id'=>$insert_id));
                    }
                }
                else if ( isset( $_POST['saveandexit'] ) )
                {
                    $this->redirect(array('finance/contractors'));
                }
            }
            
        }
        else
        {
            if (isset($id))
            {
                $model->contractorId = $id;
                $docsModel->contractorId = $id;
                $model->populate();
            }
        }
        
        $this->render('contractor', array('model' => $model, 'docsModel' => $docsModel));
    }
    
    public function actionEarnings($id, $date, $ui = NULL)
    {
        $this->layout = (isset($ui)) ? '//layouts/'.$ui : $this->layout;
        $model = new FinanceEarningsForm();
        
        $model->contractorId = $id;
        $model->weekStarting = $date;
        
        if (isset($_POST['axn']))
        {
            switch ($_POST['axn']) {
                case 'export':
                    $model->outputCsv();
                    break;
                case 'email':
                    if ($model->sendEmail())
                        echo json_encode (array('result' => 'success'));
                    else
                        echo json_encode (array('result' => 'fail'));
                    break;
                default:
                    break;
            }
            Yii::app()->end();
        }
        else
        {
            $model->populate();
        }
        
        $this->render('earnings', array('model' => $model));
    }
    
    public function actionInvoices($date, $ui = NULL, $category = NULL)    
    {
        $this->layout = (isset($ui)) ? '//layouts/'.$ui : $this->layout;
        
        $model = new FinanceInvoicing();
        $model->weekStarting = $date;
        $model->category = $category;
        
        if (isset($_POST['FinanceInvoicing']))
        {
            $model->setAttributes($_POST['FinanceInvoicing'], false);
            $model->generateInvoices();
            Yii::app()->end();
        }
        
        $this->render('invoices', array('model' => $model));
    }
    
    public function actionContractorDocument($contractorId)
    {
        //$chunksFolder=Yii::getPathOfAlias('webroot').'/_uploads/temp/chunks';
        //$finalFolder = Yii::getPathOfAlias('webroot').'/_uploads/contractors/'.$contractorId;
        $chunksFolder = Yii::getPathOfAlias('webroot').'/_uploads/temp/chunks';
        $fileFolder = '/_uploads/contractors/'.$contractorId;
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

        $uploadedFile = $fileFolder.'/'.$result['filename'];
        
        $newFile = new FinanceContractorDocument();
        $newFile->ContractorId = $contractorId;
        $newFile->TypeId = 1;
        $newFile->FileName = $uploadedFile;
        $newFile->UploadedBy = Yii::app()->user->loginId;
        $newFile->UploadedDate = new CDbExpression('NOW()');
        $newFile->save();

        //header("Content-Type: text/plain");
        $result = htmlspecialchars(json_encode($result), ENT_NOQUOTES);
        echo $result;
        Yii::app()->end();
    }
    
    public function actionDroproute()
    {
        $id = isset($_POST['id']) ? $_POST['id'] : NULL;
        $result = array(
            'result' => 'success'
        );
        if (!isset($id))
            $result = array(
                'result' => 'fail',
                'error' => 'No id defined'
            );
        else {
            $errors = FinanceControl::dropRouteByInstanceId($id);
            if (!empty($errors))
                $result = array(
                    'result' => 'fail',
                    'error' => implode('//', $errors)
                );
        }
        
        echo json_encode($result);
        Yii::app()->end();
    }

    public function actionDisabledocument()
    {
        $id = isset($_POST['id']) ? $_POST['id'] : NULL;
        $result = array(
            'result' => 'success'
        );
        if (!isset($id))
            $result = array(
                'result' => 'fail',
                'error' => 'No id defined'
            );
        else {
            $errors = FinanceControl::disableContractorDocument($id);
            if (!empty($errors))
                $result = array(
                    'result' => 'fail',
                    'error' => implode('//', $errors)
                );
        }
        
        echo json_encode($result);
        
        Yii::app()->end();
        
    }

    public function actionControlupdate($weekStarting, $baseEdit, $editingCategoryBase, $routeId, $catId) {
        $model = new FinanceControl();
        $model->weekStarting = $weekStarting;
        $model->baseEdit = $baseEdit;
        $model->editingCategoryBase = $editingCategoryBase;
        $model->routeId = $routeId;
        $model->categoryId = $catId;
        
        $model->populate();
        
        $this->renderPartial('control_update', array('model' => $model));
    }
}

?>
