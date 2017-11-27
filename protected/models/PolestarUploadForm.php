<?php
/**
 * Description of PolestarUploadForm
 *
 * @author ramon
 */
class PolestarUploadForm extends CFormModel {
    public $spreadsheet;
    public $printCentreId;
    public $planningDate;

    public $uploadedFileName;
    private $fileData;

    public function rules() {
        return array(
            array('spreadsheet', 'file', 'types'=>'xls, xlsx'),
            array('spreadsheet', 'md5Unique'),
        );
    }

    public function md5Unique() {
        $md5 = md5_file($this->spreadsheet->tempName);
        $file = PolestarUploadedFile::model()->findByAttributes(array(
            'Md5' => $md5,
        ));
        if (isset($file)) {
            $this->addError('spreadsheet','The file has already been imported.');
        }
    }

    private function getValidationMsg($row, $field, $value, $type) {
        return sprintf('Row %03d: Invalid value: %s (%s) - expected %s', $row, $field, $value, $type);
    }

    public function validateSheetContents()
    {
        $data = $this->getFileData();
        // validate sheet contents
        foreach ($data as $idx => $row) {
            // validate integer values
            $fields = array('OrderNo', 'Quantity', 'Pallets', 'PalletsFull', 'PalletsHalf', 'PalletsQtr', 'Kg');
            foreach ($fields as $field) {
                if (!empty($row->$field) && (!is_numeric($row->$field) || (intval($row->$field) != $row->$field)))
                    $this->addError('spreadsheet', $this->getValidationMsg ($idx+1, $field, $row->$field, 'numeric integer'));
            }
            // validate dates
            $fields = array('DeliveryDate');
            foreach ($fields as $field) {
                if (!empty($row->$field)) {
                    $dt = DateTime::createFromFormat('d/m/Y', $row->$field);
                    if (($dt === FALSE) || ($dt->format('d/m/Y') != $row->$field))
                        $this->addError('spreadsheet', $this->getValidationMsg ($idx+1, $field, $row->$field, 'dd/mm/yyyy'));
                }
            }
            // validate times
            $fields = array('DelScheduledTime', 'CollScheduledTime');
            foreach ($fields as $field) {
                if (!empty($row->$field) &&
                        (((strlen($row->$field) < 3) || (strlen($row->$field) > 4)) ||
                        (intval($row->$field) > 2400)))
                    $this->addError('spreadsheet', $this->getValidationMsg ($idx+1, $field, $row->$field, 'hhmm (or hmm)'));
            }
        }

        // TODO: validate sheet not imported already

        return !($this->hasErrors());
    }

    private function getFileData() {
        if (!isset($this->fileData)) {
            $processor = new PolestarJobXlsParser();
            $processor->fileName = $this->uploadedFileName;
            $this->fileData = $processor->getData();
        }
        return $this->fileData;
    }

