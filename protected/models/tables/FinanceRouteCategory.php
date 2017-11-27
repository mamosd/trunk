<?php
/**
 * Description of FinanceRouteCategory
 *
 * @author ramon
 */
class FinanceRouteCategory extends CActiveRecord
{
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return 'finance_route_category';
    }
}

?>