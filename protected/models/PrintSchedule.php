<?php
/**
 * Description of PrintSchedule
 *
 * @author ramon
 */

$fpdfPath = Yii::getPathOfAlias('ext.fpdf');
require_once $fpdfPath.'/fpdf.php';

$fpdiPath = Yii::getPathOfAlias('ext.fpdi');
require_once $fpdiPath.'/fpdi.php';

class PrintSchedule {
    
    public function outputSchedule($criteria)
    {
        $pdf = $this->getPDF($criteria);
        $fileName = "schedule-".date("Ymd-His").".pdf";
        $pdf->Output($fileName, 'D');
    }
    
    private function getData($criteria)
    {
        $crit = new CDbCriteria();
        $crit->addColumnCondition($criteria);
        $crit->order = "DepartureTimeSort ASC, RouteId ASC, ArrivalTimeSort ASC, WholesalerAlias ASC, TitleType DESC, TitleName ASC";
        $lines = ClientRouteInstanceInfo::model()->findAll($crit);
        
        // group results by ClientRouteInstanceDetailsId (wholesaler)
        $result = array();
        foreach($lines as $line)
        {
            if (!isset($result[$line->ClientWholesalerId]))
                    $result[$line->ClientWholesalerId] = array();
            $result[$line->ClientWholesalerId][] = $line;
            /*if (!isset($result[$line->ClientRouteInstanceDetailsId]))
                    $result[$line->ClientRouteInstanceDetailsId] = array();
            $result[$line->ClientRouteInstanceDetailsId][] = $line;*/
        }
        
        return $result;
    }
    
    public function getPDF($criteria)
    {
        $data = $this->getData($criteria);
        
        $pdf = new FPDI('L','mm','A4');
        $pdf->SetMargins(0,0,0);
        $pdf->SetAutoPageBreak(false);
        $pdf->SetFillColor(255,255,255); // fill background to avoid overlapping
        
        $pagecount = $pdf->setSourceFile('_uploads/templates/express-schedule.pdf');
        $tplidx = $pdf->importPage(1);
        
        $newPage = TRUE;
        $route = NULL;
        
        $topX = 20;
        $topY = 50;
        $lineIdx = 0;
        $lineHeight = 7;
        $linesPerPage = 20;
        $totalPages = ceil( count(array_keys($data)) / $linesPerPage );
        $currentPage = 0;
                
        foreach($data as $id => $lines)
        {
            $info = $lines[0];
            $pdf->SetFillColor(255,255,255);
            
            if ($newPage === TRUE)
            {
                $pdf->AddPage();
                $pdf->useTemplate($tplidx);
                $currentPage++;
                
                // display header information
                $pdf->SetFont('Arial', 'B', 14);
                // Print Centre
                $pdf->SetXY(80, 25);
                $pdf->Cell(50, 3, $info->PrintCentreName, 0, 0, 'L', true);
                
                // Delivery Date
                $pdf->SetXY(130, 25);
                $dateTS = CDateTimeParser::parse($info->DeliveryDate, 'yyyy-MM-dd');
                $pdf->Cell(50, 3, Yii::app()->dateFormatter->format("dd/MM/yyyy", $dateTS)." - $currentPage/$totalPages", 0, 0, 'L', false);
                                
                $newPage = FALSE;
            }
            
            $x = $topX;
            $y = $topY + ($lineIdx * $lineHeight);
            
            $pdf->SetFont('Arial', '', 10);
            $pdf->SetXY($x, $y);
            //$pdf->Cell(50, 3, $info->WholesalerName, 0, 0, 'L', true);
            $pdf->Cell(50, 3, 
                    ((!empty($info->WholesalerAlias)) ? $info->WholesalerAlias : '(empty)')
                    , 0, 0, 'L', true);
            
            if ($route != $info->RouteId)
            {
                // route separator
                $pdf->SetXY($x, $y-2);
                $pdf->Cell(245, 3, '', 'T', 0, 'L', false);
                
                $pdf->SetXY($x + 53, $y);
                $pdf->Cell(15, 3, $info->RouteId, 0, 0, 'L', true);

                $pdf->SetXY($x + 75, $y);
                $pdf->Cell(25, 3, $info->VehicleDescription, 0, 0, 'L', true);

                $pdf->SetXY($x + 105, $y);
                $pdf->Cell(25, 3, $info->DepartureTime, 0, 0, 'L', true);
            }
            
            $pdf->SetXY($x + 160, $y);
            $pdf->Cell(25, 3, $info->ArrivalTime, 0, 0, 'L', true);
            
            $pdf->SetXY($x + 215, $y);
            $pdf->Cell(25, 3, $info->NPATime, 0, 0, 'L', true);
            
            
            $pdf->SetFillColor(255,255,130);
            // highlighted boxes
            if ($route != $info->RouteId)
            {
                $pdf->SetXY($x + 120, $y);
                $pdf->Cell(20, 3, $info->DepartureTimeActual, 0, 0, 'C', true); 
                
                $depVar = RoutingScheduleForm::getDepartureVariance($info);
                if ($depVar !== FALSE)
                {
                    $fDepVar = RoutingScheduleForm::formatHHMM($depVar);
                    $txt = "-";
                    if ($depVar < 0)
                        $txt = "-{$fDepVar}m";
                    else if ($depVar > 0)
                        $txt = "+{$fDepVar}m";
                        
                    $pdf->SetXY($x + 140, $y);
                    $pdf->Cell(15, 3, $txt, 0, 0, 'C', true); 
                }
               
                $route = $info->RouteId;
            }
            
            $pdf->SetXY($x + 175, $y);
            $pdf->Cell(20, 3, $info->ArrivalTimeActual, 0, 0, 'L', true);
            $arrVar = RoutingScheduleForm::getArrivalVariance($info);
            if ($arrVar !== FALSE)
            {
                $fArrVar = RoutingScheduleForm::formatHHMM($arrVar);
                $txt = "-";
                if ($arrVar < 0)
                    $txt = "-{$fArrVar}m";
                else if ($arrVar > 0)
                    $txt = "+{$fArrVar}m";
                
                $pdf->SetXY($x + 195, $y);
                $pdf->Cell(15, 3, $txt, 0, 0, 'C', true); 
            }
            
            $npaVar = RoutingScheduleForm::getNPAVariance($info);
            if ($npaVar !== FALSE)
            {
                $fNpaVar = RoutingScheduleForm::formatHHMM($npaVar);
                $txt = "-";
                if ($npaVar > 0)
                    $txt = "+{$fNpaVar}m";
                    
                $pdf->SetXY($x + 230, $y);
                $pdf->Cell(15, 3, $txt, 0, 0, 'C', true);     
            }
            

            $lineIdx++;
            if ($lineIdx == $linesPerPage)
            {
                $lineIdx = 0;
                $newPage = TRUE;
            }
        }
            
        return $pdf;
    }
}

?>
