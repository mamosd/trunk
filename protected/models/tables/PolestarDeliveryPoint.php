<?php
/**
 * Description of PolestarDeliveryPoint
 *
 */
class PolestarDeliveryPoint extends CActiveRecord
{
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return 'polestar_deliverypoint';
    }
    
    public function beforeSave(){
        // store sanitized postcode
        $postcode = $this->PostalCode;
        $postcode = strtolower($postcode);
        $postcode = preg_replace("/[^a-z0-9]/", '', $postcode);
        $this->SanitizedPostcode = $postcode;
        
        return parent::beforeSave();
    }
}
?>