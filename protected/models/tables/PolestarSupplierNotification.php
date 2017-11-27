<?php
/**
 * Description of PolestarSupplierNotification
 *
 * @author ramon
 */
class PolestarSupplierNotification extends CActiveRecord
{

    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return 'polestar_supplier_notification';
    }

    public function relations() {
        return array(
            'SentByLogin'  => array( self::BELONGS_TO, 'Login', 'SentBy' ),
        );
    }
}
