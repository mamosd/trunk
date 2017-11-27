<?php
/**
 * Description of ClientRoutingController
 *
 * @author ramon
 */
class ClientroutingController extends Controller {
    
    public function getValidUsers()
    {
        $result = array();
        $logins = Login::model()->findAll("LoginRoleId=:role OR LoginRoleId=:rolesa", array(":role"=>LoginRole::ADMINISTRATOR, ":rolesa" => LoginRole::SUPER_ADMIN));
        foreach ($logins as $login) {
            $result[] = $login->UserName;
        }
        return $result;
    }

    public function actionImport()
    {
        $model = new RoutingImportForm();
        $message = "";
        
        if (isset($_POST["RoutingImportForm"])) // UPLOAD 
        {
            $model->setAttributes($_POST['RoutingImportForm'], false);
            if($model->validate()) {
                $model->orderFile = CUploadedFile::getInstance($model, 'orderFile');
                $fileName = '_uploads/orders/'.date('Ymd-Hi').'-'.$model->clientId.'-';
                $fileName .= $model->orderFile->getName();
                $model->orderFile->saveAs($fileName);
                
                $model->parse($fileName);
                
                if (!$model->hasErrors())
                    $message = "Spreadsheet successfully imported.";
            }
        }
        
        $this->render("import", array('model' => $model, 'message' => $message));
    }
    
    public function actionCleardata()
    {
        $model = new RoutingDataClearForm();
        if (isset($_POST["RoutingDataClearForm"])) 
        {
            $model->setAttributes($_POST["RoutingDataClearForm"], false);
            if ($model->validate())
            {
                $model->process();
            }
        }
        $this->render("cleardata", array('model' => $model));
    }
    
    public function actionSchedules()
    {
        $model = new RoutingScheduleForm();
        
        if (isset($_GET['RoutingScheduleForm']))
        {
            $model->setAttributes($_GET['RoutingScheduleForm'], false);
            if ($model->validate())
            {
                // populate
                $model->getSchedule();
            }
        }
        else
        {
            $model->deliveryDate = date('d/m/Y');
        }
        
        $this->render("schedules", array('model' => $model));
    }
    
    public function actionBlankroute()
    {
        $result = array('result' => -1);
        
        if (isset($_POST['Route']))
        {
            if (RoutingInstanceForm::generateBlankRoute($_POST['Route']))
                $result['result'] = 0;
        }
        
        echo json_encode($result);
        Yii::app()->end();
    }
    
    public function actionRouteinstance($id)
    {
        $ui = isset($_GET['ui']) ? $_GET['ui'] : null;
        $this->layout = (isset($ui)) ? '//layouts/'.$ui : $this->layout;
        
        $model = new RoutingInstanceForm();
        
        if (isset($_POST['RoutingInstanceForm']))
        {
            // grab attrs
            $model->setAttributes($_POST['RoutingInstanceForm'], false);
            $routeData = $_POST['RtData'];
            $dropsData = $_POST['WsData'];
            
            // save
            $model->save($routeData, $dropsData);
            if("popUp" === $ui) {
                echo "<script>parent.$.colorbox.close()</script>";
                Yii::app()->end();
            }
        }
        else
        {
            if ($model->populate($id) === FALSE)
            {
                echo "Incorrect identifier";
                Yii::app()->end();
            }
        }
        
        $this->render("instance", array('model' => $model));
    }
    
    public function actionRouteinstancedrop($rid, $wsid)
    {
        $ui = isset($_GET['ui']) ? $_GET['ui'] : null;
        $this->layout = (isset($ui)) ? '//layouts/'.$ui : $this->layout;
        
        //echo "routeinstance: $rid wholesaler: $wsid";
        $model = new RoutingDropForm();
        //$expressTitle = new ExpressTitleForm();
        
        if (isset($_POST['TtData']))
        {
            $model->saveDrops($_POST['TtData']);
            if("popUp" === $ui) {
                echo "<script>parent.$.colorbox.close()</script>";
                Yii::app()->end();
            }
        }
        else {
            if ($model->populate($rid, $wsid) === FALSE)
            {
                echo 'Incorrect identifiers.';
                Yii::app()->end();
            }
        }
        
        //$this->render('drop', array('model' => $model,'expressTitle'=>$expressTitle));
        $this->render('drop', array('model' => $model));
    }
    
    
    
    public function actionType($id = NULL)
    {
        
        $ui = isset($_GET['ui']) ? $_GET['ui'] : null;
        $this->layout = (isset($ui)) ? '//layouts/'.$ui : $this->layout;

        $model = new ExpressTypeForm();

        // collect user input data
        if(isset($_POST['ExpressTypeForm']))
        {
           $model->setAttributes($_POST['ExpressTypeForm'], false);

            // validate user input and redirect to the titles page if valid
            if($model->validate() && $model->save()) {
                
                if("popUp" === $ui) {
                    echo "<script>parent.$.colorbox.close()</script>";
                    Yii::app()->end();
                }
                else
                    $this->redirect(array('clientrouting/types'));
            }
        }
        else {
            
            if (isset($id)) {
                $model->populate($id);
            }
        }

        $this->render('type', array('model' => $model, 'ui'=>$ui));
    }    
    
    
    
