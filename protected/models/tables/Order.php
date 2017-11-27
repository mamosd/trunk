<?php
/**
 * Description of Order
 *
 * @author Ramon
 */
class Order extends CActiveRecord
{
    const STATUS_DRAFT = 'DRAFT';
    const STATUS_SUBMITTED = 'SUBMITTED';
    const STATUS_CHANGED = 'CHANGED';
    const STATUS_PRINTED = 'PRINTED';
    
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return 'order';
    }
}
?>