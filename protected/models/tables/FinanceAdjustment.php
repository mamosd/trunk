<?php
/**
 * Description of FinanceAdjustment
 *
 * @author ramon
 */
class FinanceAdjustment extends CActiveRecord
{
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return 'finance_adjustment';
    }
}

?>