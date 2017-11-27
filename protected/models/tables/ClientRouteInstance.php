<?php
/**
 * Description of ClientRouteInstance
 *
 * @author ramon
 */
class ClientRouteInstance extends CActiveRecord
{
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return 'client_route_instance';
    }
}
?>
