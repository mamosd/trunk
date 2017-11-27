<?php
/**
 * Description of FinanceEarningsForm
 *
 * @author ramon
 */
class FinanceEarningsForm extends CFormModel
{
    public $contractorId;
    public $weekStarting;
    
    public $data;
    
    public function populate()
    {
        $weekStart = $this->weekStarting;
        $crit = new CDbCriteria();
        $crit->addCondition("RouteDate >= str_to_date(:ws, '%d/%m/%Y')");
        $crit->addCondition("RouteDate < date_add(str_to_date(:ws, '%d/%m/%Y'), interval 7 day)");
        
        $crit->addCondition("ContractorId = :cid OR AdjContractorId = :cid OR ParentContractorId = :cid OR AdjParentContractorId = :cid");
        
        $crit->params = array(
                            ':ws' => $weekStart,
                            ':cid' => $this->contractorId,
            );
        //$crit->order = "Category, RouteDate, Route";
        $crit->order = "RouteDate";
        
        $data = FinanceRouteInstanceDetails::model()->findAll($crit);
        
        $result = array();
        
        $info = $this->getContractorInfo();
        
        foreach ($data as $row){
            if (($row->ContractorId == $this->contractorId || $row->ParentContractorId == $this->contractorId) && (!empty($row->AdjContractorId)))
                    continue; // row belongs to other contractor

            $fee = (!empty($row->AdjFee)) ? $row->AdjFee : $row->Fee;
            $total = floatval($fee) + floatval($row->MiscFee);
        
            $isConfirmed = TRUE;
            if ($row->IsBase == 0)
                    $isConfirmed = !empty($row->AckDate);
            if ($isConfirmed && ($row->IsAdjustment != 0))
                    $isConfirmed = !empty($row->AdjAckDate);

            $replaces = "";
            if ($row->AdjContractorId == $this->contractorId)
                $replaces = trim($row->ContractorFirstName.' '.$row->ContractorLastName);
            
            $contractor = trim($info->FirstName.' '.$info->LastName);;
            if ($row->ParentContractorId == $this->contractorId)
                $contractor = trim($row->ContractorFirstName.' '.$row->ContractorLastName);
            if ($row->AdjParentContractorId == $this->contractorId)
                $contractor = trim($row->AdjContractorFirstName.' '.$row->AdjContractorLastName);
            
            $line = array(
                'date' => $row->RouteDate,
                'category' => $row->Category,
                'route' => $row->Route,
                'contractor' => $contractor,
                'replaces' => $replaces,
                'confirmed' => ($isConfirmed) ? 1 : 0,
                'fee' => $fee,
                'miscfee' => $row->MiscFee,
                'total' => $total
            );
            $result[] = $line;
        }
        
        $this->data = $result;
    }
    
    public function getContractorInfo()
    {
        $c = FinanceContractor::model()->findByPk($this->contractorId);
        return $c;
    }
    
    public function outputCsv()
    {
        $this->populate();
        $data = $this->data;
        
        // grab header keys
        $local_file = tempnam('/tmp','');
        $temp = fopen($local_file,"w");
        $headerDone = FALSE;
        foreach ($data as $d){
            if ($headerDone === FALSE)
            {
                $headerDone = TRUE;
                fputcsv($temp, array_keys($d));
            }
            
            fputcsv($temp, array_values($d));
        }

        fclose($temp);
        header("Content-Disposition: attachment; filename=\"".str_replace('/', '-', $this->weekStarting)."-{$this->contractorId}-earnings.csv\"");
        header("Content-Type: application/force-csv");
        header("Content-Length: " . filesize($local_file));
        header("Connection: close");
        readfile($local_file);

        unlink($local_file); // this removes the file
    }
    
    public function sendEmail()
    {
        $mailPath = Yii::getPathOfAlias('ext.yii-mail');
        require_once $mailPath.'/YiiMailMessage.php';
        
        $mail = new YiiMailMessage();
        $mail->setSubject("Weekly earnings");
        $mail->view = "contractorearnings";
        $contractor = $this->getContractorInfo();
        $this->populate();
        $mail->setBody(array("model"=>$this, 'contractor' => $contractor), 'text/html');

        // UNCOMMENT THIS LINE TO PUT LIVE!
        $recipients = explode(",", $contractor->Email);
        foreach($recipients as $toEmail)
            $mail->addTo(trim($toEmail));
        
        $mail->setFrom(array(Yii::app()->params['notificationsEmail'] => Yii::app()->params['notificationsEmailName']));
        
        return (Yii::app()->mail->send($mail) > 0);
    }
}

?>
