<?php
/**
 * Description of Supplier
 *
 * @author Ramon
 */

class Supplier extends CActiveRecord
{
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return 'supplier';
    }
}
?>