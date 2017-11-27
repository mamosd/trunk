<?php
/**
 * Description of FinanceContractorForm
 *
 * @author ramon
 */
class FinanceContractorForm extends CFormModel
{
    public $listData;
    
    public $contractorId;
    public $code;
    public $firstName;
    public $lastName;
    public $division;
    public $type;
    public $isLive;
    public $accountNumber;
//    public $data;
    public $tax;
    public $vatNo;
    public $email;
    public $telephone;
    public $parentContractor;
    
    public $VIN01;
    public $INS01;
    public $MOT01;
    public $CMM01;
    
    public $VIN02;
    public $INS02;
    public $MOT02;
    public $CMM02;
    
    public $VIN03;
    public $INS03;
    public $MOT03;
    public $CMM03;
    
    public $contractStartDate;
    public $contractFinishDate;
    public $passIssueDate;
    public $passCancelDate;
    public $passCancelBy;
    
    public $immigrationStatus;
    public $idType;
    public $idNumber;
    public $idExpiryDate;
    public $nationalInsuranceNumber;
    public $emergencyContactNumber;
    
    public $bankName;
    public $bankSortCode;
    public $bankAccountNumber;
    
    public $addressLine1;
    public $addressLine2;
    public $addressLine3;
    public $town;
    public $county;
    public $postcode;
    
    public function rules()
    {
        return array(
            array('code, firstName, lastName, type, tax, division', 'required'),
            array('vatNo', 'validateVatNo'),
            array('bankSortCode, bankAccountNumber', 'numerical', 'integerOnly'=>true),
            array('bankSortCode','length', 'encoding' => 'UTF-8', 'min'=>6, 'max'=>6),
            array('bankAccountNumber','length', 'encoding' => 'UTF-8', 'min'=>8, 'max'=>8),
            array('bankSortCode', 'validateBankAcc', 'className' => 'FinanceContractor'),

        );
    }
    
    public function validateBankAcc($attribute,$params)
    {
        if ( !empty( $this->bankSortCode ) && empty( $this->bankAccountNumber ) )
        {
            $this->addError('Bank Sort Code','Bank Account Numer cannot be blank if Bank Sort Code has value.');
        }
        else if ( empty( $this->bankSortCode ) && !empty( $this->bankAccountNumber ) )
        {
            $this->addError('Bank Account Numer (sort code / account number)','Bank Sort Code cannot be blank if Bank Account Numer has value.');
        }
    }

    
    public function attributeLabels()
    {
        return array(
                'email' => 'E-Mail',
            'division' => 'Contract Type',
            'telephone' => 'Telephone Number',
            'VIN01' => 'Vehicle Registration Number',
            'INS01' => 'Insurance Expiry Date',
            'MOT01' => 'MOT Expiry Date',
            'CMM01' => 'Comments',
            'VIN02' => 'Vehicle Registration Number',
            'INS02' => 'Insurance Expiry Date',
            'MOT02' => 'MOT Expiry Date',
            'CMM02' => 'Comments',
            'VIN03' => 'Vehicle Registration Number',
            'INS03' => 'Insurance Expiry Date',
            'MOT03' => 'MOT Expiry Date',
            'CMM03' => 'Comments',
            'bankAccountNumber' => 'Bank Account Numer (sort code / account number)',
            'vatNo' => 'VAT No (if applicable)'
        ); 
    }
    
    public function validateVatNo($attribute,$params)
    {
        if (!empty($this->tax) && (intval($this->tax) > 1)) {
            $vatNo = trim($this->vatNo);
            if (empty($vatNo))
                $this->addError('vatNo', 'VAT No is required for selected tax option.');
        }
    }
    
