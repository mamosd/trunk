<?php
/**
 * Description of AllTitles
 *
 * @author Ramon
 */
class AllTitles extends CActiveRecord
{
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return 'alltitles';
    }
}
?>