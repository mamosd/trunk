<?php
/**
 * Description of DeliveryPoint
 *
 * @author Ramon
 */
class DeliveryPoint extends CActiveRecord
{
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return 'deliverypoint';
    }
}
?>