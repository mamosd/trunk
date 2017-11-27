<?php
/**
 * Description of PolestarJobMap
 *
 * @author ramon
 */
class PolestarJobMap {
    public $jobId;
    private $jobInfo;
    
    public $messages = array();
    
    public function getJob() {
        if (!isset($this->jobInfo))
            $this->jobInfo = PolestarJob::model()->with('Loads', 'CollectionPoints')->findByPk($this->jobId);
        return $this->jobInfo;
    }
    
    public static function clearMileageInfo($jobId) {
        $job = PolestarJob::model()->with('Loads', 'CollectionPoints')->findByPk($jobId);
        $job->Mileage = NULL;
        $job->TotalMileage = NULL;
        $job->save();
        foreach ($job->CollectionPoints as $point) {
            $point->Mileage = NULL;
            $point->save();
        }
        foreach ($job->Loads as $load) {
            $load->Mileage = NULL;
            $load->save();
        }
    }
    
    public function isMileageOutdated() {
        $job = $this->getJob();
        
        return (empty($job->Mileage)); // TODO: fine tune this logic
    }
    
    public function updateMileage() {
        $jsonData = $this->getJsonData();
        
        if ($jsonData === FALSE) {
            $this->messages[] = array(
                'class' => 'error',
                'message' => 'Failed to reach the directions service, please try again later.'
            );
            return;
        }
        
        $data = json_decode($jsonData);
        
        if ($data->status == "OK") {
        
            $d = new PolestarJobDirections();
            $d->JobId       = $this->jobId;
            $d->Directions  = $jsonData;
            $d->CreatedBy   = Yii::app()->user->loginId;
            $d->CreatedDate = new CDbExpression('NOW()');
            $d->save();
        
            $route = $data->routes[0];
        
            $legInfo = array();
            $totalMileage = 0.0;
            foreach ($route->legs as $leg) {
                $destination = $this->sanitizePostcode($leg->end_address);
                $distance = $leg->distance;
                
                $meters = $distance->value;
                $calcMiles = $this->metersToMiles($meters);
                
                $miles = ceil($calcMiles); // #201
                
                $totalMileage += $miles;
                //$legInfo[$destination] = number_format($miles, 2);
                $legInfo[$destination] = $miles; //#201
            }
            
//            var_dump($legInfo); die;
            
            $job = $this->getJob();
            
            $job->Mileage = '0'; // #201 //number_format(0, 2);
            //$job->TotalMileage = number_format($totalMileage, 2);
            $job->TotalMileage = $totalMileage; // #201
            $job->save();
            
            $current = '';
            foreach ($job->CollectionPoints as $point) {
                $postcode = $this->sanitizePostcode($point->CollPostcode);
                $legMiles = '0';
                if ($postcode != $current) {
                    $current = $postcode;
                    $legAddress = NULL;
                    foreach ($legInfo as $address => $legMiles) {
                        if (stristr($address, $postcode))
                           $legAddress = $address;
                    }
                    if (isset($legAddress)) {
                        $legMiles = $legInfo[$legAddress];
                        unset($legInfo[$legAddress]);
                    }
                }
                $point->Mileage = $legMiles;
                $point->save();
            }
            
            $current = '';
            foreach ($job->Loads as $load) {
                if ($load->StatusId == PolestarStatus::CANCELLED_ID)
                    continue;
                
                $postcode = $this->sanitizePostcode($load->DelPostcode);
                $legMiles = '0';
                if ($postcode != $current) {
                    $current = $postcode;
                    $legAddress = NULL;
                    foreach ($legInfo as $address => $legMiles) {
                        if (stristr($address, $postcode))
                           $legAddress = $address;
                    }
                    if (isset($legAddress)) {
                        $legMiles = $legInfo[$legAddress];
                        unset($legInfo[$legAddress]);
                    }
                }
                $load->Mileage = $legMiles;
                $load->save();
            }
            
            $this->messages[] = array(
                'class' => 'success',
                'message' => 'Mileage information updated successfully. Information stored for future usage/reference.'
            );
        }
        else {
            $this->messages[] = array(
                'class' => 'error',
                'message' => 'Failed to retrieve information from the directions service. Please review load information and try again'
            );
        }
        
        //print_r($data);
    }
    
    private function metersToMiles($meters) {
        return $meters * 0.00062137;
    }
    
    private function getJsonData() {
        $url = $this->getDirectionsUrl();
        
        // Make our API request
        /*$curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $return = curl_exec($curl);
        curl_close($curl);*/
        
        $return = FALSE;
        if ($url !== FALSE) {
            try {
                $return = file_get_contents($url); // TODO: cater for errors here!
            } catch (Exception $ex) {
                $return = FALSE;
            }
        }
        else {
            $this->messages[] = array(
                'class' => 'error',
                'message' => 'Failed to build proper routing information with load definition available, please review the job loads.'
            );
        }
        return $return;
    }
    
    private function sanitizePostcode($pc) {
        return PolestarJobMap::doSanitizePostcode($pc);
    }
    
    public static function doSanitizePostcode($pc) {
        $postcode = $pc;
        $postcode = strtolower($postcode);
        $postcode = preg_replace("/[^a-z0-9]/", '', $postcode);
        return $postcode;
    }
    
    private function getDirectionsUrl() {
        $job = $this->getJob();
        
        $origin = $this->sanitizePostcode($job->CollPostcode);
        $waypoints = array();
        $current = '';
        foreach ($job->CollectionPoints as $point) {
            $postcode = $this->sanitizePostcode($point->CollPostcode);
            if ($postcode != $current) {
                $waypoints[] = $postcode;
                $current = $postcode;
            }
        }
        foreach ($job->Loads as $load) {
            if ($load->StatusId == PolestarStatus::CANCELLED_ID)
                    continue;
            
            $postcode = $this->sanitizePostcode($load->DelPostcode);
            if ($postcode != $current) {
                $waypoints[] = $postcode;
                $current = $postcode;
            }
        }
        
        if (empty($waypoints)) // no loads / collection points
            return FALSE;
        
        $destination = array_pop($waypoints);
        
        $params = array(
            'origin'        => $origin,
            'destination'   => $destination,
            'sensor'        => 'false',
            'units'         => 'imperial',
            'region'        => 'gb'
        );
        if (count($waypoints) > 0)
            $params['waypoints'] = implode ('|', $waypoints);
        
        //print_r($params);
        
        $params_string = "";
        foreach($params as $var => $val){
            $params_string .= '&' . $var . '=' . urlencode($val);  
        }
        
        $url = "http://maps.googleapis.com/maps/api/directions/json?".ltrim($params_string, '&');
        return $url;
    }
    
    public function getLatestDirections() {
        $info = PolestarJobDirections::model()->find(array(
            'condition' => 'JobId = :jid',
            'params' => array(':jid' => $this->jobId),
            'order' => 'Id DESC'
        ));
        
        if (isset($info))
            $info = json_decode($info->Directions);
        
        return $info;
    }
}
