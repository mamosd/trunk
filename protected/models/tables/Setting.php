<?php
/**
 * Description of Setting
 *
 * @author ramon
 */
class Setting extends CActiveRecord
{
	public static function model($className=__CLASS__) {
		return parent::model($className);
	}

        public function tableName()
        {
            return 'setting';
        }

        public static function get($subsystem, $name)
        {
            $setting = Setting::model()->find(array(
                'condition' => '`Subsystem` = :ss AND `Name` = :nm',
                'params' => array(
                    ':ss' => $subsystem,
                    ':nm' => $name
                )
            ));

            $result = "";
            if(isset($setting))
                $result = $setting->Value;

            return $result;
        }

        public static function set($subsystem, $name, $value) {
            $setting = Setting::model()->find(array(
                    'condition' => '`Subsystem` = :ss AND `Name` = :nm',
                    'params' => array(
                            ':ss' => $subsystem,
                            ':nm' => $name
                    )
            ));
            if (!isset($setting)) {
                $setting = new Setting();
                $setting->Subsystem = $subsystem;
                $setting->Name = $name;
            }
            $setting->Value = $value;
            return $setting->save();
        }
}
