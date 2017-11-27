<?php
/**
 * Description of FinanceInvoicing
 *
 * @author ramon
 */
$fpdfPath = Yii::getPathOfAlias('ext.fpdf');
require_once $fpdfPath.'/fpdf.php';

$fpdiPath = Yii::getPathOfAlias('ext.fpdi');
require_once $fpdiPath.'/fpdi.php';

class FinanceInvoicing extends CFormModel {
    public $weekStarting;
    public $contractor;
    public $sendOverEmail;
    public $category;
    
    public function rules()
    {
        return array(
            array('weekStarting', 'required'),
        );
    }
    
    public function attributeLabels()
    {
        return array(
            'sendOverEmail' => 'Send over email',
        );
    }
    
    // #58 - added forGeneration to sort by route when selected
    public function getContractorOptions($ui = TRUE, $filter = NULL, $forGeneration = FALSE)
    {
        // list contractors and adjusted contractors participating in the current week
        $weekStart = $this->weekStarting;
        $crit = new CDbCriteria();
        $crit->addCondition("RouteDate >= str_to_date(:ws, '%d/%m/%Y')");
        $crit->addCondition("RouteDate < date_add(str_to_date(:ws, '%d/%m/%Y'), interval 7 day)");
        
        if ($filter !== NULL)
            $crit->addCondition("CategoryType = '$filter'");
        
        $crit->params = array(
                            ':ws' => $weekStart,
            );
        //$crit->order = "Category, RouteDate, Route";
        $crit->order = "Category, Route, RouteDate"; // #58
        $crit->group = "RouteCategoryId, RouteId, ContractorId, AdjContractorId";
        
        $data = FinanceRouteInstanceDetails::model()->findAll($crit);
        
        $result = array();
        foreach ($data as $row)
        {
            $allowed = TRUE;
            if (!Login::checkPermission(Permission::PERM__FUN__LSC__DTC) &&
                    ($row->CategoryType == 'DTC'))
                $allowed = FALSE;
            if (!Login::checkPermission(Permission::PERM__FUN__LSC__DTR) &&
                    ($row->CategoryType == 'DTR'))
                $allowed = FALSE;
            
            if ($allowed) {
                $key = $row->ContractorId;
                $parentId = $row->ParentContractorId;
                $name = trim($row->ContractorFirstName.' '.$row->ContractorLastName);

                if (!empty($row->AdjContractorId) && !isset($result[$row->AdjContractorId]))
                {
                    $key = $row->AdjContractorId;
                    $parentId = $row->AdjParentContractorId;
                    $name = trim($row->AdjContractorFirstName.' '.$row->AdjContractorLastName);
                }

                // check for parent contractor
                //$contractor = FinanceContractor::model()->with('parent')->findByPk($key);
                //if (isset($contractor->parent))
                if (!empty($parentId))
                {
                    //$key = $contractor->parent->ContractorId;
                    $key = $parentId;
                    $parent = FinanceContractor::model()->findByPk($key);
                    $name = trim($parent->FirstName.' '.$parent->LastName);
                }

                if (!isset($result[$key]))
                    $result[$key] = $name;
            }
        }
        if (!$forGeneration) // #58
            asort($result);
        
        $options = $result;
        if ($ui)
        {
            // #84 - no longer required as it'd be always DTR/DTC generation 
            /*$options = array('DTC' => '(all DTC)', 'DTR' => '(all DTR)');
            if (!Login::checkPermission(Permission::PERM__FUN__LSC__DTC))
                unset($options['DTC']);
            if (!Login::checkPermission(Permission::PERM__FUN__LSC__DTR))
                unset($options['DTR']);
            foreach ($result as $key => $value)
                $options[$key] = $value;
             * 
             */
        }
        
        return $options;
    }
    
