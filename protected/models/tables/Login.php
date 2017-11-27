<?php
/**
 * Description of Login
 *
 * @author Ramon
 */

class Login extends CActiveRecord
{
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return 'login';
    }
    
    public function relations(){
        return array(
            'permissions'   => array (self::HAS_MANY,   'LoginPermission',  'LoginId'),
            'clientLogin'   => array (self::HAS_ONE,    'ClientLogin',      'LoginId'),
            'lastLogin'     => array (self::BELONGS_TO, 'AuditLogin',       'LastAuditLoginId'),
            'roles'         => array (self::MANY_MANY,  'Role',      'role_user(LoginId,RoleId)'),
            'role'          => array (self::BELONGS_TO, 'LoginRole',        'LoginRoleId'),
        );
    }
    
    public function getPermissions(){
        $cs = $this->permissions;
        $result = array();
        foreach ($cs as $s){
            $perm = Permission::get($s->PermissionId);
            $result[] = isset($perm['Code']) ? $perm['Code'] : $perm['PermissionId'];
        }

        $checked_roles = array();
        foreach ($this->roles as $role){
            $role->fillPermissions($result,$checked_roles);
        }
        return $result;
    }
    
    static function checkPermission($permission, $isregex = false)
    {
        if (Yii::app()->user->role->LoginRoleId == LoginRole::SUPER_ADMIN) return TRUE; // SUPER ADMIN user has all permissions
        
        if (!isset(Yii::app()->user->permissions)) return FALSE;
        if (is_array($permission)){
            if ($isregex) {
                foreach (Yii::app()->user->permissions as $up) {
                    foreach($permission as $p) {
                        if (preg_match($p,$sp) === 1) return true;
                    }
                }
                return false;
            } else {
            	foreach ($permission as $p){
            		if (in_array($p, Yii::app()->user->permissions))
            			return true;
            	}
            	return false;
            }
        } else {
            if ($isregex) {
                foreach (Yii::app()->user->permissions as $p) {
                    //if (preg_match($permission,$p) === 1) return true;
                    if (preg_match($permission, Permission::$PERMS[$p]['Route'] ) === 1) return true;
                }
                return false;
            } else {
        	   return (in_array($permission, Yii::app()->user->permissions));
            }
        }
    }
}
?>
