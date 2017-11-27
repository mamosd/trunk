<?php
/**
 * Description of AllRouteInstanceOrderDetails
 *
 * @author Ramon
 */
class AllRouteInstanceOrderDetails extends CActiveRecord
{
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return 'allrouteinstanceorderdetails';
    }
}
?>