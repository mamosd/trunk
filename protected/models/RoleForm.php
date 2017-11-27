<?php
class RoleForm extends CFormModel
{
    public $roleId;
    public $name;
    public $description;
    public $parentRoles;
    public $perms;
    public $defaultForLoginRoleId;

    public function init()
    {
        $this->parentRoles = array();
        $this->perms = array();
    }

    /**
     * Declares the validation rules.
     */
    public function rules()
    {
        return array(
            array('name', 'required'),
        );
    }

    /**
     * Declares attribute labels.
     */
    public function attributeLabels()
    {
        return array(
        );
    }

    public function populate()
    {
        if(!isset($this->roleId)) return;
        $role = Role::model()->findByPk($this->roleId);
        if (!isset($role)) return;

        $this->name = $role->Name;
        $this->description = $role->Description;
        $this->defaultForLoginRoleId = $role->DefaultForLoginRoleId;
        $this->parentRoles = array();
        foreach ($role->ParentRoles as $p){
            $this->parentRoles[] = $p->Id;
        }
        $this->perms = array();
        foreach ($role->Perms as $p){
            $this->perms[] = $p->PermissionId;
        }
    }

    public function save()
    {
        if (empty($this->roleId)) {
            $role = new Role();
            $role->IsLive = 1;
            $role->DateCreated = new CDbExpression('NOW()');
            $role->CreatedBy = Yii::app()->user->loginId;
        } else {
            $role = Role::model()->findByPk($this->roleId);
        }

        $role->Name = $this->name;
        $role->Description = $this->description;
        $role->DefaultForLoginRoleId = $this->defaultForLoginRoleId;
        $role->DateUpdated = new CDbExpression('NOW()');
        $role->UpdatedBy = Yii::app()->user->loginId;


        $result = $role->save();
        if (!$result) return $result;

        // PARENT ROLES
        // delete unused
        $crit = new CDbCriteria();
        $crit->addColumnCondition(array("RoleId" => $role->Id));
        $crit->addNotInCondition("ParentRoleId",$this->parentRoles);
        RoleChild::model()->deleteAll($crit);

        // insert the rest
        if (!empty($this->parentRoles)) {
            foreach ($this->parentRoles as $p){
                $tcs = RoleChild::model()->findByAttributes(array(
                        "ParentRoleId" => $p,
                        "RoleId" => $role->Id,
                ));
                if (isset($tcs)) continue;

                $tcs = new RoleChild();
                $tcs->ParentRoleId = $p;
                $tcs->RoleId = $role->Id;
                $tcs->insert();
            }
        }


        // PERMS
        // delete unused

        $crit = new CDbCriteria();
        $crit->addColumnCondition(array("RoleId" => $role->Id));
        if (!empty($this->perms))
            $crit->addNotInCondition("PermissionId",$this->perms);
        RolePerm::model()->deleteAll($crit);
        // insert the rest
        if (!empty($this->perms)) {
            foreach ($this->perms as $p) {
                if (empty($p)) continue;

                $tcs = RolePerm::model()->findByAttributes(array(
                    "RoleId" => $role->Id,
                    "PermissionId" => $p,
                ));
                if (isset($tcs)) continue;

                $tcs = new RolePerm();
                $tcs->RoleId = $role->Id;
                $tcs->PermissionId = $p;
                $tcs->insert();
            }
        }
        return $result;
    }

    public function listUsers() {
        $role = Role::model()->findByPk($this->roleId);
        if (!isset($role)) return array();
        $users = $role->listUsers();
        usort($users,function($a,$b){
            return strcasecmp($a['FriendlyName'],$b['FriendlyName']);
        });

        return $users;
    }

    public function listExcludedUsers() {
        $role = Role::model()->findByPk($this->roleId);
        if (!isset($role)) return array();

        $includedUsers = $role->listUsers();
        $includedUsersIds = array();
        foreach ($includedUsers as $user) {
            $includedUsersIds[] = $user->LoginId;
        }

        $crit = new CDbCriteria();
        $crit->AddCondition("IsActive = 1");
        $crit->AddCondition("t.LoginRoleId <> :l");
        $crit->params["l"] = LoginRole::SUPER_ADMIN;
        $crit->order = "role.Description, t.FriendlyName";
        $allUsers = Login::model()->with("role")->findAll($crit);
        $result = array();
        foreach ($allUsers as $user) {
            if (!in_array($user->LoginId, $includedUsersIds)) {
                $result[$user->LoginId] = $user->role->Description." - ".$user->FriendlyName;
            }
        }
        return $result;
    }
}
?>
