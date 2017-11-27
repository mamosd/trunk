<?php
/**
 * Description of FinanceAdjustmentExpense
 *
 * @author ramon
 */
class FinanceAdjustmentExpense extends CActiveRecord
{
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return 'finance_adjustment_expense';
    }
}

?>