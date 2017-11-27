<?php
/**
 * Description of PolestarLoadComment
 *
 * @author ramon
 */
class PolestarLoadComment extends CActiveRecord
{
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return 'polestar_load_comment';
    }

    public function relations() {
        return array(
            'Login'       => array( self::BELONGS_TO, 'Login', 'CreatedBy'),
        );
    }
}
