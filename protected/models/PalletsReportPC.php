<?php
/**
 * Description of PalletsReportPC
 *
 * @author ramon
 */
class PalletsReportPC extends CFormModel
{
    public $supplierId;
    public $printCentre;
    public $route;
    public $dateFrom;
    public $dateTo;
    
    public function rules()
    {
        return array(
            array('dateFrom, dateTo', 'required'),
        );
    }
    
    public function getReportDp($getCsv=false)
    {
        $sql = "select 	p.PrintCentreId,
			max(p.PrintCentreName) as PrintCentreName,
			p.RouteId,
			max(p.RouteName) as RouteName,
			p.SupplierId,
			max(p.SupplierName) as SupplierName,
			ifnull(sum(p.PlasticSentOut),0) as PlasticSentOut,
			ifnull(sum(p.PlasticReturned),0) as PlasticReturned,
			(ifnull(sum(p.PlasticReturned),0) - ifnull(sum(p.PlasticSentOut),0)) as PlasticBalance,
			ifnull(sum(p.WoodenSentOut),0) as WoodenSentOut,
			ifnull(sum(p.WoodenReturned),0) as WoodenReturned,
			(ifnull(sum(p.WoodenReturned),0) - ifnull(sum(p.WoodenSentOut),0)) as WoodenBalance			
                from allpalletreportpc p
                where Date BETWEEN STR_TO_DATE('{$this->dateFrom}', '%d/%m/%Y') and STR_TO_DATE('{$this->dateTo}', '%d/%m/%Y')";
                
        if ($this->supplierId !== '')                
            $sql .= " AND SupplierId = {$this->supplierId} ";
            
        if ($this->printCentre !== '')
            $sql .= " AND PrintCentreId = {$this->printCentre} ";
        
        if ($this->route !== '')
            $sql .= " AND RouteId = {$this->route} ";
            
        $sql .= "group by p.SupplierId, p.RouteId, p.PrintCentreId";

        if($getCsv){

            $headers = array(
                'Print Centre',
                'Supplier',
                'Route',
                'Plastic Sent Out',
                'Plastic Returned',
                'Plastic Balance',
                'Wooden Sent Out',
                'Wooden Returned',
                'Wooden Balance'
            );
            $res = Yii::app()->db->createCommand($sql)->queryAll();
            $data = array();
            foreach($res as $v){
                $data[] = array(
                    $v['PrintCentreName'],
                    $v['SupplierName'],
                    $v['RouteName'],
                    $v['PlasticSentOut'],
                    $v['PlasticReturned'],
                    $v['PlasticBalance'],
                    $v['WoodenSentOut'],
                    $v['WoodenReturned'],
                    $v['WoodenBalance']
                );
            }
            $csv = $this->getCsv($data, $headers);
            header('Content-Type: text/csv; charset=utf-8');
            header('Content-Disposition: attachment;filename="print-center-report-' . date('YmdHi') . '.csv"');
            echo $csv;
            Yii::app()->end();
        }else{

            $dataProvider = new CSqlDataProvider($sql, array(
                'pagination' => false,
                'keyField' => 'SupplierId'
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
                $result .= $cell;
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
    
    public function getSearchCriteria()
    {
        $result = array();
        if (!empty($this->supplierId))
                $result["Supplier"] = Supplier::model()->find("SupplierId = :sid", array(":sid" => $this->supplierId))->Name;
        if (!empty($this->printCentre))
                $result["Print Centre"] = PrintCentre::model()->find("PrintCentreId = :pid", array(":pid" => $this->printCentre))->Name;
        $result["Date Range"] = $this->dateFrom." to ".$this->dateTo;        
        
        return $result;        
    }


    
}

?>
