<?php
/**
 * Description of ClientVehicle
 *
 * @author ramon
 */
class ClientVehicle extends CActiveRecord
{
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return 'client_vehicle';
    }
}
?>
