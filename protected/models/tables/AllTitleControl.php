<?php
/**
 * Description of AllTitleControl
 *
 * @author Ramon
 */
class AllTitleControl extends CActiveRecord
{
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return 'alltitlecontrol';
    }
}
?>