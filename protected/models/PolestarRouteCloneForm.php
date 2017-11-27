<?php
/**
 * Description of PolestarRouteCloneForm
 *
 * @author ramon
 */
class PolestarRouteCloneForm extends CFormModel {
    
    public $sourceJobId;
    public $newJobInfo;
    public $sourceJobInfo;
    
    public $newRef = '-select-date-';
    public $collDate;
    
    public $collScheduledTime;
    
    public $delDate;
    public $delScheduledTime;
    
    public $fullPallets;
    public $halfPallets;
    public $qtrPallets;
    public $quantity;
    public $kg;
    public $ref;
    public $bookingRef;
    public $specialInstructions;
    
    public function attributeLabels()
    {
        return array(
            'newRef' => 'New AktrionJobRef',
            'collDate' => 'Collection Date',
        );
    }
    
    public function rules()
    {
        return array(
            array('collDate', 'required'),
            array('delDate', 'validateDateTime', 'skipOnError' => TRUE)
        );
    }
    
    public function validateDateTime() {
        if (empty($this->collDate))
            return;
        
        $jobDtt = DateTime::createFromFormat('d/m/Y H:i:s', $this->collDate.(!empty($this->collScheduledTime[0]) ? ' '.$this->collScheduledTime[0] : ' 00:00:00'));
        foreach ($this->delDate as $loadId => $delDate) {
            if (empty($delDate)){
                $this->addError('delDate', 'Load Delivery Dates cannot be blank');
                return;
            }
            
            
            $delScheduledtime = $this->delScheduledTime[$loadId];
            $loadDt = DateTime::createFromFormat('d/m/Y', $delDate);
            $loadDtt = new DateTime($loadDt->format('Y-m-d').' '.$delScheduledtime);

            $jobTs = intval($jobDtt->format("U"));
            $loadTs = intval($loadDtt->format("U"));

            if ($loadTs < $jobTs) {
                if ($this->collDate != $delDate)
                    $this->addError('delDate', 'Load Delivery Dates cannot be previous to Job Collection Date');
                else if (!empty($delScheduledtime))
                    $this->addError('delScheduledTime', 'Load Scheduled Delivery Times cannot be previous to Job Scheduled Collection Time');
            }
        }
    }
    
    public function getJob() {
        if (!isset($this->sourceJobInfo))
            $this->sourceJobInfo = PolestarJob::model()->with('Loads', 'CollectionPoints')->findByPk($this->sourceJobId);
        return $this->sourceJobInfo;
    }
    
    public function populateFromSource() {
        $src = $this->getJob();
        
        $this->collDate = '';
        $colTimes = array();
        $colTimes[] = $src->CollScheduledTime;
        foreach ($src->CollectionPoints as $p)
            $colTimes[$p->Id] = $p->CollScheduledTime;
        $this->collScheduledTime = $colTimes;
        
        $emptyList = array();
        $delScheduledTime = array();
        foreach ($src->Loads as $l) {
            $emptyList[$l->Id] = '';
            $delScheduledTime[$l->Id] = $l->DelScheduledTime;
        }
        $this->delDate = $emptyList;
        $this->fullPallets = $emptyList;
        $this->halfPallets = $emptyList;
        $this->qtrPallets = $emptyList;
        $this->quantity = $emptyList;
        $this->kg = $emptyList;
        $this->ref = $emptyList;
        $this->bookingRef = $emptyList;
        $this->specialInstructions = $emptyList;
        $this->delScheduledTime = $delScheduledTime;
    }
    
