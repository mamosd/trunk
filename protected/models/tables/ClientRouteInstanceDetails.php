<?php
/**
 * Description of ClientRouteInstanceDetails
 *
 * @author ramon
 */
class ClientRouteInstanceDetails extends CActiveRecord
{
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return 'client_route_instance_details';
    }
}
?>