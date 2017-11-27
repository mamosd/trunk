<?php
/**
 * Description of PolestarJobDirections
 *
 * @author ramon
 */
class PolestarJobDirections extends CActiveRecord
{
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return 'polestar_job_directions';
    }

    public function relations() {
        return array(
            'Login'       => array( self::BELONGS_TO, 'Login', 'CreatedBy'),
        );
    }
}