    public function save() {
        $src = $this->getJob();
        
        $job = new PolestarJob();
        $job->setAttributes($src->getAttributes(), FALSE);
        $job->OriginalOrderNo = $job->Id; // for cloning reference
        $job->Id = NULL;
        $job->Ref = PolestarJob::getValidReference($job->PrintCentreId, $this->collDate);
        $job->CreatedBy = Yii::app()->user->loginId;
        $job->CreatedDate = new CDbExpression('NOW()');
        $job->EditedBy = NULL;
        $job->EditedDate = NULL;
        $job->CreationDate = new CDbExpression('NOW()');
        $job->DeliveryDate = new CDbExpression("STR_TO_DATE(:dt, '%d/%m/%Y')", array(':dt' => $this->collDate));
        
        $job->CollScheduledTime = !empty($this->collScheduledTime[0]) ? $this->collScheduledTime[0] : NULL;
        $job->CollArrivalTime = NULL;
        $job->CollDepartureTime = NULL;
        
        $job->SupplierId = NULL;
        $job->DriveName = NULL;
        $job->VehicleRegNo = NULL;
        $job->ContactNo = NULL;
        
        $job->RevisionNo = 1;
        $job->RevisionChanges = NULL;
        $job->AgreedPrice = NULL;
        
        $job->Mileage = NULL;
        $job->TotalMileage = NULL;
        
        $job = PolestarStatusUpdater::saveJobDetails($job, $this->collDate); // sets initial status
        
        $this->newJobInfo = PolestarJob::model()->findByPk($job->Id);
        
        foreach ($src->CollectionPoints as $srcPoint) {
            $p = new PolestarJobCollectionPoint();
            $p->JobId = $job->Id;
            $p->Sequence = $srcPoint->Sequence;
            $p->CollPostcode        = $srcPoint->CollPostcode;
            $p->CollAddress         = $srcPoint->CollAddress;
            $p->CollCompany         = $srcPoint->CollCompany;
            $p->CollScheduledTime   = !empty($this->collScheduledTime[$srcPoint->Id]) ? $this->collScheduledTime[$srcPoint->Id] : NULL;
            $p->SpecialInstructions = $srcPoint->SpecialInstructions;
            $p->CreatedBy           = Yii::app()->user->loginId;
            $p->CreatedDate         = new CDbExpression('NOW()');
            $p->save();
        }
        
        foreach ($src->Loads as $srcLoad) {
            if ($srcLoad->StatusId == PolestarStatus::CANCELLED_ID) // skip cloning cancelled loads
                continue;
            
            $load = new PolestarLoad();
            $load->setAttributes($srcLoad->getAttributes(), FALSE);
            $load->Id = NULL;
            $load->JobId = $job->Id;
            $load->Ref = $this->ref[$srcLoad->Id];
            $load->DeliveryDate = new CDbExpression("STR_TO_DATE(:dt, '%d/%m/%Y')", array(':dt' => $this->delDate[$srcLoad->Id]));
            $load->DelScheduledTime = !empty($this->delScheduledTime[$srcLoad->Id]) ? $this->delScheduledTime[$srcLoad->Id] : NULL;
            
            $load->PalletsFull = (!empty($this->fullPallets[$srcLoad->Id])) ? intval($this->fullPallets[$srcLoad->Id]) : 0;
            $load->PalletsHalf = (!empty($this->halfPallets[$srcLoad->Id])) ? intval($this->halfPallets[$srcLoad->Id]) : 0;
            $load->PalletsQtr = (!empty($this->qtrPallets[$srcLoad->Id])) ? intval($this->qtrPallets[$srcLoad->Id]) : 0;
            $load->PalletsTotal = $load->PalletsFull + $load->PalletsHalf + $load->PalletsQtr;
            
            $load->Quantity = (!empty($this->quantity[$srcLoad->Id])) ? intval($this->quantity[$srcLoad->Id]) : 0;
            $load->Kg = $this->kg[$srcLoad->Id];
            
            $load->BookingRef = $this->bookingRef[$srcLoad->Id];
            $load->SpecialInstructions = $this->specialInstructions[$srcLoad->Id];
            
            $load->DelArrivalTime = NULL;
            $load->DelDepartureTime = NULL;
            $load->Mileage = NULL;
            $load->RevisionNo = 1;
            $load->RevisionChanges = NULL;
            $load->CreatedBy = Yii::app()->user->loginId;
            $load->CreatedDate = new CDbExpression('NOW()');
            $load->EditedBy = NULL;
            $load->EditedDate = NULL;
            
            $load->save();
        }
        
        return TRUE;
    }
}
