<?php
/**
 * Description of SecondaryRound
 *
 * @author Ramon
 */
class SecondaryRound extends CActiveRecord
{
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return 'secondaryround';
    }
}
?>
