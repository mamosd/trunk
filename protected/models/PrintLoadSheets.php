<?php
/**
 * Description of PrintLoadSheets
 *
 * @author ramon
 */

$fpdfPath = Yii::getPathOfAlias('ext.fpdf');
require_once $fpdfPath.'/fpdf.php';

$fpdiPath = Yii::getPathOfAlias('ext.fpdi');
require_once $fpdiPath.'/fpdi.php';

class PrintLoadSheets
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
                            TitleType,
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
          and DeliveryDate = STR_TO_DATE(:dd, '%d/%m/%Y') ) r2
        group by RouteId, ClientWholesalerId, TitleFilter
        order by DepartureTimeSort ASC, RouteId ASC, ArrivalTimeSort ASC, TitleType DESC, TitleFilter ASC";
        
        $data = Yii::app()->db
                ->createCommand($query)
                ->queryAll(true, array(
                    ':cid' => $this->clientId,
                    ':pcid' => $this->printCentreId,
                    ':dd' => $this->deliveryDate
                ));
        
        return $data;
    }
    
    public function outputSheets($data)
    {
        // emulate setAttributes
        foreach($data as $key => $value)
            $this->$key = $value;

        $info = $this->getData();
        
        $pdf = $this->getPDF($info);
        $fileName = "loadsheets-".date("Ymd-His").".pdf";
        $pdf->Output($fileName, 'D');
    }
    
    private function getPDF($info)
    {
        $data = array();
        foreach($info as $row)
        {
            if (!empty($row['WholesalerId'])) // avoid empty routes
            {
                if (!isset($data[$row['RouteId']]))
                    $data[$row['RouteId']] = array();

                // group wholesalers for display
                if (!isset($data[$row['RouteId']]['ws']))
                    $data[$row['RouteId']]['ws'] = array();

                if (!in_array($row['WholesalerAlias'], $data[$row['RouteId']]['ws']))
                        $data[$row['RouteId']]['ws'][] = $row['WholesalerAlias'];

                // append items to grouped array
                if (!isset($data[$row['RouteId']]['items']))
                    $data[$row['RouteId']]['items'] = array();

                $data[$row['RouteId']]['items'][] = $row;
            }
        }
        
        // output one sheet per route, showing wholesalers and grouped titles
        $pdf = new FPDI('P','mm','A4');
        $pdf->SetMargins(0,0,0);
        $pdf->SetAutoPageBreak(false);
        $pdf->SetFillColor(255,255,255); // fill background to avoid overlapping
        
        $pagecount = $pdf->setSourceFile('_uploads/templates/express-load-sheet.pdf');
        $tplidx = $pdf->importPage(1);
        
        foreach($data as $routeId => $routeData)
        {
            $wholesalers = $routeData['ws'];
            $lineInfo = $routeData['items'];
            
            // group lines per TitleName
            $lines = array();
            foreach($lineInfo as $line)
            {
                if (!isset($lines[$line['TitleName']]))
                    $lines[$line['TitleName']] = $line;
                else
                {
                    // add copies/bundles/odds/weight
                    $lines[$line['TitleName']]['Quantity']  += $line['Quantity'];
                    $lines[$line['TitleName']]['Bundles']   += $line['Bundles'];
                    $lines[$line['TitleName']]['Odds']      += $line['Odds'];
                    $lines[$line['TitleName']]['Weight']    += $line['Weight'];
                    
                    if ($lines[$line['TitleName']]['Odds']  >= $lines[$line['TitleName']]['BundleSize'])
                    {
                        $lines[$line['TitleName']]['Bundles'] += floor($lines[$line['TitleName']]['Odds'] / $lines[$line['TitleName']]['BundleSize']);
                        $lines[$line['TitleName']]['Odds'] = $lines[$line['TitleName']]['Odds'] % $lines[$line['TitleName']]['BundleSize'];
                    }
                }
            }
            $lines = array_values($lines);
                        
            $pdf->AddPage();
            $pdf->useTemplate($tplidx);
            
            $deliveryDate = $lines[0]['DeliveryDate'];
            $pdf->SetFont('Arial', 'B', 12);
            // Delivery Date
            $pdf->SetXY(140, 38);
            $dateTS = CDateTimeParser::parse($deliveryDate, 'yyyy-MM-dd');
            $pdf->Cell(50, 3, Yii::app()->dateFormatter->format("dd/MM/yyyy", $dateTS), 0, 0, 'L', true);
            
            // Wholesaler Info
            $wsX = 25;
            $wsY = 45;
            foreach($wholesalers as $name)
            {
                $pdf->SetXY($wsX, $wsY);
                $pdf->Cell(100, 3, $name, 0, 0, 'L', true);
                $wsY += 5;
            }
            
            // Title List (separate standard and magazines)
            // assumption - on resultset we get S titles first, then M's.
            $headers = array(
                'S' => array('text' => 'Standard Titles', 'printed' => TRUE), // avoid showing header for Standard
                'M' => array('text' => 'Newspaper Supplements/Magazines', 'printed' => FALSE)
            );
            
            $tX = 22;
            $tY = 85; //tX and tY define top left corner of title listing.
            $lineHeight = 5;
            foreach($lines as $line)
            {
                // print header (if applicable)
                if (isset($headers[$line['TitleType']]) && ($headers[$line['TitleType']]['printed'] == FALSE))
                {
                    $tY += $lineHeight * 2; // blank line left intentionally
                    $pdf->SetFont('Arial', 'B', 12);
                    
                    $pdf->SetXY($tX, $tY-2);
                    $pdf->Cell(165, 3, '', 'T', 0, 'L', true);
                    
                    $pdf->SetXY($tX, $tY);
                    $pdf->Cell(75, 3, $headers[$line['TitleType']]['text'], 0, 0, 'L', true);
                    $headers[$line['TitleType']]['printed'] = TRUE;
                    $tY += $lineHeight * 1.5;
                }
                
                $pdf->SetFont('Arial', '', 11);
                // print title information                
                $pdf->SetXY($tX, $tY);
                $pdf->Cell(75, 3, $line['TitleName'], 0, 0, 'L', true);
                
                $pdf->SetXY($tX + 70, $tY);
                $pdf->Cell(10, 3, $line['Quantity'], 0, 0, 'C', true);
                
                $pdf->SetXY($tX + 90, $tY);
                $pdf->Cell(10, 3, $line['Bundles'], 0, 0, 'C', true);
                
                $pdf->SetXY($tX + 120, $tY);
                $pdf->Cell(10, 3, $line['Odds'], 0, 0, 'C', true);
                
                $pdf->SetXY($tX + 140, $tY);
                $pdf->Cell(25, 3, sprintf('%.2f', $line['Weight']), 0, 0, 'R', true);
                                
                $tY += $lineHeight;
            }
        }
        
        return $pdf;
        
    }
}

?>
