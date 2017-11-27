<?php
/**
 * Description of SupplierLogin
 *
 * @author Ramon
 */
class SupplierLogin extends CActiveRecord
{
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return 'supplierlogin';
    }
}
?>