    public function getContractorList()
    {
        $catsAllowed = array('');
        if (Login::checkPermission(Permission::PERM__FUN__LSC__DTC))
            $catsAllowed[] = 'DTC';
        if (Login::checkPermission(Permission::PERM__FUN__LSC__DTR))
            $catsAllowed[] = 'DTR';
        $inCats = "'".implode("', '", $catsAllowed)."'";
        
        $this->listData = FinanceContractorDetails::model()->findAll(
                array(
                    'condition' => 'Data IN ('.$inCats.')',
                    'order' => 'FirstName ASC'
                ));
    }
    
    public function populate()
    {
        $data = FinanceContractor::model()->findByPk($this->contractorId);
        $this->code = $data->Code;
        $this->firstName = $data->FirstName;
        $this->lastName = $data->LastName;
        $this->type = $data->ContractorTypeId;
        $this->isLive = $data->IsLive;
        $this->accountNumber = $data->AccountNumber;
        $this->tax = $data->ApplicableTaxId;
        $this->vatNo = $data->VATNo;
        $this->email = $data->Email;
        $this->telephone = $data->TelephoneNumber;
        $this->parentContractor = $data->ParentContractorId;
        $this->division = $data->Data;
        
        $this->VIN01 = $data->VehicleRegNo01;
        $this->MOT01 = $data->MOTExpiryDate01;
        $this->INS01 = $data->InsExpiryDate01;
        $this->CMM01 = $data->Comments01;
        
        $this->VIN02 = $data->VehicleRegNo02;
        $this->MOT02 = $data->MOTExpiryDate02;
        $this->INS02 = $data->InsExpiryDate02;
        $this->CMM02 = $data->Comments02;
        
        $this->VIN03 = $data->VehicleRegNo03;
        $this->MOT03 = $data->MOTExpiryDate03;
        $this->INS03 = $data->InsExpiryDate03;
        $this->CMM03 = $data->Comments03;
        
        $this->contractStartDate = $data->ContractStartDate;
        $this->contractFinishDate = $data->ContractFinishDate;
        $this->passIssueDate = $data->PassIssueDate;
        $this->passCancelDate = $data->PassCancelDate;
        $this->passCancelBy = $data->PassCancelBy;
        
        $this->immigrationStatus = $data->ImmigrationStatus;
        $this->idType = $data->IDType;
        $this->idNumber = $data->IDNumber;
        $this->idExpiryDate = $data->IDExpiryDate;
        
        $this->nationalInsuranceNumber = $data->NationalInsuranceNumber;
        $this->emergencyContactNumber = $data->EmergencyContactNumber;
        
        $this->bankName = $data->BankName;
        $this->bankSortCode = $data->BankSortCode;
        $this->bankAccountNumber = $data->BankAccountNumber;
        
        $this->addressLine1 = $data->AddressLine1;
        $this->addressLine2 = $data->AddressLine2;
        $this->addressLine3 = $data->AddressLine3;
        $this->town = $data->Town;
        $this->county = $data->County;
        $this->postcode = $data->Postcode;
        
    }
    
