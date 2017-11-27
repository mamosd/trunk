<?php
/**
 * Description of PolestarJobComment
 *
 * @author ramon
 */
class PolestarJobComment extends CActiveRecord
{
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return 'polestar_job_comment';
    }

    public function relations() {
        return array(
            'Login'       => array( self::BELONGS_TO, 'Login', 'CreatedBy'),
        );
    }
}
