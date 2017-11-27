<?php
/**
 * @property int    Id
 * @property int    SupplierId
 * @property string FileName
 * @property int    UploadedBy
 * @property string UploadedDate

*/
class PolestarSupplierDocument extends CActiveRecord
{

    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return 'polestar_supplier_document';
    }

    public function relations() {
        return array(
            'UploadedByUser'  => array( self::BELONGS_TO, 'Login', 'UploadedBy' ),
        );
    }

}
?>