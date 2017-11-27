<?php
/**
 * Description of AllClientLogins
 *
 * @author Ramon
 */
class AllClientLogins extends CActiveRecord
{
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return 'allclientlogins';
    }
}
?>