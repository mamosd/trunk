<?php
/**
 * Description of FinanceControl
 *
 * @author ramon
 */
class FinanceControl extends CFormModel
{
    public $weekStarting;
    public $thisMonday;
    public $baseDataAvailable;
    public $doInitialize;
    public $baseEdit = FALSE;
    
    public $routes;
    
    public $dtrRoutes = FALSE;
    public $dtcRoutes = FALSE;
    public $editingCategoryBase;
    
    public $routeId = NULL; // used for ajax refresh
    public $categoryId = NULL; // used for ajax refresh
    
    public function init()
    {
        $dt = new DateTime();
        if ($dt->format("N") != '1')
            $dt = $dt->modify("last monday");
        $this->weekStarting = $dt->format('d/m/Y');
        $this->thisMonday = $this->weekStarting;
        $this->baseDataAvailable = FALSE;
    }
    
    public function rules()
    {
        return array(
            array('weekStarting', 'required'),
        );
    }
    
    public function setBaseEdit($category)
    {
        $query = "select date_format(max(RouteDate),'%d/%m/%Y') as Date
                from finance_route_instance_details ri
                where dayofweek(RouteDate) = 2
                  and CategoryType = '$category'";
        $row = Yii::app()->db
                    ->createCommand($query)
                    ->queryRow(true);
        
        if (isset($row['Date']))
        {
            $this->weekStarting = $row['Date'];
            $this->baseDataAvailable = TRUE;
        }
        else
            $this->baseDataAvailable = FALSE;
        
        $this->baseEdit = TRUE;
        $this->editingCategoryBase = $category;
    }
    
    private function getRouteInstanceDetails($ws = NULL, $baseOnly = FALSE, $category = NULL, $routeId = NULL, $catId = NULL)
    {
        $weekStart = (isset($ws)) ? $ws : $this->weekStarting;
        $crit = new CDbCriteria();
        $crit->addCondition("RouteDate >= str_to_date(:ws, '%d/%m/%Y')");
        $crit->addCondition("RouteDate < date_add(str_to_date(:ws, '%d/%m/%Y'), interval 7 day)");
        
        $crit->params = array(':ws' => $weekStart);
        
        $catsAllowed = array('');
        if (Login::checkPermission(Permission::PERM__FUN__LSC__DTC))
            $catsAllowed[] = 'DTC';
        if (Login::checkPermission(Permission::PERM__FUN__LSC__DTR))
            $catsAllowed[] = 'DTR';
        
        if ($baseOnly) {
            $crit->addCondition('IsBase = 1');
            $catsAllowed = array($this->editingCategoryBase);
        }

        if ($category !== NULL) // used for DTR/DTC initialization
            $catsAllowed = array($category);
        
        $crit->addInCondition("CategoryType", $catsAllowed);
        
        if ($routeId !== NULL) // used for ajax refresh
            $crit->addColumnCondition(array('RouteId' => $routeId));
        if ($catId !== NULL) // used for ajax refresh
            $crit->addColumnCondition(array('RouteCategoryId' => $catId));
        
        $crit->order = "Category, Route, RouteDate";
        
        $routes = FinanceRouteInstanceDetails::model()->findAll($crit);
        
        foreach($routes as $r) {
            if ($r->CategoryType == 'DTR')
                $this->dtrRoutes = TRUE;
            if ($r->CategoryType == 'DTC')
                $this->dtcRoutes = TRUE;
        }
        
        return $routes;
    }
    
