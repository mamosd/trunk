<?php
/**
 * Description of AllRouteInstanceDetails
 *
 * @author Ramon
 */
class AllRouteInstanceDetails extends CActiveRecord
{
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return 'allrouteinstancedetails';
    }
}
?>