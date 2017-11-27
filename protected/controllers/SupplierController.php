<?php
/**
 * Description of SupplierController
 *
 * @author Ramon
 */
class SupplierController extends Controller {
/*
    // this is to be retrieved by implementation (hosting customer) when SaaS
    public $menu = array(
            array('label'=>'Home', 'url'=>array('supplier/routes')),
        );
*/
    /**
     * Used to retrieve the users with granted access to this controller's actions.
     * @return <type>
     */
    public function getValidUsers()
    {
        $result = array();
        $logins = Login::model()->findAll("LoginRoleId=:role OR LoginRoleId=:admin OR LoginRoleId=:sadmin",
                    array(":role"=>LoginRole::SUPPLIER, ':admin' => LoginRole::ADMINISTRATOR, ":sadmin" => LoginRole::SUPER_ADMIN));
        foreach ($logins as $login) {
            $result[] = $login->UserName;
        }
        return $result;
    }

    public function actionIndex()
    {
        //$this->render('index');
        $this->redirect(array('supplier/routes'));
    }

    public function actionRoutes($status = null)
    {
        $model = new SupplierRoutes();

        if($status==null)
            $status = RouteInstance::STATUS_ACTIVE;

        $model->populate(Yii::app()->user->loginId, $status);
        $this->render('routes', array('model'=>$model));
    }

    public function actionRoutePrint($id = null)
    {
        $this->layout = '//layouts/popUp';
        // validate route id and supplier id
        $model = new SupplierRoute();
        if (isset($id))
        {
            $model->printRoute($id);
        }

        $this->render("routeprint", array('model'=>$model));
    }

    public function actionRouteStatus()
    {
        $model = new SupplierRoutes();
        $model->populate(Yii::app()->user->loginId);

        $result = array();
        $result['routes'] = array();
        foreach($model->routes as $route)
        {
            $routedetails = array();
            $routedetails['id'] = $route->RouteInstanceId;
            $routedetails['isPrinted'] = $route->IsPrinted;
            $routedetails['departureTime'] = $route->DepartureTime;
            $result['routes'][] = $routedetails;
        }
        echo json_encode($result);
        Yii::app()->end();
    }

    public function actionRouteDepartureTime($id = null)
    {
        $this->layout = '//layouts/popUp';
        $model = new SupplierDepartureTimeForm();

        // collect user input data
        if(isset($_POST['SupplierDepartureTimeForm']))
        {
            $model->setAttributes($_POST['SupplierDepartureTimeForm'], false);

            // validate user input and redirect to the titles page if valid
            if($model->validate() && $model->save())
            {
                echo "<script>parent.$.colorbox.close()</script>";
                Yii::app()->end();
            }
        }
        else {
            if (isset($id)) {
                $model->populate($id);
            }
        }

        $this->render('routedeparturetime', array('model'=>$model));
    }

    public function actionRouteDeliveryInfo($id)
    {
        $this->layout = '//layouts/popUp';
        $model = new SupplierDeliveryInformation();

        // collect user input data
        if(isset($_POST['SupplierDeliveryInformation']))
        {
            $model->setAttributes($_POST['SupplierDeliveryInformation'], false);

            // validate user input and redirect to the titles page if valid
            if($model->validate() && $model->save($_POST['Details']))
            {
                RouteInstanceManager::setStatus($id, RouteInstance::STATUS_ARCHIVED); // set as archived once all data is entered

                echo "<script>parent.$.colorbox.close()</script>";
                Yii::app()->end();
            }
        }
        else {
            if (isset($id)) {
                $model->populate($id);
            }
        }

        $this->render('routedeliveryinfo', array('model'=>$model));
    }

    public function actionRouteArchive($id, $status = null)
    {
        if ($status == null)
            $status = RouteInstance::STATUS_ARCHIVED;

        $result = array("result" => "OK");

        if (!RouteInstanceManager::setStatus($id, $status))
            $result['result'] = 'Error updating the route, please try again later';

        /*$ri = RouteInstance::model()->findByPk($id);
        if(isset($ri))
        {
            $ri->Status = $status;
            if (!$ri->save())
                $result['result'] = 'Error updating the route, please try again later';
        }
        else
            $result['result'] = 'Route could not be found';
        */
        echo json_encode($result);
        Yii::app()->end();
    }
}
?>
