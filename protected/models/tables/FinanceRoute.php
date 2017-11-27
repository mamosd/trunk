<?php
/**
 * Description of FinanceRoute
 *
 * @author ramon
 */
class FinanceRoute extends CActiveRecord
{
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return 'finance_route';
    }
}

?>