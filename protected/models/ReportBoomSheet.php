<?php
/**
 * Description of ReportBoomSheet
 *
 * @author ramon
 */
class ReportBoomSheet extends ReportCsv
{
    public $clientId;
    public $deliveryDate;
    public $printCentreId;
    
    protected function getData()
    {
        $query = "select    DeliveryDate,
                            PrintCentreName as PrintCentre,
                            RouteId,
                            WholesalerId, 
                            WholesalerAlias,
                            TitleFilter as TitleName,
                            BundleSize,
                            sum(Quantity) as Quantity,
                            floor(sum(Quantity)/BundleSize) as Bundles,
                            sum(Quantity) % BundleSize as Odds,
                            round((sum(Quantity) * PubWeight) / 10000, 2) as Weight,
                            group_concat(TitleName SEPARATOR '; ') as Titles
        from 
        (select r.*, case substr(TitleId,1,2) 
                       when 'DX' then 'Express(All)'
                       when 'SX' then 'Express(All)'
                       when 'DS' then 'Star (All)' 
                       when 'SS' then 'Star (All)' 
                                else TitleName end as TitleFilter
        from client_route_instance_info r
        where ClientId = :cid
          and PrintCentreId = :pcid
          and DeliveryDate = STR_TO_DATE(:dd, '%d/%m/%Y')
          and ClientRouteInstanceDetailsId IS NOT NULL /* avoid empty routes */ ) r2
        group by RouteId, ClientWholesalerId, TitleFilter
        order by DepartureTimeSort ASC, RouteId ASC, ArrivalTimeSort ASC, WholesalerAlias ASC, TitleFilter ASC";
        
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
        $name = date("His")."-{$this->printCentreId}-boom.csv";
        $this->output($data, $name);
    }
}

?>
