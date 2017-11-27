<?php
/**
 * Description of RouteInstanceManager
 *
 * @author Ramon
 */
class RouteInstanceManager {

    /**
     *
     * @param INT $orderId
     * @return INT RouteInstanceId
     */
    public static function addOrder($orderId)
    {
        // fetch order history, if any
        $ohCriteria = new CDbCriteria();
        $ohCriteria->condition = 'NewOrderId=:newid';
        $ohCriteria->params = array(':newid' => $orderId);
        $ohCriteria->order = 'OrderId DESC';
        $orderHistory = OrderHistory::model()->find($ohCriteria); // latest record from history

        // is order (or any of the historic) on a route?
        $onRoute = null;
        if (isset($orderHistory))
            $onRoute = RouteInstanceOrder::model()->find('OrderId=:oid OR OrderId=:poid', array(':oid' => $orderId, ':poid' => $orderHistory->OrderId));
        else
            $onRoute = RouteInstanceOrder::model()->find('OrderId=:oid', array(':oid' => $orderId));

        // NO
        if (!isset($onRoute))
        {
            // fetch delivery date from order
            $order = Order::model()->findByPk($orderId);
            $deliveryDate = $order->DeliveryDate;
            $titleId = $order->TitleId;

            // fetch route id from title
            $routeDetails = RouteDetails::model()->find('TitleId=:tid', array(':tid' => $titleId));
            $routeId = $routeDetails->RouteId;

            // check if there is a route instance for that delivery date
            $routeInstance = RouteInstance::model()->find('RouteId=:rid AND Date=:date', array(':rid' => $routeId, ':date' => $deliveryDate));

            if(!isset($routeInstance))
            {
                // fetch vehicle id
                $routeInfo = AllRouteSuppliers::model()->find('RouteId=:rid', array(':rid' => $routeId));

                // create ROUTE INSTANCE
                $routeInstance = new RouteInstance();
                $routeInstance->RouteId = $routeId;
                $routeInstance->SupplierId = $routeInfo->SupplierId;
                $routeInstance->VehicleId = $routeInfo->DefaultVehicleId;
                $routeInstance->Date = $deliveryDate;

                $routeInstance->DateCreated = new CDbExpression('NOW()');
                $routeInstance->UpdatedBy = Yii::app()->user->name;
                $routeInstance->save();
            }

            // append ORDER to new ROUTE INSTANCE
            $onRoute = new RouteInstanceOrder();
            $onRoute->RouteInstanceId = $routeInstance->RouteInstanceId;
            $onRoute->OrderId = $orderId;
            $onRoute->save();
        }
        else
        {
            // YES -- update order id on routeinstanceorder with new orderId
            $onRoute->OrderId = $orderId;
            $onRoute->save();
        }

        return $onRoute->RouteInstanceId;
    }

    public static function createDetails($routeInstanceId)
    {
        $riods = AllRouteInstanceOrderDetails::model()->findAll('RouteInstanceId=:rid', array(':rid'=>$routeInstanceId));
        foreach ($riods as $riod)
        {
            $rid = new RouteInstanceDetails();
            $rid->RouteInstanceId = $riod->RouteInstanceId;
            $rid->OrderDetailsId = $riod->OrderDetailsId;
            $rid->save();
        }
        $ri = RouteInstance::model()->find('RouteInstanceId=:rid', array('rid' => $routeInstanceId));
        $ri->DateDetailsCreated = new CDbExpression('NOW()');
        $ri->DetailsCreatedBy = Yii::app()->user->name;
    }

    public static function lockOrders($routeInstanceId)
    {
        $rios = RouteInstanceOrder::model()->findAll('RouteInstanceId=:rid', array(':rid'=>$routeInstanceId));
        foreach ($rios as $rio)
        {
            $order = Order::model()->findByPk($rio->OrderId);
            if (isset($order))
            {
                $order->Status = $order->Status.' - '.Order::STATUS_PRINTED;
                $order->save();
            }
        }
    }

    public static function setStatus($routeInstanceId, $status)
    {
        $ri = RouteInstance::model()->findByPk($routeInstanceId);
        if(isset($ri))
        {
            $ri->Status = $status;
            return $ri->save();
        }
        return false;
    }
}
?>
