<?php
/**
 * Description of PolestarJobHistory
 *
 * @author ramon
 */
class PolestarJobHistory  extends PolestarJobLoadRecord
{
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return 'polestar_job_history';
    }
    
    public function relations() {
        return array(
            'EditedByLogin'     => array( self::BELONGS_TO, 'Login', 'EditedBy'),
            'CreatedByLogin'    => array( self::BELONGS_TO, 'Login', 'CreatedBy'),
        );
    }
}
