<?php
/**
 * Description of OrderDetails
 *
 * @author Ramon
 */
class OrderDetails extends CActiveRecord
{
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return 'orderdetails';
    }
}
?>