<?php
/**
 * Description of PalletsSheetPrintPC
 *
 * @author ramon
 */
class PalletsSheetPrintPC extends CFormModel
{
    public $printCentreId;
    
    public function attributeLabels()
    {
        return array(
            'printCentreId'=>'Print Centre',
        );
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
    
    public function generateSheetDownload()
    {
        $printCentreInfo = PrintCentre::model()->find('PrintCentreId = :pcid', array(':pcid' => $this->printCentreId));
        $printCentreFileName = strtolower(str_replace (" ", "", $printCentreInfo->Name));
        
        $dummy = new AllRouteDetails();
        
        // get a reference to the path of PHPExcel classes
        $phpExcelPath = Yii::getPathOfAlias('ext.phpexcel.Classes');
        // Turn off YII library autoload
        spl_autoload_unregister(array('YiiBase','autoload'));

        // PHPExcel_IOFactory
        require_once $phpExcelPath.'/PHPExcel.php';

        $objReader = PHPExcel_IOFactory::createReader('Excel2007');
        $objPHPExcel = $objReader->load('_uploads/templates/PalletSummaryTemplatePC.xlsx');
        $objPHPExcel->setActiveSheetIndex(0);
        
        $sheet = $objPHPExcel->getActiveSheet();
        // print centre name
        $sheet->setCellValue('A1', $printCentreInfo->Name);
        $sheet->setCellValue('K1', $this->printCentreId);
        
        // next sunday
        $sheet->setCellValue('D1', date('d/m/Y', strtotime("Next Sunday")));
        
        $crit = new CDbCriteria();
        $crit->condition = "PrintCentreId = :pcid";
        $crit->params = array(":pcid" => $this->printCentreId);
        $pcRoutes = AllRouteDetails::model()->findAll($crit);
        
        // collect title/s per supplier
        $routes = array();
        foreach ($pcRoutes as $route)
        {
            if (!isset($routes[$route->RouteId]))
                    $routes[$route->RouteId] = array("supplier" => $route->SupplierName, 
                                                    "supplierid" => $route->SupplierId,
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
            $sheet->setCellValue("D$ln", $item["supplier"]);
            
            $printDay = $item["titles"][0]['printday'];
            $sheet->setCellValue("B$ln", "=D1-".(7 - $printDay));
            
            $sheet->setCellValue("K$ln", $routeId);
            $sheet->setCellValue("L$ln", $item["supplierid"]);
            $sheet->setCellValue("M$ln", $printDay);            
            
            $i++;
        }
       
        // add styling
        $last = $firstExcelLine + $i - 1;
        foreach (range('B', 'F') as $col) {
            $sheet->duplicateStyle($sheet->getStyle("{$col}4"), "{$col}4:$col$last");
        }
        $sheet->getColumnDimension('C')->setAutoSize(true);
        $sheet->getColumnDimension('D')->setAutoSize(true);
        
        // add cell locking / hiding
        $sheet->getProtection()->setSheet(true);
        $sheet->getStyle("D1")->getProtection()->setLocked(PHPExcel_Style_Protection::PROTECTION_UNPROTECTED); // week ending 
        foreach (range('E', 'F') as $col) {
            $sheet->getStyle("{$col}3:$col$last")->getProtection()->setLocked(PHPExcel_Style_Protection::PROTECTION_UNPROTECTED);
        }
        
        foreach (range('K', 'M') as $col)
            $sheet->getColumnDimension($col)->setVisible(false);
                
        // Redirect output to a client’s web browser (Excel2003)
        header('Content-Type: application/excel');
        header('Content-Disposition: attachment;filename="'.date('YmdHi').'-'.$printCentreFileName.'.xlsx"');
        header('Cache-Control: max-age=0');

        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save('php://output');

        // Once we have finished using the library, give back the
        // power to Yii...
        spl_autoload_register(array('YiiBase','autoload'));
    }
}

?>