    public function generateInvoices($promptDownload = TRUE) {
        
        $contractorIds = array();
        //if (!empty($this->contractor) && is_numeric($this->contractor))
        //        $contractorIds[] = $this->contractor;
        if (is_array($this->contractor)) {
            $contractorIds = $this->contractor;
            $idx = array_search('', $contractorIds); // if ALL is selected, it overrides selection
            if ($idx !== FALSE)
                $contractorIds = array();
        }

        if (empty($contractorIds))
        {
            //$filter = (!empty($this->contractor) && !is_numeric($this->contractor)) ? $this->contractor : NULL;
            //if ($filter === NULL)
            if (isset($this->category))
                $filter = $this->category;
            $contractors = $this->getContractorOptions(FALSE, $filter, TRUE); // #58
            
            $contractorIds = array_keys($contractors);
        }
                
        $files = array();
        $contractorEmails = array();
        foreach ($contractorIds as $cid)
        {
            $pdfName = ($promptDownload === TRUE) ? tempnam('/tmp','inv-').'.pdf' : NULL;
            $result = $this->generateSingleInvoice($this->weekStarting, $cid, $pdfName);
            //if ($result === TRUE) {
            if ($result !== FALSE) {
                $sent = $this->sendInvoiceOverEmail($this->weekStarting, $cid, $pdfName, $result);
                //if (!$sent)
                if ($sent !== TRUE)
                    $files[$pdfName] = 'P';
                else
                    unlink($pdfName);
                $contractorEmails[$cid] = $sent;
            }
            else if ($pdfName !== NULL)
                unlink(str_replace ('.pdf', '', $pdfName));
        }
        
        if ($promptDownload === TRUE) {
            
            // append summary of notified contractors (if selected to send over email)
            // TODO - loop through $contractorEmails
            if ($this->sendOverEmail == '1') {
                $summaryPdfName = tempnam('/tmp','sum-');
                $this->generateSummaryEmailPage($summaryPdfName, $contractorEmails);
                $files[$summaryPdfName] = 'P';
            }
            
            $pdf = new PDFConcat(); 
            $pdf->setFiles($files);
            $pdf->concat();

            $fnames = array_keys($files);
            foreach ($fnames as $fname)
                unlink($fname);
                //@chmod( $fname, 0777 );
                //@unlink( $fname );

            //$suffix = empty($this->contractor) ? $this->category : implode('_', $this->contractor);
            $suffix = empty($this->contractor) || (array_search('', $this->contractor) !== FALSE) ? $this->category : implode('_', $contractorIds);
            $fileName = str_replace('/', '',$this->weekStarting)."-invoices-$suffix.pdf";
            $pdf->Output($fileName, 'D');
        }
    }
    
