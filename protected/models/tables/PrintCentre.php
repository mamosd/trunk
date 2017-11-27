<?php
/**
 * Description of PrintCentre
 *
 * @author Ramon
 */
class PrintCentre extends CActiveRecord
{
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return 'printcentre';
    }
}
?>