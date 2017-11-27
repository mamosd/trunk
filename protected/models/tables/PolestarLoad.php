<?php
/**
 * Description of PolestarLoad
 *
 * @author ramon
 */
class PolestarLoad extends PolestarJobLoadRecord
{
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return 'polestar_load';
    }
    
    // attributes to be ignored while comparing
    public $ignoreKeyChanges = array( 
        'EditedBy',
        'EditedDate'
    );
    
    public function behaviors() {
        return array( 'CompareBehavior'); // <-- and other behaviors your model may have
    }
    
    public function relations() {
        return array(
            'Comments'  => array( self::HAS_MANY, 'PolestarLoadComment', 'LoadId', 'alias' => 'cmm', 'order' => 'cmm.Id DESC'),
            'JobType'   => array( self::BELONGS_TO, 'PolestarJobType', 'JobTypeId' ),
            'EditedByLogin'     => array( self::BELONGS_TO, 'Login', 'EditedBy'),
            'CreatedByLogin'    => array( self::BELONGS_TO, 'Login', 'CreatedBy'),
        );
    }
}
