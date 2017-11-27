<?php
/**
 * Description of RoutingDataClearForm
 *
 * @author ramon
 */
class RoutingDataClearForm extends CFormModel
{
    public $date;
    public $clientId;
    public $message;
    
    public function rules()
    {
        return array(
            array('date,clientId', 'required'),
            );
    }
    
    public function attributeLabels()
    {
        return array(
            'clientId' => 'Client',
        );
    }
    
    public function process()
    {
        // clear all route imported/entered data, keeping only Magazines entries.
        // grab all entries for selected date/client
        $details = ClientRouteInstanceInfo::model()->findAll(array(
                                                'condition' => "ClientId = :cid AND DeliveryDate = STR_TO_DATE(:dd, '%d/%m/%Y')",
                                                'params' => array(':cid' => $this->clientId, 'dd' => $this->date),
                    ));
        if (!empty($details))
        {
            /* DROPS TO DELETE */
            $query = "delete dr.*
                        from client_route_instance_drop dr left join client_title t
                                on (dr.ClientTitleId = t.ClientTitleId)
                        left join client_route_instance_details dt
                                on (dr.ClientRouteInstanceDetailsId = dt.ClientRouteInstanceDetailsId)
                        left join client_route_instance i
                                on (dt.ClientRouteInstanceId = i.ClientRouteInstanceId)
                        where i.DeliveryDate = STR_TO_DATE(:dd, '%d/%m/%Y')
                          and i.ClientId = :cid
                          and t.TitleType != 'M'";
            
            Yii::app()->db
                ->createCommand($query)
                ->execute(array(':cid' => $this->clientId, 'dd' => $this->date));
            
            /* DETAILS TO DELETE */
            $query = "delete dt.*
                        from client_route_instance_details dt left join client_route_instance_drop dr
                                on (dt.ClientRouteInstanceDetailsId = dr.ClientRouteInstanceDetailsId)
                        left join client_route_instance i
                                on (dt.ClientRouteInstanceId = i.ClientRouteInstanceId)
                        where i.DeliveryDate = STR_TO_DATE(:dd, '%d/%m/%Y')
                          and i.ClientId = :cid
                          and dr.ClientRouteInstanceDropId is null";
            
            Yii::app()->db
                ->createCommand($query)
                ->execute(array(':cid' => $this->clientId, 'dd' => $this->date));
            
            /* ROUTE INSTANCES TO DELETE */
            $query = "delete i.*
                        from client_route_instance i left join client_route_instance_details d
                                on (i.ClientRouteInstanceId = d.ClientRouteInstanceId)
                        where i.DeliveryDate = STR_TO_DATE(:dd, '%d/%m/%Y')
                          and i.ClientId = :cid
                          and d.ClientRouteInstanceDetailsId is null";
            
            Yii::app()->db
                ->createCommand($query)
                ->execute(array(':cid' => $this->clientId, 'dd' => $this->date));
                        
            $this->message = "Data successfully cleared for {$this->date}.";
        }
        else        
            $this->message = "There is no data for {$this->date}.";
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
}

?>
