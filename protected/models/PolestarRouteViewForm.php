<?php
/**
 * Description of PolestarRouteViewForm
 *
 * @author ramon
 */
class PolestarRouteViewForm extends CFormModel {

    public $planningDate;

    public $printCentre;
    public $printCentreId;

    public $jobsData;

    public function attributeLabels()
    {
        return array(
            'printCentreId' => 'Print Centre',
        );
    }

    public function rules()
    {
        return array(
            array('planningDate, printCentreId', 'required'),
        );
    }

    public function getPrintCentre() {
        if (!isset($this->printCentre) && !empty($this->printCentreId)) {
            $this->printCentre = PolestarPrintCentre::model()->findByPk($this->printCentreId);
        }
        return $this->printCentre;
    }

    public function populateJobs() {

        $this->jobsData = array();

        $crit = new CDbCriteria();
        $crit->alias = 'j';
        $dt = DateTime::createFromFormat('d/m/Y', $this->planningDate);
        $dbDate = $dt->format('Y-m-d');

        // delivery date = creation date = planning date
        $crit->AddCondition("j.PrintCentreId = :pci AND j.CreationDate = :dt AND j.DeliveryDate = :dt");
        // delivery date = planning date + 1
        $crit->AddCondition("j.PrintCentreId = :pci AND j.DeliveryDate = date_add(:dt, interval 1 day)", 'OR');
        
        $crit->params = array(
            ':dt' => $dbDate,
            ':pci' => $this->printCentreId,
        );
        $crit->order = 'ifnull(Provider.ProviderSortOrder,999) ASC, j.DeliveryDate ASC, j.CollScheduledTime ASC, j.Ref ASC';

        $this->jobsData = PolestarJob::model()
                            ->with('Vehicle','Provider','Supplier','Status', 'CollectionPoints', 'Loads', 'EditedByLogin')
                            ->findAll($crit);
    }
    
    public function getSingleJob($jobId) {
        return PolestarJob::model()
                    ->with('Vehicle','Provider','Supplier','Status', 'CollectionPoints', 'Loads', 'EditedByLogin')
                    ->findByPk($jobId);
    }
}
