<?php
/**
 * Description of RouteForm
 *
 * @author Ramon
 */
class RouteForm extends CFormModel
{
    public $routeId;
    public $name;
    public $showDetailed;
    public $supplierId;
    public $routeDetails;

    /**
     * Declares the validation rules.
     */
    public function rules()
    {
        return array(
            array('name, showDetailed, supplierId', 'required'),
            array('routeDetails', 'validateDetails'),
        );
    }

    /**
     *  validateDetails used in rules().
     */
    public function validateDetails($attribute,$params)
    {
        if (!isset($this->routeDetails)) {
            $this->addError('routeDetails', 'Route Details need to be entered');
        }
    }

    /**
     * Declares attribute labels.
     */
    public function attributeLabels()
    {
        return array(
            'showDetailed'=>'Delivery Detail',
            'supplierId'=>'Supplier',
        );
    }

    public function getOptionsSupplier()
    {
        $result = array();
        $criteria = new CDbCriteria();
        $criteria->order = "Name ASC";
        $suppliers = Supplier::model()->findAll($criteria);
        foreach ($suppliers as $supplier) {
            $result[$supplier->SupplierId] = $supplier->Name;
        }
        return $result;
    }

    public function getOptionsShowDetailed()
    {
        $result = array();
        $result[0] = 'simple';
        $result[1] = 'full';
        return $result;
    }

    public function getOptionsTitle()
    {
        $result = array();
        $criteria = new CDbCriteria();
//        $criteria->condition = "RouteId is null";
//        if (isset($this->routeId))
//            $criteria->condition .= " or RouteId = ".$this->routeId;
        $criteria->condition = "IsLive = 1";
        $criteria->order = "PrintCentreName ASC, Name ASC";
        $titles = AllTitles::model()->findAll($criteria);
        foreach ($titles as $title) {
            $result[$title->TitleId] = $title->PrintCentreName.' - '.$title->Name;
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

    public function setRouteDetails($arr)
    {
        if(isset($arr))
        {
            $this->routeDetails = array();
            foreach($arr as $key => $value)
            {
                $tfld = "title";
                $dpfld = "delpoint";
                if($tfld == substr($key, 0, strlen($tfld)))
                {
                    $idx = substr($key, strlen($tfld));
                    //$this->routeDetails[] = array($tfld=>$value, $dpfld=>$arr[$dpfld.$idx]); // [title, delpoint]
                    $this->routeDetails[] = array("seq" => $idx, $tfld=>$value, $dpfld=>$arr[$dpfld.$idx]); // [sequence#, title, delpoint]
                }
            }
        }
    }

    public function populate($id = NULL)
    {
        if(isset($id))
        {
            $rt = Route::model()->findByPk($id);
            if (isset($rt)) {
                $this->routeId = $rt->RouteId;
                $this->supplierId = $rt->SupplierId;
                $this->name = $rt->Name;
                $this->showDetailed = $rt->ShowDetailed;

                // retrieve details
                $crit = new CDbCriteria();
                $crit->condition = "RouteId=:routeId";
                $crit->params = array(":routeId"=>$rt->RouteId);
                $crit->order = "Sequence ASC";
                $dt = RouteDetails::model()->findall($crit);
                if(isset($dt)) {
                    $this->routeDetails = array();
                    $cnt = count($dt);
                    for ($i = 0; $i < $cnt; $i++) {
                        $this->routeDetails[] = array('title' => $dt[$i]->TitleId, 'delpoint' => $dt[$i]->DeliveryPointId); // [title, delpoint]
                    }
                }
            }
        }
    }

    public function save()
    {
        $rt = ($this->routeId !== '') ? Route::model()->findByPk($this->routeId) : new Route();
        if ($rt->isNewRecord) {
            $rt->DateCreated = new CDbExpression('NOW()');
        }
        $rt->SupplierId = $this->supplierId;
        $rt->Name = $this->name;
        $rt->ShowDetailed = $this->showDetailed;
        $rt->DateUpdated = new CDbExpression('NOW()');
        $rt->UpdatedBy = Yii::app()->user->name;
        if ($rt->save()) {
            RouteDetails::model()->deleteAll('RouteId=:routeId', array(':routeId'=>$rt->RouteId));
            $cnt = count($this->routeDetails);
            for ($i = 0; $i < $cnt; $i++) {
                $rd = new RouteDetails();
                $rd->RouteId = $rt->RouteId;
                $rd->TitleId = $this->routeDetails[$i]['title'];
                $rd->DeliveryPointId = $this->routeDetails[$i]['delpoint'];
                $rd->Sequence = $this->routeDetails[$i]['seq'];
                $rd->save();
            }
            return true;
        }
        return false;
    }
    
    public static function deleteRoute($id) {
        Route::model()->updateByPk($id, array(
            'IsLive' => 0
        ));
    }
}
?>