    public function save()
    {
        $c = new FinanceContractor();
        if (!empty($this->contractorId))
        {
            $c = FinanceContractor::model()->findByPk($this->contractorId);
        }
        $c->Code = $this->code;
        $c->FirstName = $this->firstName;
        $c->LastName = $this->lastName;
        $c->ContractorTypeId = $this->type;
        $c->IsLive = $this->isLive;
        $c->AccountNumber = $this->accountNumber;
        $c->ApplicableTaxId = $this->tax;
        $c->VATNo = $this->vatNo;
        $c->Email = $this->email;
        $c->TelephoneNumber = $this->telephone;
        $c->ParentContractorId = $this->parentContractor;
        $c->Data = $this->division;
        
        $c->VehicleRegNo01 = $this->VIN01;
        $c->MOTExpiryDate01 = !empty($this->MOT01) ? $this->MOT01 : NULL;
        $c->InsExpiryDate01 = !empty($this->INS01) ? $this->INS01 : NULL;
        $c->Comments01 = $this->CMM01;
        
        $c->VehicleRegNo02 = $this->VIN02;
        $c->MOTExpiryDate02 = !empty($this->MOT02) ? $this->MOT02 : NULL;
        $c->InsExpiryDate02 = !empty($this->INS02) ? $this->INS02 : NULL;
        $c->Comments02 = $this->CMM02;
        
        $c->VehicleRegNo03 = $this->VIN03;
        $c->MOTExpiryDate03 = !empty($this->MOT03) ? $this->MOT03 : NULL;
        $c->InsExpiryDate03 = !empty($this->INS03) ? $this->INS03 : NULL;
        $c->Comments03 = $this->CMM03;
        
        $c->ContractStartDate = !empty($this->contractStartDate) ? $this->contractStartDate : NULL;
        $c->ContractFinishDate = !empty($this->contractFinishDate) ? $this->contractFinishDate : NULL;
        $c->PassIssueDate = !empty($this->passIssueDate) ? $this->passIssueDate : NULL;
        $c->PassCancelDate = !empty($this->passCancelDate) ? $this->passCancelDate : NULL;
        $c->PassCancelBy = $this->passCancelBy;
        
        $c->ImmigrationStatus = $this->immigrationStatus;
        $c->IDType = $this->idType;
        $c->IDNumber = $this->idNumber;
        $c->IDExpiryDate = !empty($this->idExpiryDate) ? $this->idExpiryDate : NULL;
        
        $c->NationalInsuranceNumber = $this->nationalInsuranceNumber;
        $c->EmergencyContactNumber = $this->emergencyContactNumber;
        
        $c->BankName = $this->bankName;
        $c->BankSortCode = $this->bankSortCode;
        $c->BankAccountNumber = $this->bankAccountNumber;
        
        $c->AddressLine1 = $this->addressLine1;
        $c->AddressLine2 = $this->addressLine2;
        $c->AddressLine3 = $this->addressLine3;
        $c->Town = $this->town;
        $c->County = $this->county;
        $c->Postcode = $this->postcode;
        
        $c->save();
        
        return TRUE;
    }
    
    public function getTypeOptions()
    {
        $all = FinanceContractorType::model()->findAll(array('order'=>'Description ASC'));
        $result = array();
        foreach($all as $i)
            $result[$i->Id] = $i->Description;
        return $result;
    }
    
    public function getTaxOptions()
    {
        $all = FinanceTax::model()->findAll(array('order'=>'Code ASC'));
        $result = array();
        foreach($all as $i)
            $result[$i->Id] = $i->Code.' - '.$i->Description;
        return $result;
    }
    
    public function getParentContractorOptions()
    {
        $all = FinanceContractor::model()->findAll(array(
                'condition' => 'ContractorId != :cid AND ifnull(ParentContractorId,0) = 0',
                'params' => array(':cid' => isset($this->contractorId) ? $this->contractorId : -1),
                'order'=>'Data ASC, FirstName ASC, LastName ASC'
            ));
        $result = array();
        foreach($all as $i)
            $result[$i->ContractorId] = $i->Data.' - '.$i->FirstName.' '.$i->LastName;
        return $result;
    }
    
    public function hasChildren()
    {
        $child = FinanceContractor::model()->find(array(
                'condition' => 'ParentContractorId = :cid',
                'params' => array(':cid' => isset($this->contractorId) ? $this->contractorId : -1)
            ));
        return isset($child);
    }
    
    public function getDivisionOptions()
    {
        $result = array(
            'DTR' => 'DTR',
            'DTC' => 'DTC',
            'FLT' => 'FLT',
            'LSC' => 'LSC',
            'PACKER' => 'PACKER'
            );
        
        if (!Login::checkPermission(Permission::PERM__FUN__LSC__DTC))
            unset($result['DTC']);
        if (!Login::checkPermission(Permission::PERM__FUN__LSC__DTR))
            unset($result['DTR']);
        
        return $result;
    }
    
}

?>
