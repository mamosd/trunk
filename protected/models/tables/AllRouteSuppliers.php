<?php
/**
 * Description of AllRouteSuppliers
 *
 * @author Ramon
 */
class AllRouteSuppliers extends CActiveRecord
{
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return 'allroutesuppliers';
    }
}
?>
