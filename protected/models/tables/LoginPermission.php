<?php
class LoginPermission extends CActiveRecord
{
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return 'login_permission';
    }

    public function relations(){
        return array(
        );
    }

    public static function addLoginPerm($loginId, $permId) {
        $lp = new LoginPermission();
        $lp->LoginId = $loginId;
        $lp->PermissionId = $permId;
        $lp->insert();
    }
}
?>