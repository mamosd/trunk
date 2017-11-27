<?php
/**
 * Description of PolestarDeliveryPointTime
 *
 * @author aldroid
 */
class PolestarDeliveryPointTime  extends CActiveRecord
{
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return 'polestar_deliverypoint_time';
    }
}
