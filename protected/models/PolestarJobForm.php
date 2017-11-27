<?php
/**
 * Description of PolestarJobForm
 *
 * @author ramon
 */
class PolestarJobForm extends CFormModel {

    public $printCentre;

    public $Id;
    public $Ref;
    public $PrintCentreId;
    public $CreationDate;
    public $DeliveryDate; /** STORES COLLECTION DATE **/
    public $ProviderId;
    public $SupplierId;
    public $VehicleId;
    public $SpecialInstructions;
    public $NewComment;
    public $CollId;
    public $CollSeq;
    public $CollPostcode;
    public $CollAddress;
    public $CollCompany;
    public $CollScheduledTime;
    
    public $ClearHighlighting;

    public $Comments = array();

    public function attributeLabels()
    {
        return array(
            'DeliveryDate' => 'Collection Date',
            'ProviderId' => 'Provider',
            'SupplierId' => 'Supplier',
            'VehicleId' => 'Vehicle Req.',
            'CollPostcode' => 'Postcode',
            'CollAddress' => 'Address',
            'CollScheduledTime' => 'Scheduled Time'
        );
    }

    public function rules()
    {
        return array(
            array('DeliveryDate,ProviderId,VehicleId,CollPostcode,CollAddress', 'required'),
            array('CollPostcode', 'validPostcodes')
        );
    }

    public function validPostcodes() {
        foreach ($this->CollPostcode as $pc) {
            $postcode = urlencode($pc);
            $token = Yii::app()->params['geotools_token'];
            $url = "http://geotools.logicc.co.uk/api/postcode_exists?token={$token}&postcode={$postcode}";
            $params = array('http' => array('method' => 'GET'));
            $ctx = stream_context_create($params);
            $fp = @fopen($url, 'rb', false, $ctx);
            if ($fp) {
                $result = stream_get_contents($fp);
                if ($result != 'true') {
                    $this->addError('CollPostcode',$pc . ' is not valid a valid postcode');
                }
            }
        }
    }
    
    public function initialize() {
        $this->Ref = PolestarJob::getValidReference($this->PrintCentreId, $this->DeliveryDate); //$this->getValidReference();
        $pc = $this->getPrintCentre();
        $this->CollId = array ( 0 );
        $this->CollSeq = array ( 0 );
        $this->CollPostcode = array($pc->Postcode);
        $this->CollAddress = array($pc->getSingleLineAddress());
        $this->CollCompany = array($pc->Address1);
        
        $this->ClearHighlighting = 'N';
    }

    public function getPrintCentre() {
        if (!isset($this->printCentre) && !empty($this->PrintCentreId)) {
            $this->printCentre = PolestarPrintCentre::model()->findByPk($this->PrintCentreId);
        }
        return $this->printCentre;
    }

    public function getSupplierOptions() {
        return PolestarSupplier::getAllAsOptions();
    }

    public function getVehicleOptions() {
        return PolestarVehicle::getAllAsOptions();
    }

