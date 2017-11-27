<?php
/**
 * Description of AdminOrderGroupForm
 *
 * @author Ramon
 */
class AdminOrderGroupForm extends CFormModel
{
    public $orderDetails;
    public $message;
/*
    public function getTitles()
    {
        $criteria = new CDbCriteria();
        $criteria->order = "Name ASC";
        $titles = AllTitles::model()->findAll($criteria);
        return $titles;
    }
 *
 */
    public function getRoutes()
    {
        $criteria = new CDbCriteria();
        $criteria->order = "Name ASC";
        $routes = Route::model()->findAll($criteria);
        return $routes;
    }

    public function getOptionsRoutes()
    {
        $result = array();
        $criteria = new CDbCriteria();
        $criteria->condition = 'IsLive = 1';
        $criteria->order = "Name ASC";
        $routes = Route::model()->findAll($criteria);

        foreach ($routes as $route)
            $result[$route->RouteId] = $route->Name;

        return $result;
    }

    public function getDraft()
    {
        $result = array();

        $criteria = new CDbCriteria();
        $criteria->condition = 'Status=:status';
        $criteria->params = array(':status' => Order::STATUS_DRAFT);
        $orders = AllOrders::model()->findAll($criteria);
        $countorders = count($orders);

        for ($i = 0; $i < $countorders; $i++)
        {
            $order = $orders[$i];
            $o = array();

            $o['titlename'] = $order->TitleName;
            $o['printcentrename'] = $order->PrintCentreName;
            $o['orderid'] = $order->OrderId;

            $o['pagination'] = $order->Pagination;
            $o['bundlesize'] = $order->BundleSize;
            $o['publicationdate'] = $order->PublicationDate;
            $o['deliverydate'] = $order->DeliveryDate;
            $o['status'] = $order->Status;

            $ods = AllOrderDetails::model()->findAll('OrderId=:orderid', array(':orderid' => $order->OrderId));
            $countods = count($ods);
            $details = array();
            for ($j = 0; $j < $countods; $j++)
            {
                $line = array();
                $line['delpoint'] = $ods[$j]->DeliveryPointId;
                $line['descdelpoint'] = $ods[$j]->DeliveryPointName;
                $line['copies'] = $ods[$j]->Copies;
                $details[] = $line;
            }
            $o['details'] = $details;

            $result[$order->TitleId] = $o;
        }

        return $result;
    }


    public function setOrdersDetails($arr)
    {
        if(isset($arr))
        {
            $this->orderDetails = array();
            foreach($arr as $titleId => $details)
            {
                $result = array();

                $result['isSelected'] = $details['isSelected'];
                $result['titleId'] = $titleId;
                $result['orderId'] = $details['orderId'];
                $result['orderStatus'] = $details['status'];
                $result['bundleSize'] = $details['bundleSize'];
                $result['pagination'] = $details['pagination'];
                $result['publicationDate'] = $details['publicationDate'];
                $result['deliveryDate'] = $details['deliveryDate'];
                $result['routeId'] = $details['routeId'];

                $result['orderDetails'] = array();
                foreach ($details as $key => $value)
                {
                    $cfld = "copies";
                    $dpfld = "delpoint";
                    $dpflddesc = "descdelpoint";
                    if($cfld == substr($key, 0, strlen($cfld)))
                    {
                        $idx = substr($key, strlen($cfld));
                        $result['orderDetails'][] = array($dpfld=>$details[$dpfld.$idx], $dpflddesc=>$details[$dpflddesc.$idx], $cfld=>$value); // [delpoint, delpointdesc, copies]
                    }
                }

                $this->orderDetails[$titleId] = $result;
            }
        }
    }

    public function save($task)
    {
        // task: SUBMIT
        foreach ($this->orderDetails as $titleId => $details) {
            if ($details['isSelected'] == "1")
            {
                $oldOrderId = -1;
                $order = ($details['orderId'] == '') ? new Order() : Order::model()->findByPk($details['orderId']);
                if ($order->isNewRecord)
                    $order->DateCreated = new CDbExpression('NOW()');
                else
                {
                    // save current order for history purposes
                    $order->Status = $order->Status.'-'.Order::STATUS_CHANGED;
                    $order->save();
                    $oldOrderId = $order->OrderId;

                    // generate new OrderHistory
                    $orderHistory = new OrderHistory();
                    $orderHistory->OrderId = $oldOrderId;
                    $orderHistory->NewOrderId = $oldOrderId;
                    $orderHistory->save();

                    // generate new Order
                    $order = new Order();
                    $order->DateCreated = new CDbExpression('NOW()');
                }

                $order->DateUpdated = new CDbExpression('NOW()');
                $order->UpdatedBy = Yii::app()->user->name;

                $order->TitleId = $titleId;
                $order->Pagination = $details['pagination'];
                $order->BundleSize = $details['bundleSize'];
                $order->PublicationDate = $details['publicationDate'];
                $order->DeliveryDate = $details['deliveryDate'];
                $order->RouteId = $details['routeId'];

                $order->Status = Order::STATUS_SUBMITTED;

                if ($order->save())
                {
                    OrderDetails::model()->deleteAll('OrderId=:orderId', array(':orderId'=>$order->OrderId));
                    $cnt = count($details['orderDetails']);
                    for ($i = 0; $i < $cnt; $i++) {
                        $od = new OrderDetails();
                        $od->OrderId = $order->OrderId;
                        $od->Sequence = ($i + 1);
                        $od->DeliveryPointId = $details['orderDetails'][$i]['delpoint'];
                        $od->Copies = $details['orderDetails'][$i]['copies'];
                        $od->save();
                    }

                    // if there is a history, update it with new order id
                    if ($oldOrderId != -1)
                        OrderHistory::model()->updateAll(array('NewOrderId' => $order->OrderId), 'NewOrderId=:oldorderid', array(':oldorderid'=>$oldOrderId));

                    //if ($task == 'SAVE-DRAFT')
                    //    $this->message = "Draft saved successfully.";
                    //if ($task == 'SUBMIT-SELECTED')
                    //{
                        // append/update record on ROUTE INSTANCE orders reference
                        //if ($details["isSelected"] == "1")
                            RouteInstanceManager::addOrder($order->OrderId);

                        $this->message = "Order submitted successfully.";
                    //}
                }
                else
                    $this->message = "The action could not be completed at this time.";
            }
        }
    }
}
?>
