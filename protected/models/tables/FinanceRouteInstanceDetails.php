<?php
/**
 * Description of FinanceRouteInstanceDetails
 *
 * @author ramon
 */
class FinanceRouteInstanceDetails extends CActiveRecord
{
    public static $REGULAR_ENTRY_TYPE = 'R';
    //public static $SPECIAL_ENTRY_TYPES = array('B', 'P'); // Entry Type R is regular route
    public static $SPECIAL_ENTRY_TYPES = array(); // not required in phase 1 (empty array)
    
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return 'finance_route_instance_details';
    }
}
?>