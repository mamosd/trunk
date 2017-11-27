<?php

/**
 * Description of AdminController
 *
 * @author Ramon
 */
class AdminController extends Controller {

    // this is to be retrieved by implementation (hosting customer) when SaaS
/*    
    public $menu = array(
           
        array('label'=>'Client Routing', 'url'=>'#'),
        
        
        array('label'=>'Secondary Routing', 'url'=>'#', 'items'=>array(
               array('label'=>'Process Rounds', 'url'=>array('admin/secondaryrouting')),
               array('label'=>'Route Maintenance', 'url'=>array('admin/secondaryroutingroutes'))
           )),
//           array('label'=>'Control', 'url'=>'#', 'items'=> array(
//               array('label'=>'Control Sheet', 'url'=>array('admin/reportingcontrol')),
//               array('label'=>'Pallet Report', 'url'=>array('admin/reportingpallet')),
//           )),
           array('label'=>'Pallets', 'url'=>'#', 'items'=> array(
               array('label' => 'Print Centres', 'url' => '#', 'items' => array(
                   array('label'=>'Print Print Centre Sheets', 'url'=>array('admin/printpalletsheetspc')),
                   array('label'=>'Upload Summary', 'url'=>array('admin/uploadpalletsheetpc')),
               )),
               array('label' => 'Suppliers', 'url' => '#', 'items' => array(
                   array('label'=>'Print Supplier Sheets', 'url'=>array('admin/printpalletsheets')),
                   array('label'=>'Upload Summary', 'url'=>array('admin/uploadpalletsheet')),
                   array('label'=>'Report', 'url'=>array('admin/palletreport')),
               )),
               array('label'=>'Delivery Point Report', 'url'=>array('admin/palletreportdp')),
           )),
//           array('label'=>'Orders', 'url'=>'#', 'items' => array(
//               array('label'=>'Entry', 'url'=>array('admin/orders')),
//               array('label'=>'Listing', 'url'=>array('admin/ordersedit')),
//               array('label'=>'Archived Orders', 'url'=>array('admin/reportingcontrol', 'archived'=>'1')),
//           )),
//           array('label'=>'Routes', 'url'=>array('admin/routes')),
//           array('label'=>'D. Points', 'url'=>array('admin/deliverypoints')),
//           array('label'=>'Titles', 'url'=>array('admin/titles')),
//           array('label'=>'P. Centres', 'url'=>array('admin/printcentres')),
//           array('label'=>'Suppliers', 'url'=>array('admin/suppliers')),

           array('label' => 'Routing', 'url' => '#', 'items' => array(
               array('label'=>'Routes', 'url'=>array('admin/routes')),
               array('label'=>'D. Points', 'url'=>array('admin/deliverypoints')),
               array('label'=>'Titles', 'url'=>array('admin/titles')),
               array('label'=>'P. Centres', 'url'=>array('admin/printcentres')),
               array('label'=>'Suppliers', 'url'=>array('admin/suppliers')),
               array('label'=>'Orders', 'url'=>'#', 'items' => array(
                   array('label'=>'Entry', 'url'=>array('admin/orders')),
                   array('label'=>'Listing', 'url'=>array('admin/ordersedit')),
                   array('label'=>'Archived Orders', 'url'=>array('admin/reportingcontrol', 'archived'=>'1')),
               )),
               array('label'=>'Control', 'url'=>'#', 'items'=> array(
                   array('label'=>'Control Sheet', 'url'=>array('admin/reportingcontrol')),
                   array('label'=>'Pallet Report', 'url'=>array('admin/reportingpallet')),
               )),               
           )),
           array('label'=>'Admin', 'url'=>'#', 'items'=>array(
               array('label'=>'Login Management', 'url'=>array('admin/logins')),
               array('label'=>'Area Management', 'url'=>array('admin/areas')),
               
               ),
            ),
        );
*/
    /**
     * Used to retrieve the users with granted access to this controller's actions.
     * @return <type>
     */
    public function getValidUsers()
    {
        $result = array();
        $logins = Login::model()->findAll("LoginRoleId=:role OR LoginRoleId=:rolesa", array(":role"=>LoginRole::ADMINISTRATOR, ":rolesa" => LoginRole::SUPER_ADMIN));
        foreach ($logins as $login) {
            $result[] = $login->UserName;
        }
        return $result;
    }

    public function actionDataReset()
    {
        $filename = '_utils/resetdata.sql';
        $script = file_get_contents($filename);

        $cmd = new CDbCommand(Yii::app()->db,$script);
        $records = $cmd->execute();
        echo 'Data reset successfully <br/><a href="'.$this->createUrl(Yii::app()->user->role->HomeUrl).'">go home</a>';

        Yii::app()->end();
    }


    /**
     *
     */
    public function actionIndex()
    {
        $this->render('index');
    }


    public function actionSuppliers()
    {
        $criteria = new CDbCriteria();
        $criteria->order = 'Name ASC';
        $suppliers = Supplier::model()->findAll($criteria);
        $this->render('suppliers', array('suppliers' => $suppliers));
    }

    public function actionSupplier($id = NULL)
    {
        $model = new SupplierForm;

        // collect user input data
        if(isset($_POST['SupplierForm']))
        {
            $model->setAttributes($_POST['SupplierForm'], false);
            // validate user input and redirect to the suppliers page if valid
            if($model->validate() && $model->save()) {
                $this->redirect(array('admin/suppliers'));
            }
        }
        else {
            if (isset($id)) {
                $model->populate($id);
            }
        }
        
        $this->render('supplier', array('model'=>$model));
    }