    private function cloneBase($category)
    {
        $weekStart = $this->getDateTimeObj($this->weekStarting);        
        
        // grab last monday available for cloning
        $lastMonday = FinanceRouteInstanceDetails::model()->find(array(
            'condition' => ' WEEKDAY(RouteDate) = 0 AND RouteDate < :dt AND CategoryType = :cat',
            'params' => array(
                            ':dt' => $weekStart->format("Y-m-d"),
                            ':cat' => $category
                ),
            'order' => 'RouteDate DESC' 
        ));
        if (!isset($lastMonday))
        {
            $this->addError('weekStarting', 'There is no available routing info to create base routing plan. Please edit base routing plan instead.');
            return FALSE;
        }
        
        $date = new DateTime($lastMonday->RouteDate);
        $lastMondayForProcessing = $date->format("d/m/Y");
        
        //echo "clone from $lastMondayForProcessing";die;
        
        $weeksDelta = 0;
        while ($date->format("d/m/Y") != $weekStart->format("d/m/Y"))
        {
            $weeksDelta++;
            $date = $date->modify('+7 days');
        }
        
        $data = $this->getRouteInstanceDetails($lastMondayForProcessing, TRUE, $category);
        
        if (count($data) > 0)
        {
            $daysAdd = $weeksDelta * 7;
            $insertData = array();
            foreach($data as $instance)
            {
                /*$new = new FinanceRouteInstance();
                $new->RouteId = $instance->RouteId;
                $newDate = new DateTime($instance->RouteDate);
                $newDate = $newDate->modify("+$daysAdd days");
                $new->Date = $newDate->format('Y-m-d');
                $new->EntryType = $instance->EntryType;
                $new->ContractorId = $instance->ContractorId;
                $new->Fee = $instance->Fee;
                $new->IsBase = $instance->IsBase;
                $new->CreatedDate = new CDbExpression('now()');
                $new->CreatedBy = Yii::app()->user->loginId;
                $new->save();*/
                $newDate = new DateTime($instance->RouteDate);
                $newDate = $newDate->modify("+$daysAdd days");
                $row = array(
                    'RouteId' => $instance->RouteId,
                    'Date' => $newDate->format('Y-m-d'),
                    'EntryType' => $instance->EntryType,
                    'ContractorId' => $instance->ContractorId,
                    'Fee' => $instance->Fee, 
                    'IsBase' => $instance->IsBase,
                    'CreatedDate' => new CDbExpression('now()'),
                    'CreatedBy' => Yii::app()->user->loginId
                );
                $insertData[] = $row;
            }
            $builder = Yii::app()->db->schema->commandBuilder;
            $command = $builder->createMultipleInsertCommand('finance_route_instance', $insertData);
            $command->execute();
        }
        else
        {
            $this->addError('weekStarting', 'No base routing plan to clone for week starting on '.$weekStart->format("d/m/Y"));
            return FALSE;
        }
        return TRUE;
    }
    
    public function populate()
    {
        // grab routes
        
        $info = $this->getRouteInstanceDetails(NULL, $this->baseEdit, NULL, $this->routeId, $this->categoryId);
        
        if (in_array($this->doInitialize, array('DTR', 'DTC'))) // DTR / DTC
        {
            if (($this->doInitialize == 'DTR') && ($this->dtrRoutes)) {
                $this->addError('weekStarting', 'There is DTR information in system for selected week.');
            }
            elseif (($this->doInitialize == 'DTC') && ($this->dtcRoutes)){
                $this->addError('weekStarting', 'There is DTC information in system for selected week.');
            }
            else {
                if ($this->cloneBase($this->doInitialize))
                    $info = $this->getRouteInstanceDetails(NULL, $this->baseEdit, NULL, $this->routeId, $this->categoryId);
            }
        }
        
        // group by type/date under $routes
        $result = array();
        foreach($info as $r)
        {
            if (!isset($result[$r->RouteCategoryId]))
                    $result[$r->RouteCategoryId] = array();
            
            $rtIdx = $r->RouteId.'-'.$r->ContractorId;
            
            if (!isset($result[$r->RouteCategoryId][$rtIdx]))
                    $result[$r->RouteCategoryId][$rtIdx] = array();
            
            $result[$r->RouteCategoryId][$rtIdx][$r->RouteDate.'-'.$r->EntryType] = $r;
        }
        
        $this->routes = $result;
    }
    
