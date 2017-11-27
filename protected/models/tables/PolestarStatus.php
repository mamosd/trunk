<?php
/**
 * Description of PolestarStatus
 *
 * @author ramon
 */
class PolestarStatus extends CActiveRecord
{

    const CANCELLED_ID = 'CA';
    const NEWLY_ADDED_ID = 'NA';
    const SAME_DAY_ID = 'SD';
    const LATE_ADVICE_ID = 'LA';
    const AMENDED_ID = 'A';
    const BOOKED_ID = 'B';
    const CONFIRMED_ID = 'CO';
    const DATA_COMPLETED_ID = 'DC';
    const ALLOCATED_ID = 'AL';

    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return 'polestar_status';
    }
    
    public static function getAllAsOptions() {
        $all = PolestarStatus::model()->findAll(array(
            'order' => 'Name ASC',
        ));
        $result = array();
        foreach ($all as $pc) {
            $result[$pc->Id] = $pc->Name;
        }
        return $result;
    }
}
