<?php
/**
 * Description of AllOrderDetails
 *
 * @author Ramon
 */
class AllOrderDetails extends CActiveRecord
{
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return 'allorderdetails';
    }
}
?>