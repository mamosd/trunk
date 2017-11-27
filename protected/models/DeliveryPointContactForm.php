<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of DeliveryPointContactForm
 *
 * @author sanim
 */
class DeliveryPointContactForm extends CFormModel {
    public $DeliveryPointId;
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
            array('DeliveryPointId, type, department, name, surname', 'required'),
            array('email', 'email','message'=>"The email isn't correct"),
            array('email', 'customValidationRule', 'className' => 'DeliveryPointContact', 'attributeName' => 'Email')
        );
    }
    
    
    public function customValidationRule($attribute,$params)
    {
        if( $this->email !=="")
        {
            $data = DeliverypointContact::model()->find("email = '".$this->email."'");
            if (!empty($data))
            {    
                $this->addError($attribute,'The email is already in use.');
            }         
        }    
    }
    
    
    public function save()
    {
        //$DeliveryPointContact = ($this->DeliveryPointId !== '') ? DeliveryPointContact::model()->findByPk($this->DeliveryPointId) : new DeliveryPointContact();
        $DeliveryPointContact = new DeliverypointContact();
        
        
        if ($DeliveryPointContact->isNewRecord) {
            //$DeliveryPointContact->DateCreated = new CDbExpression('NOW()');
        }
        $DeliveryPointContact->DeliveryPointId = $this->DeliveryPointId;
        $DeliveryPointContact->type = $this->type;
        $DeliveryPointContact->department = $this->department;
        $DeliveryPointContact->name = $this->name;
        $DeliveryPointContact->surname = $this->surname;
        $DeliveryPointContact->telNumber = $this->telNumber;
        $DeliveryPointContact->mobileNumber = $this->mobileNumber;
        $DeliveryPointContact->email = $this->email;
        return $DeliveryPointContact->save();
    }    
    
}
