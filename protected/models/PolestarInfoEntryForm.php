<?php
/**
 * Description of PolestarInfoEntryForm
 *
 * @author ramon
 */
class PolestarInfoEntryForm extends CFormModel {
    
    public $jobId;
    public $jobInfo;
    
    public $mileage;
    public $specialInstructions;
    public $newComment;
    
    public $supplier;
    public $driverName;
    public $vehicleRegNo;
    public $contactNo;
    
    public $agreedPrice;
    
    public $arrivalTime; // collection
    public $departureTime; // collection
    public $collMileage;
    
    public $clearHighlighting;
    
    public $loads = array();
    
    public function attributeLabels()
    {
        return array();
    }

    public function rules()
    {
        return array(
            //array('mileage', 'required'),
            array('agreedPrice', 'numerical'),
        );
    }
    
    public function populate() {
        $job = PolestarJob::model()->with('Comments', 'Loads', 'CollectionPoints')->findByPk($this->jobId);
        $this->jobInfo = $job;
        $this->clearHighlighting = '';
        if (isset($job)) {
            $this->mileage = $job->TotalMileage;
            $this->specialInstructions = $job->SpecialInstructions;
            
            $this->supplier = $job->SupplierId;
            $this->driverName = $job->DriveName;
            $this->vehicleRegNo = $job->VehicleRegNo;
            $this->contactNo = $job->ContactNo;
            
            $this->arrivalTime = array();
            $this->departureTime = array();
            $this->collMileage = array();
            $this->arrivalTime[] = $job->CollArrivalTime;
            $this->departureTime[] = $job->CollDepartureTime;
            $this->collMileage[] = $job->Mileage;
            foreach ($job->CollectionPoints as $point) {
                $this->arrivalTime[$point->Id] = $point->CollArrivalTime;
                $this->departureTime[$point->Id] = $point->CollDepartureTime;
                $this->collMileage[$point->Id] = $point->Mileage;
            }
            
            $this->agreedPrice = $job->AgreedPrice;
        }
    }
    
    public function save() {
        $job = PolestarJob::model()->findByPk($this->jobId);
        if (isset($job)) {
            $job->TotalMileage = $this->mileage;
            $job->SpecialInstructions = $this->specialInstructions;
            
            $originalSupplierId = $job->SupplierId;
            $job->SupplierId = $this->supplier;
            $job->DriveName = $this->driverName;
            $job->VehicleRegNo = $this->vehicleRegNo;
            $job->ContactNo = $this->contactNo;
            
            $job->CollArrivalTime = (!empty($this->arrivalTime[0])) ? $this->arrivalTime[0] : NULL;
            $job->CollDepartureTime = (!empty($this->departureTime[0])) ? $this->departureTime[0] : NULL;
            $job->Mileage = (trim($this->collMileage[0]) != '') ? $this->collMileage[0] : NULL;
            
            $job->AgreedPrice = (!empty($this->agreedPrice)) ? $this->agreedPrice : NULL;
            
            if (!empty($this->clearHighlighting))
                $job->ClearHighlighting = $this->clearHighlighting;
            
            $job->save();
            
            $missingLoadTimes = (empty($job->CollArrivalTime) || empty($job->CollDepartureTime));
            foreach ($this->arrivalTime as $pointId => $arrivalTime) {
                $point = PolestarJobCollectionPoint::model()->findByPk($pointId);
                if (isset($point)) {
                    $point->CollArrivalTime = (!empty($arrivalTime)) ? $arrivalTime : NULL;
                    $point->CollDepartureTime = (!empty($this->departureTime[$pointId])) ? $this->departureTime[$pointId] : NULL;
                    $point->Mileage = (trim($this->collMileage[$pointId]) != '') ? $this->collMileage[$pointId] : NULL;
                    $point->save();
                    if (empty($point->CollArrivalTime) || empty($point->CollDepartureTime))
                        $missingLoadTimes = TRUE;
                }
            }
            
            // update to ALLOCATED when assigned a supplier
            if (empty($originalSupplierId) && ($originalSupplierId != $job->SupplierId))
                PolestarStatusUpdater::updateJobToStatus($this->jobId, PolestarStatus::ALLOCATED_ID);
            
            // #199: revert to previous status if allocated and supplier cleared
            if (empty($job->SupplierId) && ($job->StatusId == PolestarStatus::ALLOCATED_ID)) {
                $prevStatusId = $job->getPreviousStatus();
                PolestarStatusUpdater::updateJobToStatus($this->jobId, $prevStatusId);
            }
            
            // update to CONFIRMED when entering driver/vehicle/contactno
            if (!empty($this->driverName) || !empty($this->vehicleRegNo) || !empty($this->contactNo))
                PolestarStatusUpdater::updateJobToStatus($this->jobId, PolestarStatus::CONFIRMED_ID);
            
            if (!empty($this->newComment)) {
                $c = new PolestarJobComment();
                $c->JobId       = $this->jobId;
                $c->Comment     = $this->newComment;
                $c->CreatedBy   = Yii::app()->user->loginId;
                $c->CreatedDate = new CDbExpression('NOW()');
                $c->save();
            }
            
            // save load entered information
            //$missingLoadTimes = (empty($job->CollArrivalTime) || empty($job->CollDepartureTime));
            foreach ($this->loads as $id => $load) {
                $l = PolestarLoad::model()->findByPk($id);
                if (isset($l)) {
                    $l->DelArrivalTime = (!empty($load['arrival'])) ? $load['arrival'] : NULL;
                    $l->DelDepartureTime = (!empty($load['departure'])) ? $load['departure'] : NULL;
                    $l->Mileage = $load['mileage'];
                    $l->SpecialInstructions = $load['instructions'];
                    
                    $l->save();
                    
                    if (empty($l->DelArrivalTime) || empty($l->DelDepartureTime))
                        $missingLoadTimes = TRUE;
                    
                    if (!empty($load['comment'])) {
                        $c = new PolestarLoadComment();
                        $c->LoadId      = $l->Id;
                        $c->Comment     = $load['comment'];
                        $c->CreatedBy   = Yii::app()->user->loginId;
                        $c->CreatedDate = new CDbExpression('NOW()');
                        $c->save();
                    }
                }
            }
            
            if ((count($this->loads) > 0) && ($missingLoadTimes === FALSE)) {
                PolestarStatusUpdater::updateJobToStatus($this->jobId, PolestarStatus::DATA_COMPLETED_ID);
            }
        }
        return true;
    }
}
