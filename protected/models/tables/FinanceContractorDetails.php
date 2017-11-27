<?php
/**
 * Description of FinanceContractorDetails
 *
 * @author ramon
 */
class FinanceContractorDetails extends CActiveRecord
{
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return 'finance_contractor_details';
    }
}

?>