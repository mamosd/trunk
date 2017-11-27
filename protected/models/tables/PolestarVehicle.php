<?php
/**
 * @property int    Id
 * @property string Name
*/
class PolestarVehicle extends CActiveRecord
{


    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return 'polestar_vehicle';
    }

    public function attributeLabels() {
        return array(
        );
    }

    public function rules()
    {
        return array(
        );
    }

    public function relations() {
        return array(
        );
    }

    public static function getAllAsOptions() {
        $result = array();

        $ss = PolestarVehicle::model()->findAll();
        foreach($ss as $s) {
            $result[$s->Id] = $s->Name;
        }

        return $result;
    }

}
?>