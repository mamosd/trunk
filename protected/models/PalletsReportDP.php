<?php
/**
 * Description of PalletsReportPC
 *
 * @author ramon
 */
class PalletsReportDP extends CFormModel
{
    public $supplierId;
    public $printCentre;
    public $route;
    public $deliveryPoint;
    public $dateFrom;
    public $dateTo;
    public $byDay;
    
    function init(){
        $this->dateFrom = '01/03/2012';
        $this->dateTo = date('d/m/Y');
    }
    
    public function attributeLabels()
    {
        return array(
            'byDay' => 'Output results by day'
        );
    }
    
    public function rules()
    {
        return array(
            array('dateFrom, dateTo', 'required'),
        );
    }
    
    public function getReportDp($getCsv=false)
    {
        $sql = "select 	DeliveryPointId,
			DeliveryPointName,
			`Date`,
			sum(ifnull(PlasticDelivered,0)) as PlasticDelivered,
			sum(ifnull(PlasticCollected,0)) as PlasticCollected,
			sum(ifnull(PlasticCollected,0)-ifnull(PlasticDelivered,0)) as PlasticBalance,
			sum(ifnull(WoodenDelivered,0)) as WoodenDelivered,
			sum(ifnull(WoodenCollected,0)) as WoodenCollected,
			sum(ifnull(WoodenCollected,0)-ifnull(WoodenDelivered,0)) as WoodenBalance,
                        group_concat(distinct if(NoteNumber='',null,NoteNumber)) as NoteNumbers
                from allpalletreport p
                where p.`Date` BETWEEN STR_TO_DATE('{$this->dateFrom}', '%d/%m/%Y') and STR_TO_DATE('{$this->dateTo}', '%d/%m/%Y')";
                
        if ($this->supplierId !== '')                
            $sql .= " AND SupplierId = {$this->supplierId} ";
            
        if ($this->printCentre !== '')
            $sql .= " AND PrintCentreId = {$this->printCentre} ";
        
        if ($this->route !== '')
            $sql .= " AND RouteId = {$this->route} ";
            
        if ($this->deliveryPoint !== '')
            $sql .= " AND DeliveryPointId = {$this->deliveryPoint} ";
            
        $addGroup = '';
        if ($this->byDay)
            $addGroup = ' , `Date` ';
        $sql .= "group by DeliveryPointId $addGroup order by `DeliveryPointName`, `Date`";

        if($getCsv){

            $headers = array(
                'Delivery Point',
                'Date', 
                'Plastic Delivered',
                'Plastic Collected',
                'Plastic Balance',
                'Wooden Delivered',
                'Wooden Collected',
                'Wooden Balance',
                'Note Numbers'
                );
            if (!$this->byDay)
                unset($headers[1]);
            
            $res = Yii::app()->db->createCommand($sql)->queryAll();
            $data = array();
            foreach($res as $v){
                $row = array(
                    $v['DeliveryPointName'],
                    $v['Date'],
                    $v['PlasticDelivered'],
                    $v['PlasticCollected'],
                    $v['PlasticBalance'],
                    $v['WoodenDelivered'],
                    $v['WoodenCollected'],
                    $v['WoodenBalance'],
                    $v['NoteNumbers']
                );
                
                if (!$this->byDay)
                    unset($row[1]);
                
                $data[] = $row;
            }
            $csv = $this->getCsv($data, $headers);
            header('Content-Type: text/csv; charset=utf-8');
            header('Content-Disposition: attachment;filename="delivery-point-report-' . date('YmdHi') . '.csv"');
            echo $csv;
            Yii::app()->end();
        }else{

            $dataProvider = new CSqlDataProvider($sql, array(
                'pagination' => false,
                'keyField' => 'DeliveryPointId'
            ));

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
    
    public function getOptionsRoute()
    {
        $result = array();
        $criteria = new CDbCriteria();
        $criteria->order = "Name ASC";
        $routes = Route::model()->findAll($criteria);
        foreach ($routes as $route) {
            $result[$route->RouteId] = $route->Name;
        }
        return $result;
    }
    
    
    public function getOptionsDeliveryPoint()
    {
        $result = array();
        $criteria = new CDbCriteria();
        $criteria->order = "Name ASC";
        $dps = DeliveryPoint::model()->findAll($criteria);
        foreach($dps as $d)
            $result[$d->DeliveryPointId] = $d->Name;
        return $result;
    }
    
    public function getSearchCriteria()
    {
        $result = array();
        if (!empty($this->supplierId))
                $result["Supplier"] = Supplier::model()->find("SupplierId = :sid", array(":sid" => $this->supplierId))->Name;
        if (!empty($this->printCentre))
                $result["Print Centre"] = PrintCentre::model()->find("PrintCentreId = :pid", array(":pid" => $this->printCentre))->Name;
        if (!empty($this->route))
                $result["Route"] = Route::model ()->findByPk($this->route)->Name;
        $result["Date Range"] = $this->dateFrom." to ".$this->dateTo;        
        
        return $result;        
    }


    
}

?>
