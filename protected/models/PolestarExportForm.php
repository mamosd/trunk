<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of PolestarExportForm
 *
 * @author ramon
 */
class PolestarExportForm extends PolestarRouteViewForm {
    
    public $statusToList;
    
    public function getContents() {
        $this->populateJobs();
        $jobList = $this->jobsData;
        
        $format = array(
            'jobLoadRef'    => array('column' => 'A', 'value' => '', 'header' => 'Job/Load Ref. No.'),
            'jobRef'        => array('column' => 'B', 'value' => '', 'header' => 'JobRef'),
            'jobLoad'       => array('column' => 'C', 'value' => '', 'header' => 'Job/Load'),
            'publication'   => array('column' => 'D', 'value' => '', 'header' => 'Publication'),
            'quantity'      => array('column' => 'E', 'value' => '', 'header' => 'Quantity'),
            'pallets'       => array('column' => 'F', 'value' => '', 'header' => 'Pallets'),
            'kg'            => array('column' => 'G', 'value' => '', 'header' => 'Kg'),
            'vehicle'       => array('column' => 'H', 'value' => '', 'header' => 'Vehicle'),
            'mileage'       => array('column' => 'I', 'value' => '', 'header' => 'Mileage'),
            'colPostcode'   => array('column' => 'J', 'value' => '', 'header' => 'Load'),
            'delPostcode'   => array('column' => 'K', 'value' => '', 'header' => 'Deliver'),
            'delArea'       => array('column' => 'L', 'value' => '', 'header' => 'Del Area'),
            'delCompany'    => array('column' => 'M', 'value' => '', 'header' => 'Company'),
            'delDate'       => array('column' => 'N', 'value' => '', 'header' => 'Del Date'),
            'bookingRef'    => array('column' => 'O', 'value' => '', 'header' => 'Booking Ref'),
            'supplier'      => array('column' => 'P', 'value' => '', 'header' => 'Supplier'),
            'tmwNo'         => array('column' => 'Q', 'value' => '', 'header' => 'TMW No'),
            'driver'        => array('column' => 'R', 'value' => '', 'header' => 'Driver Name'),
            'vehicleReg'    => array('column' => 'S', 'value' => '', 'header' => 'Veh Reg'),
            'driverPhone'   => array('column' => 'T', 'value' => '', 'header' => 'Driver Phone Number'),
            'colTime'       => array('column' => 'U', 'value' => '', 'header' => 'Coll Time'),
            'colArrival'    => array('column' => 'V', 'value' => '', 'header' => 'Arr Time at PC'),
            'colDeparture'  => array('column' => 'W', 'value' => '', 'header' => 'Dep Time ex PC'),
            'delTime'       => array('column' => 'X', 'value' => '', 'header' => 'Del Time'),
            'delArrival'    => array('column' => 'Y', 'value' => '', 'header' => 'Act Del Time'),
            'delDepart'     => array('column' => 'Z', 'value' => '', 'header' => 'Act Dep Time'),
            'comments'      => array('column' => 'AA', 'value' => '', 'header' => 'Comments'),
            'status'        => array('column' => 'AB', 'value' => '', 'header' => 'Job Status'),
            
        );
        
        $contents = array();
        
        $allStatus = PolestarStatus::getAllAsOptions();
        
        $statusToList = array_keys($allStatus);
        if (isset($this->statusToList) && ($this->statusToList != '*'))
            $statusToList = explode(',', $this->statusToList);
        
        foreach($jobList as $job) {
            
            if (!in_array($job->StatusId, $statusToList))
                    continue;
            
            $collTimes = array(
                            $job->CollPostcode => $job->formatTime('CollScheduledTime', 'H:i', 'TBC')
                );
            
            foreach ($job->CollectionPoints as $point)
                $collTimes[$point->CollPostcode] = $point->formatTime('CollScheduledTime', 'H:i', 'TBC');
            
            $history = PolestarJobHistory::model()->findAll(array(
                    'condition' => 'Id = :jid',
                    'params' => array(':jid' => $job->Id),
                    'order' => 'RevisionNo asc'
                ));
            $statusHistory = array();
            $curStatusId = '';
            foreach ($history as $rev) {
                if($rev->StatusId != $curStatusId) { // avoid duplicates
                    $curStatusId = $rev->StatusId;
                    $dt = new DateTime(empty($rev->EditedDate) ? $rev->CreatedDate : $rev->EditedDate);
                    $statusHistory[] = $allStatus[$curStatusId].' '.$dt->format('(d/m G:i)');
                }
            }
            if($job->StatusId != $curStatusId) {
                $dt = new DateTime(empty($job->EditedDate) ? $job->CreatedDate : $job->EditedDate);
                $statusHistory[] = $allStatus[$job->StatusId].' '.$dt->format('(d/m G:i)');
            }
                
            $statusThread = implode("\n", $statusHistory);
            
            $totalPalletsTotal = 0;
            $totalWeight = 0;
            $totalMileage = 0;
            
            foreach ($job->Loads as $load) {
                
                //for total row
                $row1 = (object)$format;                
                foreach ($row1 as $field => $val)
                    $row1->$field = (object)$val;
                
                $row = (object)$format;                   
                foreach ($row as $field => $val)
                    $row->$field = (object)$val;
                    $totalPalletsTotal += $load->PalletsTotal;
                    $totalWeight += $load->Kg;
                    //$totalMileage += $load->Mileage;
                // job level info
                $row->jobLoadRef->value = $load->SpecialInstructions;
                $row->jobRef->value = $job->Ref;
                $row->jobLoad->value = $load->Ref;
                $row->publication->value = $load->Publication;
                $row->quantity->value = $load->Quantity;
                $row->pallets->value = $load->PalletsTotal;
                $row->kg->value = $load->Kg;
                $row->vehicle->value = @$job->Vehicle->Name;
                $row->mileage->value = $load->Mileage;
                $row->colPostcode->value = (empty($load->CollectionSequence)) ? $job->CollPostcode : $load->CollectionSequence; //$job->CollPostcode;
                $row->delPostcode->value = $load->DelPostcode;
                $row->delArea->value = $load->DelAddress;
                $row->delCompany->value = $load->DelCompany;
                $row->delDate->value = $load->formatDate('DeliveryDate', 'd/m/Y');
                $row->bookingRef->value = $load->BookingRef;
                $row->supplier->value = @$job->Supplier->Name;
                //$row->tmwNo->value = '';
                $row->driver->value = $job->DriveName;
                $row->vehicleReg->value = $job->VehicleRegNo;
                $row->driverPhone->value = $job->ContactNo;
                $row->colTime->value = (isset($collTimes[$load->CollectionSequence])) ? $collTimes[$load->CollectionSequence] : $job->formatTime('CollScheduledTime', 'H:i', 'TBC');
                $row->colArrival->value = $job->formatTime('CollArrivalTime', 'H:i');
                $row->colDeparture->value = $job->formatTime('CollDepartureTime', 'H:i');
                $row->delTime->value = $load->formatTime('DelScheduledTime', 'H:i', 'TBC');
                $row->delArrival->value = $load->formatTime('DelArrivalTime', 'H:i');
                $row->delDepart->value = $load->formatTime('DelDepartureTime', 'H:i');                
                
                $comments = $load->Comments;
                $thread = '';
                if (!empty($comments)) {
                    foreach ($comments as $comment)
                        $thread .= "{$comment->Comment}\n";                        
                        //$thread .= "{$comment->CreatedDate}\n{$comment->Comment}\n";
                }
                
                $row->comments->value = $thread;
                
                $loadStatusThread = $statusThread;
                if ($load->StatusId == PolestarStatus::CANCELLED_ID)
                    $loadStatusThread .= "\n-- Load Cancelled --";
                
            $row->status->value = $loadStatusThread;//."(".date('G:i').")";

                $contents[] = $row;            
            }
            
            //value for total row
            if ( count($job->Loads) != 0 )
            {
                $row1->jobLoadRef->value = "Total";
                $row1->pallets->value = $totalPalletsTotal;
                $row1->kg->value = $totalWeight;
                $row1->mileage->value = $job->TotalMileage; //$totalMileage;
                $contents[] = $row1;
            }
            
            $contents[] = NULL; // append blank row                   
        }
        
        return $contents;
    }
    