    private function generateSingleInvoice($weekStarting, $contractorId, $pdfName)
    {
        $invoiceId = NULL;
        $pdf = new InvoicePDF('P','mm','A4');
        $pdf->SetMargins(0,0,0);
        $pdf->SetAutoPageBreak(false);
        
        $pagecount = $pdf->setSourceFile('_uploads/templates/lsc-invoice.pdf');
        $tplidx = $pdf->importPage(1);
        
        /*** SIZING/PRINTING PARAMETERS ***/
        $linesPerPage = 12; // lines per page on listing
        $topY = 85;
        $commentTopY = 167;
        $gbp = utf8_decode("£");
        /*** END ***/
        
        $contractorInfo = FinanceContractor::model()->with('tax')->findByPk($contractorId);
        $allInstances = $this->getInstancesForContractor($weekStarting, $contractorId);
        
        // group instances by CategoryType
        $groupedInstances = array();
        foreach ($allInstances as $i)
        {
            $type = empty($i->CategoryType) ? '*' : $i->CategoryType;
            if (!isset($groupedInstances[$type]))
                $groupedInstances[$type] = array();
            
            $groupedInstances[$type][] = $i;
        }
        
        foreach ($groupedInstances as $contractType => $instances)
        {
            $allowed = TRUE;
            if (!Login::checkPermission(Permission::PERM__FUN__LSC__DTC) &&
                    ($contractType == 'DTC'))
                $allowed = FALSE;
            if (!Login::checkPermission(Permission::PERM__FUN__LSC__DTR) &&
                    ($contractType == 'DTR'))
                $allowed = FALSE;
            
            if (!$allowed)
                continue; // skip invoice generation for non allowed categories (DTR/DTC)
            
            // parse lines 
            $lines = array();
            foreach ($instances as $instance) {
                // verify whether instance applies for billing (eg contractor not adjusted)
                // check whether contractor is the parent for the adjusted contractor
                $contractor = (!empty($instance->ParentContractorId)) ? $instance->ParentContractorId : $instance->ContractorId;
                $adjContractor = (!empty($instance->AdjParentContractorId)) ? $instance->AdjParentContractorId : $instance->AdjContractorId;
                $applies = ($contractor == $contractorId);
                if (!empty($adjContractor))
                    $applies = ($adjContractor == $contractorId);
                
                if ($applies) {
                    // grab fees
                    $blankFee = '-';
                    $baseFee = $blankFee;
                    $adjFee = $blankFee;
                    if ($instance->IsBase == 1)
                    {
                        $baseFee = "$gbp ".number_format($instance->Fee, 2);
                        $adjFee = (!empty($instance->AdjFee) && ($instance->AdjFee != $instance->Fee)) 
                                        ? "$gbp ".number_format($instance->AdjFee, 2) 
                                        : $blankFee;
                    }
                    else // exception route
                    {
                        $adjFee = (!empty($instance->AdjFee)) 
                                        ? "$gbp ".number_format($instance->AdjFee, 2) 
                                        : "$gbp ".number_format($instance->Fee, 2);
                    }

                    $fee = (!empty($instance->AdjFee)) ? $instance->AdjFee : $instance->Fee;
                    $total = floatval($fee);

                    // expenses / deductions
                    $misc = $blankFee;
                    if (!empty($instance->MiscFee) && (floatval($instance->MiscFee) != 0))
                    {
                        $misc = "$gbp ".number_format($instance->MiscFee, 2);
                        $total += floatval($instance->MiscFee);
                    }
                    
                    if ($total != 0) {
                        $substitute = FALSE;
                        $routeName = $instance->Category.'/'.$instance->Route;
                        if ($instance->AdjContractorId == $contractorId)
                        {
                            $substitute = TRUE;
                            $routeName .= "*";
                        }
                        
                        $date = strtoupper(date('d/m D', CDateTimeParser::parse($instance->RouteDate, "yyyy-MM-dd")));
                        
                        $line = array (
                            'date' => $date,
                            'route' => $routeName,
                            'substitute' => $substitute,
                            'rate' => $baseFee,
                            'adjRate' => $adjFee,
                            'misc' => $misc,
                            'total' => $total,
                            'extras' => array(),
                            'comments' => array()
                        );
                        
                        $additional = array();
                        // check whether route covered (parent invoicing)
                        if (($instance->ParentContractorId == $contractorId) || ($instance->AdjParentContractorId == $contractorId))
                        {
                            $coverName = (empty($instance->AdjContractorId))
                                    ? trim($instance->ContractorFirstName.' '.$instance->ContractorLastName)
                                    : trim($instance->AdjContractorFirstName.' '.$instance->AdjContractorLastName);

                            $additional[] = array(
                                'date' => empty($additional) ? $date : '',
                                'desc' => $instance->Route.' covered by '.$coverName,
                                'charge' => $blankFee
                            );
                        }
                        
                        // grab expenses/deductions
                        if ($instance->IsAdjustment)
                        {
                            $expenses = FinanceAdjustmentExpense::model()->findAll(array(
                                'condition' => 'AdjustmentId = :aid AND IsActive = 1',
                                'params' => array(':aid' => $instance->AdjustmentId),
                                'order' => 'AdjustmentExpenseId ASC' 
                            ));
                            
                            foreach ($expenses as $exp) {
                                $additional[] = array(
                                    'date' => empty($additional) ? $date : '',
                                    'desc' => utf8_decode($exp->Comment),
                                    'charge' => "$gbp ".number_format($exp->Amount, 2)
                                );
                            }
                        }
                        
                        // grab comments for instance
                        $invoiceComments = array();
                        $comments = FinanceComment::model()->findAll(array(
                            'condition' => 'RouteInstanceId = :rid AND OutputOnInvoice = 1',
                            'params' => array(':rid' => $instance->RouteInstanceId),
                            'order' => 'CommentId ASC'
                        ));

                        foreach ($comments as $comment)
                        {
                            $invoiceComments[] = array(
                                'date' => empty($invoiceComments) ? $date : '',
                                'desc' => utf8_decode($comment->Comment), //$comment->Comment,
                                'charge' => ''
                            );
                        }
                        
                        /*
                        if ($misc != $blankFee) { // #60
                            $comments = FinanceComment::model()->findAll(array(
                                'condition' => 'RouteInstanceId = :rid',
                                'params' => array(':rid' => $instance->RouteInstanceId),
                                'order' => 'CommentId DESC'
                            ));

                            foreach ($comments as $comment)
                            {
                                $additional[] = array(
                                    'date' => $date,
                                    'desc' => utf8_decode($comment->Comment), //$comment->Comment,
                                    'charge' => $misc
                                );
                            }
                        }
                         * 
                         */
                        
                        $line['extras'] = $additional;
                        $line['comments'] = $invoiceComments;
                        $lines[] = $line;
                    }
                }
            }
            
            
            $noInstances = count($lines);
            
            if ($noInstances == 0) // avoid generating blank invoices
                return FALSE;
            
            $noPages = ceil($noInstances / $linesPerPage);
            //if ($noPages == 0)
            //    $noPages = 1;
            
            $invoiceInfo = $this->getInvoiceNumberingInfo($weekStarting, $contractorId, $contractType);
        
            $pageIdx = 0;
            $lineIdx = 1;
            $newPage = TRUE;
            $commentIdx = 0;
            $netTotal = 0;
            $substitute = FALSE;
            $pageComments = array();
            for ($i = 0; $i < $noInstances; $i++) {
                $instance = $lines[$i];
                if ($lineIdx == ($linesPerPage + 1) )
                {
                    $lineIdx = 1;
                    $commentIdx = 0;
                    $pageComments = array();
                    $newPage = TRUE;
                }                    
                //if ($lineIdx == 0)
                if ($newPage)
                {
                    $newPage = FALSE;
                    $pageIdx++;
                    $pdf->AddPage();
                    $pdf->useTemplate($tplidx);

                    // write header info
                    //$pdf->AddHeader($contractorId, $invoiceInfo, $pageIdx, $noPages);
                    $pdf->AddHeader($contractorInfo, $invoiceInfo, $pageIdx, $noPages);
                }
                
                $y = $topY + ($lineIdx * 5);
                    
                $dt = $instance['date'];
                $rt = $instance['route'];
                $baseFee = $instance['rate'];
                $adjFee = $instance['adjRate'];
                $misc = $instance['misc'];
                $total = $instance['total'];                    

                $pdf->SetXY(20, $y);
                $pdf->Cell(0, 5, $dt); 

                $pdf->SetXY(43, $y);
                $pdf->Cell(0, 5, $rt);

                $pdf->SetXY(90, $y);
                $pdf->Cell(20, 5, $baseFee, 0, 0, 'R');

                $pdf->SetXY(110, $y);
                $pdf->Cell(35, 5, $adjFee, 0, 0, 'R');

                $pdf->SetXY(137, $y);
                $pdf->Cell(35, 5, $misc, 0, 0, 'R');

                // total
                $pdf->SetXY(157, $y);
                $pdf->Cell(35, 5, "$gbp ".number_format($total, 2), 0, 0, 'R');

                //foreach ($instance['comments'] as $item)
                foreach ($instance['extras'] as $item)
                {
                    $y = $commentTopY + ($commentIdx * 3);

                    // date
                    $pdf->SetXY(20, $y);
                    $pdf->Cell(0, 5, $item['date']);

                    // description/comment
                    $pdf->SetXY(45, $y);
                    $pdf->Cell(0, 5, $item['desc']);

                    // charge
                    $pdf->SetXY(157, $y);
                    $pdf->Cell(35, 5, $item['charge'], 0, 0, 'R');

                    $commentIdx++;
                }
                
                // output comments at bottom of extras (if page/list is done)
                $pageComments = array_merge($pageComments, $instance['comments']);
                if ((($lineIdx == $linesPerPage) || ($i == ($noInstances-1))) && !empty($pageComments)) {
                    $y = $commentTopY + ($commentIdx * 3);
                    $pdf->SetXY(20, $y);
                    $pdf->Cell(0, 5, '** Notes/Comments **');
                    $commentIdx++;
                    foreach ($pageComments as $item) {
                        $y = $commentTopY + ($commentIdx * 3);

                        // date
                        $pdf->SetXY(20, $y);
                        $pdf->Cell(0, 5, $item['date']);

                        // description/comment
                        $pdf->SetXY(45, $y);
                        $pdf->Cell(0, 5, $item['desc']);

                        $commentIdx++;
                    }
                }
                
                $lineIdx++;
                $netTotal += $total;
            }
            if ($substitute)
            {
                $y = $topY + ($lineIdx * 5);
                $pdf->SetXY(20, $y);
                $pdf->Cell(0, 5, "* denotes substituting another contractor for this route");
            }

            $pdf->SetFont('Arial','',10);

            $pdf->SetXY(140, 254);
            $pdf->Cell(35, 7, "$gbp ".number_format($netTotal, 2), 0, 0, 'R');

            $taxPercentage = isset($contractorInfo->tax) ? $contractorInfo->tax->Percentage : 0;
            $vatTotal = $netTotal * floatval($taxPercentage);

            $pdf->SetXY(140, 263);
            $pdf->Cell(35, 6, "$gbp ".number_format($vatTotal, 2), 0, 0, 'R');

            $grandTotal = $vatTotal + $netTotal;
            $pdf->SetXY(140, 272);
            $pdf->Cell(35, 5, "$gbp ".number_format($grandTotal, 2), 0, 0, 'R');
            
            // store invoice details in DB
            FinanceInvoice::model()->updateByPk(
                    $invoiceInfo['id'],
                    array(
                        //'LineDetails' => json_encode($lines), // throws json_encode(): Invalid UTF-8 sequence in argument
                        'TotalBeforeVat' => $netTotal,
                        'Vat' => $vatTotal,
                        'Total' => $grandTotal,
                        'DateLastGenerated' => new CDbExpression("NOW()"),
                        'GeneratedBy' => Yii::app()->user->loginId,
                        'OutputPO' => 1
                    )
                );
            
            $invoiceId = $invoiceInfo['id']; // assume only one invoice is generated at a time for now
        }
        
        if ($pdfName !== NULL)
            $pdf->Output($pdfName, 'F');
        
        //return TRUE; // generated
        return $invoiceId; // generated
    }
    
