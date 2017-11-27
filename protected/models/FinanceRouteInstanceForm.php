<?php
/**
 * Description of FinanceRouteInstanceForm
 *
 * @author ramon
 */
class FinanceRouteInstanceForm extends CFormModel
{
    public $routeId;
    public $routeCode;
    public $routeCategory;
    
    public $date;
    public $entryType;
    
    public $instanceId;
    public $fee;
    public $adjustedFee;
    public $miscFee;
    
    public $contractorId;
    public $adjustedContractorId;
    
    public $acknowledge = 0;
    public $newComment;
    public $commentThread;
    
    public $instanceData;
    public $baseEdit = FALSE;
    
    public $newCommentOnInvoice;
    public $expenseThread = array();
    
    public $editingCategory;
    
    public function rules()
    {
        return array(
            array('contractorId', 'required'),
            array('adjustedFee', 'required', 'on'=>'control'),
            array('adjustedFee', 'numerical', 'on'=>'control'),
            array('fee', 'required', 'on'=>'baserouting'),
            array('fee', 'numerical', 'on'=>'baserouting'),
            array('routeCode', 'duplicateValidator', 'on'=>'baserouting')
        );
    }
    
    public function duplicateValidator($att,$val){
        
        $dupe = FinanceRouteInstance::model()->find(
                "RouteId = :rid AND Date = :dt AND EntryType = :et AND ContractorId = :cid AND RouteInstanceId != :id",
                array(
                    ':rid' => $this->routeId,
                    ':dt' => $this->date,
                    ':et' => $this->entryType,
                    ':cid' => $this->contractorId,
                    ':id' => $this->instanceId
                ));
        if (isset($dupe) && ($dupe->RouteInstanceId != $this->instanceId))
                $this->addError($att, 'Route Name and Contractor combination already exists for this date, please review your input. Note: Exception routes created for the week would cause this issue if editing base routing. Please make sure base routing is edited after initializing the routing for a given week, and if possible, before entering any exception routes.');
    }
    
    public function attributeLabels()
    {
        return array(
            'miscFee'=>'Expenses/Deds',
            'newCommentOnInvoice' => 'Output on invoice'
        );
    }
    
    public function setBaseEdit($base)
    {
        $this->baseEdit = ($base == 1);
    }
    
    public function isDtrAdmin()
    {
        $admins = Yii::app()->params['dtrAdmins'];
        $admins = explode(',', $admins);
        foreach($admins as $admin)
            if (trim($admin) == Yii::app()->user->name)
                return TRUE;
        return FALSE;
    }
    
    public function populate()
    {
        if (isset($this->instanceId))
        {
            $info = FinanceRouteInstanceDetails::model()->find(array(
                'condition' => 'RouteInstanceId = :rid',
                'params' => array(
                    ':rid' => $this->instanceId,
                )
            ));
        }
        else
        {
            $info = FinanceRouteInstanceDetails::model()->find(array(
                'condition' => 'RouteId = :rid AND RouteDate = :dt AND EntryType = :et',
                'params' => array(
                    ':rid' => $this->routeId,
                    ':dt' => $this->date,
                    ':et' => $this->entryType
                )
            ));
        }
        
        if (isset($info))
        {
            $this->instanceId = $info->RouteInstanceId;
            
            $this->routeCode = $info->Route;
            $this->routeCategory = $info->Category;
            $this->contractorId = $info->ContractorId;
            $this->adjustedContractorId = $info->AdjContractorId;
            $this->fee = $info->Fee;
            $this->adjustedFee = (empty($info->AdjFee)) ? $info->Fee : $info->AdjFee;
            $this->miscFee = $info->MiscFee;
            
            $this->instanceData = $info;
            
            if ($info->IsAdjustment == 1) {
                $adjustmentId = $info->AdjustmentId;
                $this->expenseThread = FinanceAdjustmentExpense::model()->findAll(array(
                    'condition' => 'AdjustmentId = :aid AND IsActive = 1',
                    'params' => array(':aid' => $adjustmentId),
                    'order' => 'AdjustmentExpenseId ASC'
                ));
            }
        }
        else // no instance defined for given date
        {
            // grab route info
            /*$data = FinanceRouteDetails::model()->find(array(
                'condition' => 'RouteId = :rid',
                'params' => array(':rid' => $this->routeId),
            ));*/
            $data = $this->getInstanceWithinWeek($this->routeId, $this->date);
            if (isset($data))
            {
                $this->routeCode = $data->Route;
                $this->routeCategory = $data->Category;
                $this->contractorId = $data->ContractorId;
            }
        }
        
        if(!empty($this->instanceId))
        {
            $this->commentThread = FinanceComment::model()->with('login')->findAll(array(
                'condition' => 'RouteInstanceId = :id',
                'params' => array(':id' => $this->instanceId),
                'order' => 'CommentId DESC'
            ));
        }
        else
            $this->commentThread = array();
    }
    