    public function outputXls() {
        $contents = $this->getContents();
        
        // get a reference to the path of PHPExcel classes
        $phpExcelPath = Yii::getPathOfAlias('ext.phpexcel.Classes');
        // Turn off YII library autoload
        spl_autoload_unregister(array('YiiBase','autoload'));

        // PHPExcel_IOFactory
        require_once $phpExcelPath.'/PHPExcel.php';

        $objPHPExcel = new PHPExcel();

        //$objPHPExcel->getDefaultStyle()->getFont()->setSize(13);
        PHPExcel_Shared_Font::setAutoSizeMethod(PHPExcel_Shared_Font::AUTOSIZE_METHOD_EXACT);
        $objPHPExcel->setActiveSheetIndex(0);
        
        $idx = 1;
        $headerDone = FALSE;
        
        foreach ($contents as $row) {
            if (!empty ($row)) {
                if (!$headerDone) {
                    foreach ($row as $key => $field) {
                        $objPHPExcel->getActiveSheet()->setCellValue($field->column.$idx, $field->header);
                        $objPHPExcel->getActiveSheet()->getColumnDimension($field->column)->setAutoSize(true);                        
                    }
                    $headerDone = TRUE;
                    $idx++;
                }
                foreach ($row as $key => $field) {
                    $objPHPExcel->getActiveSheet()->setCellValue($field->column.$idx, $field->value);
                    //bold total row
                    if ( $field->column == "A" && $field->value == "Total")
                    {
                        $objPHPExcel->getActiveSheet()->getStyle("A$idx:AZ$idx")->getFont()->setBold(true);
                    }                    
                }

                    
            }

            $idx += ($idx > 1) ? 1 : 0; // do not increment if first line is blank
            
        }
                        
//        if ($totalRow['totalPalletsTotal']!=0 && $totalRow['totalWeight']!=0)
//        {
//            $num_rows = $objPHPExcel->getActiveSheet()->getHighestRow();
//            $cellForTotal = "A".($num_rows+1);
//            $cellForTotalPallets = "E".($num_rows+1);
//            $cellForTotalWeight = "F".($num_rows+1);
//            $cellForTotalMileage = "H".($num_rows+1);
//            $objPHPExcel->getActiveSheet()->setCellValue($cellForTotal, "Total");
//            $objPHPExcel->getActiveSheet()->getStyle($cellForTotal)->getFont()->setBold(true);
//            $objPHPExcel->getActiveSheet()->setCellValue($cellForTotalPallets, $totalRow['totalPalletsTotal']);
//            $objPHPExcel->getActiveSheet()->getStyle($cellForTotalPallets)->getFont()->setBold(true);
//            $objPHPExcel->getActiveSheet()->setCellValue($cellForTotalWeight, $totalRow['totalWeight']);
//            $objPHPExcel->getActiveSheet()->getStyle($cellForTotalWeight)->getFont()->setBold(true);
//            $objPHPExcel->getActiveSheet()->setCellValue($cellForTotalMileage, $totalRow['totalMileage']);
//            $objPHPExcel->getActiveSheet()->getStyle($cellForTotalMileage)->getFont()->setBold(true);
//        }
        // Redirect output to a client's web browser (Excel2003)
        $name = str_replace('/', '', $this->planningDate);
        $name .= '-'.preg_replace("/[^a-z0-9]/", '', strtolower($this->getPrintCentre()->Name));
        header('Content-Type: application/excel');
        header('Content-Disposition: attachment;filename="'.date('Ymd-Hi').'-'.$name.'.xlsx"'); // #43
        header('Cache-Control: max-age=0');

        //$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        $objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel); // #43
        $objWriter->save('php://output');
        
        // Once we have finished using the library, give back the
        // power to Yii...
        spl_autoload_register(array('YiiBase','autoload'));
    }
}