    private function getInstancesForContractor($weekStarting, $contractorId)
    {
        $crit = new CDbCriteria();
        $crit->addCondition("RouteDate >= str_to_date(:ws, '%d/%m/%Y')");
        $crit->addCondition("RouteDate < date_add(str_to_date(:ws, '%d/%m/%Y'), interval 7 day)");
        
        $crit->addCondition("ContractorId = :cid OR AdjContractorId = :cid OR ParentContractorId = :cid OR AdjParentContractorId = :cid");
        
        $crit->params = array(
                            ':ws' => $weekStarting,
                            ':cid' => $contractorId,
            );
        $crit->order = "Category, RouteDate, Route";
        
        return FinanceRouteInstanceDetails::model()->findAll($crit);
    }
    
    private function getInvoiceNumberingInfo($weekStarting, $contractorId, $contractType)
    {
        $type = $contractType;
        if (empty($type) || ($type == '*'))
        {
            $contractor = FinanceContractor::model()->findByPk($contractorId);
            $type = $contractor->Data;
        }
        
        $existing = FinanceInvoice::model()->find(array(
            'condition' => "WeekStarting = str_to_date(:ws, '%d/%m/%Y') AND ContractorId = :cid AND ContractType = :ct",
            'params' => array(':ws' => $weekStarting, ':cid' => $contractorId, ':ct' => $type)
        ));
        $invoice = NULL;
        if (isset($existing))
           $invoice = $existing;
        else
        {
            //$contractor = FinanceContractor::model()->findByPk($contractorId);
            $max = FinanceInvoice::model()->find(array(
                'condition' => "WeekStarting = str_to_date(:ws, '%d/%m/%Y') AND ContractType = :ct",
                //'params' => array(':ws' => $weekStarting, ':ct' => $contractor->Data),
                'params' => array(':ws' => $weekStarting, ':ct' => $type),
                'order' => 'InvoiceNumber DESC'
            ));
            $newNumber = (isset($max)) ? ($max->InvoiceNumber + 1) : 1;
            
            $new = new FinanceInvoice();
            $new->ContractorId = $contractorId;
            $new->WeekStarting = new CDbExpression("str_to_date('$weekStarting', '%d/%m/%Y')");
            $new->InvoiceNumber = $newNumber;
            $new->InvoiceDate = new CDbExpression("CURDATE()"); // generation date is stored
            //$new->ContractType = $contractor->Data;
            $new->ContractType = $type;
            $new->save();
            
            $invoice = FinanceInvoice::model()->findByPk($new->Id);
        }
        $wend = CDateTimeParser::parse($invoice->WeekStarting, "yyyy-MM-dd");
        $wend +=  (6 * 24 * 60 * 60); // monday to sunday
        
        $result = array(
            //'date' => date('d/m/Y', CDateTimeParser::parse($invoice->InvoiceDate, "yyyy-MM-dd")),
            'id' => $invoice->Id,
            'date' => date('d/m/Y', $wend), // week ending date always to be displayed as invoice date
            'number' => $invoice->ContractType.date('dmy', $wend).'.'.sprintf('%1$03d', $invoice->InvoiceNumber)
        );
        return $result;
    }
    
