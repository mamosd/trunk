<?php
/**
 * Description of ClientTitle
 *
 * @author sanim
 */
class ExpressTitles extends CActiveRecord
{
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return 'express_title';
    }
}
?>