    private function getInstanceWithinWeek($routeId, $date)
    {
        $monday = new DateTime($date);
        $weekday = intval($monday->format('w'));
        $monday = $monday->modify("-".($weekday-1)." day");
        $weekStarting = $monday->format('Y-m-d');

        $crit = new CDbCriteria();
        $crit->addCondition("RouteDate >= :ws");
        $crit->addCondition("RouteDate < date_add(:ws, interval 7 day)");
        $crit->addCondition("RouteId = :rid");
        $crit->params = array(':ws' => $weekStarting, ':rid' => $routeId);

        // find instance within week to grab general details
        return FinanceRouteInstanceDetails::model()->find($crit);
    }
    
    public function save()
    {
        $instance = new FinanceRouteInstance();
        
        if (!empty($this->instanceId))
        {
            $instance = FinanceRouteInstance::model()->findByPk($this->instanceId);
        }
        else  // new instance
        {
            $routeId = $this->routeId;
            $date = $this->date;
            $entryType = $this->entryType;
            
            /*$monday = new DateTime($date);
            $weekday = intval($monday->format('w'));
            $monday = $monday->modify("-".($weekday-1)." day");
            $weekStarting = $monday->format('Y-m-d');
            
            $crit = new CDbCriteria();
            $crit->addCondition("Date >= :ws");
            $crit->addCondition("Date < date_add(:ws, interval 7 day)");
            $crit->addCondition("RouteId = :rid");
            $crit->params = array(':ws' => $weekStarting, ':rid' => $routeId);
            
            // find instance within week to grab general details
            $info = FinanceRouteInstance::model()->find($crit);*/
            
            $info = $this->getInstanceWithinWeek($routeId, $date);
            
            $instance = new FinanceRouteInstance();
            $instance->RouteId = $routeId;
            $instance->Date = $date;
            $instance->EntryType = $entryType;
            $instance->IsBase = $info->IsBase;
            $instance->CreatedDate = new CDbExpression('now()');
            $instance->CreatedBy = Yii::app()->user->loginId;
        }
        
        $instance->Fee = $this->fee;
        $instance->ContractorId = $this->contractorId;

        if (($instance->IsBase != 1) && ($this->acknowledge == 1))
        {
            $instance->AckBy = Yii::app()->user->loginId;
            $instance->AckDate = new CDbExpression('now()');
        }

        $instance->save();
        if (!isset($this->instanceId)) // set instanceId to newly generated instance
                $this->instanceId = $instance->RouteInstanceId;
        
        $expenses = $this->getEnteredExpenses();
                
        $curFee = $this->fee;
        $curAdjContractorId = NULL;
        $adjustment = FinanceAdjustment::model()->find('RouteInstanceId = :rid', array(':rid' => $this->instanceId));
        if (isset($adjustment)) {
            if (!empty($adjustment->Fee))
                $curFee = $adjustment->Fee;
            if (!empty($adjustment->ContractorId))
                $curAdjContractorId = $adjustment->ContractorId;
        }
        //$adjustmentFound = (isset($this->adjustedFee) && ($this->adjustedFee != $this->fee)) 
        $adjustmentFound = (isset($this->adjustedFee) && ($this->adjustedFee != $curFee)) 
                            || ((isset($this->adjustedContractorId)) && ($this->adjustedContractorId != $curAdjContractorId))
                            || (!empty($expenses)); // #59

        if ($adjustmentFound)
        {
            //$adjustment = FinanceAdjustment::model()->find('RouteInstanceId = :rid', array(':rid' => $this->instanceId));
            if (!isset($adjustment))
            {
                $adjustment = new FinanceAdjustment();
                $adjustment->RouteInstanceId = $this->instanceId;
                $adjustment->CreatedDate = new CDbExpression('now()');
                $adjustment->CreatedBy = Yii::app()->user->loginId;
            }

            $adjTweaked = ($adjustment->Fee != $this->adjustedFee) || 
                ($adjustment->ContractorId != $this->adjustedContractorId);
            
            $adjustment->Fee = $this->adjustedFee;
            $adjustment->ContractorId = $this->adjustedContractorId;
            
            $adjustment->save();
            
            // process expenses
            $miscFee = 0;
            $expKeys = array();
            foreach($expenses as $key => $expense) {
                $exp = new FinanceAdjustmentExpense();
                if ($key > 0)
                    $exp = FinanceAdjustmentExpense::model()->findByPk($key);
                if (isset($exp)) {
                    if ($expense['visible'] == '1')
                        $miscFee += floatval ($expense['amount']);
                    
                    $exp->AdjustmentId = $adjustment->AdjustmentId;
                    $exp->Amount = $expense['amount'];
                    if (isset($expense['comment']))
                        $exp->Comment = $expense['comment'];
                    $exp->IsActive = $expense['visible'];
                    $exp->CreatedBy = Yii::app()->user->loginId;
                    $exp->DateCreated = new CDbExpression('now()');
                    $exp->save();
                    $expKeys[] = $exp->AdjustmentExpenseId;
                }
            }
            // remove unnecessary expenses (to avoid dupes)
            $crit = new CDbCriteria();
            $crit->addColumnCondition(array('AdjustmentId' => $adjustment->AdjustmentId));
            $crit->addNotInCondition('AdjustmentExpenseId', $expKeys);
            FinanceAdjustmentExpense::model()->DeleteAll($crit);
            
            // clear ack status if adjustment modified
            if ($adjTweaked ||
                ($adjustment->MiscFee != $miscFee))
            {
                $adjustment->AckBy = NULL;
                $adjustment->AckDate = NULL;
            }
            
            $adjustment->MiscFee = $miscFee;
            $adjustment->save();
        }
        
        // PROCESS ACK
        if ($this->acknowledge == 1)
        {
            $adjustment = FinanceAdjustment::model()->find('RouteInstanceId = :rid', array(':rid' => $this->instanceId));
            if (isset($adjustment)) {
                $adjustment->AckBy = Yii::app()->user->loginId;
                $adjustment->AckDate = new CDbExpression('now()');
                $adjustment->save();
            }
        }
        
        // manage comment
        if (!empty($this->newComment))
        {
            $comment = new FinanceComment();
            $comment->RouteInstanceId = $this->instanceId;
            $comment->Comment = $this->newComment;
            $comment->OutputOnInvoice = $this->newCommentOnInvoice;
            $comment->CreatedDate = new CDbExpression('now()');
            $comment->CreatedBy = Yii::app()->user->loginId;
            $comment->save();
        }
        
        return true;
    }

