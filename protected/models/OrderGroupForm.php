<?php
/**
 * Description of OrderGroupForm
 *
 * @author Ramon
 */
class OrderGroupForm  extends CFormModel
{
    public $orderDetails;

    public function getTitles($loginId)
    {
        $criteria = new CDbCriteria();
        $criteria->condition = 'LoginId=:loginId';
        $criteria->params = array(':loginId' => $loginId);
        $criteria->order = "Name ASC";
        $titles = AllTitles::model()->findAll($criteria);
        return $titles;
    }

    public function getLastOrder($loginId, $titleId)
    {
        if (isset($this->orderDetails[$titleId]))
        {
            return $this->orderDetails[$titleId];
        }
        $result = array();
        // get title information
        if (isset($titleId)) {
            $criteria = new CDbCriteria();
            $criteria->limit = 1;
            $criteria->condition = 'LoginId=:loginId and TitleId=:titleId';
            $criteria->params = array(':loginId'=>  $loginId, ':titleId'=> $titleId);
            $title = AllTitles::model()->find($criteria);
            if (isset($title)) {
                
                $result['titleId'] = $titleId;
//                $result['titleName'] = $title->Name;
//                $result['weightPerPage'] = $title->WeightPerPage;

                // get delivery points information from last order posted
                $criteria = new CDbCriteria();
                $criteria->condition = 'TitleId=:titleId';
                $criteria->params = array(':titleId' => $result['titleId']);
                $criteria->order = 'DateCreated DESC';
                $criteria->limit = 1;
                $lastOrder = Order::model()->find($criteria);

                $result['orderId'] = $lastOrder->OrderId;
                $result['orderStatus'] = $lastOrder->Status;
                $result['dateCreated'] = $lastOrder->DateUpdated;
                $result['bundleSize'] = $lastOrder->BundleSize;
                $result['pagination'] = $lastOrder->Pagination;
                $result['publicationDate'] = $lastOrder->PublicationDate;
                $result['deliveryDate'] = $lastOrder->DeliveryDate;

                $result['orderDetails'] = array();

                if (isset($lastOrder)) {
                    $criteria = new CDbCriteria();
                    $criteria->condition = 'OrderId=:orderId';
                    $criteria->params = array(':orderId' => $lastOrder->OrderId);
                    $criteria->order = 'Sequence ASC';
                    
                    $ods = AllOrderDetails::model()->findAll($criteria);
                    $cfld = "copies";
                    $dpfld = "delpoint";
                    $dpflddesc = "descdelpoint";
                    foreach ($ods as $od) {
                        $result['orderDetails'][] = array($dpfld => $od->DeliveryPointId , $dpflddesc => $od->DeliveryPointName, $cfld => $od->Copies); // [delpoint, delpointdesc, copies]
                    }
                }
            }
        }
        return $result;
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

    public function setOrdersDetails($arr)
    {
        if(isset($arr))
        {
            $this->orderDetails = array();
            foreach($arr as $titleId => $details)
            {
                $result = array();

                $result['save'] = $details['save'];
                $result['titleId'] = $titleId;
                $result['orderId'] = $details['orderId'];
                $result['orderStatus'] = $details['orderStatus'];
                $result['dateCreated'] = $details['dateCreated'];
                $result['bundleSize'] = $details['bundleSize'];
                $result['pagination'] = $details['pagination'];
                $result['publicationDate'] = $details['publicationDate'];
                $result['deliveryDate'] = $details['deliveryDate'];

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

                if($details['save'] == '1')
                {
                    if($details['orderId'] == '')
                    {
                        $result['orderStatus'] = self::STATUS_DRAFT;
                    }
                }

                $this->orderDetails[$titleId] = $result;
            }
        }
    }

    public function save($status)
    {
        foreach ($this->orderDetails as $titleId => $details) {
            if($details['save'] == '1')
            {
                $order = ($details['orderId'] == '') ? new Order() : Order::model()->findByPk($details['orderId']);
                if ($order->isNewRecord)
                    $order->DateCreated = new CDbExpression('NOW()');
                $order->DateUpdated = new CDbExpression('NOW()');
                $order->UpdatedBy = Yii::app()->user->name;

                $order->TitleId = $details['titleId'];
                $order->Pagination = $details['pagination'];
                $order->BundleSize = $details['bundleSize'];
                $order->PublicationDate = $details['publicationDate'];
                $order->DeliveryDate = $details['deliveryDate'];
                $order->Status = $status;

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
                }

                $this->orderDetails[$titleId]['orderStatus'] = $status;
                $this->orderDetails[$titleId]['dateCreated'] = date(DATE_ATOM);
            }
            else
            {
                if($details['orderId'] != '')
                {
                    // delete order
                    OrderDetails::model()->deleteAll('OrderId=:orderId', array(':orderId'=>$details['orderId']));
                    Order::model()->deleteByPk($details['orderId']);
                }
                $this->orderDetails[$titleId]['orderStatus'] = "";
            }
        }

        $this->orderDetails = array(); // force fetch from db
    }
}
?>
