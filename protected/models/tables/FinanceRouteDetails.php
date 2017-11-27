<?php

/**
 * Description of FinanceRouteDetails
 *
 * @author ramon
 */
class FinanceRouteDetails extends CActiveRecord
{
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return 'finance_route_details';
    }
}

?>
