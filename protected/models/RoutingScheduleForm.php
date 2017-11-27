<?php
/**
 * Description of RoutingScheduleForm
 *
 * @author ramon
 */
class RoutingScheduleForm  extends CFormModel 
{
    public $clientId;
    public $deliveryDate;
    public $printCentreId;
    
    public $scheduleInfo;
    
    public function attributeLabels()
    {
        return array(
            'clientId' => 'Client',
            'printCentreId' => 'Print Centre'
        );
    }
    
    public function rules()
    {
        return array(
            array('clientId, printCentreId, deliveryDate', 'required')
            );
    }
    
    public function getSchedule()
    {
        $details = ClientRouteInstanceInfo::model()->findAll(array(
                                                'condition' => "ClientId = :cid AND DeliveryDate = STR_TO_DATE(:dd, '%d/%m/%Y') AND PrintCentreId = :pcid",
                                                'params' => array(':cid' => $this->clientId, 'dd' => $this->deliveryDate, ':pcid' => $this->printCentreId),
                                                'order' => 'DepartureTimeSort ASC, RouteId ASC, ArrivalTimeSort ASC, WholesalerAlias ASC, TitleName ASC'
                    ));
        if (!empty($details))
            $this->scheduleInfo = $details;
    }
    
    public function getClientOptions()
    {
        $clients = Client::model()->findAll(array(
                                                'condition' => 'RoutingEnabled = 1',
                                                'order' => 'Name ASC'
            ));
        $result = array();
        foreach ($clients as $client) {
            $result[$client->ClientId] = $client->Name;
        }
        return $result;
    }
    
    public function getPrintCentreOptions()
    {
        // TODO: make this query client-specific
        $pcs = ClientPrintCentre::model()->findAll(array(
                                                'condition' => 'ClientId = 2 AND Enabled = 1',
                                                'order' => 'Name ASC'
            ));
        
        $result = array();
        foreach ($pcs as $pc) {
            $result[$pc->PrintCentreId] = $pc->Name;
        }
        return $result;
    }
    
    public static function toMinutes($time)
    {
        $parts = explode(':', $time);
        return (intval($parts[0]) * 60) + intval($parts[1]);
    }
    
    public static function formatHHMM($mins)
    {
        $mins = abs($mins);
        $h = floor($mins / 60);
        $m = $mins % 60;
        return sprintf('%02d:%02d', $h, $m);
    }


    public static function getDepartureVariance($drop)
    {
        if (empty($drop->DepartureTimeSort) || empty($drop->DepartureTimeActualSort))
                return FALSE;
        
        return RoutingScheduleForm::toMinutes($drop->DepartureTimeActualSort) - RoutingScheduleForm::toMinutes($drop->DepartureTimeSort);
    }

    public static function getArrivalVariance($drop)
    {
        $result = FALSE;
        if (empty($drop->ArrivalTimeSort) || empty($drop->ArrivalTimeActualSort))
                return $result;
        
//        $depVar = $this->getDepartureVariance($drop);
//        if ($depVar !== FALSE)
//        {
            $arrVar = RoutingScheduleForm::toMinutes($drop->ArrivalTimeActualSort) - RoutingScheduleForm::toMinutes($drop->ArrivalTimeSort);
            
//            if ($depVar > 0) // delay on departure
//                $arrVar = $arrVar - $depVar;
            
            $result = $arrVar;
//        }
        
        return $result;
    }
    
    public static function getNPAVariance($drop)
    {
        $result = FALSE;
        if (empty($drop->NPATimeSort) || empty($drop->ArrivalTimeActualSort))
                return $result;
        
        $result = RoutingScheduleForm::toMinutes($drop->ArrivalTimeActualSort) - RoutingScheduleForm::toMinutes($drop->NPATimeSort);
        
        return $result;                
    }
}

?>
