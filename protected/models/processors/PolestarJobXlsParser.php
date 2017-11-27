<?php
/**
 * Description of PolestarJobXlsParser
 *
 * @author ramon
 */
class PolestarJobXlsParser {
    
    public $fileName;
    
    private $columns = array(
        'OrderNo' => 'Order',
        'Provider' => 'Provider',
        'LoadRef' =>  'Job/Load',
        'Publication' => 'Publication',
        'Quantity' => 'Quantity',
        'Pallets' => 'Pallets',
        'PalletsFull' => 'Full Pal',
        'PalletsHalf' => 'half Pal',
        'PalletsQtr' => 'Qtr Pal',
        'Kg' => 'Kg',
        'Vehicle' => 'Vehicle',        
        'CollPostcode' => 'Load',
        'DelPostcode' => 'Deliver',
        'DelArea' => 'Del Area',
        'DelCompany' => 'Company',
        'DeliveryDate' => 'Del Date',
        'DelScheduledTime' => 'Del Time',
        'DelTimeCode' => 'Time Code',
        'CollScheduledTime' => 'Coll Time',
//        '' => 'PO Number', // ignore
        'BookingRef' => 'Booking Ref',
//        '' => 'Contact Tel.', // ignore
        'SpecialInstructions' => 'Special Instr.'
    );
    
    function getData() {
        $data = $this->getIndexedData();
        $result = array();
        foreach ($data as $row) {
            $dataRow = $this->columns;
            foreach($dataRow as $key => $index)
                $dataRow[$key] = isset($row[$index]) ? $row[$index] : NULL;
            $result[] = (object)$dataRow;
        }
        return $result;
    }
    
    private function getIndexedData() {
        $raw = $this->getRawData();
        $rowCount = count($raw);
        $result = array();
        if ($rowCount > 1) {
            $header = $raw[0];
            for ($i = 1; $i < $rowCount; $i++) {
                $row = array();
                foreach ($header as $cell => $name)
                    if (isset($name))
                        $row[$name] = $raw[$i][$cell];
                $result[] = $row;
            }
        }
        return $result;
    }
    
    private function getRawData() {
        // get a reference to the path of PHPExcel classes
        $phpExcelPath = Yii::getPathOfAlias('ext.phpexcel.Classes');
        // Turn off YII library autoload
        spl_autoload_unregister(array('YiiBase','autoload'));
        
        // PHPExcel_IOFactory
        require_once $phpExcelPath.'/PHPExcel/IOFactory.php';
        
        $objPHPExcel = PHPExcel_IOFactory::load($this->fileName);
        $ws = $objPHPExcel->getSheet(0); // process only FIRST sheet
        
        $emptyRows = 0;
        $row = 1;
        $result = array();
        while ($emptyRows < 5) { // 5 empty rows denote end of file
            $empty = TRUE;
            $content = array();
            foreach (range('A', 'Z') as $col) {
                $value = $this->getCellValue($ws, "$col$row");
                if (!empty($value)) {
                    $empty = FALSE;
                }
                $content[] = $value;
            }
            if ($empty) {
               $emptyRows ++;
            }
            else {
                $emptyRows = 0;
                $result[] = $content;                
            }
            $row++;
        }
        
        // Once we have finished using the library, give back the
        // power to Yii...
        spl_autoload_register(array('YiiBase','autoload'));
        
        return $result;
    }
    
    private function getCellValue($ws, $coord, $default = "")
    {
        return (!is_null($ws->getCell($coord))) ? $ws->getCell($coord)->getCalculatedValue() : $default;
    }
}
