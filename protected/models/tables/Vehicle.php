<?php
/**
 * Description of Vehicle
 *
 * @author Ramon
 */
class Vehicle extends CActiveRecord
{
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return 'vehicle';
    }
}
?>
