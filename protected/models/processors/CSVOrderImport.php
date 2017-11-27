<?php
/**
 * Description of CSVOrderImport
 *
 * @author ramon
 */
class CSVOrderImport {
    
    private $columns = array(
        'Delivery_Date',
        'Pub_Code',
        'Pub_Weight',
        'Pub_Pagination',
        'Bundle_size',
        'Wholesaler_ID',
        'Route_id',
        'Quantity'
    );
    private $routeIdColumn = 'Route_id';
    
    public $errorMessage = "";
    
    public function parseFile ($fileName, $clientId){
        $processor = new Csv();
        $rows = $processor->parseFileAssoc($fileName);
        $error = array();
        
        // validate all columns exist
        $header = array_keys($rows[0]);
        foreach ($this->columns as $col) {
            if (!in_array($col, $header))
                    $error[] = "Required column $col missing.";
        }
        if (!empty ($error))
        {
            $this->errorMessage = implode("\n", $error);
            return false;
        }
        
        // TODO: validate order file has not been imported already
        
        // process file
        // group items by route id
        $items = array();
        foreach ($rows as $row) {
            if (!isset($items[$row[$this->routeIdColumn]]))
                    $items[$row[$this->routeIdColumn]] = array();
            $items[$row[$this->routeIdColumn]][] = $row;
        }
        
        foreach($items as $routeId => $lines)
        {
            // weekday
            $date = CDateTimeParser::parse(substr($lines[0]['Delivery_Date'], 0, 10) , 'dd/MM/yyyy');
            $weekday = date('w', $date);
            
            //check if route is defined for given route id
            $clientRouteId = $this->getClientRouteId($clientId, $routeId, $lines, $weekday);
            
            // create route instance based on route details.
            $route = ClientRoute::model()->findByPk($clientRouteId);   
            $ri = ClientRouteInstance::model()->find(
                    "ClientId = :cid AND RouteId = :rid AND DeliveryDate = str_to_date(:dt, '%d/%m/%Y')",
                    array(':cid' => $clientId, ':rid' => $routeId, ':dt' => $lines[0]['Delivery_Date'])
                    );
            if (!isset($ri))
            {
                $ri = new ClientRouteInstance();
                $ri->DateCreated = new CDbExpression('NOW()');
                $ri->ClientId = $clientId;
                $ri->RouteId = $routeId;
                $ri->DeliveryDate = new CDbExpression("str_to_date('".$lines[0]['Delivery_Date']."', '%d/%m/%Y')");
                $ri->PrintCentreId = $route->PrintCentreId;
                $ri->VehicleId = $route->VehicleId;
                $ri->DepartureTime = $route->DepartureTime;
            }
            $ri->FileUploaded = $fileName;            
            $ri->save();
            
            // build route instance details -- cater for differences
            foreach($lines as $line)
            {
                $titleInfo = $this->parseTitleInfo($clientId, $line['Pub_Code']);
                
                $wsId = $this->getClientWholesalerId($clientId, $line['Wholesaler_ID']);
                $tId = $this->getClientTitleId($clientId, $line['Pub_Code'], $titleInfo['TitleName']);
                
                $rd = ClientRouteDetails::model()->find(array('condition' => 'ClientRouteId = :crid AND ClientWholesalerId = :cwid',
                                                'params' => array(
                                                    ':crid' => $clientRouteId,
                                                    ':cwid' => $wsId,
                                                )));
                
                $rid = ClientRouteInstanceDetails::model()->find(array(
                    'condition' => 'ClientRouteInstanceId = :rid AND ClientWholesalerId = :wsid',
                    'params' => array(':rid' => $ri->ClientRouteInstanceId, ':wsid' => $wsId)
                ));
                if (!isset($rid))
                {
                    $rid = new ClientRouteInstanceDetails();
                    $rid->ClientRouteInstanceId = $ri->ClientRouteInstanceId;
                    // if rd exists
                    if (isset($rd))
                    {
                        $rid->ClientWholesalerId = $rd->ClientWholesalerId;
                        $rid->ArrivalTime = $rd->ArrivalTime;
                        $rid->NPATime = $rd->NPATime;
                    }
                    else
                    {
                        $rid->ClientWholesalerId = $wsId;
                    }
                    $rid->DateUpdated = new CDbExpression('NOW()');
                    $rid->save();
                }
                
                // drop -- check if exists for the case when new blank routes are created
                $drop = ClientRouteInstanceDrop::model()->find(array(
                    'condition' => 'ClientRouteInstanceDetailsId = :id AND ClientTitleId = :tid',
                    'params' => array(':id' => $rid->ClientRouteInstanceDetailsId, ':tid' => $tId)
                ));
                if (!isset($drop))
                {
                    $drop = new ClientRouteInstanceDrop();
                    $drop->ClientRouteInstanceDetailsId = $rid->ClientRouteInstanceDetailsId;
                    $drop->ClientTitleId = $tId;
                }
                $drop->PubPagination = $line['Pub_Pagination'];
                $drop->PubWeight = $line['Pub_Weight'];
                $drop->BundleSize = $line['Bundle_size'];
                $drop->Quantity = $line['Quantity'];
                
                $drop->DateUpdated = new CDbExpression('NOW()');
                $drop->save();
            }
        }
        return TRUE;
    }
    
