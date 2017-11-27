<?php
/**
 * Description of ReportWholesaler
 *
 * @author ramon
 */
class ReportWholesaler extends ReportCsv
{
    public $clientId;
    public $deliveryDate;
    public $printCentreId;
    
    protected function getData()
    {
        $query = "select 	DeliveryDate,
                                PrintCentreName as PrintCentre,
                                RouteId as Route,
                                WholesalerId, 
                                WholesalerName as Wholesaler,                                
                                WholesalerAlias as WholesalerAlias,
                                TitleName,
                                PubPagination,
                                PubWeight,
                                BundleSize,
                                Quantity,
                                floor(Quantity/BundleSize) as Bundles,
                                Quantity % BundleSize as Odds,
                                round((Quantity * PubWeight) / 10000, 2) as Weight
        from client_route_instance_info
        where ClientId = :cid
          and PrintCentreId = :pcid
          and DeliveryDate = STR_TO_DATE(:dd, '%d/%m/%Y')
          and ClientRouteInstanceDetailsId IS NOT NULL /* avoid empty routes */
        group by RouteId, ClientWholesalerId, ClientTitleId
        order by DepartureTimeSort ASC, RouteId ASC, ArrivalTimeSort ASC, WholesalerAlias ASC, TitleName ASC";
        
        $data = Yii::app()->db
                ->createCommand($query)
                ->queryAll(true, array(
                    ':cid' => $this->clientId,
                    ':pcid' => $this->printCentreId,
                    ':dd' => $this->deliveryDate
                ));
        
        return $data;
    }
    
    public function outputCsv()
    {
        $data = $this->getData();
        $name = date("His")."-{$this->printCentreId}-wholesalers.csv";
        $this->output($data, $name);
    }
    
/*    public function outputCsv()
    {
        $data = $this->getData();
        
        $local_file = tempnam('/tmp','');
        $temp = fopen($local_file,"w");
        $headerDone = FALSE;
        foreach ($data as $d){
            if ($headerDone === FALSE)
            {
                $headerDone = TRUE;
                fputcsv($temp, array_keys($d));
            }
            else
                fputcsv($temp, array_values($d));
        }

        fclose($temp);
        header("Content-Disposition: attachment; filename=\"".date("His")."-wholesalers.csv\"");
        header("Content-Type: application/force-csv");
        header("Content-Length: " . filesize($local_file));
        header("Connection: close");
        readfile($local_file);

        unlink($local_file); // this removes the file
        
        return TRUE;
    }*/
}

?>
