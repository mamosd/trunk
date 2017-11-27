<?php
/**
 * Description of ClientRouteInstanceInfo
 *
 * @author ramon
 */
class ClientRouteInstanceInfo extends CActiveRecord
{
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return 'client_route_instance_info';
    }
}
?>