    public function getContractorOptions($exclude = NULL)
    {
        $catsAllowed = array('');
        // 2014.11.18 - assume that permission is granted if reached this point
        //if (Login::checkPermission(Permission::PERM__FUN__LSC__DTC))
        //    $catsAllowed[] = 'DTC';
        //if (Login::checkPermission(Permission::PERM__FUN__LSC__DTR))
        //    $catsAllowed[] = 'DTR';
        $catsAllowed[] = $this->editingCategory;
        $inCats = "'".implode("', '", $catsAllowed)."'";
        
        if (isset($exclude))
        {
            $data = FinanceContractorDetails::model()->findAll(array(
                'condition' => 'IsLive = 1 AND ContractorId != :cid AND Data IN ('.$inCats.')',
                'params' => array(':cid' => $exclude),
                'order' => 'FirstName ASC'
            ));
        }
        else 
        {
            $data = FinanceContractorDetails::model()->findAll(array(
                'condition' => 'IsLive = 1 AND Data IN ('.$inCats.')',
                'order' => 'FirstName ASC'
            ));
        }
        
        $result = array();
        foreach ($data as $row)
            $result[$row->ContractorId] = sprintf('%s (%s)', trim($row->FirstName.' '.$row->LastName), $row->Code);
            //$result[$row->ContractorId] = sprintf('%s (%s - %s)', trim($row->FirstName.' '.$row->LastName), $row->Data, $row->Code);
            //$result[$row->ContractorId] = $row->Data.' - '.$row->Code.' - '.$row->FirstName.' '.$row->LastName;
        
        return $result;
    }
    
    function getCommentUIAttrs($comment) {
        $baseUrl = Yii::app()->request->baseUrl;
        $toInvoice = ($comment->OutputOnInvoice == 1);
        $cssClass = ($toInvoice) ? 'blue' : '';
        $icon = ($toInvoice) ? 'comment_add' : 'comment_delete';
        $iconPath = "$baseUrl/img/icons/$icon.png";
        $tooltip = ($toInvoice) 
                    ? 'This comment will be output to the invoice (click to toggle)'
                    : 'This comment is private (click to toggle)';
        return array(
            'toInvoice' => $toInvoice,
            'cssClass' => $cssClass,
            'iconPath' => $iconPath,
            'tooltip' => $tooltip
        );
    }
    
    function toggleCommentVisibility($id) {
        $comment = FinanceComment::model()->findByPk($id);
        $comment->OutputOnInvoice = ($comment->OutputOnInvoice == 1) ? 0 : 1;
        $comment->save();
        
        return $this->getCommentUIAttrs($comment);
    }
    
    function getEnteredExpenses(){
        $result = array();
        if (isset($_POST['Expense'])) {
            $posted = $_POST['Expense'];

            foreach($posted as $key => $expense) {
                if (($key < 0) && ($key != -999))
                    if (($expense['visible'] == 1) && is_numeric($expense['amount']) && !empty($expense['amount']))
                        $result[$key] = $expense;
                    
                if ($key > 0)
                    $result[$key] = $expense;
            }
        }
        return $result;
    }
}

?>
