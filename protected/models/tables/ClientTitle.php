<?php
/**
 * Description of ClientTitle
 *
 * @author ramon
 */
class ClientTitle extends CActiveRecord
{
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return 'client_title';
    }
}
?>