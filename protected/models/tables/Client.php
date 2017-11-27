<?php
/**
 * Description of Client
 *
 * @author Ramon
 */
class Client extends CActiveRecord
{
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return 'client';
    }
}
?>