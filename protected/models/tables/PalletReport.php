<?php
/**
 * Description of PalletReport
 *
 * @author ramon
 */
class PalletReport extends CActiveRecord
{
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return 'palletreport';
    }
}
?>
