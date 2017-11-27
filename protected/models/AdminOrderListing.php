<?php
/**
 * Description of AdminOrderListing
 *
 * @author Ramon
 */
class AdminOrderListing extends CFormModel
{
    public $orders;

    public function populate()
    {
        $criteria = new CDbCriteria();
        $criteria->condition = 'Status=:Status';
        $criteria->params = array(':Status' => Order::STATUS_SUBMITTED);
        $criteria->order = 'DateUpdated DESC, TitleName ASC';
        $this->orders = AllOrders::model()->findAll($criteria);
    }

    public function getOrderDetails($orderId)
    {
        $criteria = new CDbCriteria();
        $criteria->condition = 'OrderId=:OrderId';
        $criteria->params = array(':OrderId' => $orderId);
        $criteria->order = 'Sequence ASC';
        return AllOrderDetails::model()->findAll($criteria);
    }

    public function getOptionsDeliveryPoint()
    {
        $result = array();
        $criteria = new CDbCriteria();
        $criteria->order = "Name ASC";
        $dps = DeliveryPoint::model()->findAll($criteria);
        foreach ($dps as $dp) {
            $result[$dp->DeliveryPointId] = $dp->Name;
        }
        return $result;
    }

    public function saveOrder($arr)
    {
        foreach ($arr as $orderId => $details) {
            $oldOrder = null;
            $orderId = $details['orderid'];

            $order = Order::model()->findByPk($orderId);
            // history begin
                // save current order for history purposes
                Order::model()->updateByPk($order->OrderId, array('Status'=>($order->Status.'-'.Order::STATUS_CHANGED)));
                $oldOrder = $order;

                // generate new OrderHistory
                $orderHistory = new OrderHistory();
                $orderHistory->OrderId = $oldOrder->OrderId;
                $orderHistory->NewOrderId = $oldOrder->OrderId;
                $orderHistory->save();

                // generate new Order
                $order = new Order();
                $order->DateCreated = new CDbExpression('NOW()');
                $order->TitleId = $oldOrder->TitleId;
                $order->Status = $oldOrder->Status;
            // history end
            $order->DateUpdated = new CDbExpression('NOW()');
            $order->UpdatedBy = Yii::app()->user->name;
            $order->Pagination = $details['pagination'];
            $order->BundleSize = $details['bundleSize'];
            $order->PublicationDate = $details['publicationDate'];
            $order->DeliveryDate = $details['deliveryDate'];
            $order->RouteId = $details['routeId'];
            //$order->Status = $status;

            if ($order->save())
            {
                OrderDetails::model()->deleteAll('OrderId=:orderId', array(':orderId'=>$order->OrderId));

                foreach ($details as $key => $value)
                {
                    $cfld = "copies";
                    $dpfld = "delpoint";
                    $dpflddesc = "descdelpoint";
                    if($cfld == substr($key, 0, strlen($cfld)))
                    {
                        $idx = substr($key, strlen($cfld));
                        $od = new OrderDetails();
                        $od->OrderId = $order->OrderId;
                        $od->Sequence = $idx;
                        $od->DeliveryPointId = $details[$dpfld.$idx];
                        $od->Copies = $value;
                        $od->save();
                    }
                }

                // if there is a history, update it with new order id
                if ($oldOrder != null)
                    OrderHistory::model()->updateAll(array('NewOrderId' => $order->OrderId), 'NewOrderId=:oldorderid', array(':oldorderid' => $oldOrder->OrderId));

                // update ROUTE INSTANCE order reference with new order id
                RouteInstanceManager::addOrder($order->OrderId);
            }
            else
            {
                return FALSE;
            }
        }

        return TRUE;
    }
}
?>
