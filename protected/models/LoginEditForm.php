<?php
/**
 * Description of LoginEditForm
 *
 * @author Ramon
 */
class LoginEditForm extends CFormModel
{
    public $loginId;
    public $username;
    public $friendlyName;
    
    public $email;
    
    public $role;
    public $clientId;
    public $supplierId;

    public $isActive = true;
    public $newPassword;
    public $newPassword2;
    
    public $roles;
    public $permissions = array();
    
    public $polestarPrintCentres = array();
    
    /**
     * Declares the validation rules.
     */
    public function rules()
    {
        return array(
            array('username, friendlyName, role, email', 'required'),
            array('isActive', 'boolean'),
            array('newPassword', 'compare', 'compareAttribute'=>'newPassword2'),
            array('email', 'email','message'=>"The email isn't correct"),
            array('email', 'customValidationRule', 'className' => 'login', 'attributeName' => 'Email')
        );
    }

    public function customValidationRule($attribute,$params)
    {
        $login = ($this->loginId !== '') ? Login::model()->findByPk($this->loginId) : new Login();
        //echo $this->email."         ".$login->Email;
        
        $data = Login::model()->find("email = '".$this->email."' AND email <> '".$login->Email."'");
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
            'newPassword2'=>'Confirm Password',
            'clientId'=>'Associated Client',
            'supplierId'=>'Associated Supplier',
        );
    }

    public function getRoleOptions()
    {
        $result = array();
        if (Yii::app()->user->role->LoginRoleId == LoginRole::SUPER_ADMIN)
            $roles = LoginRole::model()->findAll();
        else
        {
            $roles = LoginRole::model()->findAll("LoginRoleId !=:sadmin", array(":sadmin" => LoginRole::SUPER_ADMIN));
        }
        foreach ($roles as $role) {
            $result[$role->LoginRoleId] = $role->Description;
        }
        return $result;
    }

    public function getSupplierOptions()
    {
        $result = array();
        $criteria = new CDbCriteria();
        $criteria->order = 'Name ASC';
        $suppliers = Supplier::model()->findAll($criteria);
        foreach ($suppliers as $supplier) {
            $result[$supplier->SupplierId] = $supplier->Name;
        }
        return $result;
    }

    public function getClientOptions()
    {
        $result = array();
        $criteria = new CDbCriteria();
        $criteria->order = 'Name ASC';
        $clients = Client::model()->findAll($criteria);
        foreach ($clients as $client) {
            $result[$client->ClientId] = $client->Name;
        }
        return $result;
    }


    public function populate($id)
    {
        if(isset($id)){
            $login = Login::model()->findByPk($id);
            if (isset($login)){
                $this->loginId = $login->LoginId;
                $this->username = $login->UserName;
                $this->role = $login->LoginRoleId;
                $this->isActive = ('1' === $login->IsActive);
                $this->friendlyName = $login->FriendlyName;
                $this->email = $login->Email;
                
                $this->permissions = array();
                foreach ($login->permissions as $s){
                        $this->permissions[] = $s->PermissionId;
                }

                $this->roles = array();
                foreach ($login->roles as $p){
                    $this->roles[] = $p->Id;
                }
                
                $pcs = PolestarPrintCentreLogin::model()->findAll(array(
                    'condition' => 'LoginId = :lid',
                    'params' => array(':lid' => $login->LoginId)
                ));
                $this->polestarPrintCentres = array();
                foreach ($pcs as $pc) 
                    $this->polestarPrintCentres[] = $pc->PrintCentreId;

                switch ($this->role) {
                    case LoginRole::CLIENT:
                        $rel = ClientLogin::model()->find('LoginId=:loginid', array(':loginid'=>$login->LoginId));
                        if(isset($rel)){
                            $this->clientId = $rel->ClientId;
                        }
                        break;
                    case LoginRole::SUPPLIER:
                        $rel = SupplierLogin::model()->find('LoginId=:loginid', array(':loginid'=>$login->LoginId));
                        if(isset($rel)){
                            $this->supplierId = $rel->SupplierId;
                        }
                        break;
                }
            }
        }
    }

    public function save()
    {   
        $login = ($this->loginId !== '') ? Login::model()->findByPk($this->loginId) : new Login();
        $login->UserName = $this->username;
        $login->FriendlyName = $this->friendlyName;
        $login->DateUpdated = new CDbExpression('NOW()');
        $login->UpdatedBy = Yii::app()->user->name;
        $login->IsActive = ($this->isActive) ? 1 : 0;
        $login->Email = $this->email;

        
        // password change
        if ('' !== $this->newPassword) {
            $login->Password = md5($this->newPassword);
        }
        
        $saved = FALSE;

        if (!$login->isNewRecord) {
            if (strcmp($login->LoginRoleId, $this->role) !== 0) { // role change
                // delete link record if any
                if (0 === strcmp($login->LoginRoleId, LoginRole::CLIENT))
                    ClientLogin::model()->deleteAll('LoginId=:loginId', array(':loginId'=>$this->loginId));

                if (0 === strcmp($login->LoginRoleId, LoginRole::SUPPLIER))
                    SupplierLogin::model()->deleteAll('LoginId=:loginId', array(':loginId'=>$this->loginId));

                // create new link record
                if (0 === strcmp($this->role, LoginRole::CLIENT)){
                    $rel = new ClientLogin();
                    $rel->LoginId = $login->LoginId;
                    $rel->ClientId = $this->clientId;
                    $rel->DateUpdated = new CDbExpression('NOW()');
                    $rel->save();
                }

                if (0 === strcmp($this->role, LoginRole::SUPPLIER)){
                    $rel = new SupplierLogin();
                    $rel->LoginId = $login->LoginId;
                    $rel->SupplierId = $this->supplierId;
                    $rel->DateUpdated = new CDbExpression('NOW()');
                    $rel->save();
                }
            }
            else { // same role - update link record
                if (0 === strcmp($login->LoginRoleId, LoginRole::CLIENT)){
                    $rel = ClientLogin::model()->find('LoginId=:loginId', array(':loginId'=>$this->loginId));
                    $rel->ClientId = $this->clientId;
                    $rel->DateUpdated = new CDbExpression('NOW()');
                    $rel->save();
                }

                if (0 === strcmp($login->LoginRoleId, LoginRole::SUPPLIER)){
                    $rel = SupplierLogin::model()->find('LoginId=:loginId', array(':loginId'=>$this->loginId));
                    $rel->SupplierId = $this->supplierId;
                    $rel->DateUpdated = new CDbExpression('NOW()');
                    $rel->save();
                }
            }

            $login->LoginRoleId = $this->role;
            $saved = $login->save();
        }
        else { // new record, create link record

            $login->LoginRoleId = $this->role;
            $login->DateCreated = new CDbExpression('NOW()');
            if($login->save()) {
                if (0 === strcmp($login->LoginRoleId, LoginRole::CLIENT)){
                    $rel = new ClientLogin();
                    $rel->LoginId = $login->LoginId;
                    $rel->ClientId = $this->clientId;
                    $rel->DateUpdated = new CDbExpression('NOW()');
                    return $rel->save();
                }

                if (0 === strcmp($login->LoginRoleId, LoginRole::SUPPLIER)){
                    $rel = new SupplierLogin();
                    $rel->LoginId = $login->LoginId;
                    $rel->SupplierId = $this->supplierId;
                    $rel->DateUpdated = new CDbExpression('NOW()');
                    return $rel->save();
                }

                $saved = TRUE; // administrator
            }
        }
        
        if ($saved) {
            $this->loginId = $login->LoginId;
            
            // ROLES
            $crit = new CDbCriteria();
            $crit->addColumnCondition(array("LoginId" => $this->loginId));
            $crit->addNotInCondition("RoleId",$this->roles);
            RoleUser::model()->deleteAll($crit);

            // SAVE POLESTAR PRINT CENTRES
            PolestarPrintCentreLogin::model()->deleteAll(array(
                'condition' => 'LoginId = :lid',
                'params' => array(':lid' => $this->loginId)
            ));
            foreach ($this->polestarPrintCentres as $pcId) {
                $pcl = new PolestarPrintCentreLogin();
                $pcl->PrintCentreId = intval($pcId);
                $pcl->LoginId = $this->loginId;
                $pcl->DateCreated = new CDbExpression('now()');
                $pcl->CreatedBy = Yii::app()->user->loginId;
                $pcl->save();
            }

            // insert the rest
            if (!empty($this->roles)) {
                foreach ($this->roles as $p){
                    $tcs = RoleUser::model()->findByAttributes(array(
                            "LoginId" => $this->loginId,
                            "RoleId" => $p,
                    ));
                    if (isset($tcs)) continue;

                    $tcs = new RoleUser();
                    $tcs->LoginId = $this->loginId;
                    $tcs->RoleId = $p;
                    $tcs->insert();
                }
            }
            
        }

        return $saved;
    }

}
?>