    private function getDateTimeObj($formattedDate)
    {
        $wsParts = explode('/', $formattedDate);
        $wsYmd = array($wsParts[2], $wsParts[1], $wsParts[0]);
        $wsYmd = implode('-',$wsYmd);
        return new DateTime($wsYmd);
    }
    
    public static function dropRouteByInstanceId($id)
    {
        $errors = array();
        
        $instance = FinanceRouteInstanceDetails::model()->find('RouteInstanceId = :rid', array(':rid' => $id));
        if (!isset($instance))
        {
            $errors[] = 'Entry not found';
        }
        else 
        {
            // verify none of the instances of this route contain adjustments
            $crit = new CDbCriteria();
            $crit->addCondition("RouteId = :rt");
            $crit->addCondition("ContractorId = :cid");
            $crit->addCondition("RouteDate >= :dt");
            $crit->addCondition("RouteDate < date_add(:dt, interval 7 day)");
            $crit->addCondition("IsBase = 1"); // only apply validation to base routes (exception routes can be deleted)
            $crit->addCondition("IsAdjustment = 1");
            $crit->params = array(':rt' => $instance->RouteId, ':dt' => $instance->RouteDate, ':cid' => $instance->ContractorId);
            $adj = FinanceRouteInstanceDetails::model()->find($crit);
            if (isset($adj))
                $errors[] = 'Adjustments found for selected route.';
            else
            {
                $crit = new CDbCriteria();
                $crit->addCondition("RouteId = :rt");
                $crit->addCondition("ContractorId = :cid");
                $crit->addCondition("Date >= :dt");
                $crit->addCondition("Date < date_add(:dt, interval 7 day)");
                $crit->params = array(':rt' => $instance->RouteId, ':dt' => $instance->RouteDate, ':cid' => $instance->ContractorId);
                FinanceRouteInstance::model()->deleteAll($crit);
            }
        }
        return $errors;
    }
    
    public static function disableContractorDocument($id)
    {
        $errors = array();
        
        $instance = FinanceContractorDocument::model()->find('Id = :rid', array(':rid' => $id));
        if (!isset($instance))
        {
            $errors[] = 'Entry not found';
        }
        else 
        {
            FinanceContractorDocument::model()->updateByPk($id, array('deleteStatus'=>1));
        }
        
        return $errors;
    }
    
    
    function getInstanceUIDetails($instance, $baseEdit) {
        return FinanceControl::doGetInstanceUIDetails($instance, $baseEdit);
    }
    
    static function getInstanceUIDetailsByAttrs($instanceId, $baseEdit, $routeId, $contractorId, $dateIdx) {
        $baseEdit = $baseEdit === 'true'? true: false;
        $criteria = array('RouteInstanceId' => $instanceId);
        if ($instanceId == -1) {
            $dateParts = explode('-', $dateIdx);
            $entryType = array_pop($dateParts);
            $date = implode('-', $dateParts);
            
            $criteria = array(
                'RouteId' => $routeId,
                'RouteDate' => $date,
                'EntryType' => $entryType,
                'ContractorId' => $contractorId
            );
        }
        $result = array();
        $instance = FinanceRouteInstanceDetails::model()->findByAttributes($criteria);
        if (isset($instance))
            $result = FinanceControl::doGetInstanceUIDetails ($instance, $baseEdit, TRUE); // pull overall NACK status
        
        return $result;
    }
    