    private function getClientRouteId($clientId, $routeId, $lines, $weekday)
    {
        $route = ClientRoute::model()->find(array('condition' => 'ClientId = :cid AND RouteId = :rid AND Weekday = :wd',
                                                        'params' => array(':cid' => $clientId, ':rid' => $routeId, ':wd' => $weekday)));

        if (!isset($route))
        {
            // if not defined, create route based on import
            // trigger alert
            $titleInfo = $this->parseTitleInfo($clientId, $lines[0]['Pub_Code']);
            
            $route = new ClientRoute();
            $route->ClientId = $clientId;
            $route->RouteId = $routeId;
            $route->Weekday = $weekday;
            $route->DateCreated = new CDbExpression('NOW()');
            $route->PrintCentreId = $this->getPrintCentreId($clientId, $titleInfo['PrintCentreName']);
            $route->save();
        }
            
        foreach($lines as $line)
        {
            //$titleInfo = $this->parseTitleInfo($clientId, $line['Pub_Code']);
            $wsId = $this->getClientWholesalerId($clientId, $line['Wholesaler_ID']);
            $routeDetail = ClientRouteDetails::model()->find(array(
                'condition' => 'ClientRouteId = :rid AND ClientWholesalerId = :wsid',
                'params' => array(':rid' => $route->ClientRouteId, ':wsid' => $wsId)
            ));
            if (!isset($routeDetail))
                $routeDetail = new ClientRouteDetails();
            
            $routeDetail->ClientRouteId = $route->ClientRouteId;
            $routeDetail->ClientWholesalerId = $wsId;
            $routeDetail->DateUpdated = new CDbExpression('NOW()');

            $routeDetail->save();
        }
        
        return $route->ClientRouteId;
    }
    
    private function getClientWholesalerId($clientId, $wholesalerId)
    {
        $wholesaler = ClientWholesaler::model()->find(array('condition' => 'ClientId = :cid AND WholesalerId = :wid',
                                                        'params' => array(':cid' => $clientId, ':wid' => $wholesalerId)));
        if (!isset($wholesaler))
        {
            $wholesaler = new ClientWholesaler();
            $wholesaler->ClientId = $clientId;
            $wholesaler->WholesalerId = $wholesalerId;
            
            $wholesaler->save();
        }
        
        return $wholesaler->ClientWholesalerId;
    }
    
    private function getClientTitleId($clientId, $titleId, $titleName)
    {
        $title = ClientTitle::model()->find(array('condition' => 'ClientId = :cid AND TitleId = :tid',
                                                        'params' => array(':cid' => $clientId, ':tid' => $titleId)));
        if (!isset($title))
        {
            $title = new ClientTitle();
            $title->ClientId = $clientId;
            $title->TitleId = $titleId;
            $title->Name = $titleName;
            $title->save();
        }
        
        return $title->ClientTitleId;
    }
    
    private function getPrintCentreId($clientId, $printCentreName)
    {
        $pc = ClientPrintCentre::model()->find(array('condition' => 'Name = :pn AND ClientId = :cid',
                                                        'params' => array(':cid' => $clientId, ':pn' => $printCentreName)));
        if (!isset($pc))
        {
            $pc = new ClientPrintCentre();
            $pc->Name = $printCentreName;
            $pc->DateCreated = new CDbExpression('NOW()');
            $pc->ClientId = $clientId;
            $pc->save();
        }
        return $pc->PrintCentreId;
    }
    
    /****** CLIENT SPECIFIC PARSING ****/
    private function parseTitleInfo($clientId, $titleId)
    {
        $pubs = array(
            'DS' => 'Star',
            'DX' => 'Express',
            'SS' => 'Star',
            'SX' => 'Express'
        );
        
        $centres = array(
            'BR' => 'Broughton',
            'LU' => 'Luton',
            'GL' => 'Glasgow',
            'UL' => 'Ulster'
        );
        
        $days = array(
            '01' => 'Monday',
            '02' => 'Tuesday',
            '03' => 'Wednesday',
            '04' => 'Thursday',
            '05' => 'Friday',
            '06' => 'Saturday',
            '07' => 'Sunday'
        );
        
        $types = array(
            'FREE' => 'Free',
            'MAIN' => 'Main',
            'PROM' => 'Promotion',
        );
        
        $pubCode = substr($titleId, 0, 2);
        $day = substr($titleId, 2, 2);
        $printCentreCode = substr($titleId, 4, 2);
        $pubType = substr($titleId, 6);
        
        $p = isset($pubs[$pubCode]) ? $pubs[$pubCode] : $pubCode;
        $t = isset($types[$pubType]) ? $types[$pubType] : $pubType;
        $d = isset($days[$day]) ? $days[$day] : $day;
        $pc = isset($centres[$printCentreCode]) ? $centres[$printCentreCode] : $printCentreCode;
        
        $titleName = sprintf("%s (%s) - %s", $p, $t, $d);
        
        return array(
                'TitleName' => $titleName, 
                'PrintCentreName' => $pc
            );
    }
    
}

?>
