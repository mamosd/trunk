<?php
/**
 * Description of SecondaryRoute
 *
 * @author Ramon
 */
class SecondaryRoute extends CActiveRecord
{
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return 'secondaryroute';
    }
}
?>
