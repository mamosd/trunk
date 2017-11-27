<?php
/**
 * Description of AllOrders
 *
 * @author Ramon
 */
class AllOrders extends CActiveRecord
{
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return 'allorders';
    }
}
?>
