<?php
/**
 * Description of PalletSheetUploadPC
 *
 * @author ramon
 */
class PalletsSheetUploadPC extends CFormModel
{
    public $spreadSheet;
    public $uploadedFileName;
    public $uploadFileAgain=false;
    
    public function rules()
    {
        return array(
            array('spreadSheet', 'file', 'types'=>'xlsx'),
        );
    }
    
    public function processFile()
    {
        $result = array('printcentre' => '', 
                        'weekending' => '', 
                        'error' => false,
                        'errordesc' => '',
                        'errortype' => '',
                        'errorfile' => ''
        );
        
        $dummy = new CDbCriteria();
        $dummy = new PalletReportPC();
        $dummy = new CDbExpression('NOW()');

        // get a reference to the path of PHPExcel classes
        $phpExcelPath = Yii::getPathOfAlias('ext.phpexcel.Classes');
        // Turn off YII library autoload
        spl_autoload_unregister(array('YiiBase','autoload'));

        // PHPExcel_IOFactory
        require_once $phpExcelPath.'/PHPExcel.php';

        $objPHPExcel = PHPExcel_IOFactory::load($this->uploadedFileName);
        $sheet = $objPHPExcel->getSheet(0); // process only FIRST sheet

        $error = false;
        $printCentreId = $sheet->getCell("K1")->getCalculatedValue();
        if (!isset($printCentreId))
            $error = true;
        
        $weekEnding = $sheet->getCell("D1")->getCalculatedValue();
        if(stristr($weekEnding, "/") === FALSE)
                $weekEnding = PHPExcel_Style_NumberFormat::toFormattedString(round($weekEnding), "DD/MM/YYYY");        
        
        // validate week ending date not posted 
        $crit = new CDbCriteria();
        $crit->condition = 'PrintCentreId = :pcid and WeekEnding = STR_TO_DATE(:we, "%d/%m/%Y")';
        $crit->params = array(':pcid' => $printCentreId, 
                            ':we' => $weekEnding);
        $existing = PalletReportPC::model()->find($crit);
        if (isset($existing) && !$this->uploadFileAgain)
        {
            $error = true;
            $result['errordesc'] = 'A sheet for that week ending date ('.$weekEnding.') and print centre has already been submitted.';
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
                    $item['supplierId'] = $sheet->getCell("L$i")->getCalculatedValue();
                    
                    // grab print day:
                    $printDay = $sheet->getCell("M$i")->getCalculatedValue();
                    $sub = 7 - $printDay;
                    $we = strptime($weekEnding, "%d/%m/%Y");
                    $wef = sprintf("%04d-%02d-%02d", ($we['tm_year']+1900), ($we['tm_mon']+1), $we['tm_mday']);
                    $weekEndingDate = new DateTime($wef);
                    $newDate = $weekEndingDate->modify("-{$sub} days");
                    $item['date'] = date_format($weekEndingDate, 'd/m/Y');
                    
                    $item['pSentOut'] = $sheet->getCell("E$i")->getCalculatedValue();
                    $item['wSentOut'] = $sheet->getCell("F$i")->getCalculatedValue();
                                        
                    $items[] = $item;
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
            PalletReportPC::model()->deleteAll($criteria);
            $nbrItems = count($items);
            for ($i = 0; $i < $nbrItems; $i++)
            {
                $item = $items[$i];
                $p = new PalletReportPC();
                $p->PrintCentreId = $printCentreId;
                $p->RouteId = $item['routeId'];
                $p->SupplierId = $item['supplierId'];
                $p->Date = new CDbExpression("STR_TO_DATE(:dt, '%d/%m/%Y')", array(':dt' => $item['date']));
                
                $p->PlasticSentOut = $item['pSentOut'];
                $p->WoodenSentOut = $item['wSentOut'];
                
                $p->DateUploaded = new CDbExpression('NOW()');
                $p->SourceFile = $this->uploadedFileName;
                $p->WeekEnding = new CDbExpression("STR_TO_DATE(:dt2, '%d/%m/%Y')", array(':dt2' => $weekEnding));
                
                $p->save();
            }
        }
        
        if (!$error)
        {
            $result['printcentre'] = PrintCentre::model()->find("PrintCentreId = $printCentreId")->Name ;
            $result['weekending'] = $weekEnding;
        }
        $result['error'] = $error;
                
        return $result;
    }
    
    
}

?>
