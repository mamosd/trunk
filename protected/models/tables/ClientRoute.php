<?php
/**
 * Description of ClientRoute
 *
 * @author ramon
 */
class ClientRoute extends CActiveRecord
{
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return 'client_route';
    }
}
?>