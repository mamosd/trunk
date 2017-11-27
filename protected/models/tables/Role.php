<?php
class Role extends CActiveRecord
{

    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return 'role';
    }

    public function relations(){
        return array(
                'Perms'                 => array (self::HAS_MANY,   'RolePerm',      'RoleId'),
                'UpdatedByUser'         => array (self::BELONGS_TO, 'Login',                'UpdatedBy'),
                'DefaultForLoginRole'   => array (self::BELONGS_TO, 'LoginRole',            'DefaultForLoginRoleId'),
                'ParentRoles'           => array (self::MANY_MANY,  'Role',          'role_child(RoleId,ParentRoleId)'),
                'ChildRoles'            => array (self::MANY_MANY,  'Role',          'role_child(ParentRoleId,RoleId)'),
                'Users'                 => array (self::MANY_MANY,  'Login',                'role_user(RoleId,LoginId)','on' => "IsActive = 1"),
        );
    }

    public function fillPermissions(&$permissions, &$used_roles){
        if (in_array($this->Id,$used_roles)) return;
        $used_roles[] = $this->Id;

        foreach ($this->Perms as $p){
            $perm = Permission::get($p->PermissionId);
            $code = isset($perm['Code']) ? $perm['Code'] : $perm['PermissionId'];
            if (!in_array($code, $permissions)) {
                $permissions[] = $code;
            }
        }

        foreach ($this->ParentRoles as $role){
            $role->fillPermissions($permissions,$used_roles);
        }

    }

    public static function getAllAsOptionList($removeId = -1){
        $list = array();
        foreach (Role::model()->findAll(array("condition" => "IsLive = 1","order" => "Name")) as $s){
            if ($s->Id != $removeId)
                $list[$s->Id] = $s->Name;
        }
        return $list;
    }

    public function listUsers() {
        $users = $this->Users;
        foreach ($this->ChildRoles as $r) {
            $u = $r->listUsers();
            foreach ($u as $user) {
                if (!in_array($user,$users))
                    $users[] = $user;
            }
        }
        return $users;
    }
}
?>
