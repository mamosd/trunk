<?php
/**
 * Description of SupplierRoute
 *
 * @author Ramon
 */
class SupplierRoute extends CFormModel
{
    public $routeDetails;

    public function printRoute($routeInstanceId)
    {
        // check if there are routeinstancedetails
        $ridc = new CDbCriteria();
        $ridc->condition = 'RouteInstanceId=:rid';
        $ridc->params = array('rid' => $routeInstanceId);
        $ridc->order = 'PrintCentreName ASC, TitleName ASC, Sequence ASC';
        $rids = AllRouteInstanceDetails::model()->findAll($ridc);
        
        if (count($rids) == 0)
        {
            // NO - create them
            // create routeinstancedetails with order details
            RouteInstanceManager::createDetails($routeInstanceId);
            // flag orders for edit lockdown
            RouteInstanceManager::lockOrders($routeInstanceId);

            $rids = AllRouteInstanceDetails::model()->findAll($ridc);
        }

        // set the list of the routeinstancedetails (AllRouteInstanceDetails)
        //return $rids;
        $this->routeDetails = $rids;
    }
}
?>
