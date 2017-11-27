<?php
/**
 * Description of AllRouteOrderDetails
 *
 * @author Ramon
 */
class AllRouteOrderDetails extends CActiveRecord
{
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return 'allrouteorderdetails';
    }
}
?>