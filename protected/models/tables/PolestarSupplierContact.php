<?php
/**
 * @property int    Id
 * @property int    SupplierId
 * @property string Department
 * @property string Name
 * @property string Surname
 * @property string Telephone
 * @property string Mobile
 * @property string Email
 * @property int    CreatedBy
 * @property string CreatedDate
 * @property int    ReceiveAdviceEmails
*/
class PolestarSupplierContact extends CActiveRecord
{

    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return 'polestar_supplier_contact';
    }

}
?>