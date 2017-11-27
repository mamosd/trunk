<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of PrintCentreContactForm
 *
 * @author sanim
 */
class PrintCentreContactForm extends CFormModel
{
    public $PrintCentreId;
    public $type;
    public $department;
    public $name;
    public $surname;
    public $telNumber;
    public $mobileNumber;
    public $email;
    
    public function rules()
    {
        return array(
            array('PrintCentreId, type, department, name, surname, email', 'required'),
            array('email', 'email','message'=>"The email isn't correct"),
            array('email', 'customValidationRule', 'className' => 'PrintcentreContact', 'attributeName' => 'Email')
        );
    }
    
    
    public function customValidationRule($attribute,$params)
    {
        $data = PrintcentreContact::model()->find("email = '".$this->email."'");
        if (!empty($data))
        {    
            $this->addError($attribute,'The email is already in use.');
        }
            
    }
    
    
    public function save()
    {
        //$printcentreContact = ($this->PrintCentreId !== '') ? PrintcentreContact::model()->findByPk($this->PrintCentreId) : new PrintcentreContact();
        $printcentreContact = new PrintcentreContact();
        
        
        if ($printcentreContact->isNewRecord) {
            //$printcentreContact->DateCreated = new CDbExpression('NOW()');
        }
        $printcentreContact->PrintCentreId = $this->PrintCentreId;
        $printcentreContact->type = $this->type;
        $printcentreContact->department = $this->department;
        $printcentreContact->name = $this->name;
        $printcentreContact->surname = $this->surname;
        $printcentreContact->telNumber = $this->telNumber;
        $printcentreContact->mobileNumber = $this->mobileNumber;
        $printcentreContact->email = $this->email;
        return $printcentreContact->save();
    }    
    
    
}
