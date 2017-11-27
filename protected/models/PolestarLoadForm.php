<?php
/**
 * Description of PolestarLoadForm
 *
 * @author ramon
 */
class PolestarLoadForm extends CFormModel {

    public $job;

    public $Id;
    public $JobId;

    public $Ref;
    public $JobType;
    public $Publication;
    public $Quantity;
    public $PalletsFull;
    public $PalletsHalf;
    public $PalletsQtr;
    public $Kg;
    public $Postcode;
    public $Area;
    public $Company;
    public $DeliveryDate;
    public $ScheduledTime;
    public $TimeCode;
    public $BookingRef;
    public $SpecialInstructions;
    public $LoadSpecialInstructions;
    public $Sequence;
    
    public $CollectionSequence;
    
    public $Comments = array();
    public $NewComment;

    public function attributeLabels()
    {
        return array(
            'Area' => 'Full Address',
            'Ref' => 'Polestar Load Ref',
            'PalletsFull' => 'Full Pallets',
            'PalletsHalf' => 'Half Pallets',
            'PalletsQtr' => 'Quarter Pallets',
            'SpecialInstructions' => 'Invoice Ref. No.',
            'LoadSpecialInstructions' => 'Special Instructions'
        );
    }

    public function rules()
    {
        return array(
            array('Ref,Publication,Quantity, PalletsFull, PalletsHalf, PalletsQtr, Kg, Postcode, Area, Company,DeliveryDate', 'required'),
            array('Quantity, PalletsFull, PalletsHalf, PalletsQtr, Kg', 'numerical', 'integerOnly'=>true),
            array('DeliveryDate', 'validateDateTime', 'skipOnError' => TRUE),
            array('Postcode','validPostcode'),
        );
    }
    
    public function validPostcode() {
        $postcode = urlencode($this->Postcode);
        $token = Yii::app()->params['geotools_token'];
        $url = "http://geotools.logicc.co.uk/api/postcode_exists?token={$token}&postcode={$postcode}";
        $params = array('http' => array('method' => 'GET'));
        $ctx = stream_context_create($params);
        $fp = @fopen($url, 'rb', false, $ctx);
        if ($fp) {
            $result = stream_get_contents($fp);
            if ($result != 'true') {
                $this->addError('Postcode','Postcode is not valid');
            }
        }
    }
    
    public function validateDateTime() {
        $jobDtt = new DateTime($this->getJob()->DeliveryDate.' '.$this->getJob()->CollScheduledTime);
        $loadDt = DateTime::createFromFormat('d/m/Y', $this->DeliveryDate);
        $loadDtt = new DateTime($loadDt->format('Y-m-d').' '.$this->ScheduledTime);
        
        $jobTs = intval($jobDtt->format("U"));
        $loadTs = intval($loadDtt->format("U"));
        
        if ($loadTs < $jobTs) {
            if ($jobDtt->format('d/m/Y') != $this->DeliveryDate)
                $this->addError('DeliveryDate', 'Load Delivery Date cannot be previous to Job Collection Date');
            else if (!empty($this->ScheduledTime))
                $this->addError('ScheduledTime', 'Load Scheduled Delivery Time cannot be previous to Job Scheduled Collection Time');
        }
    }

    public function initialize() {
        $j = $this->getJob();
        $this->Sequence = count($j->Loads) + 1;
    }

    public function populate() {
        $l = PolestarLoad::model()->with('Comments')->findByPk($this->Id);
        if (isset($l)) {
            $this->JobId = $l->JobId;
            $this->Ref = $l->Ref;
            $this->JobType = $l->JobTypeId;
            $this->Publication = $l->Publication;
            $this->Quantity = $l->Quantity;
            $this->PalletsFull = $l->PalletsFull;
            $this->PalletsHalf = $l->PalletsHalf;
            $this->PalletsQtr = $l->PalletsQtr;
            $this->Kg = $l->Kg;
            $this->Postcode = $l->DelPostcode;
            $this->Area = $l->DelAddress;
            $this->Company = $l->DelCompany;
            $this->DeliveryDate = $l->formatDate('DeliveryDate', 'd/m/Y');
            $this->ScheduledTime = $l->DelScheduledTime;
            $this->TimeCode = $l->DelTimeCode;
            $this->BookingRef = $l->BookingRef;
            $this->SpecialInstructions = $l->SpecialInstructions;
            $this->LoadSpecialInstructions = $l->LoadSpecialInstructions;
            $this->Sequence = $l->Sequence;
            $points = explode(',', $l->CollectionSequence);
            $collSeq = array();
            foreach ($points as $p)
                $collSeq[$p] = $p;
            $this->CollectionSequence = $collSeq;
            if (isset($l->Comments))
                $this->Comments = $l->Comments;
        }
    }