    public function importFile() {

        $fileInfo = new PolestarUploadedFile();
        $fileInfo->Md5          = md5_file($this->uploadedFileName);
        $fileInfo->FileName     = $this->uploadedFileName;
        $fileInfo->UploadedBy   = Yii::app()->user->loginId;
        $fileInfo->UploadedDate = new CDbExpression('NOW()');
        $fileInfo->insert();

        $result = array (
            'status' => 'success',
            'dates' => array(),
        );

        $aktrionSupplierId = Setting::get('polestar', 'aktrion-supplier-id');

        $data = $this->getFileData();

        $pc = PolestarPrintCentre::model()->findByPk($this->printCentreId);
        $defaultPostcode = $pc->Postcode;
        $defaultCollAddress = $pc->getSingleLineAddress();

        // group items by OrderNo
        $jobs = array();
        foreach ($data as $row) {
            if (!isset($jobs[$row->OrderNo]))
                $jobs[$row->OrderNo] = array();
            $jobs[$row->OrderNo][] = $row;
        }

        // process orders
        foreach ($jobs as $orderNo => $loads) {
            $jobInfo = $loads[0];
            $job = new PolestarJob();

            $job->Ref             = PolestarJob::getValidReference($this->printCentreId, $jobInfo->DeliveryDate); //$this->getValidReference(); //$this->Ref;
            $job->OriginalOrderNo = $jobInfo->OrderNo;
            $job->CreatedBy       = Yii::app()->user->loginId;
            $job->CreatedDate     = new CDbExpression('NOW()');
            $job->CreationDate    = new CDbExpression('NOW()');
            $job->PrintCentreId   = $this->printCentreId;
            $job->DeliveryDate    = new CDbExpression("STR_TO_DATE(:dt, '%d/%m/%Y')", array(':dt' => $jobInfo->DeliveryDate));

            if (!empty($jobInfo->Provider)) {
                $provider = PolestarSupplier::model()->find('Name = :name OR Code = :name', array(':name' => $jobInfo->Provider));
                if (isset($provider)) {
                    $job->ProviderId = $provider->Id;
                    if ($provider->Id != $aktrionSupplierId) // #168: Polestar Service provider Selection
                        $job->SupplierId      = $provider->Id;

                }
            }

            $vehicle = PolestarVehicle::model()->find('Name = :name', array(':name' => $jobInfo->Vehicle));
            if (isset($vehicle))
                $job->VehicleId = $vehicle->Id;

            $job->CollPostcode        = $jobInfo->CollPostcode;
            if ($job->CollPostcode == $defaultPostcode) {
                $job->CollAddress         = $defaultCollAddress;
                //$job->CollCompany         = $jobInfo->CollCompany; // deafult to print centre's?
            }
            $job->CollScheduledTime   = !empty($jobInfo->CollScheduledTime) ? "{$jobInfo->CollScheduledTime}00" : NULL;

            $job = PolestarStatusUpdater::saveJobDetails($job, $jobInfo->DeliveryDate);

            $fileInfoJob = new PolestarUploadedFileJob();
            $fileInfoJob->FileId = $fileInfo->Id;
            $fileInfoJob->JobId  = $job->Id;
            $fileInfoJob->insert();

            if (!in_array($jobInfo->DeliveryDate, $result['dates']))
                $result['dates'][] = $jobInfo->DeliveryDate;

            foreach ($loads as $idx => $loadInfo) {
                $load = new PolestarLoad();
                $load->JobId = $job->Id;
                $load->Ref = $loadInfo->LoadRef;
                $load->CreatedBy       = Yii::app()->user->loginId;
                $load->CreatedDate     = new CDbExpression('NOW()');

                //$load->JobTypeId = -- // not on import file atm

                $load->Publication = $loadInfo->Publication;
                $load->Quantity = $loadInfo->Quantity;

                // calculate total pallets
                $load->PalletsFull = empty($loadInfo->PalletsFull) ? 0 : $loadInfo->PalletsFull;
                $load->PalletsHalf = empty($loadInfo->PalletsHalf) ? 0 : $loadInfo->PalletsHalf;
                $load->PalletsQtr = empty($loadInfo->PalletsQtr) ? 0 : $loadInfo->PalletsQtr;
                $load->PalletsTotal = empty($loadInfo->Pallets) ? ($load->PalletsFull+$load->PalletsHalf+$load->PalletsQtr) : $loadInfo->Pallets;
                
                $total = intval($load->PalletsTotal);
                $sum = intval($load->PalletsQtr) + intval($load->PalletsHalf) + intval($load->PalletsFull);
                if ($total >= $sum)
                    $load->PalletsFull = $total - (intval($load->PalletsQtr) + intval($load->PalletsHalf));
                else 
                    $load->PalletsTotal = $sum;

                $load->Kg = $loadInfo->Kg;
                $load->DelPostcode = $loadInfo->DelPostcode;
                $load->DelAddress = $loadInfo->DelArea;
                $load->DelCompany = $loadInfo->DelCompany;
                $load->DelScheduledTime = (!empty($loadInfo->DelScheduledTime)) ? "{$loadInfo->DelScheduledTime}00" : NULL;
                $load->DelTimeCode = $loadInfo->DelTimeCode;
                $load->BookingRef = $loadInfo->BookingRef;
                $load->SpecialInstructions = $loadInfo->SpecialInstructions;
                $load->Sequence = ($idx + 1);

                $load->save();

                $fileInfoLoad = new PolestarUploadedFileLoad();
                $fileInfoLoad->FileId = $fileInfo->Id;
                $fileInfoLoad->LoadId  = $load->Id;
                $fileInfoLoad->insert();

                PolestarDeliveryPointForm::ensureExists($load->DelPostcode, $load->DelAddress, $load->DelCompany);

            }
        }

        return $result;
    }

}
