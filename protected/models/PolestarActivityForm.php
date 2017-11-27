<?php
/**
 * Description of PolestarActivityForm
 *
 * DEPRECATED - Replaced by ActivityLogForm
 * 
 * @author ramon
 */
class PolestarActivityForm {
    
    public $printCentreId = array();
    
    public function getData() {
        
        $minutesToList = Setting::get('polestar', 'activity-minutes');
        $dt = new DateTime();
        $dt = $dt->modify("-$minutesToList minute");
        $fromDt = $dt->format('Y-m-d H:i:s');
        
        $statusToList = Setting::get('polestar', 'activity-status');
        $statuses = explode(';', $statusToList);
        $statusIn = implode("','", $statuses);
        
        $pcToList = array();
        if ($this->printCentreId > 0) {
            $pcToList[] = $this->printCentreId;
        }
        else {
            $allPcs = PolestarPrintCentre::getAllForLoginAsOptions();
            foreach ($allPcs as $key => $name)
                $pcToList[] = $key;
        }
        $pcIn = implode("','", $pcToList);
        
        $crit = new CDbCriteria();
        $crit->alias = 'j';
        $crit->condition = "j.PrintCentreId IN ('".$pcIn."') AND (j.CreatedDate > :dt OR j.StatusId IN ('".$statusIn."'))";
        $crit->params = array(':dt' => $fromDt);
        $crit->order = 'ifnull(Provider.ProviderSortOrder,999) ASC, j.CreatedDate DESC';
        
        return PolestarJob::model()->with('Vehicle','Provider','Supplier','Status', 'CreatedByLogin')->findAll($crit);
    }
}
