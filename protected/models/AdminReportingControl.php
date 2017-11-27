<?php
/**
 * Description of AdminReportingControl
 *
 * @author Ramon
 */
class AdminReportingControl extends CFormModel
{
    public $details;
    public $showArchived;

    public $from;
    public $to;
    public $printCentre;

    public function populate($archived) {
        $showArchived = ($archived == '1');
				
				if (!isset($this->from))
					$this->from =	(date("w") != 1 ) ? date('d/m/Y', strtotime('last monday')) : date('d/m/Y');
				if (!isset($this->to))
					$this->to = (date("w") != 6 ) ? date('d/m/Y', strtotime('next sunday')) : date('d/m/Y');	
				
        $criteria = new CDbCriteria();
        $criteria->condition = "ifnull(DTDate, NextPublication) BETWEEN str_to_date(:from,'%d/%m/%Y') AND str_to_date(:to,'%d/%m/%Y')";
        $criteria->params = array(':from' => $this->from, ':to' => $this->to);
        if ($showArchived)
        {
            $criteria->condition .= ' AND Status=:stt';
            $criteria->params = array_merge($criteria->params, array(':stt' => RouteInstance::STATUS_ARCHIVED));
        }
        else
            $criteria->condition .= ' AND IsLive = 1';
        
        if (isset($this->printCentre) && ($this->printCentre != ""))
				{
					$criteria->condition .= ' AND PrintCentreId=:pcid';
					$criteria->params = array_merge($criteria->params, array(':pcid' => $this->printCentre));
				}
        
        $criteria->order = 'PrintCentreName, PrintDay, TitleName, RouteName';
        $criteria->distinct = true;

        $this->showArchived = $showArchived;
        $this->details = AllTitleControl::model()->findAll($criteria);
    }

    public function getOptionsPrintCentre()
    {
        $result = array();
        $criteria = new CDbCriteria();
        $criteria->order = 'Name ASC';
        $pcs = PrintCentre::model()->findAll($criteria);
        foreach ($pcs as $pc) {
            $result[$pc->PrintCentreId] = $pc->Name;
        }
        return $result;
    }
}
?>
