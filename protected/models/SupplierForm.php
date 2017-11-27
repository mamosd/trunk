<?php
/**
 * Description of SupplierForm
 *
 * @author Ramon
 */
class SupplierForm extends CFormModel
{
    public $supplierId;
    public $name;
    public $contactPerson;
    public $telephoneNumber;
    public $LandlineNumber;
    public $Email;
    public $isLive;

    public $vehicleDescription;
    public $vehicleCapacity;

    /**
     * Declares the validation rules.
     */
    public function rules()
    {
        return array(
            array('name', 'required'),
            array('Email', 'email','message'=>"The email isn't correct"),
            array('Email', 'customValidateEmail', 'className' => 'supplier', 'attributeName' => 'Email')
        );
    }


    public function customValidateEmail($attribute,$params)
    {
    
        //$id = $_GET['id'];
        //$supplier = Supplier::model()->findByPk($id);
        
        //$data = Supplier::model()->find("Email = '".$this->Email."'");
        //$data = Supplier::model()->find("Email = '".$this->Email."' AND Email <> '".$supplier->Email."'");
        //if (!empty($data))
        //{    
        //    $this->addError($attribute,'The email is already in use.');
        //}
        if ( isset( $_GET['id'] ))
        {
            $id = $_GET['id'];
            $supplier = Supplier::model()->findByPk($id);

            $data = Supplier::model()->find("Email = '".$this->Email."'");
            $data = Supplier::model()->find("Email = '".$this->Email."' AND Email <> '".$supplier->Email."'");
            
        }
        else
        {
            $data = Supplier::model()->find("Email = '".$this->Email."' AND Email<>''");
        }
        if (!empty($data))
        {    
            $this->addError($attribute,'The email is already in use.');
        }
        
        
            
    }
    
    
    /**
     * Declares attribute labels.
     */
    public function attributeLabels()
    {
        return array(
            'vehicleCapacity'=>'Vehicle Capacity (Kgs)',
            'telephoneNumber'=>'Telephone Number (Mobile)',
            'LandlineNumber'=>'Telephone Number (Landline)',
        );
    }

    public function populate($id)
    {
        if(isset($id)){
            $supplier = Supplier::model()->findByPk($id);
            if (isset($supplier)) {
                $this->supplierId = $supplier->SupplierId;
                $this->name = $supplier->Name;
                $this->contactPerson = $supplier->ContactPerson;
                $this->telephoneNumber = $supplier->TelephoneNumber;
                $this->LandlineNumber = $supplier->LandlineNumber;
                $this->Email = $supplier->Email;
                $this->isLive = $supplier->IsLive;

                if(isset($supplier->DefaultVehicleId)) {
                    $vehicle = Vehicle::model()->findByPk($supplier->DefaultVehicleId);
                    $this->vehicleDescription = $vehicle->Description;
                    $this->vehicleCapacity = $vehicle->Capacity;
                }
            }
        }
    }

    /**
     *  Coded in a way that a supplier can have only one vehicle associated to 
     * it at a time.
     * 
     * @return boolean if save succeeds
     */
    public function save()
    {
        $supplier = ($this->supplierId !== '') ? Supplier::model()->findByPk($this->supplierId) : new Supplier();
        if ($supplier->isNewRecord) {
            $supplier->DateCreated = new CDbExpression('NOW()');
        }
        $supplier->Name = $this->name;
        $supplier->ContactPerson = $this->contactPerson;
        $supplier->TelephoneNumber = $this->telephoneNumber;
        $supplier->DateUpdated = new CDbExpression('NOW()');
        $supplier->UpdatedBy = Yii::app()->user->name;
        $supplier->LandlineNumber = $this->LandlineNumber;
        $supplier->Email = $this->Email;
        $supplier->IsLive = $this->isLive;
        if($supplier->save()){
            $vehicle = (isset($supplier->DefaultVehicleId)) ? Vehicle::model()->findByPk($supplier->DefaultVehicleId) : new Vehicle();
            $vehicle->SupplierId = $supplier->SupplierId;
            $vehicle->Description = $this->vehicleDescription;
            $vehicle->Capacity = $this->vehicleCapacity;
            if($vehicle->save()) {
                $supplier->DefaultVehicleId = $vehicle->VehicleId;
                if($supplier->save()){
                    return true;
                }
            }
        }
        
        return false;
    }
}
?>
