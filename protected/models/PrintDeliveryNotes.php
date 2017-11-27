<?php
/**
 * Description of PrintDeliveryNotes
 *
 * @author ramon
 */

$fpdfPath = Yii::getPathOfAlias('ext.fpdf');
require_once $fpdfPath.'/fpdf.php';

$fpdiPath = Yii::getPathOfAlias('ext.fpdi');
require_once $fpdiPath.'/fpdi.php';

class PrintDeliveryNotes {
    
    private function getData($criteria)
    {
        // remove blank filters
        foreach($criteria as $col => $val)
            if (empty($val))
                unset($criteria[$col]);
            
        $crit = new CDbCriteria();
        $crit->addColumnCondition($criteria);
        $crit->order = "DepartureTimeSort ASC, RouteId ASC, ArrivalTimeSort ASC, TitleType DESC, TitleName ASC";
        $lines = ClientRouteInstanceInfo::model()->findAll($crit);
        
        // group results by ClientRouteInstanceDetailsId (wholesaler)
        $result = array();
        foreach($lines as $line)
        {
            if (!empty($line->ClientWholesalerId)) // avoid empty routes
            {
                if (!isset($result[$line->ClientWholesalerId]))
                        $result[$line->ClientWholesalerId] = array();
                $result[$line->ClientWholesalerId][] = $line;
            }
        }
        /*foreach($lines as $line)
        {
            if (!isset($result[$line->ClientRouteInstanceDetailsId]))
                    $result[$line->ClientRouteInstanceDetailsId] = array();
            $result[$line->ClientRouteInstanceDetailsId][] = $line;
        }*/
        
        return $result;
    }
    
    public function outputNotes($criteria)
    {
        $pdf = $this->getPDF($criteria);
        $fileName = "notes-".date("Ymd-His").".pdf";
        $pdf->Output($fileName, 'D');
//        Yii::app()->end();
    }
    
    public function getPDF($criteria)
    {
        $data = $this->getData($criteria);
        
        // add one page per ClientRouteInstanceDetailsId (wholesaler)
        $pdf = new FPDI('P','mm','A4');
        $pdf->SetMargins(0,0,0);
        $pdf->SetAutoPageBreak(false);
        $pdf->SetFillColor(255,255,255); // fill background to avoid overlapping
        
        $pagecount = $pdf->setSourceFile('_uploads/templates/express-delivery-note.pdf');
        $tplidx = $pdf->importPage(1);
        
        foreach($data as $id => $lines)
        {
            $pdf->AddPage();
            $pdf->useTemplate($tplidx);
            
            $info = $lines[0];
            $pdf->SetFont('Arial', 'B', 12);
            // Delivery Date
            $pdf->SetXY(140, 40);
            $dateTS = CDateTimeParser::parse($info->DeliveryDate, 'yyyy-MM-dd');
            $pdf->Cell(50, 3, Yii::app()->dateFormatter->format("dd/MM/yyyy", $dateTS), 0, 0, 'L', true);
            
            // Wholesaler Info
            $wsX = 25;
            $pdf->SetXY($wsX, 45);
            $pdf->Cell(100, 3, $info->WholesalerName, 0, 0, 'L', true);
            $pdf->SetXY($wsX, 50);
            $pdf->Cell(100, 3, $info->WSAddress1, 0, 0, 'L', true);
            $pdf->SetXY($wsX, 55);
            $pdf->Cell(100, 3, $info->WSAddress2, 0, 0, 'L', true);
            $pdf->SetXY($wsX, 60);
            $pdf->Cell(100, 3, $info->WSAddress3, 0, 0, 'L', true);
            $pdf->SetXY($wsX, 65);
            $pdf->Cell(100, 3, $info->WSAddress4, 0, 0, 'L', true);
            $pdf->SetXY($wsX, 70);
            $pdf->Cell(100, 3, $info->WSAddress5, 0, 0, 'L', true);        
                    
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
                if (isset($headers[$line->TitleType]) && ($headers[$line->TitleType]['printed'] == FALSE))
                {
                    $tY += $lineHeight * 2; // blank line left intentionally
                    $pdf->SetFont('Arial', 'B', 12);
                    
                    $pdf->SetXY($tX, $tY-2);
                    $pdf->Cell(165, 3, '', 'T', 0, 'L', true);
                    
                    $pdf->SetXY($tX, $tY);
                    $pdf->Cell(75, 3, $headers[$line->TitleType]['text'], 0, 0, 'L', true);
                    $headers[$line->TitleType]['printed'] = TRUE;
                    $tY += $lineHeight * 1.5;
                }
                
                $pdf->SetFont('Arial', '', 11);
                // print title information                
                $pdf->SetXY($tX, $tY);
                $pdf->Cell(75, 3, $line->TitleName, 0, 0, 'L', true);
                
                $copies = intval($line->Quantity);
                $pdf->SetXY($tX + 70, $tY);
                $pdf->Cell(10, 3, $copies, 0, 0, 'C', true);
                
                $bundleSize = intval($line->BundleSize);
                $bundles = floor($copies / $bundleSize);
                $pdf->SetXY($tX + 90, $tY);
                $pdf->Cell(10, 3, $bundles, 0, 0, 'C', true);
                
                $odds = $copies % $bundleSize;
                $pdf->SetXY($tX + 120, $tY);
                $pdf->Cell(10, 3, $odds, 0, 0, 'C', true);
                
                $weight = intval($line->PubWeight);
                $totalWeight = $copies * $weight;
                // TODO: cater for weight being expressed in something other than milligrams
                $totalWeight = round($totalWeight / 10000, 2);
                $pdf->SetXY($tX + 140, $tY);
                $pdf->Cell(25, 3, sprintf('%.2f', $totalWeight), 0, 0, 'R', true);
                                
                $tY += $lineHeight;
            }
        }
        
        return $pdf;
        
    }
}

?>