    public function save() {
        $clearMileage = FALSE;
        $l = NULL;
        $original = PolestarLoad::model()->findByPk($this->Id);
        if (isset($original))
            $l = clone $original;
        
        if (!isset($l)) {
            $l = new PolestarLoad();
            $l->CreatedBy       = Yii::app()->user->loginId;
            $l->CreatedDate     = new CDbExpression('NOW()');
        }
        else {
            $l->EditedBy    = Yii::app()->user->loginId;
            $l->EditedDate  = new CDbExpression('NOW()');
        }

        $l->JobId = $this->JobId;
        $l->Ref = $this->Ref;
        $l->JobTypeId = $this->JobType;
        $l->Publication = $this->Publication;
        $l->Quantity = $this->Quantity;

        // calculate total pallets
        $l->PalletsFull = empty($this->PalletsFull) ? 0 : $this->PalletsFull;
        $l->PalletsHalf = empty($this->PalletsHalf) ? 0 : $this->PalletsHalf;
        $l->PalletsQtr = empty($this->PalletsQtr) ? 0 : $this->PalletsQtr;
        $l->PalletsTotal = intval($l->PalletsFull)+intval($l->PalletsHalf)+intval($l->PalletsQtr);

        $l->Kg = $this->Kg;
        
        $clearMileage = ($l->isNewRecord || (!$l->isNewRecord && ($l->DelPostcode != $this->Postcode)));
        
        $l->DelPostcode = $this->Postcode;
        
        $l->DelAddress = $this->Area;
        $l->DelCompany = $this->Company;
        $l->DelScheduledTime = (!empty($this->ScheduledTime)) ? $this->ScheduledTime : NULL;
        $l->DelTimeCode = $this->TimeCode;
        //$l->DeliveryDate = (!empty($this->DeliveryDate)) ? new CDbExpression("STR_TO_DATE(:dt, '%d/%m/%Y')", array(':dt' => $this->DeliveryDate)) : NULL;
        $dt = DateTime::createFromFormat('d/m/Y', $this->DeliveryDate);
        $l->DeliveryDate = $dt->format('Y-m-d'); // not using expression to show properly on change history
        $l->BookingRef = $this->BookingRef;
        $l->SpecialInstructions = $this->SpecialInstructions;
        $l->LoadSpecialInstructions = $this->LoadSpecialInstructions;
        $l->Sequence = $this->Sequence;
        
        if (isset($this->CollectionSequence)) {
            $points = array();
            foreach($this->CollectionSequence as $postcode => $value)
                if ($postcode == $value)
                    $points[] = $postcode;
            $l->CollectionSequence = implode(',', $points);
        }
        else {
            $l->CollectionSequence = $this->getJob()->CollPostcode;
        }
            
        $jobRevision = array();
        
        if (isset($original)) {
            $diff = $original->compare($l);
            if (!empty($diff)) {
                // store revision
                $history = new PolestarLoadHistory();
                $history->setAttributes($original->attributes, FALSE);
                $history->save();
                
                // store revision changes
                $l->RevisionChanges = json_encode($diff);
                $l->RevisionNo = $l->RevisionNo + 1;
                
                $jobRevisionFields = array('PalletsTotal', 'DelScheduledTime', 'Quantity');
                foreach ($diff as $field => $change) {
                    if (in_array($field, $jobRevisionFields)) {
                        $jobRevision[] = PolestarStatusUpdater::getLoadFieldChangeRevision($l, $field, $change['old'], $change['new']);
                    }
                }
            }
        }
        
        $l->save();
        
        PolestarDeliveryPointForm::ensureExists($l->DelPostcode, $l->DelAddress, $l->DelCompany);
        
        if (!isset($original))  // newly added load - track for job status update
            $jobRevision[] = PolestarStatusUpdater::getLoadStatusChangeRevision($l, PolestarStatus::NEWLY_ADDED_ID);
        
        if(!empty($jobRevision)) {
            $job = $this->getJob();
            PolestarStatusUpdater::saveJobDetails($job, 
                    $job->formatDate('DeliveryDate', 'd/m/Y'), 
                    $jobRevision);
        }

        if (!empty($this->NewComment)) {
            $c = new PolestarLoadComment();
            $c->LoadId       = $l->Id;
            $c->Comment     = $this->NewComment;
            $c->CreatedBy   = Yii::app()->user->loginId;
            $c->CreatedDate = new CDbExpression('NOW()');
            $c->save();
        }
        
        if ($clearMileage)
            PolestarJobMap::clearMileageInfo ($this->JobId);
        
        return true;
    }

    public function getJob() {
        if (!isset($this->job) && !empty($this->JobId)) {
            $this->job = PolestarJob::model()->with('CollectionPoints')->findByPk($this->JobId);
        }
        return $this->job;
    }
    
    private $loadInfo;
    public function getLoad() {
        $result = new PolestarLoad();
        if (!empty($this->Id)) {
            if (!isset($this->loadInfo))
                $this->loadInfo = PolestarLoad::model()->findByPk($this->Id);
            $result = $this->loadInfo;
        }
        return $result;
    }
}
