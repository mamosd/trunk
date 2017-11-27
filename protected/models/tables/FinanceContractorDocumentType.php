<?php
/**
 * Description of FinanceContractorDocumentType
 *
 * @author ramon
 */
class FinanceContractorDocumentType  extends CActiveRecord
{
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return 'finance_contractor_document_type';
    }
}
