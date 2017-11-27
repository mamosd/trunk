<?php
/**
 * Description of PrintPalletsSheets
 *
 * @author ramon
 */
class PalletsSheetPrint extends CFormModel
{
    public $supplierId;
    
    public function attributeLabels()
    {
        return array(
            'supplierId'=>'Supplier',
        );
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
    
    public function generateSheetDownload()
    {
        $supplierInfo = Supplier::model()->find('SupplierId = :sid', array(':sid' => $this->supplierId));
        $supplierFileName = strtolower(str_replace (" ", "", $supplierInfo->Name));
        
        $dummy = new AllRouteDetails();
        
        // get a reference to the path of PHPExcel classes
        $phpExcelPath = Yii::getPathOfAlias('ext.phpexcel.Classes');
        // Turn off YII library autoload
        spl_autoload_unregister(array('YiiBase','autoload'));

        // PHPExcel_IOFactory
        require_once $phpExcelPath.'/PHPExcel.php';

        $objReader = PHPExcel_IOFactory::createReader('Excel2007');
        $objPHPExcel = $objReader->load('_uploads/templates/PalletSummaryTemplate.xlsx');
        
        /*******************************************************/
        // SHEET 1 - Pallets Returned/Collected SUMMARY
        /*******************************************************/
        
        $objPHPExcel->setActiveSheetIndex(0);
        
        $sheet = $objPHPExcel->getActiveSheet();
        // supplier name
        $sheet->setCellValue('A1', $supplierInfo->Name);
        $sheet->setCellValue('K1', $this->supplierId);
        
        // next sunday
        $sheet->setCellValue('E1', date('d/m/Y', strtotime("Next Sunday")));
        
        // titles + delivery point info
        $crit = new CDbCriteria();
        $crit->condition = 'SupplierId = :sid';
        $crit->params = array(':sid' => $this->supplierId);
        $items = AllRouteDetails::model()->findAll($crit);
        
        $firstExcelLine = 3; // based on the template
        $nbrItems = count($items);
        for ($i = 0; $i < $nbrItems; $i++)
        {
            $ln = $i + $firstExcelLine;
            $item = $items[$i];
            
            $sheet->setCellValue("A$ln", $item->PrintCentreName);
            $sheet->setCellValue("C$ln", $item->TitleName);
            $sheet->setCellValue("D$ln", $item->DeliveryPointName);
            
            $sheet->setCellValue("B$ln", "=E1-".(7 - $item->PrintDay));
            //$sheet->setCellValue("B$ln", "=DATE(YEAR(E1), MONTH(E1), DAY(E1)-".(7 - $item->PrintDay).")");
            
            $sheet->setCellValue("K$ln", $item->RouteId);
            $sheet->setCellValue("L$ln", $item->TitleId);
            $sheet->setCellValue("M$ln", $item->DeliveryPointId);
            $sheet->setCellValue("N$ln", $item->PrintDay);            
        }
        
        // add styling
        $last = $firstExcelLine + $nbrItems - 1;
        foreach (range('A', 'J') as $col) {
            //$sheet->duplicateStyle($sheet->getStyle('B4'), "B4:B$last");
            $sheet->duplicateStyle($sheet->getStyle("{$col}4"), "{$col}4:$col$last");
        }
        $sheet->getColumnDimension('A')->setAutoSize(true);
        $sheet->getColumnDimension('C')->setAutoSize(true);
        $sheet->getColumnDimension('D')->setAutoSize(true);
        
        // add cell locking / hiding
        $sheet->getProtection()->setSheet(true);
        $sheet->getStyle("E1")->getProtection()->setLocked(PHPExcel_Style_Protection::PROTECTION_UNPROTECTED); // week ending 
        foreach (range('E', 'J') as $col) {
            $sheet->getStyle("{$col}3:$col$last")->getProtection()->setLocked(PHPExcel_Style_Protection::PROTECTION_UNPROTECTED);
        }
        $sheet->getColumnDimension("K")->setVisible(false);
        $sheet->getColumnDimension("L")->setVisible(false);
        $sheet->getColumnDimension("M")->setVisible(false);
        $sheet->getColumnDimension("N")->setVisible(false);
        
        /*******************************************************/
        // SHEET 2 - Pallets RETURNED per print centre
        /*******************************************************/
        $objPHPExcel->setActiveSheetIndex(1);
        
        $sheet = $objPHPExcel->getActiveSheet();
        // print centre name
        $sheet->setCellValue('A1', $supplierInfo->Name);
        $sheet->setCellValue('K1', $this->supplierId);
        
        // next sunday
        $sheet->setCellValue('D1', date('d/m/Y', strtotime("Next Sunday")));
        
        $pcRoutes = $items;
        // collect title/s per supplier
        $routes = array();
        foreach ($pcRoutes as $route)
        {
            if (!isset($routes[$route->RouteId]))
                    $routes[$route->RouteId] = array("printcentre" => $route->PrintCentreName, 
                                                    "printcentreid" => $route->PrintCentreId,
                                                    "titles" => array());
            $routes[$route->RouteId]["titles"][] = array("titleid" => $route->TitleId, 
                                                        "name" => $route->TitleName,
                                                        "printday" => $route->PrintDay);
        }
        
        $firstExcelLine = 3; // based on the template
        $i = 0;
        foreach ($routes as $routeId => $item)
        {
            $ln = $i + $firstExcelLine;
            
            $titles = array();
            foreach($item["titles"] as $title)
                if (!in_array($title["name"], $titles))
                    $titles[] = $title["name"];
            $titles = implode("\n", $titles);
            
            $sheet->setCellValue("C$ln", $titles);
            $sheet->setCellValue("D$ln", $item["printcentre"]);
            
            $printDay = $item["titles"][0]['printday'];
            $sheet->setCellValue("B$ln", "=D1-".(7 - $printDay));
            
            $sheet->setCellValue("K$ln", $routeId);
            $sheet->setCellValue("L$ln", $item["printcentreid"]);
            $sheet->setCellValue("M$ln", $printDay);            
            
            $i++;
        }
       
        // add styling
        $last = $firstExcelLine + $i - 1;
        foreach (range('B', 'G') as $col) {
            $sheet->duplicateStyle($sheet->getStyle("{$col}4"), "{$col}4:$col$last");
        }
        $sheet->getColumnDimension('C')->setAutoSize(true);
        $sheet->getColumnDimension('D')->setAutoSize(true);
        
        // add cell locking / hiding
        $sheet->getProtection()->setSheet(true);
        $sheet->getStyle("D1")->getProtection()->setLocked(PHPExcel_Style_Protection::PROTECTION_UNPROTECTED); // week ending 
        foreach (range('E', 'G') as $col) {
            $sheet->getStyle("{$col}3:$col$last")->getProtection()->setLocked(PHPExcel_Style_Protection::PROTECTION_UNPROTECTED);
        }
        
        foreach (range('K', 'M') as $col)
            $sheet->getColumnDimension($col)->setVisible(false);
        
        
        $objPHPExcel->setActiveSheetIndex(0); // so it opens on first sheet upon download
        
        // Redirect output to a client’s web browser (Excel2003)
        header('Content-Type: application/excel');
        header('Content-Disposition: attachment;filename="'.date('YmdHi').'-'.$supplierFileName.'.xlsx"');
        header('Cache-Control: max-age=0');

        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save('php://output');

        // Once we have finished using the library, give back the
        // power to Yii...
        spl_autoload_register(array('YiiBase','autoload'));
    }
}

?>
