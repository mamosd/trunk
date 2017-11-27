<?php
/**
 * Description of PalletSheetUpload
 *
 * @author ramon
 */
class PalletsSheetUpload extends CFormModel
{
    public $spreadSheet;
    public $uploadedFileName;
    public $uploadFileAgain=false;
    
    public function rules()
    {
        return array(
            array('spreadSheet', 'file', 'types'=>'xlsx'),
//            array('spreadSheet', 'validateSheet', 'skipOnError'=>true),
        );
    }
    
    public function processFile()
    {
        $result = array('supplier' => '', 
                        'weekending' => '', 
                        'error' => false,
                        'errordesc' => '',
                        'errortype' => '',
                        'errorfile' => ''
        );
        
        $dummy = new CDbCriteria();
        $dummy = new PalletReport();
        $dummy = new CDbExpression('NOW()');
        
        // get a reference to the path of PHPExcel classes
        $phpExcelPath = Yii::getPathOfAlias('ext.phpexcel.Classes');
        // Turn off YII library autoload
        spl_autoload_unregister(array('YiiBase','autoload'));

        // PHPExcel_IOFactory
        require_once $phpExcelPath.'/PHPExcel.php';
        
        $objPHPExcel = PHPExcel_IOFactory::load($this->uploadedFileName);
        
        $sheet = $objPHPExcel->getSheet(0); // process Delivery SUMMARY
        
        $error = false;
        $supplierId = $sheet->getCell("K1")->getCalculatedValue();
        if (!isset($supplierId))
            $error = true;
        
        $weekEnding = $sheet->getCell("E1")->getCalculatedValue();
        if(stristr($weekEnding, "/") === FALSE)
                $weekEnding = PHPExcel_Style_NumberFormat::toFormattedString(round($weekEnding), "DD/MM/YYYY");        
        
        // validate week ending date not posted 
        $crit = new CDbCriteria();
        $crit->condition = 'SupplierId = :sid and WeekEnding = STR_TO_DATE(:we, "%d/%m/%Y")';
        $crit->params = array(':sid' => $supplierId, 
                            ':we' => $weekEnding);
        $existing = PalletReport::model()->find($crit);
        if (isset($existing) && !$this->uploadFileAgain)
        {
            $error = true;
            $result['errordesc'] = 'A sheet for that week ending date ('.$weekEnding.') and supplier has already been submitted.';
            $result['errortype'] = 'existing';
            $result['errorfile'] = $this->uploadedFileName;
        }
        
        $items = array();
        
        if (!$error)
        {
            $done = false;
                        
            for($i = 3; !$done; $i++)
            {
                $routeId = !is_null($sheet->getCell("K$i")) ? $sheet->getCell("K$i")->getCalculatedValue() : NULL;
                if (!is_null($routeId))
                {
                    $item = array();
                    $item['routeId'] = $routeId;
                    $item['titleId'] = $sheet->getCell("L$i")->getCalculatedValue();
                    $item['delPointId'] = $sheet->getCell("M$i")->getCalculatedValue();
                    
                    //$item['date'] = $sheet->getCell("B$i")->getCalculatedValue();
                    //if ($item['date'] == '#VALUE!')
                    //{
                    //    $formula = $sheet->getCell("B$i")->getValue();
                    //    $parts = explode("-", $formula);
                    //    $daysAdd = "-".$parts[count($parts)-1]." days";
                    //    $weekEndingDate = date_create_from_format('d/m/Y', $weekEnding);
                    //    $newDate = date_add($weekEndingDate, date_interval_create_from_date_string($daysAdd));
                    //    $item['date'] = date_format($newDate, 'd/m/Y');
                    //}    
                    // grab print day:
                    $printDay = $sheet->getCell("N$i")->getCalculatedValue();
                    $sub = 7 - $printDay;
                    $we = strptime($weekEnding, "%d/%m/%Y");
                    $wef = sprintf("%04d-%02d-%02d", ($we['tm_year']+1900), ($we['tm_mon']+1), $we['tm_mday']);
                    $weekEndingDate = new DateTime($wef);
                    $newDate = $weekEndingDate->modify("-{$sub} days");
                    //$item['date'] = date_format($newDate, 'd/m/Y');
                    $item['date'] = date_format($weekEndingDate, 'd/m/Y');
                    
                    $item['time'] = $sheet->getCell("E$i")->getCalculatedValue();
                    $item['pDelivered'] = $sheet->getCell("F$i")->getCalculatedValue();
                    $item['pCollected'] = $sheet->getCell("G$i")->getCalculatedValue();
                    $item['wDelivered'] = $sheet->getCell("H$i")->getCalculatedValue();
                    $item['wCollected'] = $sheet->getCell("I$i")->getCalculatedValue();
                    $item['note'] = $sheet->getCell("J$i")->getCalculatedValue();
                    
                    $items[] = $item;
                }
                else
                    $done = true;
            }
        }
        
        $returnedItems = array();
        if (!$error)
        {
            $sheet = $objPHPExcel->getSheet(1); // process RETURNED Sheet
            
            $done = false;
                        
            for($i = 3; !$done; $i++)
            {
                $routeId = !is_null($sheet->getCell("K$i")) ? $sheet->getCell("K$i")->getCalculatedValue() : NULL;
                if (!is_null($routeId))
                {
                    $item = array();
                    $item['routeId'] = $routeId;
                    $item['printCentreId'] = $sheet->getCell("L$i")->getCalculatedValue();
                    
                    // grab print day:
                    $printDay = $sheet->getCell("M$i")->getCalculatedValue();
                    $sub = 7 - $printDay;
                    $we = strptime($weekEnding, "%d/%m/%Y");
                    $wef = sprintf("%04d-%02d-%02d", ($we['tm_year']+1900), ($we['tm_mon']+1), $we['tm_mday']);
                    $weekEndingDate = new DateTime($wef);
                    $newDate = $weekEndingDate->modify("-{$sub} days");
                    $item['date'] = date_format($weekEndingDate, 'd/m/Y');
                    
                    $item['pReturned'] = $sheet->getCell("E$i")->getCalculatedValue();
                    $item['wReturned'] = $sheet->getCell("F$i")->getCalculatedValue();
                    $item['noteNumber'] = $sheet->getCell("G$i")->getCalculatedValue();
                                        
                    $returnedItems[] = $item;
                }
                else
                    $done = true;
            }
        }
        
        
        // Once we have finished using the library, give back the
        // power to Yii...
        spl_autoload_register(array('YiiBase','autoload'));        
        
        // insert uploaded rows to db, if no error
        if (!$error)
        {
            //delete old records
            $fileOriginalName = substr($this->uploadedFileName, strpos($this->uploadedFileName, '-')+1);
            $criteria = new CDbCriteria();
            $criteria->addSearchCondition('SourceFile', '%'.$fileOriginalName, false);
            PalletReport::model()->deleteAll($criteria);
            $nbrItems = count($items);
            for ($i = 0; $i < $nbrItems; $i++)
            {
                $item = $items[$i];
                $p = new PalletReport();
                $p->SupplierId = $supplierId;
                $p->RouteId = $item['routeId'];
                $p->TitleId = $item['titleId'];
                $p->DeliveryPointId = $item['delPointId'];
                $p->Date = new CDbExpression("STR_TO_DATE(:dt, '%d/%m/%Y')", array(':dt' => $item['date']));
                $p->DeliveryTime = $item['time'];
                $p->PlasticDelivered = $item['pDelivered'];
                $p->PlasticCollected = $item['pCollected'];
                $p->WoodenDelivered = $item['wDelivered'];
                $p->WoodenCollected = $item['wCollected'];
                $p->NoteNumber = $item['note'];
                $p->DateUploaded = new CDbExpression('NOW()');
                $p->SourceFile = $this->uploadedFileName;
                $p->WeekEnding = new CDbExpression("STR_TO_DATE(:dt2, '%d/%m/%Y')", array(':dt2' => $weekEnding));
                
                $p->save();
            }
            
            foreach($returnedItems as $item)
            {
                $p = new PalletReportPC();
                $p->PrintCentreId = $item['printCentreId'];
                $p->RouteId = $item['routeId'];
                $p->SupplierId = $supplierId;
                $p->Date = new CDbExpression("STR_TO_DATE(:dt, '%d/%m/%Y')", array(':dt' => $item['date']));
                
                $p->PlasticReturned = $item['pReturned'];
                $p->WoodenReturned = $item['wReturned'];
                $p->NoteNumber = $item['noteNumber'];
                
                $p->DateUploaded = new CDbExpression('NOW()');
                $p->SourceFile = $this->uploadedFileName;
                $p->WeekEnding = new CDbExpression("STR_TO_DATE(:dt2, '%d/%m/%Y')", array(':dt2' => $weekEnding));
                
                $p->save();
            }
        }
        
        if (!$error)
        {
            $result['supplier'] = Supplier::model()->find("SupplierId = $supplierId")->Name ;
            $result['weekending'] = $weekEnding;
        }
        $result['error'] = $error;
                
        return $result;
    }
    
    
}

?>
