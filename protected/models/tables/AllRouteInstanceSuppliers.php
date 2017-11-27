<?php
/**
 * Description of AllRouteInstanceSuppliers
 *
 * @author Ramon
 */
class AllRouteInstanceSuppliers extends CActiveRecord
{
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return 'allrouteinstancesuppliers';
    }
}
?>