    public function save() {
        $model = (empty($this->Id)) ? NULL : PolestarJob::model()->findByPk($this->Id);
        if (!isset($model)) {
            $model = new PolestarJob();
            $model->Ref             = PolestarJob::getValidReference($this->PrintCentreId, $this->DeliveryDate); //$this->getValidReference(); //$this->Ref;
            $model->CreatedBy       = Yii::app()->user->loginId;
            $model->CreatedDate     = new CDbExpression('NOW()');
            $model->CreationDate    = new CDbExpression('NOW()');
            $model->PrintCentreId   = $this->PrintCentreId;
            $model->DeliveryDate    = new CDbExpression("STR_TO_DATE(:dt, '%d/%m/%Y')", array(':dt' => $this->DeliveryDate));
        } else {
            if ($model->Ref != $this->Ref) { // date changed on UI
                $model->Ref = PolestarJob::getValidReference($this->PrintCentreId, $this->DeliveryDate);
                $dt = DateTime::createFromFormat('d/m/Y', $this->DeliveryDate);
                $model->DeliveryDate = $dt->format('Y-m-d'); // not using expression to show properly on change history
            }
        }
        $model->ProviderId          = $this->ProviderId;
        $aktrionSupplierId = Setting::get('polestar', 'aktrion-supplier-id');
        if ($this->ProviderId != $aktrionSupplierId) // #168: Polestar Service provider Selection
            $model->SupplierId      = $this->ProviderId;
        $model->VehicleId           = $this->VehicleId;
        
        $isNewJob = $model->isNewRecord;
        $oldPointSequence = $model->CollectionSequence;
        $newPointSequence = implode(',', $this->CollPostcode);
        $oldJobPostcode = $model->CollPostcode;
        
        $prevCPId                   = $this->CollId[0];
        $model->CollPostcode        = $this->CollPostcode[0];
        $model->CollAddress         = $this->CollAddress[0];
        $model->CollCompany         = $this->CollCompany[0];
        $model->CollScheduledTime   = !empty($this->CollScheduledTime[0]) ? $this->CollScheduledTime[0] : NULL;
        $model->SpecialInstructions = $this->SpecialInstructions[0];
        
        $model->CollectionSequence = $newPointSequence;
        
        if (!empty($this->ClearHighlighting))
            $model->ClearHighlighting = $this->ClearHighlighting;
        
        $model = PolestarStatusUpdater::saveJobDetails($model, $this->DeliveryDate);
        
        $this->Id = $model->Id;
        
        if ($oldPointSequence !== $newPointSequence) {
            if ( !$isNewJob )
                PolestarJobMap::clearMileageInfo ($model->Id);
            
            $noPoints = count($this->CollPostcode);
            
            // update loads' collection sequence as per new postcodes
            if ($noPoints == 1) {
                // set all loads collection sequence to current job collection postcode
                PolestarLoad::model()->updateAll(
                        array('CollectionSequence' => $newPointSequence),
                        'JobId = :jid',
                        array(':jid' => $model->Id)
                        );
            }
            else
                $this->updateLoadsMultipleCollPoints ($oldJobPostcode, $model->CollPostcode);
            
            for ($i = 1; $i < $noPoints; $i++ ) {
                $id = $this->CollId[$i];
                if (empty($id)) {
                    $id = $prevCPId;
                }
                
                $p = PolestarJobCollectionPoint::model()->findByPk($id);
                if (!isset($p)) {
                    $p = new PolestarJobCollectionPoint();
                    $p->JobId = $model->Id;
                }
                $p->Sequence = $i;
                
                if (empty($this->CollId[$i])) {
                    $oldPostcode = $oldJobPostcode;
                } else {
                    $oldPostcode = $p->CollPostcode;
                }
                $newPostcode = $this->CollPostcode[$i];
                
                $p->CollPostcode        = $newPostcode;
                $p->CollAddress         = $this->CollAddress[$i];
                $p->CollCompany         = $this->CollCompany[$i];
                $p->CollScheduledTime   = !empty($this->CollScheduledTime[$i]) ? $this->CollScheduledTime[$i] : NULL;
                $p->SpecialInstructions = $this->SpecialInstructions[$i];
                $p->CreatedBy           = Yii::app()->user->loginId;
                $p->CreatedDate         = new CDbExpression('NOW()');
                $p->save();
                
                $this->updateLoadsMultipleCollPoints($oldPostcode, $newPostcode);
            }
        }

        if (!empty($this->NewComment)) {
            $c = new PolestarJobComment();
            $c->JobId       = $model->Id;
            $c->Comment     = $this->NewComment;
            $c->CreatedBy   = Yii::app()->user->loginId;
            $c->CreatedDate = new CDbExpression('NOW()');
            $c->save();

        }
        //$this->Id = $model->Id;
        return true;
    }
    
    private function updateLoadsMultipleCollPoints($oldPostcode, $newPostcode) {
        $loads = PolestarLoad::model()->findAll('JobId = :jid', array(':jid' => $this->Id));
        foreach ($loads as $load) {
            $sequence = explode(',', $load->CollectionSequence);
            $idx = array_search($oldPostcode, $sequence);
            if ($idx !== FALSE) {
                $sequence[$idx] = $newPostcode;
                
                $load->CollectionSequence = implode(',', $sequence);
                $load->save();
            }
        }
    }

    private $jobInfo;
    public function getJob() {
        $result = new PolestarJob();
        if (!empty($this->Id)) {
            if (!isset($this->jobInfo))
                $this->jobInfo = PolestarJob::model()->findByPk($this->Id);
            $result = $this->jobInfo;
        }
        return $result;
    }
    
    public function populate() {
        $model = PolestarJob::model()->with('CollectionPoints')->findByPk($this->Id);
        $this->Ref                  = $model->Ref;
        $this->PrintCentreId        = $model->PrintCentreId;
        $this->DeliveryDate         = $model->formatDate('DeliveryDate','d/m/Y');
        $this->ProviderId           = $model->ProviderId;
        //$this->SupplierId           = $model->SupplierId;
        $this->VehicleId            = $model->VehicleId;
        
        $this->ClearHighlighting = '';
        
        $id = array( 0 );
        $seq = array( 0 );
        $pc = array($model->CollPostcode);
        $address = array($model->CollAddress);
        $company = array($model->CollCompany);
        $time = array($model->CollScheduledTime);
        $instructions = array($model->SpecialInstructions);
        
        if (isset($model->CollectionPoints)) {
            foreach ($model->CollectionPoints as $p) {
                $id[] = $p->Id;
                $seq[] = $p->Sequence;
                $pc[] = $p->CollPostcode;
                $address[] = $p->CollAddress;
                $company[] = $p->CollCompany;
                $time[] = $p->CollScheduledTime;
                $instructions[] = $p->SpecialInstructions;
            }
        }
        
        $this->CollId               = $id;
        $this->CollSeq              = $seq;
        $this->CollPostcode         = $pc;
        $this->CollAddress          = $address;
        $this->CollCompany          = $company;
        $this->CollScheduledTime    = $time;
        $this->SpecialInstructions  = $instructions;
        
        $this->Comments = $model->Comments;
    }
}
