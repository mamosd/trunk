<?php
/**
 * Description of RouteInstanceDetails
 *
 * @author Ramon
 */
class RouteInstanceDetails extends CActiveRecord
{
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return 'routeinstancedetails';
    }
}
?>