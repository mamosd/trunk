<?php
/**
 * Description of PolestarJobType
 *
 * @author ramon
 */
class PolestarJobType extends CActiveRecord
{
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return 'polestar_job_type';
    }
    
    public static function getAllAsOptions() {
        $all = PolestarJobType::model()->findAll(array(
            'order' => 'Name ASC',
        ));
        $result = array();
        foreach ($all as $jt) {
            $result[$jt->Id] = $jt->Name;
        }
        return $result;
    }
}
