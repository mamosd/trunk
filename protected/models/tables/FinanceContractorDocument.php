<?php
/**
 * Description of FinanceContractorDocument
 *
 * @author ramon
 */
class FinanceContractorDocument  extends CActiveRecord
{
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }
    
    public function relations(){
        return array(
                'documentType' => array (self::BELONGS_TO, 'FinanceContractorDocumentType', 'TypeId'),
                'uploadedBy' => array (self::BELONGS_TO, 'Login', 'UploadedBy'),
        );
    }

    public function tableName()
    {
        return 'finance_contractor_document';
    }
}
