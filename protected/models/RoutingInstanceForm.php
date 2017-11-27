<?php
/**
 * Description of RoutingInstanceForm
 *
 * @author ramon
 */
class RoutingInstanceForm  extends CFormModel 
{
    public $routeInstanceId;
    public $details;
    
    public function populate($routeInstanceId)
    {
        $info = ClientRouteInstanceInfo::model()->findAll(array(
                                                'condition' => "ClientRouteInstanceId = :rid",
                                                'params' => array(':rid' => $routeInstanceId),
                                                'order' => 'DepartureTimeSort ASC, RouteId ASC, ArrivalTimeSort ASC, WholesalerAlias ASC, TitleName ASC'
                    ));
        
        if (!empty($info))
        {
            $this->details = $info;
            $this->routeInstanceId = $routeInstanceId;
        }
        else
            return FALSE;
    }
    
    public function save($routeData, $drops)
    {
        // update instance details
        $instance = ClientRouteInstance::model()->findByPk($this->routeInstanceId);
        $instance->VehicleId = $routeData['Vehicle'];
        $instance->DepartureTime = (empty($routeData['DepartureTime'])) ? NULL : $routeData['DepartureTime'];
        $instance->DepartureTimeActual = (empty($routeData['DepartureTimeActual'])) ? NULL : $routeData['DepartureTimeActual'];
        $instance->save();
        
        // update route details
        // weekday
        $date = CDateTimeParser::parse($instance->DeliveryDate, 'yyyy-MM-dd');
        $weekday = date('w', $date);
        $route = ClientRoute::model()->find('ClientId = :cid AND RouteId = :rid AND Weekday = :wd', 
                                    array(':cid' => $instance->ClientId, ':rid' => $instance->RouteId, ':wd' => $weekday));
        if (isset($route))
        {
            $route->VehicleId = $instance->VehicleId;
            $route->DepartureTime = $instance->DepartureTime;
            $route->save();
        }
        $clientRouteId = $route->ClientRouteId;
        
        // update drops details
        foreach($drops as $wsId => $details)
        {
            // get linked wholesalerId's
            $condition = 'ClientRouteInstanceId = :rid';
            $params = array(':rid' => $this->routeInstanceId);
            
            $routeCondition = 'ClientRouteId = :rid';
            $routeParams = array(':rid' => $clientRouteId);
            
            $linked = ClientWholesaler::model()->findAll('MainClientWholesalerId = :wsid', array(':wsid' => $wsId));
            if (!empty($linked))
            {
                $ids = array();
                foreach($linked as $item)
                    $ids[] = $item->ClientWholesalerId;
                $ids[] = $wsId;
                
                $newCondition = " AND ClientWholesalerId in (".  implode(',', $ids).")";
                $condition .= $newCondition;
                $routeCondition .= $newCondition;
            }
            else
            {
                $newCondition = " AND ClientWholesalerId = :wsid";
                $condition .= $newCondition;
                $routeCondition .= $newCondition;
                
                $params[':wsid'] = $wsId;
                $routeParams[':wsid'] = $wsId;                
            }
            
            // update instance
            ClientRouteInstanceDetails::model()->updateAll(
                            array('ArrivalTime' => (empty($details['ArrivalTime'])) ? NULL : $details['ArrivalTime'],
                            'ArrivalTimeActual' => (empty($details['ArrivalTimeActual'])) ? NULL : $details['ArrivalTimeActual'],
                            'PlasticPalletsDelivered' => $details['PlasticDelivered'],
                            'PlasticPalletsCollected' => $details['PlasticCollected'],
                            'WoodenPalletsDelivered' => $details['WoodenDelivered'],
                            'WoodenPalletsCollected' => $details['WoodenCollected'],
                            'NPATime' => (empty($details['NPATime'])) ? NULL : $details['NPATime'],
                            'DateUpdated' => new CDbExpression('NOW()')),
                            $condition,
                            $params
                    );
            
            // update route
            ClientRouteDetails::model()->updateAll(
                            array('ArrivalTime' => (empty($details['ArrivalTime'])) ? NULL : $details['ArrivalTime'],
                            'NPATime' => (empty($details['NPATime'])) ? NULL : $details['NPATime'],
                            'DateUpdated' => new CDbExpression('NOW()')),
                            //'ClientRouteId = :rid AND ClientWholesalerId = :wsid',
                            $routeCondition,
                            //array(':rid' => $clientRouteId, ':wsid' => $wsId)
                            $routeParams
                    );
        }
    }
    
