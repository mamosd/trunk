<?php
/**
 * Description of OrderListing
 *
 * @author Ramon
 */
class OrderListing extends CFormModel
{
    public $orders;

    public function populate($loginId)
    {
        $criteria = new CDbCriteria();
        $criteria->condition = 'Status=:Status and LoginId=:LoginId';
        $criteria->params = array(':Status' => Order::STATUS_SUBMITTED, ':LoginId' => $loginId);
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
            $orderId = $details['orderid'];

            $order = Order::model()->findByPk($orderId);
            $order->DateUpdated = new CDbExpression('NOW()');
            $order->UpdatedBy = Yii::app()->user->name;
            $order->Pagination = $details['pagination'];
            $order->BundleSize = $details['bundleSize'];
            $order->PublicationDate = $details['publicationDate'];
            $order->DeliveryDate = $details['deliveryDate'];
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
