<?php
/**
 * Description of SupplierDeliveryInformation
 *
 * @author Ramon
 */
class SupplierDeliveryInformation extends CFormModel
{
    public $routeInstanceId;
    public $routeName;
    public $date;
    public $departureTime;
    public $showDetailed;
    public $details;
    public $status;

    public function populate($id)
    {
        $ridc = new CDbCriteria();
        $ridc->condition = 'RouteInstanceId=:rid';
        $ridc->params = array('rid' => $id);
        $ridc->order = 'PrintCentreName ASC, TitleName ASC, Sequence ASC';

        $details = AllRouteInstanceDetails::model()->findAll($ridc);
        if(count($details) > 0)
        {
            $this->routeInstanceId = $id;
            
            $route = AllRouteInstanceSuppliers::model()->find('RouteInstanceId=:rid', array(':rid'=>$id));
            $this->date = $route->Date;
            $this->routeName = $route->RouteName;
            $this->departureTime = $route->DepartureTime;
            $this->showDetailed = $route->ShowDetailed;
            $this->status = $route->Status;

            $this->details = $details;
        }
    }

    public function save($post)
    {
        foreach($post as $key => $detail)
        {
            $rid = RouteInstanceDetails::model()->findByPk($key);
            if (isset($rid))
            {
                //$rid->DeliveryTime = $detail['deliveryTime'];
                $rid->DeliveryTime = $detail['deliveryTimeHH'].':'.$detail['deliveryTimeMM'];
                $rid->PalletsCollected = $detail['palletsCollected'];
                $rid->PalletsDelivered = $detail['palletsDelivered'];
                $rid->save();
            }
        }
        return true;
    }

}
?>
