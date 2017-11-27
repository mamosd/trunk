<?php
/**
 * Description of LoginRole
 *
 * @author Ramon
 */
class LoginRole extends CActiveRecord
{
    const SUPER_ADMIN = 0;
    const ADMINISTRATOR = 1;
    const CLIENT = 2;
    const SUPPLIER = 3;

    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return 'loginrole';
    }
    
    public static function getRoleOptions()
    {
        $result = array();
        $roles = LoginRole::model()->findAll("LoginRoleId !=:sadmin", array(":sadmin" => LoginRole::SUPER_ADMIN));
        foreach ($roles as $role) {
            $result[$role->LoginRoleId] = $role->Description;
        }
        return $result;
    }
}
?>
