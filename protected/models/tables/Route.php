<?php
/**
 * Description of Route
 *
 * @author Ramon
 */
class Route extends CActiveRecord
{
    public $supplierName;
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return 'route';
    }
}
?>