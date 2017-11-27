<?php
/**
 * Description of FinanceRouteForm
 *
 * @author ramon
 */
class FinanceRouteForm extends CFormModel
{
    public $weekStarting;
    public $routeCode;
    public $routeCategory;
    public $contractorId;
    
    public $fees;
    public $baseEdit;
    
    public $editingCategory;
    
    public function rules()
    {
        return array(
            array('routeCode, routeCategory, contractorId', 'required'),
            array('routeCode', 'duplicateValidator')
        );
    }
    
    public function duplicateValidator($att,$val){
        
        $route = FinanceRoute::model()->find(array(
            'condition' => 'Code = :code and RouteCategoryId = :cat',
            'params' => array(':code' => $this->routeCode, ':cat' => $this->routeCategory)
        ));
        if (isset($route))
        {
            $dates = array_keys($this->fees);
            array_pop($dates); // remove the 'all' entry
            
            $crit = new CDbCriteria();
            $crit->addInCondition('Date', $dates);
            $crit->addCondition('ContractorId = '.$this->contractorId);
            $crit->addCondition('RouteId = '.$route->RouteId);
                        
            $dupe = FinanceRouteInstance::model()->find($crit);
            if (isset($dupe))
                $this->addError($att, 'Entry duplicated, please review your input.');            
        }
    }
    
    public function attributeLabels()
    {
        return array(
                'routeCategory'=>'Category',
        );
    }
    
    public function setBaseEdit($base)
    {
        $this->baseEdit = ($base == 1);
    }
    
    public function setFees($fees)
    {
        $this->fees = $fees;
    }
    
    public function getFee($date)
    {
        if (isset($this->fees) && isset($this->fees[$date]))
                return $this->fees[$date];
        return "";
    }
    
    public function getContractorOptions()
    {
        $catsAllowed = array('');
        // 2014.11.18 - assume that permission is granted if reached this point
        //if (Login::checkPermission(Permission::PERM__FUN__LSC__DTC))
        //    $catsAllowed[] = 'DTC';
        //if (Login::checkPermission(Permission::PERM__FUN__LSC__DTR))
        //    $catsAllowed[] = 'DTR';
        $catsAllowed[] = $this->editingCategory;
        $inCats = "'".implode("', '", $catsAllowed)."'";
        
        $data = FinanceContractorDetails::model()->findAll(array(
                'condition' => 'IsLive = 1 AND Data IN ('.$inCats.')',
                'order' => 'Data ASC, FirstName ASC'
            ));
        
        $result = array();
        foreach ($data as $row)
            $result[$row->ContractorId] = sprintf('%s (%s)', trim($row->FirstName.' '.$row->LastName), $row->Code);
            //$result[$row->ContractorId] = sprintf('%s (%s - %s)', trim($row->FirstName.' '.$row->LastName), $row->Data, $row->Code);
            //$result[$row->ContractorId] = trim($row->Data.' - '.$row->Code.' - '.$row->FirstName.' '.$row->LastName);
        
        return $result;
    }
    
    public function getCategoryOptions()
    {
        $catsAllowed = array('');
        // 2014.11.18 - assume that permission is granted if reached this point
        //if (Login::checkPermission(Permission::PERM__FUN__LSC__DTC))
        //    $catsAllowed[] = 'DTC';
        //if (Login::checkPermission(Permission::PERM__FUN__LSC__DTR))
        //    $catsAllowed[] = 'DTR';
        $catsAllowed[] = $this->editingCategory;
        $inCats = "'".implode("', '", $catsAllowed)."'";
        
        $data = FinanceRouteCategory::model()->findAll(array(
                'condition' => 'ContractType IN ('.$inCats.')',
                'order' => 'ContractType ASC, Description ASC'
            ));
        
        $result = array();
        foreach ($data as $row)
            $result[$row->RouteCategoryId] = sprintf('%s', $row->Description);
            //$result[$row->RouteCategoryId] = sprintf('%s (%s)', $row->Description, $row->ContractType);
            //$result[$row->RouteCategoryId] = $row->ContractType.' - '.$row->Description;
        
        return $result;
    }
    
    public function save()
    {
        // create route if required
        $route = FinanceRoute::model()->find(array(
            'condition' => 'Code = :code and RouteCategoryId = :cat',
            'params' => array(':code' => $this->routeCode, ':cat' => $this->routeCategory)
        ));
        if (!isset($route))
        {
            $route = new FinanceRoute();
            $route->Code = $this->routeCode;
            $route->RouteCategoryId = $this->routeCategory;
            $route->save();
        }
        // create an instance per fee entered
        // exclude "all"
        foreach ($this->fees as $date => $fee)
        {
            //if (!empty($fee) && (stristr($date, 'all') === FALSE))
            if (is_numeric($fee) && (stristr($date, 'all') === FALSE))
            {
                $i = new FinanceRouteInstance();
                $i->RouteId = $route->RouteId;
                $i->Date = $date;
                $i->EntryType = 'R';
                $i->ContractorId = $this->contractorId;
                $i->Fee = $fee;
                $i->IsBase = ($this->baseEdit) ? 1 : 0;
                $i->CreatedDate = new CDbExpression('now()');
                $i->CreatedBy = Yii::app()->user->loginId;
                $i->save();
            }
        }
    }
}

?>