    public function outputAccountingCsv()
    {
        $weDate = $this->getDateTimeObj($this->weekStarting);
        $wsForDb = $weDate->format('Y-m-d');
        $weDate = $weDate->modify("next sunday");
        $weDateDesc = "w/e ".$weDate->format('d.m.Y'); // #175: PO XLS - Description change

        $category = $this->category;

        // flag all invoices not to output PO to avoid unnecessary invoices
        FinanceInvoice::model()->updateAll(array(
                        'OutputPO' => 0,
                    ),
                    'WeekStarting = :ws AND ContractType = :cat',
                    array (
                        ':ws' => $wsForDb,
                        ':cat' => $category
                    )
                );

        // force invoice generation to be completed for selected week
        $this->contractor = $category; // used to force generation of ALL DTC / ALL DTR invoices
        $this->generateInvoices(FALSE); // generate ALL without downloading
        
        $rowData = array(
            'Doc Type' => '1',
            'Description' => '', // to be set per route/driver
            'Vendor' => '', // contractor code
            'Doc Date' => '', // end of week? - current date?
            'Doc Number' => '001', // ?
            'Purchases Amount' => 0, // total 
            'PO Number' => 'PO01', // ? 
            'Check Amount' => '0', // no cheques handled
            'TaxID' => '',
            'TaxAmount' => 0,
            //'Contract Type' => ''
        );
        
        $taxInfo = FinanceTax::model()->findAll();
        $taxDetails = array();
        foreach ($taxInfo as $tax)
            $taxDetails[$tax->Id] = $tax;
        
        //$info = $this->getRouteInstanceDetails();
        $info = FinanceInvoice::model()->with('contractor')->findAll( array(
                    'condition' => 'WeekStarting = :ws AND OutputPO = 1 AND ContractType = :cat',
                    'params' => array(
                                    ':ws' => $wsForDb,
                                    ':cat' => $category
                                ),
                    'order' => 'ContractType ASC, InvoiceNumber ASC'
                ));
        
        // create CR rows by contractor code
        $credits = array();
        foreach ($info as $instance)
        {
            $invoiceId = $instance->Id;
            $wend = CDateTimeParser::parse($instance->WeekStarting, "yyyy-MM-dd");
            $wend +=  (6 * 24 * 60 * 60); // monday to sunday
            $invoiceNumber = $instance->ContractType.date('dmy', $wend).'.'.sprintf('%1$03d', $instance->InvoiceNumber);
            $poNumber = 'PO'.$invoiceNumber;
            
            $contractType = $instance->ContractType;
            $code = $instance->contractor->Code;
            $name = trim($instance->contractor->FirstName.' '.$instance->contractor->LastName);
//            $account = $instance->contractor->AccountNumber;
            $fee = $instance->TotalBeforeVat;
            $taxCode = ''; // ? $taxDetails[$instance->contractor->ApplicableTaxId]->Code : '';
            $taxPercentage = 0;
            if (isset($taxDetails[$instance->contractor->ApplicableTaxId])) {
                $taxDetail = $taxDetails[$instance->contractor->ApplicableTaxId];
                $taxCode = $taxDetail->Code;
                $taxPercentage = floatval($taxDetail->Percentage);
            }
            
            if (floatval($fee) != 0) {
                $credits[$invoiceId] = $rowData;
                $credits[$invoiceId]["Vendor"] = $code;
                //$credits[$invoiceId]["Description"] = "$name $weDateDesc";
                $credits[$invoiceId]["Description"] = "$contractType $weDateDesc";  // #175: PO XLS - Description change
                $credits[$invoiceId]["Doc Number"] = $invoiceNumber;
                $credits[$invoiceId]["PO Number"] = $poNumber;
                $credits[$invoiceId]["TaxID"] = $taxCode;
                $credits[$invoiceId]["Doc Date"] = date('d/m/Y', $wend);
                $credits[$invoiceId]["Purchases Amount"] = number_format(floatval($fee), 2);
//                $total = $credits[$invoiceId]["Purchases Amount"];
                $credits[$invoiceId]["TaxAmount"] = number_format(round((floatval($fee) * $taxPercentage), 2), 2);

                //$credits[$invoiceId]["Contract Type"] = $contractType;
            }
        }

        $local_file = tempnam('/tmp','');
        $temp = fopen($local_file,"w");
        
        fputcsv($temp, array_keys($rowData));
        foreach($credits as $code => $cr)
        {
            $both = array($cr);
            foreach ($both as $line)
            {
                // set general fields
                //$line['Doc Date'] = $this->weekStarting;
                // output csv
                fputcsv($temp, array_values($line));
            }
        }
        fclose($temp);

        header("Content-Disposition: attachment; filename=\"" . date("Ymd-His")."-$category-pos.csv" . "\"");
        header("Content-Type: text/csv");
        header("Content-Length: " . filesize($local_file));
        header("Connection: close");
        readfile($local_file);

        unlink($local_file); // this removes the file
    }
    