    static function doGetInstanceUIDetails($instance, $baseEdit, $pullOverallData = FALSE) {
        
        $verifyAdjustments = TRUE;
        $anythingNotAck = array();
        $excValue = 0;
        $cssClass = '';
                    
        if (!$baseEdit)
        {
            if (!empty($instance->AdjFee))
                $fee = $instance->AdjFee;
            else
                $fee = $instance->Fee;

            if (!empty($instance->MiscFee))
                    $fee += $instance->MiscFee;

            if ($instance->IsBase != 1)
                $excValue += $fee;
            else
                $excValue += $fee - $instance->Fee;

            if ($instance->IsBase != 1) // exception route - verify if acknowledged
            {
                //$fee = $instance->Fee;

                if (empty($instance->AckBy))
                {
                    $verifyAdjustments = FALSE;
                    $cssClass = 'red';
                    $anythingNotAck[$instance->CategoryType] = TRUE;
                }
                else
                {
                    $verifyAdjustments = TRUE;
                    $cssClass = 'blue';
                }
            }

            if ($verifyAdjustments) // base route (or acknowledged exception) -- verify adjustments
            {
                // verify if adjustment acknowledged (if any)
                if (!empty($instance->AdjFee) || !empty($instance->AdjContractorId))
                {
                    if (empty($instance->AdjAckBy))
                    {
                        $cssClass = 'red';
                        $anythingNotAck[$instance->CategoryType] = TRUE;
                    }
                    else
                        $cssClass = 'blue';
                }
            }
        }
        else
        {
            $fee = $instance->Fee;
        }

        $dtFormat = $baseEdit ? 'l' : 'd/m';
        $tooltip = date($dtFormat, CDateTimeParser::parse($instance->RouteDate, "yyyy-MM-dd"))." -- ".$instance->Route.(($instance->IsBase != 1) ? " (exception)" : "");
        $tooltip .= "<br>";
        if (!$baseEdit && !empty($instance->AdjContractorId))
            $tooltip .= "\n".trim($instance->AdjContractorFirstName." ".$instance->AdjContractorLastName)." (replacing ".trim($instance->ContractorFirstName." ".$instance->ContractorLastName).")";
        else
            $tooltip .= "\n".trim($instance->ContractorFirstName." ".$instance->ContractorLastName);

        if (!$baseEdit && (!empty($instance->AdjFee) && (floatval($instance->AdjFee) > 0)))
            $tooltip .= "<br>\nFee: ".sprintf("%01.2f", $instance->AdjFee)." (originally ".sprintf("%01.2f", $instance->Fee).")";
        else
            $tooltip .= "<br>\nFee: ".sprintf("%01.2f", $instance->Fee);

        if (!$baseEdit && (!empty($instance->MiscFee) && (floatval($instance->MiscFee) != 0)))
            $tooltip .= "<br>\nTotal expenses/deds: ".sprintf("%01.2f", $instance->MiscFee);
        
        $result = array(
            'id' => $instance->RouteInstanceId,
            'fee' => $fee,
            'formattedFee' => sprintf("%01.2f", $fee),
            'exception' => $excValue,
            'cssClass' => $cssClass,
            'nack' => $anythingNotAck,
            'tooltip' => $tooltip
        );
        
        if ($pullOverallData == TRUE) { // to output overall NACK status
            $weekStart = new DateTime($instance->RouteDate);
            if ($weekStart->format("N") != '1')
                $weekStart = $weekStart->modify("last monday");
            
            $query = "select 
                CategoryType, count(1) as NoNacks
                from finance_route_instance_details
                where RouteDate >= :dt
                  and RouteDate < date_add(:dt, interval 7 day)
                  and ((IsBase != 1 AND ifnull(AckBy, '') = '') OR 
                                (IsAdjustment = 1 AND ifnull(AdjAckBy, '') = ''))
                group by CategoryType";
            
            $overall = array(
                'dtr' => false,
                'dtc' => false
            );
            
            $rows = Yii::app()->db
                    ->createCommand($query)
                    ->queryAll(true, array(':dt' => $weekStart->format('Y-m-d')));
            
            foreach ($rows as $row)
                $overall[strtolower ($row['CategoryType']) ] = ($row['NoNacks'] > 0);
            
            $result['overallNack'] = $overall;
        }
        
        return $result;
    }
}

?>
