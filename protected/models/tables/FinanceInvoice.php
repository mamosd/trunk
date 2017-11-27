<?php
/**
 * Description of FinanceInvoice
 *
 * @author ramon
 */
class FinanceInvoice  extends CActiveRecord
{
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return 'finance_invoice';
    }
    
    public function relations(){
        return array(
                'contractor' => array (self::BELONGS_TO, 'FinanceContractor', 'ContractorId'),
        );
    }
}

?>