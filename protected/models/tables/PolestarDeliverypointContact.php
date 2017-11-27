<?php
/**
 * Description of PolestarDeliverypointContact
 *
 */
class PolestarDeliverypointContact  extends CActiveRecord{
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return 'polestar_deliverypoint_contact';
    }
}
