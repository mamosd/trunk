<?php
/**
 * Description of OrderForm
 *
 * @author Ramon
 */
class OrderForm  extends CFormModel
{
    public $orderId;
    public $titleId;
    public $titleName;
    public $weightPerPage;
    public $pagination;
    public $bundleSize;
    public $publicationDate;
    public $deliveryDate;

    public $totalCopies;
    public $orderWeight;

    public $orderDetails = array();

    /**
     * Declares the validation rules.
     */
    public function rules()
    {
        return array(
            array('pagination, bundleSize, publicationDate, deliveryDate', 'required'),
            array('pagination, bundleSize', 'numerical'),
            array('orderDetails', 'validateDetails'),
        );
    }

    /**
     *  validateDetails used in rules().
     */
    public function validateDetails($attribute,$params)
    {
        if (count($this->orderDetails) == 0) {
            $this->addError('orderDetails', 'Order Details need to be entered');
        }
    }

    /**
     * Declares attribute labels.
     */
    public function attributeLabels()
    {
        return array(
            'orderWeight'=>'Order Weigth (Kgs)',
        );
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

    public function setOrderDetails($arr)
    {
        if(isset($arr))
        {
            $this->orderDetails = array();
            foreach($arr as $key => $value)
            {
                $cfld = "copies";
                $dpfld = "delpoint";
                $dpflddesc = "descdelpoint";
                if($cfld == substr($key, 0, strlen($cfld)))
                {
                    $idx = substr($key, strlen($cfld));
                    $this->orderDetails[] = array($dpfld=>$arr[$dpfld.$idx], $dpflddesc=>$arr[$dpflddesc.$idx], $cfld=>$value); // [delpoint, delpointdesc, copies]
                }
            }
        }
    }
    
    public function populate($titleId)
    {
        // get title information
        if (isset($titleId)) {
            $criteria = new CDbCriteria();
            $criteria->limit = 1;
            $criteria->condition = 'LoginId=:loginId and TitleId=:titleId';
            $criteria->params = array(':loginId'=>  Yii::app()->user->loginId, ':titleId'=> $titleId);
            $title = AllTitles::model()->find($criteria);
            if (isset($title)) {
                $this->titleId = $titleId;
                $this->titleName = $title->Name;
                $this->weightPerPage = $title->WeightPerPage;
                // get delivery points information from last order posted
                $criteria = new CDbCriteria();
                $criteria->condition = 'TitleId=:titleId';
                $criteria->params = array(':titleId' => $this->titleId);
                $criteria->order = 'DateCreated DESC';
                $criteria->limit = 1;
                $lastOrder = Order::model()->find($criteria);
                if (isset($lastOrder)) {
                    $ods = AllOrderDetails::model()->findAll('OrderId=:orderId', array(':orderId' => $lastOrder->OrderId));
                    $this->orderDetails = array();
                    $cfld = "copies";
                    $dpfld = "delpoint";
                    $dpflddesc = "descdelpoint";
                    foreach ($ods as $od) {
                        $this->orderDetails[] = array($dpfld => $od->DeliveryPointId , $dpflddesc => $od->DeliveryPointName, $cfld => ''); // [delpoint, delpointdesc, copies]
                    }
                }
            }
        }
    }

    public function save()
    {
        $order = new Order();
        $order->DateCreated = new CDbExpression('NOW()');
        $order->CreatedBy = Yii::app()->user->name;
        $order->TitleId = $this->titleId;
        $order->Pagination = $this->pagination;
        $order->BundleSize = $this->bundleSize;
        $order->PublicationDate = $this->publicationDate;
        $order->DeliveryDate = $this->deliveryDate;
        if($order->save()) {
            OrderDetails::model()->deleteAll('OrderId=:orderId', array(':orderId'=>$order->OrderId));
            $cnt = count($this->orderDetails);
            for ($i = 0; $i < $cnt; $i++) {
                $od = new OrderDetails();
                $od->OrderId = $order->OrderId;
                $od->DeliveryPointId = $this->orderDetails[$i]['delpoint'];
                $od->Copies = $this->orderDetails[$i]['copies'];
                $od->save();
            }
            return true;
        }
        return false;
    }
}
?>
