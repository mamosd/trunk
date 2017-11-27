<?php

class PolestarPrintCentreLogin extends CActiveRecord
{
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return 'polestar_printcentre_login';
    }
    
    public function relations() {
        return array(
            'Login'     => array( self::BELONGS_TO, 'Login', 'LoginId'),
        );
    }
}
