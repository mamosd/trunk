<?php

/**
 * UserIdentity represents the data needed to identity a user.
 * It contains the authentication method that checks if the provided
 * data can identity the user.
 */
class UserIdentity extends CUserIdentity
{
    /**
     * Authenticates a user.
     * @return boolean whether authentication succeeds.
     */
    public function authenticate()
    {
        $login = Login::model()->find('UserName=:userName', array(':userName'=>  $this->username));
        if (isset($login)) {
            if (0 === strcmp(0, $login->IsActive)) { // login is disabled
                $this->errorCode = self::ERROR_UNKNOWN_IDENTITY;
            }
            else {
                if ($login->Password == $this->password) {
                    $role = LoginRole::model()->find('LoginRoleId=:roleId', array(':roleId'=> $login->LoginRoleId));
                    $this->setState('role', $role);
                    $this->setState('loginId', $login->LoginId);
                    $this->setState('friendlyName', $login->FriendlyName);
                    $this->setState('permissions', $login->getPermissions());

                    $this->errorCode=self::ERROR_NONE;
                }
                else {
                    $this->errorCode=self::ERROR_PASSWORD_INVALID;
                }
            }
        }
        else {
            $this->errorCode=self::ERROR_USERNAME_INVALID;
        }
        return !$this->errorCode;
    }
}