    private function getDateTimeObj($formattedDate)
    {
        $wsParts = explode('/', $formattedDate);
        $wsYmd = array($wsParts[2], $wsParts[1], $wsParts[0]);
        $wsYmd = implode('-',$wsYmd);
        return new DateTime($wsYmd);
    }
    
    private function sendInvoiceOverEmail($weekStarting, $cid, $pdfName, $invoiceId) {

        if ($this->sendOverEmail != '1')
            return FALSE;
        
        $contractor = FinanceContractor::model()->findByPk($cid);
        if (isset($contractor)) {
            $email = trim($contractor->Email);
            if (!empty($email)) {
                
                $invoice = FinanceInvoice::model()->findByPk($invoiceId);
                $contractType = strtolower($invoice->ContractType);
                
                $mailPath = Yii::getPathOfAlias('ext.yii-mail');
                require_once $mailPath.'/YiiMailMessage.php';
                try {
                    $mail = new YiiMailMessage();
                    $mail->setSubject("Weekly earnings summary - {$contractor->FirstName} {$contractor->LastName} - Week Starting {$this->weekStarting}");
                    $mail->view = "contractorinvoice-$contractType"; // allows a template for DTR and another one for DTC
                    $mail->setBody(array("model"=>$this, 'contractor' => $contractor), 'text/html');

                    $mail->attach(Swift_Attachment::fromPath($pdfName));

                    // UNCOMMENT THIS LINE TO PUT LIVE!
                    $recipients = explode(",", $email);
                    foreach($recipients as $toEmail)
                        $mail->addTo(trim($toEmail));

                    $mail->setFrom(array(Yii::app()->params['notificationsEmail'] => Yii::app()->params['notificationsEmailName']));

                    $sent = Yii::app()->mail->send($mail);
                } catch (Exception $ex) {
                    $sent = -1; // trigger error so invoice will be attached into pdf
                }
                if ($sent > 0)
                    return TRUE;
                else 
                    return -1;
            }
        }
        return FALSE;
    }
    