    public function actionTypes()
    {
        $filter = isset($_GET['f']) ? $_GET['f'] : '1';
        
        $criteria = new CDbCriteria();
        
        $criteria->condition='';
        
        if ($filter != '*')
        {
            $criteria->condition .= "IsLive = $filter";
        }
        
        $criteria->order = "name ASC";
        $types = ExpressTypes::model()->findall($criteria);
        
        $this->render('types', array('types'=>$types));
    }
    
    
    public function actionTitle($id = NULL)
    {
        
        $ui = isset($_GET['ui']) ? $_GET['ui'] : null;
        $this->layout = (isset($ui)) ? '//layouts/'.$ui : $this->layout;

        $model = new ClientTitleForm();

        // collect user input data
        if(isset($_POST['ClientTitleForm']))
        {
           $model->setAttributes($_POST['ClientTitleForm'], false);

            // validate user input and redirect to the titles page if valid
            if($model->validate() && $model->save()) {
                
                if("popUp" === $ui) {
                    echo "<script>parent.$.colorbox.close()</script>";
                    Yii::app()->end();
                }
                else
                    $this->redirect(array('clientrouting/titles'));
            }
        }
        else {
            
            if (isset($id)) {
                $model->populate($id);
            }
        }

        $this->render('title', array('model' => $model, 'ui'=>$ui));
    }    
    
    
    public function actionTitles()
    {
        $filter = isset($_GET['f']) ? $_GET['f'] : '1';
        
        $criteria = new CDbCriteria();
        
        $criteria->condition='';
        
        if ($filter != '*')
        {
            $criteria->condition .= "IsLive = $filter";
        }
        
        $criteria->order = "name ASC";
        $titles = ClientTitle::model()->findall($criteria);
        
        $this->render('titles', array('titles'=>$titles));
    }
    
    public function actionDroptitle()
    {
        $result = array('error' => 0);
        
        if (isset($_POST['NewTitle']))
        {
            RoutingDropForm::addTitle($_POST['NewTitle']);
        }
        else
            $result['error'] = 1;
        
        echo json_encode($result);
        Yii::app()->end();
    }
    
    public function actionDroptitledelete()
    {
        $result = array('error' => 0);
        
        if (isset($_POST['dropid']))
        {
            RoutingDropForm::deleteTitle($_POST['dropid']);
        }
        else
            $result['error'] = 1;
        
        echo json_encode($result);
        Yii::app()->end();
    }
    
    public function actionPrintdeliverynotes()
    {
        if (isset($_POST['Print']))
        {
            $model = new PrintDeliveryNotes();
            $model->outputNotes($_POST['Print']);            
        }
        Yii::app()->end();
    }
    
    public function actionPrintloadsheets()
    {
        if (isset($_POST['Print']))
        {
            $model = new PrintLoadSheets();
            $model->outputSheets($_POST['Print']);            
        }
        Yii::app()->end();
    }
    
    public function actionPrintschedule()
    {
        if (isset($_POST['Print']))
        {
            $model = new PrintSchedule();
            $model->outputSchedule($_POST['Print']);            
        }
        Yii::app()->end();
    }
    
    public function actionWholesaler()
    {
        $ui = isset($_GET['ui']) ? $_GET['ui'] : null;
        $this->layout = (isset($ui)) ? '//layouts/'.$ui : $this->layout;
        
        $model = new RoutingWholesalerForm();
        if (isset($_POST['RoutingWholesalerForm']))
        {
            $model->setAttributes($_POST['RoutingWholesalerForm'], false);
            if ($model->validate())
                if ($model->save())
                {
                     echo "<script>parent.$.colorbox.close()</script>";
                    Yii::app()->end();
                }
        }
        else
        {
            $model->wholesalerId = isset($_GET['wsid']) ? $_GET['wsid'] : null;
            if (!$model->populate())
            {
                echo "Wrong identifier";
                Yii::app()->end();
            }
        }
        
        $this->render('wholesaler', array('model' => $model));
    }
    
    public function actionDropmove()
    {
        $ui = isset($_GET['ui']) ? $_GET['ui'] : null;
        $this->layout = (isset($ui)) ? '//layouts/'.$ui : $this->layout;
        
        $model = new RoutingDropMoveForm();
        if (isset($_POST['RoutingDropMoveForm']))
        {
            $model->setAttributes($_POST['RoutingDropMoveForm'], false);
            if ($model->validate())
                if ($model->save())
                {
                     echo "<script>parent.$.colorbox.close()</script>";
                    Yii::app()->end();
                }
        }
        else
        {
            $model->instanceDetailsId = isset($_GET['id']) ? $_GET['id'] : null;
            if (!$model->populate())
            {
                echo "Wrong identifier";
                Yii::app()->end();
            }
        }
        
        $this->render('dropmove', array('model' => $model));
    }
    
    public function actionRoutedelete()
    {
        $result = array('error' => 0);
        
        if (isset($_POST['instanceid']))
        {
            //RoutingDropForm::deleteTitle($_POST['dropid']);
            RoutingInstanceForm::deleteRoute($_POST['instanceid']);
        }
        else
            $result['error'] = 1;
        
        echo json_encode($result);
        Yii::app()->end();
    }
}

?>
