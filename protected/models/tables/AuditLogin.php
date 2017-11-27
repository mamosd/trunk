<?php
/**
 * Description of AuditLogin
 *
 * @author Ramon
 */
class AuditLogin extends CActiveRecord
{
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return 'auditlogin';
    }
}
?>