    public function actionLogins()
    {
        $criteria = new CDbCriteria();
        $criteria->order = 'LoginRoleId ASC, UserName ASC';
        $logins = Login::model()->findAll($criteria);
        $this->render('logins', array('logins'=>$logins));
    }

    public function actionLogin($id = NULL)
    {
        $model = new LoginEditForm();

        // collect user input data
        if(isset($_POST['LoginEditForm']))
        {
            $model->setAttributes($_POST['LoginEditForm'], false);

            // validate user input and redirect to the suppliers page if valid
            if($model->validate() && $model->save()) {
                $this->redirect(array('admin/logins'));
            }
        }
        else {
            if (isset($id)) {
                $model->populate($id);
            }
        }

        $this->render('login', array('model' => $model));
    }

    public function actionPrintcentres()
    {
        $criteria = new CDbCriteria();
        $criteria->order = 'Name ASC';
        $pcs = PrintCentre::model()->findAll($criteria);
        $this->render('printcentres', array('pcs'=>$pcs));
    }

    public function actionPrintcentre($id = NULL)
    {
        $ui = isset($_GET['ui']) ? $_GET['ui'] : NULL;
        $this->layout = (isset($ui)) ? '//layouts/'.$ui : $this->layout;
        
        $model = new PrintCentreForm();
        $contacts= array();

        // collect user input data
        if(isset($_POST['PrintCentreForm']))
        {
            $model->setAttributes($_POST['PrintCentreForm'], false);

            // validate user input and redirect to the suppliers page if valid
            if($model->validate() && $model->save()) 
            {
                    
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
                            $insert_id = Yii::app()->db->getLastInsertID();
                            //$insert_id = Yii::app()->session['getLastIdDeliveryPoint'];
                            //unset(Yii::app()->session['getLastIdDeliveryPoint']);
                            $this->redirect(array("admin/printcentre", 'id'=>$insert_id));
                        }
                    }
                    else if ( isset( $_POST['saveandexit'] ) )
                    {
                        $this->redirect(array('admin/printcentres'));
                    }
                    
                    
                }

            }
        }
        
        if (isset($id)) {
            $model->populate($id);

            $criteria = new CDbCriteria();
            $criteria->condition = 'PrintCentreId = '.$id;
            $criteria->order = 'Name ASC';
            $contacts = PrintcentreContact::model()->findAll($criteria);
        }
        
        $this->render('printcentre', array('model' => $model, 'contacts' => $contacts));
    }

    public function actionContact($id = NULL)
    {
        $ui = isset($_GET['ui']) ? $_GET['ui'] : null;
        $this->layout = (isset($ui)) ? '//layouts/'.$ui : $this->layout;

        $model = new PrintCentreContactForm();

        // collect user input data
        if(isset($_POST['PrintCentreContactForm']))
        {
            $model->setAttributes($_POST['PrintCentreContactForm'], false);

            // validate user input and redirect to the titles page if valid
            if($model->validate() && $model->save()) {
                if("popUp" === $ui) 
                {
                    echo "<script>parent.$.colorbox.close()</script>";
                    //echo "<script>location.reload();</script>";
                    //$this->redirect(array('admin/titles'));
                    
                    Yii::app()->end();
                    
                }
                else
                    $this->redirect(array('admin/titles'));
              
            }
        }
        else {
            if (isset($id)) {
                //$model->populate($id);
            }
        }

        $this->render('contact', array('model' => $model, 'ui'=>$ui, 'PrintCentreId'=>$_GET['PrintCentreId']));
    }
    
    public function actionContactdelete($contactid,$printcentreid)
    {
        PrintcentreContact::model()->deleteByPk($contactid);
        $this->redirect(array('admin/printcentre','id'=>$printcentreid));
    }
    
    public function actionTitles()
    {
        $filter = isset($_GET['f']) ? $_GET['f'] : '1';
        $filterPrintCentres = isset($_GET['fp']) ? $_GET['fp'] : '*';
        
        $criteria = new CDbCriteria();
        
        $criteria->condition='';
        
        if ($filter != '*')
            $criteria->condition .= "IsLive = $filter";
        if ($filterPrintCentres != '*')
        {
            $criteriaFilterP = "PrintCentreName = '$filterPrintCentres'";
            $criteria->condition .= (!empty($criteria->condition))? ' AND '.$criteriaFilterP : ''.$criteriaFilterP;
        }
            
        $criteria->order = "Name ASC";
        $titles = AllTitles::model()->findall($criteria);
        $this->render('titles', array('titles'=>$titles));
    }

    public function actionTitle($id = NULL)
    {
        $ui = isset($_GET['ui']) ? $_GET['ui'] : null;
        $this->layout = (isset($ui)) ? '//layouts/'.$ui : $this->layout;

        $model = new TitleForm();

        // collect user input data
        if(isset($_POST['TitleForm']))
        {
            $model->setAttributes($_POST['TitleForm'], false);

            // validate user input and redirect to the titles page if valid
            if($model->validate() && $model->save()) {
                if("popUp" === $ui) {
                    echo "<script>parent.$.colorbox.close()</script>";
                    Yii::app()->end();
                }
                else
                    $this->redirect(array('admin/titles'));
            }
        }
        else {
            if (isset($id)) {
                $model->populate($id);
            }
        }

        $this->render('title', array('model' => $model, 'ui'=>$ui));
    }

    public function actionDeliveryPoints()
    {
        
        $criteria = new CDbCriteria();
        $criteria->order = 'Name ASC';

        if ( Login::checkPermission('#^navigation/NQ/secondary/#',true) && Login::checkPermission('#^navigation/NQ/secondary/#',true) )
        {}
        else
        {
            if ( Login::checkPermission('#^navigation/NQ/primary/#',true))
            {$criteria->condition = "NQ='Primary'";}
            else if ( Login::checkPermission('#^navigation/NQ/secondary/#',true))
            {$criteria->condition = "NQ='Secondary'";}
            
        }
        
        
        $dps = DeliveryPoint::model()->findAll($criteria);
        $this->render('deliverypoints', array('dps'=>$dps));
    }

    public function actionDeliveryPoint($id = NULL)
    {
        $ui = isset($_GET['ui']) ? $_GET['ui'] : NULL;
        $this->layout = (isset($ui)) ? '//layouts/'.$ui : $this->layout;
        
        $model = new DeliveryPointForm();
        $contacts= array();

        // collect user input data
        if(isset($_POST['DeliveryPointForm']))
        {
            $model->setAttributes($_POST['DeliveryPointForm'], false);

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
                            $insert_id = Yii::app()->session['getLastIdDeliveryPoint'];
                            unset(Yii::app()->session['getLastIdDeliveryPoint']);
                            $this->redirect(array("admin/deliverypoint", 'id'=>$insert_id));
                        }
                    }
                    else if ( isset( $_POST['saveandexit'] ) )
                    {
                        $this->redirect(array('admin/deliverypoints'));
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
            $contacts = DeliverypointContact::model()->findAll($criteria);                
        }

        $this->render('deliverypoint', array('model' => $model, 'ui'=> $ui, 'contacts' => $contacts));
    }


    public function actionDeliveryPointContact($id = NULL)
    {
        $ui = isset($_GET['ui']) ? $_GET['ui'] : null;
        $this->layout = (isset($ui)) ? '//layouts/'.$ui : $this->layout;

        $model = new DeliveryPointContactForm();

        // collect user input data
        if(isset($_POST['DeliveryPointContactForm']))
        {
            //print_r($_POST);
            //yii::app()->end();
            
            $model->setAttributes($_POST['DeliveryPointContactForm'], false);

            // validate user input and redirect to the titles page if valid
            if($model->validate() && $model->save()) {
                if("popUp" === $ui) 
                {
                    echo "<script>parent.$.colorbox.close()</script>";
                    Yii::app()->end();
                    //echo "<script>location.reload();</script>";
                    //$this->redirect(array('admin/titles'));
                    
                    $redirectURL = Yii::app()->createUrl("admin/deliverypoints");
                    echo "<script>parent.window.location='$redirectURL';</script>";
                    
                }
                else
                    $this->redirect(array('admin/deliverypoints'));
              
            }
        }
        else {
            if (isset($id)) {
                //$model->populate($id);
            }
        }

        $this->render('contact', array('model' => $model, 'ui'=>$ui, 'DeliveryPointId'=>$_GET['DeliveryPointId']));
    }
    
    public function actionDeliveryPointContactdelete($contactid,$deliverypointid)
    {
        DeliverypointContact::model()->deleteByPk($contactid);
        $this->redirect(array('admin/deliverypoint','id'=>$deliverypointid));
    }    
    
    
    
    
    
    public function actionRoutes()
    {
        $criteria = new CDbCriteria();
        $criteria->select = 't.*,supplier.Name as supplierName';
        $criteria->condition = 't.IsLive = 1';
        $criteria->join ='LEFT OUTER JOIN supplier ON supplier.SupplierId = t.SupplierId';
        $criteria->order = 't.Name ASC, supplierName ASC';
        $routes = Route::model()->findAll($criteria);
        $this->render('routes', array('routes'=>$routes));
    }

    public function actionRoute($id = NULL)
    {
        $model = new RouteForm();

        if (isset($_GET["json"])) {
            if("dds" === $_GET["json"]) {
                $result = array();
                $result["titles"] = array();
                foreach ($model->getOptionsTitle() as $key => $value) {
                    $result["titles"][] = array('value'=>$key, 'text'=>$value);
                }
                $result["delpoints"] = array();
                foreach ($model->getOptionsDeliveryPoint() as $key => $value) {
                    $result["delpoints"][] = array('value'=>$key, 'text'=>$value);
                }
                
                echo CJSON::encode($result);

                Yii::app()->end();
            }
        }

        // collect user input data
        if(isset($_POST['RouteForm']))
        {
            $model->setAttributes($_POST['RouteForm'], false);
            $model->setRouteDetails($_POST['RouteDetails']);
            
            // validate user input and redirect to the titles page if valid
            if($model->validate() && $model->save()) {
                $this->redirect(array('admin/routes'));
            }
        }
        else {
            if (isset($id)) {
                $model->populate($id);
            }
        }

        $this->render('route', array('model' => $model));
    }
    
    public function actionRoutedelete($id)
    {
        RouteForm::deleteRoute($id);
        $this->redirect(array('admin/routes'));
    }

    public function actionOrders($rid = null)
    {
        $model = new AdminOrderGroupForm();

        // collect user input data
        if(isset($_POST['OrderDetails']))
        {
            //$model->setAttributes($_POST['OrderForm'], false);
            $model->setOrdersDetails($_POST['OrderDetails']);

            // validate user input and redirect to the titles page if valid
            if($model->validate() && $model->save($_POST['task'])) {
                //$this->redirect(array('client/titles'));
                $this->refresh();
            }
        }

        $this->render('orders', array('routeId'=>$rid, 'model' => $model));
    }

    public function actionOrdersEdit()
    {
        $model = new AdminOrderListing();
        $message = '';

        if(isset($_POST['OrderDetails']))
        {
            // save order details
            if($model->saveOrder($_POST['OrderDetails']))
                $message = 'Order information saved succesfully';
            else
                $message = 'There was an error while attempting to save, please try again.';
        }

        $model->populate();
        $this->render('ordersedit', array('model'=>$model, 'message'=>$message));
    }

    public function actionRouteInfo($id = null)
    {
        $resultset = array();
        if(isset($id))
        {
            // check if there is a SUBMITTED order for this route
            $order = Order::model()->find('RouteId=:rid AND Status=:stt', array(':rid'=>$id, 'stt'=>Order::STATUS_SUBMITTED));
            if (isset($order))
            {
                $resultset = array('error'=> 'There already is a submitted order for this route in the system');
                echo json_encode($resultset);
                Yii::app()->end();
                return;
            }
            // fetch route information
            $crit = new CDbCriteria();
            $crit->condition = 'RouteId=:rid';
            $crit->params = array(':rid'=>$id);
            $crit->order = "Sequence ASC";
            $routetitles = RouteDetails::model()->findAll($crit);

            foreach ($routetitles as $routetitle)
            {
                $tid = $routetitle->TitleId;
                if (!isset($resultset[$tid]))
                {
                    $result = array();
                    
                    // fetch title information
                    $title = AllTitles::model()->find("TitleId=:titleid", array(':titleid'=>$tid));
                    if(isset($title))
                    {
                        $titleInfo = array();
                        $titleInfo['id'] = $title->TitleId;
                        $titleInfo['name'] = $title->Name;
                        $titleInfo['printcentre'] = $title->PrintCentreName;
                        $titleInfo['pageweight'] = $title->WeightPerPage;
                        $result['title'] = $titleInfo;
                    }

                    // fetch last order information
                    $criteria = new CDbCriteria();
                    //$criteria->condition = 'TitleId=:titleId and Status!=:status';
                    //$criteria->params = array(':titleId' => $tid, ':status' => Order::STATUS_DRAFT);
                    $criteria->condition = 'TitleId=:titleId';
                    $criteria->params = array(':titleId' => $tid);
                    $criteria->order = 'DateCreated DESC';
                    $criteria->limit = 1;
                    $lastOrder = Order::model()->find($criteria);

                    $dpquery = new CDbCriteria();
                    $dpquery->condition = 'TitleId=:titleId';
                    $dpquery->params = array(':titleId' => $tid); // by default, populate delivery points list from route
                    $dpquery->order = "Sequence ASC";

                    if (isset($lastOrder))
                    {
                        $orderInfo = array();
                        $orderInfo['id'] = $lastOrder->OrderId;
                        $orderInfo['status'] = $lastOrder->Status;
                        $orderInfo['dateCreated'] = $lastOrder->DateUpdated;
                        $orderInfo['bundleSize'] = $lastOrder->BundleSize;
                        $orderInfo['pagination'] = $lastOrder->Pagination;
                        $orderInfo['publicationDate'] = $lastOrder->PublicationDate;
                        $orderInfo['deliveryDate'] = $lastOrder->DeliveryDate;
                        $result['order'] = $orderInfo;

                        // populate delivery points from order
                        // merging with delivery points from route (add new, delete old)
                        $dpquery->condition .= ' and OrderId=:orderId';
                        $dpquery->params[':orderId'] = $lastOrder->OrderId;  // add new parameter to new condition

                        $crit = new CDbCriteria();
                        $crit->condition = 'RouteId=:rid AND TitleId=:tid';
                        $crit->params = array(':rid' => $dplist[0]->RouteId, ':tid' => $tid);
                        $crit->order = "Sequence ASC";
                        $routedetails = AllRouteDetails::model()->findAll($crit);
                    }

                    $dplist = AllRouteOrderDetails::model()->findAll($dpquery);
                    $crit = new CDbCriteria();
                    $crit->condition = 'RouteId=:rid AND TitleId=:tid';
                    $crit->params = array(':rid' => $id, ':tid' => $tid);
                    $crit->order = "Sequence ASC";
                    $routedetails = AllRouteDetails::model()->findAll($crit);
                    $dpcount = count($dplist);
                    $rdcount = count($routedetails);
                    $dpInfo = array();
                    for ($i = 0; $i < $dpcount; $i++)
                    {
                        // merge with route information for updated delivery points
                        // delete currently non-existing ones
                        $found = false;
                        for ($j = 0; $j < $rdcount; $j++)
                        {
                            if($routedetails[$j]->DeliveryPointId == $dplist[$i]->DeliveryPointId)
                            {
                                $dplist[$i]->Sequence = $routedetails[$j]->Sequence; // update sort order
                                unset($routedetails[$j]);
                                $found = true;
                            }
                        }

                        if ($found)
                        {
                            $dp = array();
                            $dp['seq'] = $dplist[$i]->Sequence;
                            $dp['id'] = $dplist[$i]->DeliveryPointId;
                            $dp['name'] = $dplist[$i]->DelieryPointName;
                            $dp['copies'] = $dplist[$i]->Copies;
                            $dpInfo[] = $dp;
                        }
                    }

                    $routedetails = array_values($routedetails); // reorder array to delete unset values
                    $rdcount = count($routedetails);
                    // add new delivery points added to the route
                    for ($j = 0; $j < $rdcount; $j++)
                    {
                        $dp = array();
                        $dp['seq'] = $routedetails[$i]->Sequence;
                        $dp['id'] = $routedetails[$j]->DeliveryPointId;
                        $dp['name'] = $routedetails[$j]->DeliveryPointName;
                        $dp['copies'] = '';
                        $dpInfo[] = $dp;
                    }

                    if (count($dpInfo) > 0)
                    {
                        // sort dpInfo by seq
                        $seqIdx = array();
                        foreach ($dpInfo as $key => $row)
                        {
                            $seqIdx[$key]  = $row['seq'];
                        }
                        // Sort the data with seq ascending
                        // Add $data as the last parameter, to sort by the common key
                        array_multisort($seqIdx, SORT_ASC, $dpInfo);

                        $result['deliverypoints'] = $dpInfo;
                    }
                    $resultset[$tid] = $result;
                }
            }

            $jsonresult = array();
            $jsonresult['titles'] = array();
            foreach($resultset as $key => $details)
                $jsonresult['titles'][] = $details;
            $resultset = $jsonresult;

            // append route information
            $route = Route::model()->findByPk($id);
            if(isset($route))
            {
                $resultset['route']['id'] = $route->RouteId;
                $resultset['route']['name'] = $route->Name;
            }
        }
        else
            $resultset = array('error' => 'INVALID REQUEST');

        echo json_encode($resultset);
        Yii::app()->end();
    }

    public function actionReportingControl($archived = NULL)
    {
        if ($archived == null)
            $archived = '0';
        
        $model = new AdminReportingControl();

				if(isset($_POST['AdminReportingControl']))
            $model->setAttributes($_POST['AdminReportingControl'], false);

        $model->populate($archived);
        $this->render('reportingcontrol', array('model'=>$model));
    }

    public function actionReportingPallet()
    {
        $model = new AdminReportingPallet();
        if (isset($_GET['AdminReportingPallet']))
        {
            $model->setAttributes($_GET['AdminReportingPallet'], false);

            // validate user input and redirect to the suppliers page if valid
            if($model->validate()) {
                $model->populateReport();
            }
        }
        $this->render('reportingpallet', array('model'=>$model));
    }

    public function actionSecondaryRouting($axn = 'import')
    {
        $model = new SecondaryRoutingForm();
        $result = array();
        if(isset($_POST['SecondaryRoutingForm']))
        {
            //$model->attributes = $_POST['SecondaryRoutingForm'];
            $model->setAttributes($_POST['SecondaryRoutingForm'], false);
            $model->spreadSheet = CUploadedFile::getInstance($model, 'spreadSheet');
            if($model->validate()) // validates file type
            {
                $model->uploadedFileName = '_uploads/'.date('YmdHi').'-'.$model->spreadSheet->getName();
                $model->spreadSheet->saveAs($model->uploadedFileName);
                // redirect to success page
                $result = $model->processFile($axn);
            }
        }
        $this->render('secondaryrouting', array('model'=>$model, 'axn'=>$axn, 'result'=>$result));
    }

    public function actionSecondaryRoutingExport($id, $format = 'xls')
    {
        $criteria = new CDbCriteria();
        $criteria->condition = 'SecondaryRouteId=:rid and Enabled = 1';
        $criteria->params = array(':rid'=>$id);
        $criteria->order = "SortOrder ASC";
        $details = AllSecondaryRoute::model()->findAll($criteria);

        // get a reference to the path of PHPExcel classes
        $phpExcelPath = Yii::getPathOfAlias('ext.phpexcel.Classes');
        // Turn off YII library autoload
        spl_autoload_unregister(array('YiiBase','autoload'));

        // PHPExcel_IOFactory
        require_once $phpExcelPath.'/PHPExcel.php';

        $objPHPExcel = new PHPExcel();

        $objPHPExcel->getDefaultStyle()->getFont()->setSize(13);
        $objPHPExcel->setActiveSheetIndex(0);
        $this->addHeader($objPHPExcel, 1);

        // consolidate by name/address
        $count = count($details);
        $newData = array();
        $consolidatedMark = ' (*)';
        for ($i = 0; $i < $count; $i++) { // 12/02/2013 - changed consolidation algorithm
            
            //$idx = strtolower(trim($details[$i]->Name))
            //        .strtolower(trim($details[$i]->Surname))
            //        .strtolower(trim($details[$i]->Address));
            $address = strtolower(trim($details[$i]->Address));     // 12/02/2013 -- consolidate by address as requested
            
            // verify whether it ends with phone #
            $last = substr($address, -13);
            $idx = $address;
            if(preg_match('/^[\d -]+$/', trim($last))) { // valid tel number.
                $idx = substr($address, 0, -13); // remove last 13 chars to omit the phone # in the address
                if ($idx === FALSE)
                    $idx = $address;
            }
            
            if (!isset($newData[$idx]))
                $newData[$idx] = $details[$i];
            else
            {
                $newData[$idx]->Quantity += $details[$i]->Quantity;
                if (stristr($newData[$idx]->SecondaryRoundId, $consolidatedMark) === FALSE)
                    $newData[$idx]->SecondaryRoundId .= $consolidatedMark;
            }
        }
        $details = array_values($newData); // reindex        

        //for ($i = ($count-1); $i > 0; $i--) {
            //$same = (($details[$i]->Name == $details[$i-1]->Name) && 
            //        ($details[$i]->Surname == $details[$i-1]->Surname) &&
            //        ($details[$i]->Address == $details[$i-1]->Address));
            
            //if ($same)
            //{
            //    $details[$i-1]->Quantity += $details[$i]->Quantity;
            //    $details[$i-1]->SecondaryRoundId = $details[$i-1]->SecondaryRoundId."(*)";
            //    unset($details[$i]);
            //}
        //}
        //$details = array_values($details); // reindex
        
        // Add data
        $count = count($details);
        $offset = 2;
        $copies = 0;
        for ($i = 0; $i < $count; $i++) {
            $objPHPExcel->getActiveSheet()->setCellValue('A'.($i+$offset), $details[$i]->SecondaryRoundId);
            $objPHPExcel->getActiveSheet()->setCellValue('B'.($i+$offset), $details[$i]->Name);
            $objPHPExcel->getActiveSheet()->setCellValue('C'.($i+$offset), $details[$i]->Surname);
            $objPHPExcel->getActiveSheet()->setCellValue('D'.($i+$offset), $details[$i]->Address);
            $objPHPExcel->getActiveSheet()->setCellValue('E'.($i+$offset), $details[$i]->SecondaryRouteId);

            $titleCode = "";
            $routebd = explode('-', $details[$i]->SecondaryRouteId);
            if (count($routebd) > 1)
                $titleCode = $routebd[0];

            $objPHPExcel->getActiveSheet()->setCellValue('F'.($i+$offset), $titleCode);
            $objPHPExcel->getActiveSheet()->setCellValue('G'.($i+$offset), $details[$i]->Quantity);

            $objPHPExcel->getActiveSheet()->setCellValue('H'.($i+$offset), floor($details[$i]->Quantity/$details[$i]->BundleSize));
            $objPHPExcel->getActiveSheet()->setCellValue('I'.($i+$offset), ($details[$i]->Quantity % $details[$i]->BundleSize));
            
            //remove 0 from comment
            if ( $details[$i]->Comments == '0' )
            {
                $details[$i]->Comments = "";
            }
            
            $objPHPExcel->getActiveSheet()->setCellValue('J'.($i+$offset), $details[$i]->Comments);

            $copies += $details[$i]->Quantity;

            /* changed to fit one page width by 2 pages height
             * if (($i+$offset) % 30 == 0) {

                // Add a page break
                $objPHPExcel->getActiveSheet()->setBreak( 'A'.($i+$offset), PHPExcel_Worksheet::BREAK_ROW );
                $offset++;
                $this->addHeader($objPHPExcel, ($i+$offset));
            }
             *
             */
        }

        // write footer
        $styleArray = array(
            'font' => array(
                'bold' => true,
                'italic' => true
            )
        );
        $offset++;
        $objPHPExcel->getActiveSheet()->setCellValue('A'.($count+$offset), 'Bundle Size');
        $objPHPExcel->getActiveSheet()->getStyle('A'.($count+$offset))->applyFromArray($styleArray);
        $objPHPExcel->getActiveSheet()->setCellValue('B'.($count+$offset), $details[0]->BundleSize);
        
        if ($details[0]->BundleWeight != 0)
        {
            $objPHPExcel->getActiveSheet()->setCellValue('A'.($count+$offset+1), 'Bundle Weight');
            $objPHPExcel->getActiveSheet()->getStyle('A'.($count+$offset+1))->applyFromArray($styleArray);
            $objPHPExcel->getActiveSheet()->setCellValue('B'.($count+$offset+1), $details[0]->BundleWeight);
        }
        
        $objPHPExcel->getActiveSheet()->setCellValue('F'.($count+$offset), 'Copies');
        $objPHPExcel->getActiveSheet()->getStyle('F'.($count+$offset))->applyFromArray($styleArray);
        $objPHPExcel->getActiveSheet()->setCellValue('G'.($count+$offset), $copies);
        $offset++;
        $objPHPExcel->getActiveSheet()->setCellValue('F'.($count+$offset), 'Bundles');
        $objPHPExcel->getActiveSheet()->getStyle('F'.($count+$offset))->applyFromArray($styleArray);
        $objPHPExcel->getActiveSheet()->setCellValue('G'.($count+$offset), floor($copies / $details[0]->BundleSize));
        $offset++;
        $objPHPExcel->getActiveSheet()->setCellValue('F'.($count+$offset), 'Odds');
        $objPHPExcel->getActiveSheet()->getStyle('F'.($count+$offset))->applyFromArray($styleArray);
        $objPHPExcel->getActiveSheet()->setCellValue('G'.($count+$offset), ($copies % $details[0]->BundleSize));
        
        if ($details[0]->BundleWeight != 0)
        {
            $offset++;
            $bundlesWeight = (floor($copies / $details[0]->BundleSize) * $details[0]->BundleWeight);
            $objPHPExcel->getActiveSheet()->setCellValue('F'.($count+$offset), 'Bundles Weight');
            $objPHPExcel->getActiveSheet()->getStyle('F'.($count+$offset))->applyFromArray($styleArray);
            $objPHPExcel->getActiveSheet()->setCellValue('G'.($count+$offset), round($bundlesWeight, 2) );
            
            $offset++;
            $totalWeight = ($details[0]->BundleWeight / $details[0]->BundleSize) * $copies;
            $objPHPExcel->getActiveSheet()->setCellValue('F'.($count+$offset), 'Total Weight');
            $objPHPExcel->getActiveSheet()->getStyle('F'.($count+$offset))->applyFromArray($styleArray);
            $objPHPExcel->getActiveSheet()->setCellValue('G'.($count+$offset), round($totalWeight, 2) );
        }

        // page setup
        $objPageSetup = new PHPExcel_Worksheet_PageSetup();
        $objPageSetup->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);
        $objPageSetup->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);

        $objPageSetup->setFitToHeight(2);
        $objPageSetup->setFitToWidth(1);
        $objPageSetup->setRowsToRepeatAtTopByStartAndEnd(1);

        $objPHPExcel->getActiveSheet()->setPageSetup($objPageSetup);
        $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('J')->setAutoSize(true);

        if($format == 'xls')
        {
            // Redirect output to a clientâ€™s web browser (Excel2003)
            header('Content-Type: application/excel');
            //header('Content-Disposition: attachment;filename="'.date('YmdHi').'-'.$id.'.xls"');
            header('Content-Disposition: attachment;filename="'.date('YmdHi').'-'.$id.'.xlsx"'); // #43
            header('Cache-Control: max-age=0');

            //$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
            $objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel); // #43
            $objWriter->save('php://output');
        }
        if($format == 'pdf')
        {
            // Redirect output to a clientâ€™s web browser (PDF)
            header('Content-Type: application/pdf');
            header('Content-Disposition: attachment;filename="'.date('YmdHi').'-'.$id.'.pdf"');
            header('Cache-Control: max-age=0');

            $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'PDF');
            $objWriter->save('php://output');
        }


        Yii::app()->end();

        // Once we have finished using the library, give back the
        // power to Yii...
        spl_autoload_register(array('YiiBase','autoload'));
    }

    public function actionSecondaryRouteDelete($secondaryrouteid)
    {
        $routeIds = explode('|', $secondaryrouteid);
        foreach ($routeIds as $rid)
            SecondaryRoute::model()->deleteAll("secondaryrouteid = :rid", array(
                ':rid' => $rid
            ));
        
        $this->redirect(array('admin/secondaryrouting'));        
    }
    
    public function addHeader($objPHPExcel, $idx)
    {
        $styleArray = array(
            'font' => array(
                'bold' => true,
                'italic' => true
            )
        );
        $objPHPExcel->getActiveSheet()->setCellValue('A'.$idx, "Round Id");
        $objPHPExcel->getActiveSheet()->getStyle('A'.$idx)->applyFromArray($styleArray);
        $objPHPExcel->getActiveSheet()->setCellValue('B'.$idx, "Name");
        $objPHPExcel->getActiveSheet()->getStyle('B'.$idx)->applyFromArray($styleArray);
        $objPHPExcel->getActiveSheet()->setCellValue('C'.$idx, "Surname");
        $objPHPExcel->getActiveSheet()->getStyle('C'.$idx)->applyFromArray($styleArray);
        $objPHPExcel->getActiveSheet()->setCellValue('D'.$idx, "Address");
        $objPHPExcel->getActiveSheet()->getStyle('D'.$idx)->applyFromArray($styleArray);
        $objPHPExcel->getActiveSheet()->setCellValue('E'.$idx, "Route Id");
        $objPHPExcel->getActiveSheet()->getStyle('E'.$idx)->applyFromArray($styleArray);
        $objPHPExcel->getActiveSheet()->setCellValue('F'.$idx, "Title Code");
        $objPHPExcel->getActiveSheet()->getStyle('F'.$idx)->applyFromArray($styleArray);
        $objPHPExcel->getActiveSheet()->setCellValue('G'.$idx, "Copies");
        $objPHPExcel->getActiveSheet()->getStyle('G'.$idx)->applyFromArray($styleArray);

        $objPHPExcel->getActiveSheet()->setCellValue('H'.$idx, "Bundles");
        $objPHPExcel->getActiveSheet()->getStyle('H'.$idx)->applyFromArray($styleArray);
        $objPHPExcel->getActiveSheet()->setCellValue('I'.$idx, "Odds");
        $objPHPExcel->getActiveSheet()->getStyle('I'.$idx)->applyFromArray($styleArray);
        
        $objPHPExcel->getActiveSheet()->setCellValue('J'.$idx, "Comments");
        $objPHPExcel->getActiveSheet()->getStyle('J'.$idx)->applyFromArray($styleArray);
    }

    public function actionSecondaryRoutingRoutes()
    {
        $model = new SecondaryRoutingForm();
        if(isset($_POST['RoutesForm']))
        {
            $model->saveRoutes($_POST['RoutesForm']);
            $this->redirect(array(Yii::app()->user->role->HomeUrl));
        }
        $this->render('secondaryroutingroutes', array('model'=>$model));
    }

    public function actionSecondaryRoutingRoute($id = null)
    {
        if (!isset($id))
            throw new CHttpException ("500", "Bad Request");

        $model = new SecondaryRoutingRouteForm();

        if(isset($_POST['SecondaryRoutingRouteForm']))
        {
            $model->setAttributes($_POST['SecondaryRoutingRouteForm'], false);
            if ($model->axn == "move")
            {
                $model->moveRounds();
                $this->redirect(array('admin/secondaryroutingroute', 'id' => $model->selectedRoute));
            }
            elseif ($model->axn == "delete")
            {
                $model->deleteRounds();
            }
            else
                if($model->save())
                    $this->redirect(array('admin/secondaryroutingroutes'));
        }
        $model->populate($id);

        $this->render('secondaryroutingroute', array('model'=>$model));
    }

    public function actionSecondaryRoutingRouteDelete($id = null)
    {
        if (isset($id))
        {
            SecondaryRoutingRouteForm::deleteRoute($id);
            $this->redirect(array('admin/secondaryroutingroutes'));
        }
    }
    
    public function actionPrintpalletsheets()
    {
        $model = new PalletsSheetPrint();
        
        if(isset($_POST['PalletsSheetPrint']))
        {
            $model->setAttributes($_POST['PalletsSheetPrint'], false);            
            // return xls file (prompt download) and exit.
            $model->generateSheetDownload();
            Yii::app()->end();
        }
        
        $this->render('palletsheetsprint', array('model' => $model));
    }
    
    public function actionPrintpalletsheetspc()
    {
        $model = new PalletsSheetPrintPC();
        
        if(isset($_POST['PalletsSheetPrintPC']))
        {
            $model->setAttributes($_POST['PalletsSheetPrintPC'], false);            
            // return xls file (prompt download) and exit.
            $model->generateSheetDownload();
            Yii::app()->end();
        }
        
        $this->render('palletsheetsprintpc', array('model' => $model));
    }
    
    
    public function actionUploadpalletsheet()
    {
        $model = new PalletsSheetUpload();
        $result = array();
        if(isset($_POST['PalletsSheetUpload']))
        {
            $model->setAttributes($_POST['PalletsSheetUpload'], false);
            $model->spreadSheet = CUploadedFile::getInstance($model, 'spreadSheet');
            if($model->validate()) // validates file type
            {
                $model->uploadedFileName = '_uploads/pallets/'.date('YmdHi').'-'.$model->spreadSheet->getName();
                $model->spreadSheet->saveAs($model->uploadedFileName);
                // redirect to success page
                $result = $model->processFile();
            }
        }
        //upload file again
        if(isset($_POST['uploadFileAgain'])){
            $model->uploadedFileName = $_POST['filename'];
            $model->uploadFileAgain = true;
            $result = $model->processFile();
        }
        
        $this->render('palletsheetsupload', array('model' => $model, 'result' => $result));        
    }
    
    public function actionUploadpalletsheetpc()
    {
        $model = new PalletsSheetUploadPC();
        $result = array();
        if(isset($_POST['PalletsSheetUploadPC']))
        {
            $model->setAttributes($_POST['PalletsSheetUploadPC'], false);
            $model->spreadSheet = CUploadedFile::getInstance($model, 'spreadSheet');
            if($model->validate()) // validates file type
            {
                $model->uploadedFileName = '_uploads/pallets/'.date('YmdHi').'-'.$model->spreadSheet->getName();
                $model->spreadSheet->saveAs($model->uploadedFileName);
                // redirect to success page
                $result = $model->processFile();
            }
        }
        //upload file again
        if(isset($_POST['uploadFileAgain'])){
            $model->uploadedFileName = $_POST['filename'];
            $model->uploadFileAgain = true;
            $result = $model->processFile();
        }
        
        $this->render('palletsheetsuploadpc', array('model' => $model, 'result' => $result));        
    }
    
    public function actionPalletreport()
    {
        $model = new PalletsReport();
        $dp = null;
        if(isset($_POST['PalletsReport']))
        {
            $model->setAttributes($_POST['PalletsReport'], false);
            if ($model->validate())
            {
                $getCsv = !empty($_POST['csv']) ? true : false;
                $dp = $model->getReportDp($getCsv);
            }
        }
        $this->render('palletreport', array('model' => $model, 'dp' => $dp));
    }
    
    public function actionPalletreportpc()
    {
        $model = new PalletsReportPC();
        $dp = null;
        if(isset($_POST['PalletsReportPC']))
        {
            $model->setAttributes($_POST['PalletsReportPC'], false);
            if ($model->validate())
            {
                $getCsv = !empty($_POST['csv']) ? true : false;
                $dp = $model->getReportDp($getCsv);
            }
        }
        $this->render('palletreportpc', array('model' => $model, 'dp' => $dp));
    }
    
    public function actionPalletreportdp()
    {
        $model = new PalletsReportDP();
        $dp = null;
        if(isset($_POST['PalletsReportDP']))
        {
            $model->setAttributes($_POST['PalletsReportDP'], false);
            if ($model->validate())
            {
                $getCsv = !empty($_POST['csv']) ? true : false;
                $dp = $model->getReportDp($getCsv);
            }
        }
        $this->render('palletreportdp', array('model' => $model, 'dp' => $dp));
    }
    
    public function actionAreas()
    {
        $criteria = new CDbCriteria();
        $criteria->order = 'Name ASC';
        $areas = Area::model()->findAll($criteria);
        $this->render('areas', array('areas'=>$areas));
    }
    
    public function actionArea($id = NULL)
    {
        $model = new AreaForm;

        // collect user input data
        if(isset($_POST['AreaForm']))
        {
            $model->setAttributes($_POST['AreaForm'], false);
            
            if($model->validate() && $model->save()) {
                $this->redirect(array('admin/areas'));
            }
        }
        else {
            if (isset($id)) {
                $model->populate($id);
            }
        }
        
        $this->render('area', array('model'=>$model));
    }
    
    public function actionRoles($isLive = 1)
    {
        $criteria = new CDbCriteria();

        if ($isLive == 1){
            $criteria->addCondition("IsLive = 1");
        }

        $criteria->order = 'Name ASC';
        $roles = Role::model()->with("DefaultForLoginRole")->findAll($criteria);
        $this->render('roles', array('roles' => $roles, "isLive" => $isLive));
    }


    public function actionRole($id = NULL)
    {
        $model = new RoleForm;

        if (isset($id)){
            $model->roleId = $id;
        }

        // collect user input data
        if(isset($_POST['RoleForm']))
        {
            $model->setAttributes($_POST['RoleForm'], false);
            // validate user input and redirect to the suppliers page if valid
            if($model->validate() && $model->save()) {
                $this->redirect(array('admin/roles'));
            }
        } else {
            $model->populate();
        }

        $this->render('role', array('model'=>$model));
    }
}
?>
