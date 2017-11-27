<?php
/**
 * Description of ClientRouteDetails
 *
 * @author ramon
 */
class ClientRouteDetails extends CActiveRecord
{
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return 'client_route_details';
    }
}
?>