    private function generateSummaryEmailPage($summaryPdfName, $contractorEmails) {
        $pdf = new FPDF('P','mm','A4');
        $pdf->SetMargins(0,0,0);
        $pdf->SetAutoPageBreak(false);
        
        $pdf->AddPage();
        
        $pdf->SetFont('Arial','B',14);
        $pdf->SetXY(5, 10);
        $pdf->Cell(10, 0, "Contractor e-mails summary page - Week Starting {$this->weekStarting}");
        
        $list = array(
            'email' => array(),
            'paper' => array()
        );
        foreach($contractorEmails as $cid => $sent)
            //if ($sent)
            if ($sent === TRUE)
                $list['email'][] = $cid;
            else
                $list['paper'][] = array(
                                    'cid' => $cid, 
                                    'failed' => ($sent === -1)
                    );
            
        $topY = 20;
        if (!empty($list['email'])) {
            $pdf->SetFont('Arial','B',12);
            $pdf->SetXY(5, $topY);
            $pdf->Cell(10, 0, "Contractors notified via email");
            $pdf->SetFont('Arial','',10);
//            $topY += 5;

            $contractors = FinanceContractor::model()->findAllByAttributes(array(
                'ContractorId' => $list['email']
            ));

            foreach ($contractors as $c) {
                $topY += 5;
                if ($topY > 200) {
                    $pdf->AddPage();
                    $topY = 10;
                }
                $pdf->SetXY(15, $topY);
                //$pdf->Cell(5, 0, $c->Code." - ".trim($c->FirstName.' '.$c->LastName));
                $pdf->Cell(5, 0, $c->Code." - ".trim($c->FirstName.' '.$c->LastName)." - ".trim($c->Email));

//                if( !empty($c->Email) )
//                {$pdf->Cell(5, 0, $c->Code." - ".trim($c->FirstName.' '.$c->LastName)." - ".trim($c->Email));}
//                else
//                {$pdf->Cell(5, 0, $c->Code." - ".trim($c->FirstName.' '.$c->LastName));}

                }

            $topY += 10;
        }
        
        if (!empty($list['paper'])) {
            $pdf->SetFont('Arial','B',12);
            $pdf->SetXY(5, $topY);
            $pdf->Cell(10, 0, "Contractors to be notified manually");
            $pdf->SetFont('Arial','',10);
//            $topY += 5;

            $ids = array();
            foreach ($list['paper'] as $info)
                $ids[$info['cid']] = $info['failed'];
            
            $contractors = FinanceContractor::model()->findAllByAttributes(array(
                //'ContractorId' => $list['paper']
                'ContractorId' => array_keys($ids)
            ));

            foreach ($contractors as $c) {
                $topY += 5;
                if ($topY > 200) {
                    $pdf->AddPage();
                    $topY = 10;
                }
                $pdf->SetXY(15, $topY);
                $text = $c->Code." - ".trim($c->FirstName.' '.$c->LastName);
                if ($ids[$c->ContractorId] === TRUE)
                    $text .= ' - ERROR while sending out email to '.trim($c->Email);
                $pdf->Cell(5, 0, $text);
            }
        }
        $pdf->Output($summaryPdfName, 'F');
    }
}

