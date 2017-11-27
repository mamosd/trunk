<?php

$fpdfPath = Yii::getPathOfAlias('ext.fpdf');
require_once $fpdfPath.'/fpdf.php';

$fpdiPath = Yii::getPathOfAlias('ext.fpdi');
require_once $fpdiPath.'/fpdi.php';

$mailPath = Yii::getPathOfAlias('ext.yii-mail');
require_once $mailPath.'/YiiMailMessage.php';


class PolestarAdviceSheet {

    public $id;

    private function moveTop($pdf, $current, $delta, $newPageTemplate, $initial = 0) {
        $maxTop = 630;
        $newTop = $current + $delta;
        if ($newTop > $maxTop) {
            $newTop = $initial + 10;
            $pdf->AddPage();
            $pdf->useTemplate($newPageTemplate);
            $pdf->SetFont('Arial', 'B', 8);
        }
        //$pdf->setXY(2,$newTop); // debug
        //$pdf->Cell(0, 8, $newTop); // debug
        
        return $newTop;
    }
    
    public function getPdf() {
        $model = PolestarJob::model()->with('Loads', 'CollectionPoints')->findByPk($this->id);
        $dt = new DateTime($model->DeliveryDate);

        $marginLeft = 48;
        $marginTop = 54;

        $pdf = new FPDI('P','pt','A4');
        $pdf->SetMargins($marginLeft,$marginTop,28);
        $pdf->SetAutoPageBreak(false);
        $pdf->SetFillColor(255,255,255);

        $pagecount = $pdf->setSourceFile('_uploads/templates/AdviceSheet.pdf');
        $tplidx = $pdf->importPage(1);

        $pdf->AddPage();
        $pdf->useTemplate($tplidx);
        
        $fontName = 'Arial';
        $fontSize = 10;

        $pdf->SetFont($fontName, 'B', $fontSize);

        $initialTop = 0 + $marginTop;
        $maxLines = 70;

        $top = $initialTop;
        $currentLines = 1;

        $address = "Aktrion Job Ref: " . $model->Ref;
        $pdf->SetXY(100 + $marginLeft, $top);
        $pdf->Cell(290, 8, $address, 0, 0, 'C', true);
        
        $fontSize = 8;
        $pdf->SetFont($fontName, 'B', $fontSize);
        
        $top = $this->moveTop($pdf, $top, 10, $tplidx, $initialTop);
        $pdf->SetXY(0, $top);
        $pdf->Cell(0, 8, "Vehicle Size: ".$model->Vehicle->Name, 0, 0, 'C', true);
        
        $top = $this->moveTop($pdf, $top, 24, $tplidx, $initialTop);
        
        $fontSize = 10;
        $pdf->SetFont($fontName, 'B', $fontSize);
        $pdf->SetXY(20, $top);
        $pdf->Cell(0, 12, "Collection Information", 'TB', 0, 'C', true);

        $points = array_merge(array($model), $model->CollectionPoints);
        $noPoints = count($points);
        foreach ($points as $idx => $point) {
            $top = $this->moveTop($pdf, $top, 20, $tplidx, $initialTop);
            
            $idxPoint = ($noPoints > 1) ? ($idx+1) : '';
            $fontSize = 8;
            $pdf->SetFont($fontName, 'B', $fontSize);
            $pdf->SetXY(5 + $marginLeft, $top);
            $pdf->Cell(82, 8, "Collection Address $idxPoint :", 0, 0, 'R', true);
            $pdf->SetFont($fontName, '', $fontSize);
            $address = ((!empty($point->CollCompany)) ? $point->CollCompany.", " : "") . $point->CollAddress . ", " . $point->CollPostcode;
            $pdf->SetXY(100 + $marginLeft, $top);
            $pdf->Cell(290, 8, $address, 0, 0, 'C', true);

            $top = $this->moveTop($pdf, $top, 12, $tplidx, $initialTop);
            $pdf->SetFont($fontName, 'B', $fontSize);
            $pdf->SetXY(5 + $marginLeft, $top);
            $pdf->Cell(82, 8, "Collection Date $idxPoint :", 0, 0, 'R', true);
            $pdf->SetFont($fontName, '', $fontSize);
            $pdf->SetXY(100 + $marginLeft, $top);
            $dateToOutput = trim($dt->format('d/m/Y').' '.$point->formatTime('CollScheduledTime','H:i','TBC'));
            $pdf->Cell(290, 8, $dateToOutput, 0, 0, 'C', true);
            
            $top = $this->moveTop($pdf, $top, 24, $tplidx, $initialTop);
            $pdf->SetFont($fontName, 'B', $fontSize);
            $pdf->SetXY(5 + $marginLeft, $top);
            $pdf->Cell(82, 8, 'Reference Number', 0, 0, 'C', true);
            $pdf->SetXY(100 + $marginLeft, $top);
            $pdf->Cell(290, 8, 'Title', 0, 0, 'C', true);
            $pdf->SetXY(450 + $marginLeft, $top);
            $pdf->Cell(37, 8, 'Pallets', 0, 0, 'C', true);

            $totalPallets = 0;
            $totalLoads = 0;
            $top = $this->moveTop($pdf, $top, 16, $tplidx, $initialTop);
            foreach ($model->Loads as $load) {
                if ($load->StatusId == PolestarStatus::CANCELLED_ID) {
                    continue;
                }
                
                if (($noPoints > 1) && (stristr($load->CollectionSequence, $point->CollPostcode) === FALSE))
                    continue;

                $pdf->SetXY(5 + $marginLeft, $top);
                $pdf->Cell(82, 8, $load->Ref, 0, 0, 'C', true);
                $pdf->SetXY(100 + $marginLeft, $top);
                $pdf->Cell(290, 8, $load->Publication, 0, 0, 'C', true);
                $pdf->SetXY(450 + $marginLeft, $top);
                $pdf->Cell(37, 8, $load->PalletsTotal, 0, 0, 'R', true);

                $totalLoads++;
                $totalPallets += $load->PalletsTotal;

                $top = $this->moveTop($pdf, $top, 8, $tplidx, $initialTop);
            }
            if ($totalLoads == 0) {
                $pdf->SetXY(20, $top);
                $pdf->Cell(0, 12, "-- no loads to be collected from this location --", 0, 0, 'C', true);
            }

            $top = $this->moveTop($pdf, $top, 8, $tplidx, $initialTop);
            $pdf->SetXY(100 + $marginLeft, $top);
            $pdf->Cell(340, 8, 'Total:', 0, 0, 'R', false);
            $pdf->SetXY(450 + $marginLeft, $top);
            $pdf->Cell(37, 8, $totalPallets, 'T', 0, 'R', true);
            
            $top = $this->moveTop($pdf, $top, 12, $tplidx, $initialTop);
            $pdf->SetFont($fontName, 'B', $fontSize);
            $pdf->SetXY(5 + $marginLeft, $top);
            $pdf->Cell(82, 8, "Special Inst. $idxPoint :", 0, 0, 'R', true);
            $pdf->SetFont($fontName, '', $fontSize);
            $pdf->SetXY(100 + $marginLeft, $top);
            $pdf->Cell(290, 8, $point->SpecialInstructions, 0, 0, 'C', true);
            
            if ($noPoints > 1) {
                $top = $this->moveTop($pdf, $top, 8, $tplidx, $initialTop);
                $pdf->Line(5 + $marginLeft, $top+8, 497 + $marginLeft, $top+8);
            }
        }
        
        $top = $this->moveTop($pdf, $top, 16, $tplidx, $initialTop);
        $lastAddress = null;
        $loads = array();
        foreach ($model->Loads as $load) {
            if ($load->StatusId == PolestarStatus::CANCELLED_ID) {
                continue;
            }
            $address = $load->DelCompany . ', ' . $load->DelAddress . ", ".$load->DelPostcode;
            if ($lastAddress == $address) {
                continue;
            }
            $lastAddress = $address;
            $loads[] = $load;
        }

        if (count($loads) <= 1) {
        	$current = '';
        } else {
        	$current = 1;
        }

        $fontSize = 10;
        $pdf->SetFont($fontName, 'B', $fontSize);
        $pdf->SetXY(20, $top);
        $pdf->Cell(0, 12, "Delivery Information", 'TB', 0, 'C', true);
        
        $fontSize = 9;
        $pdf->SetFont($fontName, 'B', $fontSize);
        foreach ($loads as $load) {
            $top = $this->moveTop($pdf, $top, 30, $tplidx, $initialTop);
            //$pdf->Line(5 + $marginLeft, $top - 8, 497 + $marginLeft, $top - 8);

            $pdf->SetXY(5 + $marginLeft, $top);
            $pdf->SetFont($fontName, 'B', $fontSize);
            $pdf->Cell(82, 8, "{$current} Delivery Time:", 0, 0, 'R', true);
            $pdf->SetXY(100 + $marginLeft, $top);
            $pdf->SetFont($fontName, '', $fontSize);
            $pdf->Cell(400, 8, trim($load->formatDate('DeliveryDate', 'd/m/Y').' '.$load->formatTime('DelScheduledTime','H:i','TBC')), 0, 0, 'C', true);

            $top = $this->moveTop($pdf, $top, 16, $tplidx, $initialTop);
            
            $pdf->SetXY(5 + $marginLeft, $top);
            $pdf->SetFont($fontName, 'B', $fontSize);
            $pdf->Cell(82, 8, "{$current} Delivery Address:", 0, 0, 'R', true);
            $pdf->SetXY(100 + $marginLeft, $top);
            $address = $load->DelCompany . ', ' . $load->DelAddress . ", ".$load->DelPostcode;
            $pdf->SetFont($fontName, '', $fontSize);
            $pdf->MultiCell(400, 8, $address, 0, 'C');

            $top = $this->moveTop($pdf, $top, 16, $tplidx, $initialTop);

            $pdf->SetXY(5 + $marginLeft, $top);
            $pdf->SetFont($fontName, 'B', $fontSize);
            $pdf->Cell(82, 8, "Booking Ref:", 0, 0, 'R', true);
            $pdf->SetXY(100 + $marginLeft, $top);
            $pdf->SetFont($fontName, '', $fontSize);
            $pdf->Cell(400, 8, $load->BookingRef, 0, 0, 'C', true);
            
            $top = $this->moveTop($pdf, $top, 16, $tplidx, $initialTop);
            
            $pdf->SetXY(5 + $marginLeft, $top);
            $pdf->SetFont($fontName, 'B', $fontSize);
            $pdf->Cell(82, 8, "Special Inst.:", 0, 0, 'R', true);
            $pdf->SetXY(100 + $marginLeft, $top);
            $pdf->SetFont($fontName, '', $fontSize);
            $pdf->Cell(400, 8, $load->LoadSpecialInstructions, 0, 0, 'C', true);

            $pdf->Line(5 + $marginLeft, $top + 20, 497 + $marginLeft, $top + 20);
            
            $current++;
        }

        $top = $this->moveTop($pdf, $top, 25, $tplidx, $initialTop);
        $pdf->SetXY(20, $top);
        $pdf->SetFont($fontName, 'B', 8);
        $pdf->Cell(0, 10, "### end of delivery points ###", "TB", 0, 'C', true);

        return $pdf;
    }

