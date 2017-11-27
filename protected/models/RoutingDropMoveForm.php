<?php
/**
 * Description of RoutingDropMoveForm
 *
 * @author ramon
 */
class RoutingDropMoveForm extends CFormModel
{
    public $instanceDetailsId;
    public $info;
    public $newRoute; // ClientRouteInstanceId
    
    public function populate()
    {
        if(!isset($this->instanceDetailsId))
                return FALSE;
        
        $this->info = ClientRouteInstanceInfo::model()->find(
                        'ClientRouteInstanceDetailsId = :id',
                        array(':id' => $this->instanceDetailsId)
                        );
        return (isset($this->info));
    }
    
    public function getNewRouteOptions()
    {
        $curRoute = $this->info->ClientRouteInstanceId;
        $curDate = $this->info->DeliveryDate;
        $curPrintCentre = $this->info->PrintCentreId;
        
        $routes = ClientRouteInstance::model()->findAll(
                    array(
                        'condition' => 'ClientRouteInstanceId != :crid AND DeliveryDate = :dt AND PrintCentreId = :pcid',
                        'params' => array(':crid' => $curRoute, ':dt' => $curDate, ':pcid' => $curPrintCentre),
//                        'order' => "(case when (`DepartureTime` between '17:00' and '24:00') then `DepartureTime` else addtime(`DepartureTime`,'24:00') end) ASC"
                        'order' => "RouteId ASC"
                    )
                );
        
        $result = array();
        foreach($routes as $r)
        {
            $result[$r->ClientRouteInstanceId] = $r->RouteId;
        }
        return $result;
    }
    
    public function save()
    {
/* // #38 commented out
        $details = ClientRouteInstanceDetails::model()->findByPk($this->instanceDetailsId); 
    }
        $items = ClientRouteInstanceInfo::model()->findAll(
                    'ClientRouteInstanceId = :cid AND ClientWholesalerId = :wsid',
                    array(':cid' => $details->ClientRouteInstanceId, ':wsid' => $details->ClientWholesalerId)
                );
*/
        $items = ClientRouteInstanceInfo::model()->findAll(
                    'ClientRouteInstanceDetailsId = :did',
                    array(':did' => $this->instanceDetailsId)
                );
        
//        var_dump($items);
//        die;
        
        foreach($items as $item)
        {
            $row = ClientRouteInstanceDetails::model()->findByPk($item->ClientRouteInstanceDetailsId);
            $row->Comments = $row->Comments . ' Old routeInstanceId:'.$row->ClientRouteInstanceId.' '.date('Y-m-d H:i:s');
            $row->ClientRouteInstanceId = $this->newRoute;
            $row->DateUpdated = new CDbExpression('now()');
            $row->save();
        }
        
        return TRUE;
    }
}

?>
