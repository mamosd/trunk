<?php
/**
 * Description of Area
 *
 * @author ramon
 */
class Area extends CActiveRecord
{
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return 'area';
    }
}
?>