    public function generatePdf() {
        $pdf = $this->getPdf();
        $fileName = "advice-sheet-".date("Ymd-His").".pdf";
        $pdf->Output($fileName, 'D');
    }

    public function sendPdf($contacts) {
        $model = PolestarJob::model()->findByPk($this->id);

        $destinations = array();

        $sent = FALSE;
        if (!empty($contacts)) {
            foreach($contacts as $contactId) {
                $contact = PolestarSupplierContact::model()->findByPk($contactId);
                $dbSent = PolestarSupplierNotification::model()->find(array(
                    'condition' => "ContactId = :cid AND JobId = :jid AND Type = 'advice'",
                    'params' => array(':cid' => $contactId, ':jid' => $this->id)
                ));
                if (isset($dbSent))
                    $sent = TRUE;
                    
                $destinations[$contactId] = trim($contact->Email);
            }
        }

        if (empty($destinations)) {
            return; // nothing to do
        }

        $dt = new DateTime($model->DeliveryDate);
        $dateStr = $dt->format('d/m/y');

        $mail = new YiiMailMessage();
        
        $subject = "{$dateStr} JobRef {$model->Ref} - Load Advice sheet";
        if ($sent === TRUE)
                $subject .= " (Revised)";
        $mail->setSubject($subject);
        $mail->view = "polestar-advice-sheet"; // allows a template for DTR and another one for DTC
        $mail->setBody(array("model"=>$model, 'dt' => $dt), 'text/html');

        $pdf = $this->getPdf();
        $fileName = "advice-sheet-".date("Ymd-His").".pdf";
        $attachment = Swift_Attachment::newInstance($pdf->Output('','S'), $fileName, 'application/pdf');
        $mail->attach($attachment);

        foreach ($destinations as $cid => $dst) {
            $mail->addTo($dst);
        }

        $override = trim(Setting::get('polestar', 'email-override'));
        if (!empty($override)) {
            $emails = explode(';', $override);
            $to = array();
            foreach ($emails as $email)
                $to[trim($email)] = trim($email);
            $mail->setTo($to);
        }

        $mail->setFrom(array(Yii::app()->params['notificationsEmail'] => Yii::app()->params['notificationsEmailName']));

        $sent = Yii::app()->mail->send($mail);
        
        if ($sent > 0) {
            foreach ($destinations as $cid => $email) {
                $n = new PolestarSupplierNotification();
                $n->ContactId = $cid;
                $n->JobId = $this->id;
                $n->SentBy = Yii::app()->user->loginId;
                $n->SentDate = new CDbExpression('now()');
                $n->save();
            }
        }

        return $sent;
    }
}

?>