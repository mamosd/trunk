<?php
/**
 * Description of ClientWholesaler
 *
 * @author ramon
 */
class ClientWholesaler extends CActiveRecord
{
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return 'client_wholesaler';
    }
}
?>