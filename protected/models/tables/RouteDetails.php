<?php
/**
 * Description of RouteDetails
 *
 * @author Ramon
 */
class RouteDetails extends CActiveRecord
{
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return 'routedetails';
    }
}
?>