    public function getVehicleOptions()
    {
        $vehicles = ClientVehicle::model()->findAll(array(
            'condition' => 'ClientId = :cid',
            'params' => array(':cid' => $this->details[0]->ClientId),
            'order' => 'Description ASC'
        ));
        
        $result = array();
        foreach($vehicles as $v)
        {
            $result[$v->VehicleId] = $v->Description;
        }
        return $result;        
    }
    
    /**
     * Generates a blank route based on previous instance for same weekday
     * $data: 
     * clientId
     * deliveryDate
     * printCentreId
     */
    public static function generateBlankRoute($data)
    {
        $deliveryDate = $data['deliveryDate'];
        $clientId = $data['clientId'];
        $printCentreId = $data['printCentreId'];
        
        // weekday
        $date = CDateTimeParser::parse($deliveryDate, 'dd/MM/yyyy');
        $weekday = date('w', $date);
        
        $dateForDb = date('Y-m-d', $date);
        $prevDate = date_create($dateForDb);
        //$prevDate = date_modify($prevDate, '-1 week');
        date_modify($prevDate, '-1 week');
        $prevDate = date_format($prevDate, 'Y-m-d');
        
        //var_dump($data);
        //echo "$date - $weekday - $dateForDb - $prevDate";
        //die;
        $routesToClone = ClientRouteInstance::model()->findAll(
                    'ClientId = :cid AND DeliveryDate = :dt AND PrintCentreId = :pc',
                    array(':cid' => $clientId, ':dt' => $prevDate, ':pc' => $printCentreId)
                );
        //var_dump($routesToClone);
        if (!empty($routesToClone))
        {
            foreach($routesToClone as $oldRI)
            {
                // retrieve details
                $detailsToClone = ClientRouteInstanceDetails::model()->findAll(
                            'ClientRouteInstanceId = :rid',
                            array(':rid' => $oldRI->ClientRouteInstanceId)
                        );
                // avoid cloning empty routes
                if (empty($detailsToClone))
                    continue;

                // clone instance
                $newRI = new ClientRouteInstance();
                $newRI->ClientId = $oldRI->ClientId;
                $newRI->RouteId = $oldRI->RouteId;
                $newRI->DeliveryDate = $dateForDb;
                $newRI->PrintCentreId = $oldRI->PrintCentreId;
                $newRI->VehicleId = $oldRI->PrintCentreId;
                $newRI->DepartureTime = $oldRI->DepartureTime;
                $newRI->DateCreated = new CDbExpression('now()');
                $newRI->save();
                
                // clone instance details
                foreach($detailsToClone as $oldRID)
                {
                    $newRID = new ClientRouteInstanceDetails();
                    $newRID->ClientRouteInstanceId = $newRI->ClientRouteInstanceId;
                    $newRID->ClientWholesalerId = $oldRID->ClientWholesalerId;
                    $newRID->ArrivalTime = $oldRID->ArrivalTime;
                    $newRID->NPATime = $oldRID->NPATime;
                    $newRID->DateUpdated = new CDbExpression('now()');
                    $newRID->save();
                }
                
                // clone drops
                // TEST without cloning drops -- they'll enter magazines, and main titles will be added upon import
            }
            
            return TRUE;
        }
        else
            return FALSE;
    }
    
    public static function deleteRoute($routeInstanceId)
    {
        // make sure route is empty
        $details = ClientRouteInstanceDetails::model()->findAll(
                            'ClientRouteInstanceId = :rid',
                            array(':rid' => $routeInstanceId)
                        );
        if (empty($details))
        {
            ClientRouteInstance::model()->deleteByPk($routeInstanceId);
            return TRUE;
        }
        else
            return FALSE;
    }
}

?>
