<?php
/**
 * Description of FinanceContractorType
 *
 * @author ramon
 */
class FinanceContractorType extends CActiveRecord
{
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return 'finance_contractor_type';
    }
}

?>