<?php
/**
 * @property int    SupplierId
 * @property int    PrintcentreId
*/
class PolestarSupplierPrintCentre extends CActiveRecord
{

    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return 'polestar_supplier_printcentre';
    }
}
?>