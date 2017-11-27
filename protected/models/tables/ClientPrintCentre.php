<?php
/**
 * Description of ClientPrintCentre
 *
 * @author ramon
 */
class ClientPrintCentre extends CActiveRecord
{
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return 'client_printcentre';
    }
}
?>