class InvoicePDF extends FPDI{

	function AddHeader($contractorInfo, $invoiceInfo, $pageCurrent = 1, $pageTotal = 1)
	{
            $this->SetTextColor(0, 0, 0);
            $this->SetFont('Arial','',8);

            $info = $contractorInfo;

            $this->SetXY(175, 10);
            $this->Cell(0, 0, "Page $pageCurrent of $pageTotal");
            
            $payee = strtoupper(trim($info->FirstName.' '.$info->LastName));
            $payee .= "\n".$info->AddressLine1;
            $payee .= "\n".$info->AddressLine2;
            $payee .= "\n".$info->AddressLine3;
            $payee .= "\n".$info->Town." ".$info->County;
            $payee .= "\n".$info->Postcode;
            
            $this->SetXY(45, 33);
            $this->MultiCell(0, 3, $payee);
            
            $this->SetXY(147, 30);
            //$this->Cell(0, 5, "xxxxxx"); // tax ref number
            $this->Cell(0, 5, $info->AccountNumber); // tax ref number
            
            $this->SetXY(147, 49);
            $this->Cell(0, 6, $info->BankName);
            
            $this->SetXY(147, 56);
            $this->Cell(0, 6, $info->BankSortCode);
            
            $this->SetXY(147, 62);
            $this->Cell(0, 6, $info->BankAccountNumber);
            
            $this->SetXY(147, 69);
            $vatNo = '';
            if (isset($info->tax)) {
                if (!empty($info->VATNo))
                    $vatNo = $info->VATNo;
                else
                    $vatNo = $info->tax->Code.' - '.$info->tax->Description;
            }
            //$this->Cell(0, 6, "xxxxxx"); // vatno
            $this->Cell(0, 6, $vatNo); // vatno
            
            $date = $invoiceInfo['date'];
            $number = $invoiceInfo['number'];
            $this->SetXY(147, 36);
            $this->Cell(0, 6, $date);
            $this->SetXY(147, 43);
            $this->Cell(0, 5, $number);
            
            
            
        }
}
