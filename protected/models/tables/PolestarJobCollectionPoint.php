<?php
/**
 * Description of PolestarJobCollectionPoint
 *
 * @author ramon
 */
class PolestarJobCollectionPoint extends PolestarJobLoadRecord
{
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return 'polestar_job_collection_point';
    }

    public function relations() {
        return array(
            'Login'       => array( self::BELONGS_TO, 'Login', 'CreatedBy'),
        );
    }
}
