<?php
/**
 * Description of FinanceContractor
 *
 * @author ramon
 */
class FinanceContractor extends CActiveRecord
{
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return 'finance_contractor';
    }
    
    public function relations(){
        return array(
            'tax' => array (self::BELONGS_TO, 'FinanceTax', 'ApplicableTaxId'),
            'parent' => array (self::BELONGS_TO, 'FinanceContractor', 'ParentContractorId'),
        );
    }
}

?>