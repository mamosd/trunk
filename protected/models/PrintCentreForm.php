<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of PrintCentreForm
 *
 * @author Ramon
 */
class PrintCentreForm extends CFormModel
{
    public $printCentreId;
    public $name;
    public $address;
    public $postalcode;
    public $county;
    public $enabled;

    /**
     * Declares the validation rules.
     */
    public function rules()
    {
        return array(
            array('name, address, , postalcode, county, enabled', 'required'),
        );
    }

    public function attributeLabels()
    {
            return array(
                    'enabled' => 'Is Live',
            );
    }    
    
    public function populate($id)
    {
        if (isset($id)) {
            $pc = PrintCentre::model()->findByPk($id);
            if (isset($pc)) {
                $this->printCentreId = $pc->PrintCentreId;
                $this->name = $pc->Name;
                $this->address = $pc->Address;
                $this->postalcode = $pc->postalCode;
                $this->county = $pc->county;
                $this->enabled = $pc->Enabled;
            }
        }
    }

    public function save(){
        $pc = ($this->printCentreId !== '') ? PrintCentre::model()->findByPk($this->printCentreId) : new PrintCentre();
        if ($pc->isNewRecord) {
            $pc->DateCreated = new CDbExpression('NOW()');
        }
        $pc->Name = $this->name;
        $pc->Address = $this->address;
        $pc->postalCode = $this->postalcode;
        $pc->county = $this->county;
        $pc->Enabled = $this->enabled;
        $pc->DateUpdated = new CDbExpression('NOW()');
        $pc->UpdatedBy = Yii::app()->user->name;
        return $pc->save();
    }
}
?>
