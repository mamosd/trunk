<?php
/**
 * Description of PalletsReport
 *
 * @author ramon
 */
class PalletsReport extends CFormModel
{
    public $supplierId;
    public $printCentre;
    public $dateFrom;
    public $dateTo;
    public $deliveryPoint;
    
    function init(){
        $this->dateFrom = '01/03/2012';
        $this->dateTo = date('d/m/Y');
    }
    
    public function rules()
    {
        return array(
            array('dateFrom, dateTo', 'required'),
        );
    }
    
    public function getReportDp($getCsv=false)
    {
        $sql = "select 	p.SupplierId,
			max(p.SupplierName) as SupplierName,
			p.TitleId,
			max(p.TitleName) as TitleName,
			p.PrintCentreId,
			max(p.PrintCentreName) as PrintCentreName,
			p.DeliveryPointId,
			max(p.DeliveryPointName) as DeliveryPointName,
			ifnull(sum(p.PlasticDelivered),0) as PlasticDelivered,
			ifnull(sum(p.PlasticCollected),0) as PlasticCollected,
			(ifnull(sum(p.PlasticCollected),0) - ifnull(sum(p.PlasticDelivered),0)) as PlasticBalance,
			ifnull(sum(p.WoodenDelivered),0) as WoodenDelivered,
			ifnull(sum(p.WoodenCollected),0) as WoodenCollected,
			(ifnull(sum(p.WoodenCollected),0) - ifnull(sum(p.WoodenDelivered),0)) as WoodenBalance,
                        group_concat(distinct if(NoteNumber='',null,NoteNumber)) as NoteNumbers
                from allpalletreport p
                where Date BETWEEN STR_TO_DATE('{$this->dateFrom}', '%d/%m/%Y') and STR_TO_DATE('{$this->dateTo}', '%d/%m/%Y')";
                
        if ($this->supplierId !== '')                
            $sql .= " AND SupplierId = {$this->supplierId} ";
            
        if ($this->printCentre !== '')
            $sql .= " AND PrintCentreId = {$this->printCentre} ";
        
        if ($this->deliveryPoint !== '')
            $sql .= " AND DeliveryPointId = {$this->deliveryPoint} ";            
            
        $sql .= "group by p.SupplierId, p.TitleId, p.PrintCentreId, p.DeliveryPointId";

        if($getCsv){

            $headers = array(
                'Print Centre',
                'Supplier',
                'Title',
                'Delivery Point',
                'Plastic Delivered',
                'Plastic Collected',
                'Plastic Balance',
                'Wooden Delivered',
                'Wooden Collected',
                'Wooden Balance',
                'Note Numbers'
            );
            $res = Yii::app()->db->createCommand($sql)->queryAll();
            $data = array();
            foreach($res as $v){
                $data[] = array(
                    $v['PrintCentreName'],
                    $v['SupplierName'],
                    $v['TitleName'],
                    $v['DeliveryPointName'],
                    $v['PlasticDelivered'],
                    $v['PlasticCollected'],
                    $v['PlasticBalance'],
                    $v['WoodenDelivered'],
                    $v['WoodenCollected'],
                    $v['WoodenBalance'],
                    $v['NoteNumbers']
                );
            }
            $csv = $this->getCsv($data, $headers);
            header('Content-Type: text/csv; charset=utf-8');
            header('Content-Disposition: attachment;filename="suplier-center-report-' . date('YmdHi') . '.csv"');
            echo $csv;
            Yii::app()->end();

        }else{

            $dataProvider = new CSqlDataProvider($sql, array(
                'pagination' => false,
                'keyField' => 'SupplierId'
            ));

            /*
            $criteria = new CDbCriteria();
            $cols = array();
            if ($this->supplierId !== '')
                $cols['SupplierId'] = $this->supplierId;
            if ($this->printCentre !== '')
                $cols['PrintCentreId'] = $this->printCentre;
            if (!empty($cols))
                $criteria->addColumnCondition($cols);

            $df = "STR_TO_DATE('{$this->dateFrom}', '%d/%m/%Y')";
            $dt = "STR_TO_DATE('{$this->dateTo}', '%d/%m/%Y')";
            $criteria->addBetweenCondition('Date', $df, $dt);

            $dataProvider = new CActiveDataProvider('AllPalletReportSummary', array(
                        'criteria' => $criteria,
                        'pagination'=>false,
                    ));
            */
            return $dataProvider;
        }
    }

    public function getCsv($data, $headers){
        $delimiter = ',';
        $result = '';
        array_unshift($data, $headers);
        foreach($data as $k=>$d){
            if($k>0) $result .= "\n";
            foreach($headers as $kh=>$h){
                if($kh>0) $result .= $delimiter;
                $cell = isset($d[$kh]) ? trim($d[$kh]) : '';
                $result .= "\"$cell\"";
            }
        }
        return $result;
    }
    
    public function getOptionsSupplier()
    {
        $result = array();
        $criteria = new CDbCriteria();
        $criteria->order = "Name ASC";
        $suppliers = Supplier::model()->findAll($criteria);
        foreach ($suppliers as $supplier) {
            $result[$supplier->SupplierId] = $supplier->Name;
        }
        return $result;
    }
    
    public function getOptionsPrintCentre()
    {
        $result = array();
        $criteria = new CDbCriteria();
        $criteria->order = "Name ASC";
        $pcs = PrintCentre::model()->findAll($criteria);
        foreach ($pcs as $pc) {
            $result[$pc->PrintCentreId] = $pc->Name;
        }
        return $result;
    }
    
    public function getOptionsDeliveryPoint()
    {
        $result = array();
        $criteria = new CDbCriteria();
        $criteria->order = "Name ASC";
        $dps = DeliveryPoint::model()->findAll($criteria);
        foreach ($dps as $dp) {
            $result[$dp->DeliveryPointId] = $dp->Name;
        }
        return $result;        
    }
    
    public function getSearchCriteria()
    {
        $result = array();
        if (!empty($this->supplierId))
                $result["Supplier"] = Supplier::model()->find("SupplierId = :sid", array(":sid" => $this->supplierId))->Name;
        if (!empty($this->printCentre))
                $result["Print Centre"] = PrintCentre::model()->find("PrintCentreId = :pid", array(":pid" => $this->printCentre))->Name;
        if (!empty($this->deliveryPoint))
                $result["Delivery Point"] = DeliveryPoint::model()->find("DeliveryPointId = :dpid", array(":dpid" => $this->deliveryPoint))->Name;
        $result["Date Range"] = $this->dateFrom." to ".$this->dateTo;        
        
        return $result;        
    }
    
}

?>
