<?php
/**
 * Description of PolestarActivityLogForm
 *
 * @author ramon
 */
class PolestarActivityLogForm extends CFormModel {
    
    public $printCentres;
    public $users;
    public $statuses;
    public $hourRange;
    public $dateFrom;
    public $dateTo;
    public $fullBlown = TRUE;
    
    /*public function initialize() {
        $this->dateFrom = date('d/m/Y');
        $this->dateTo = $this->dateFrom;
    }*/
    
    public function getData() {
        
        Yii::app()->session['activitylog-criteria'] = $this->getAttributes();
        
        $dt = new DateTime();
        $dt = $dt->modify("+1 day");
        
        $fromDt = date('Y-m-d 00:00:00');
        $toDt = $dt->format('Y-m-d 00:00:00');
        
        if (!empty($this->hourRange)) {
            $hoursToList = $this->hourRange;
            $dtf = new DateTime();
            $dtf = $dtf->modify("-$hoursToList hour");
            $fromDt = $dtf->format('Y-m-d H:i:s');
        }
        else {
            $dtf = DateTime::createFromFormat('d/m/Y', $this->dateFrom);
            if ($dtf === FALSE)
                $dtf = new DateTime();
            $dtt = DateTime::createFromFormat('d/m/Y', $this->dateTo);
            if ($dtt === FALSE)
                $dtt = new DateTime();
            $dtt = $dtt->modify("+1 day");
            $fromDt = $dtf->format('Y-m-d 00:00:00');
            $toDt = $dtt->format('Y-m-d 00:00:00');
        }
        
//        var_dump($this->getAttributes());die;
//        var_dump(array($fromDt, $toDt));die;
        
        $crit = new CDbCriteria();
        $crit->alias = 'j';
        $crit->addCondition("(ifnull(j.EditedDate, j.CreatedDate) > :dtf AND ifnull(j.EditedDate, j.CreatedDate) < :dtt)");
        $crit->params = array(':dtf' => $fromDt, ':dtt' => $toDt);
        
        $pcs = array();
        if (isset($this->printCentres))
            $pcs = explode('|', $this->printCentres);
        else {
            $pcl = PolestarPrintCentre::getAllForLoginAsOptions();
            $pcs = array_keys($pcl);
        }
        $pcFilter = implode('',$pcs);
        if (!empty($pcFilter))
            $crit->addInCondition('j.PrintCentreId', $pcs);
        
        if (isset($this->users)) {
            $usrs = explode('|', $this->users);
            $usrFilter = implode('', $usrs);
            if (!empty($usrFilter))
                $crit->addCondition("ifnull(j.EditedBy, j.CreatedBy) in ('".implode("','", $usrs)."')");
        }
        
        if (isset($this->statuses)) {
            $stts = explode('|', $this->statuses);
            $sttFilter = implode('', $stts);
            if (!empty($sttFilter))
                $crit->addInCondition('j.StatusId', $stts);
        }
        $crit->order = 'ifnull(j.EditedDate, j.CreatedDate) DESC';
        
        return PolestarJob::model()->with('PrintCentre', 'Vehicle','Provider','Supplier','Status', 'CreatedByLogin', 'EditedByLogin')->findAll($crit);
    }
    
}
