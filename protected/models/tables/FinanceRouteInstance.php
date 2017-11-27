<?php
/**
 * Description of FinanceRouteInstance
 *
 * @author ramon
 */
class FinanceRouteInstance extends CActiveRecord
{
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return 'finance_route_instance';
    }
}
?>