<?php
/**
 * Description of SecondaryRoutingForm
 *
 * @author Ramon
 */
class SecondaryRoutingForm extends CFormModel
{
    public $spreadSheet;
    public $area;
    public $uploadedFileName;
    
    // allow columns to be in different order (requires header)
    // default to be used if no header is found (IMPORT)
    public $importColumns = array(
                'round_id'  => array('idx' => 'A', 'required' => TRUE,  'found' => FALSE),
                'name'      => array('idx' => 'B', 'required' => TRUE,  'found' => FALSE),
                'surname'   => array('idx' => 'C', 'required' => TRUE,  'found' => FALSE),
                'address'   => array('idx' => 'D', 'required' => TRUE,  'found' => FALSE),
                'drop_id'   => array('idx' => 'E', 'required' => TRUE,  'found' => FALSE),
                'route_id'  => array('idx' => 'F', 'required' => FALSE, 'found' => FALSE),
                'quantity'  => array('idx' => 'G', 'required' => TRUE,  'found' => FALSE),
                'comments'  => array('idx' => 'H', 'required' => FALSE, 'found' => FALSE),
            );

    public function rules()
    {
        return array(
            array('area', 'required'),
            array('spreadSheet', 'file', 'types'=>'xls, xlsx'), // #43 added xlsx
            array('spreadSheet', 'validateSheet', 'skipOnError'=>true),
        );
    }
    
    public function getColumnIndexes($ws, $requiredColumns)
    {
        $header = FALSE;
        $result = $requiredColumns;
        
        $row = 1; // header row
        foreach (range('A', 'Z') as $col) {
            $colName = $this->getCellValue($ws, "$col$row");
            if (in_array($colName, array_keys($requiredColumns)))
            {
                $result[$colName]["idx"] = $col;
                $result[$colName]["found"] = TRUE;
                $header = TRUE;
            }
        }
        $result["_header"] = $header;
        return $result;
    }
    
    public function getNamedCellValue($ws, $row, $columns, $name, $default = "")
    {
        $result = $default;
        
        if ($columns['_header'] === TRUE)
        {
            if ($columns[$name]['found'] === TRUE)
                $result = $this->getCellValue ($ws, $columns[$name]['idx'].$row, $default);
        }
        else
        {
            $result = $this->getCellValue ($ws, $columns[$name]['idx'].$row, $default);
        }
        
        return $result;
    }
    
    public function getCellValue($ws, $coord, $default = "")
    {
        return (!is_null($ws->getCell($coord))) ? $ws->getCell($coord)->getCalculatedValue() : $default;
    }

    /**
     * used in the rules() section to validate the sheet
     */
    public function validateSheet($attribute,$params)
    {
        // get a reference to the path of PHPExcel classes
        $phpExcelPath = Yii::getPathOfAlias('ext.phpexcel.Classes');

        // dummy classes to make sure the classes are loaded prior to disabling Yii autoloader

        // Turn off YII library autoload
        spl_autoload_unregister(array('YiiBase','autoload'));

        // PHPExcel_IOFactory
        require_once $phpExcelPath.'/PHPExcel/IOFactory.php';

        if (!file_exists($this->spreadSheet->getTempName())) {
            exit("File NOT FOUND!.\n");
        }

        $objPHPExcel = PHPExcel_IOFactory::load($this->spreadSheet->getTempName());
        $ws = $objPHPExcel->getSheet(0); // process only FIRST sheet

        // loop through rows for errors
        $blankRoutes = array();
        $rows = 0;
        
        $cols = $this->getColumnIndexes($ws, $this->importColumns);
        $idx = ($cols['_header'] === FALSE) ? 1 : 2; // if FALSE, no header found
        
        $blanks = 0;
        while ($idx != -1)
        {
            //$roundId    = (!is_null($ws->getCell("A$idx"))) ? $ws->getCell("A$idx")->getCalculatedValue() : "";
            $roundId = $this->getNamedCellValue($ws, $idx, $cols, 'round_id');
            if ($roundId == "")
            {
                $offset = 0;
                while (($roundId == "") && ($offset < 25)) // allow for 25 consecutive blank lines max
                {
                    $offset++;
                    //$roundId = (!is_null($ws->getCell("A".($idx+$offset)))) ? $ws->getCell("A".($idx+$offset))->getCalculatedValue() : "";
                    $roundId = $this->getNamedCellValue($ws, ($idx+$offset), $cols, 'round_id');
                }

                if ($roundId == "")
                {
                    $rows = $idx - $blanks;
                    $idx = -1; // terminate loop
                }
                else
                {
                    $blanks += $offset;
                    $idx += $offset;
                }
            }

            if ($idx != -1) // validate row
            {
                // route id cannot be blank
                //$routeId = (!is_null($ws->getCell("E$idx"))) ? $ws->getCell("E$idx")->getCalculatedValue() : "";
                $routeId = $this->getNamedCellValue($ws, $idx, $cols, 'drop_id');
                if (trim($routeId) == "")
                {
                    $blankRoutes[] = $idx;
                }
                $idx++;
            }
        }

        // error display
        if (count($blankRoutes) > 0)
        {
            $this->addError('spreadSheet','Blank route identifiers found on rows: '.implode(', ', $blankRoutes));
        }


        // Once we have finished using the library, give back the
        // power to Yii...
        spl_autoload_register(array('YiiBase','autoload'));
    }

    /**
     *
     * @param <type> $fileName
     * @param <type> $axn
     *                  - import : (default) - gets round info + quantities in db
     *                  - init : initializes tables with information on routes/rounds/sort orders
     */
    public function processFile($axn)
    {
        $rows = -1;
        $blanks = 0;
        $newRounds = false;
        $reinitRoutes = array(); // used for axn = init
        
        // get a reference to the path of PHPExcel classes
        $phpExcelPath = Yii::getPathOfAlias('ext.phpexcel.Classes');

        // dummy classes to make sure the classes are loaded prior to disabling Yii autoloader
        $srr = new SecondaryRouteRound();
        $srr = new SecondaryRoute();
        $srr = new SecondaryRound();
        $srr = new CDbExpression('NOW()');
        
        // Turn off YII library autoload
        spl_autoload_unregister(array('YiiBase','autoload'));

        // PHPExcel_IOFactory
        require_once $phpExcelPath.'/PHPExcel/IOFactory.php';

        if (!file_exists($this->uploadedFileName)) {
            exit("File NOT FOUND!.\n");
        }

        $objPHPExcel = PHPExcel_IOFactory::load($this->uploadedFileName);
        $ws = $objPHPExcel->getSheet(0); // process only FIRST sheet

        if ($axn == 'init')
        {
            // TRUNCATE tables -- removed -- delete only the ones on the spreadsheet
            //SecondaryRouteRound::model()->deleteAll();
            //SecondaryRoute::model()->deleteAll();
            //SecondaryRound::model()->deleteAll();

            //$idx = 2;
            $idx = 1; //no header required
            while ($idx != -1)
            {
                $roundId    = (!is_null($ws->getCell("A$idx"))) ? $ws->getCell("A$idx")->getCalculatedValue() : "";
                if ($roundId == "")
                {
                    $offset = 0;
                    while (($roundId == "") && ($offset < 25)) // allow for 25 consecutive blank lines max
                    {
                        $offset++;
                        $roundId = (!is_null($ws->getCell("A".($idx+$offset)))) ? $ws->getCell("A".($idx+$offset))->getCalculatedValue() : "";
                    }

                    if ($roundId == "")
                    {
                        $rows = $idx - $blanks - 1; //do not count header
                        $idx = -1; // terminate loop
                    }
                    else
                    {
                        $blanks += $offset;
                        $idx += $offset;
                    }
                }
                else
                {
                    $routeId    = (!is_null($ws->getCell("B$idx"))) ? $ws->getCell("B$idx")->getCalculatedValue() : "";
                    $sortOrder  = (!is_null($ws->getCell("C$idx"))) ? $ws->getCell("C$idx")->getCalculatedValue() : "";
                    $name       = (!is_null($ws->getCell("D$idx"))) ? $ws->getCell("D$idx")->getCalculatedValue() : "";
                    $surname    = (!is_null($ws->getCell("E$idx"))) ? $ws->getCell("E$idx")->getCalculatedValue() : "";
                    $address    = (!is_null($ws->getCell("F$idx"))) ? $ws->getCell("F$idx")->getCalculatedValue() : "";
                    $postCode   = (!is_null($ws->getCell("G$idx"))) ? $ws->getCell("G$idx")->getCalculatedValue() : "";
                    $quantity   = (!is_null($ws->getCell("H$idx"))) ? $ws->getCell("H$idx")->getCalculatedValue() : "";
                    $comments   = (!is_null($ws->getCell("I$idx"))) ? $ws->getCell("I$idx")->getCalculatedValue() : "";

                    $routeId = trim($routeId);
                    $roundId = trim($roundId);

                    if (!in_array($routeId, $reinitRoutes))
                    {
                        SecondaryRouteRound::model()->deleteAll('SecondaryRouteId=:rid', array(':rid'=>$routeId));
                        //SecondaryRoute::model()->deleteAll('SecondaryRouteId=:rid', array(':rid'=>$routeId)); // keep route for bundle size maintenance
                        $reinitRoutes[] = $routeId;
                    }

                    // route exist?
                    $route = SecondaryRoute::model()->find('SecondaryRouteId=:rid', array(':rid'=>$routeId));
                    if (!isset($route))
                    {
                        $route = new SecondaryRoute();
                        $route->SecondaryRouteId = $routeId;
                        $route->BundleSize = 80;
                        $route->AreaId = $this->area;
                        $route->DateUpdated = new CDbExpression('NOW()');
                        $route->save();
                    }
                    else
                    {
                        $route->AreaId = $this->area;
                        $route->DateUpdated = new CDbExpression('NOW()');
                        $route->save();
                    }

                    // round exist?
                    $round = SecondaryRound::model()->find('SecondaryRoundId=:rid', array(':rid'=>$roundId));
                    if (!isset($round))
                    {
                        $round = new SecondaryRound();
                        $round->SecondaryRoundId = $roundId;
                    }
                    $round->Name = $name;
                    $round->Surname = $surname;
                    $round->Address = $address;
                    $round->PostCode = $postCode;
                    $round->DateUpdated = new CDbExpression('NOW()');
                    $round->save();

                    // add sort order info
                    $mapping = SecondaryRouteRound::model()->find('SecondaryRouteId=:route AND SecondaryRoundId=:round', array(':route'=> $routeId, ':round'=> $roundId));
                    if (!isset($mapping))
                    {
                        $mapping = new SecondaryRouteRound();
                        $mapping->SecondaryRouteId = $routeId;
                        $mapping->SecondaryRoundId = $roundId;
                    }
                    $mapping->SortOrder = $sortOrder;
                    $mapping->Quantity = $quantity;
                    $mapping->Comments = $comments;
                    $mapping->Enabled = 1;
                    $mapping->DateUpdated = new CDbExpression('NOW()');
                    $mapping->save();

                    $idx++;
                }
            }
        }
        else
        {
            $updatedRoutes = array();
            
            // TASK NOT INIT
            // get column indexes
            $cols = $this->getColumnIndexes($ws, $this->importColumns);
            $idx = ($cols['_header'] === FALSE) ? 1 : 2; // if FALSE, no header found
            
            
            while ($idx != -1)
            {
                //$roundId    = (!is_null($ws->getCell("A$idx"))) ? $ws->getCell("A$idx")->getCalculatedValue() : "";
                $roundId = $this->getNamedCellValue($ws, $idx, $cols, 'round_id');
                if ($roundId == "")
                {
                    $offset = 0;
                    while (($roundId == "") && ($offset < 25)) // allow for 25 consecutive blank lines max
                    {
                        $offset++;
                        //$roundId = (!is_null($ws->getCell("A".($idx+$offset)))) ? $ws->getCell("A".($idx+$offset))->getCalculatedValue() : "";
                        $roundId = $this->getNamedCellValue($ws, ($idx+$offset), $cols, 'round_id');
                    }

                    if ($roundId == "")
                    {
                        $rows = $idx - $blanks - 1; //do not count header
                        $idx = -1; // terminate loop
                    }
                    else
                    {
                        $blanks += $offset;
                        $idx += $offset;
                    }
                    //$rows = $idx - 1; // do not count header
                    //$idx = -1; // terminate loop
                }
                else
                {
                    $name       = $this->getNamedCellValue($ws, $idx, $cols, 'name'); //= (!is_null($ws->getCell("B$idx"))) ? $ws->getCell("B$idx")->getCalculatedValue() : "";
                    $surname    = $this->getNamedCellValue($ws, $idx, $cols, 'surname'); //= (!is_null($ws->getCell("C$idx"))) ? $ws->getCell("C$idx")->getCalculatedValue() : "";
                    $address    = $this->getNamedCellValue($ws, $idx, $cols, 'address'); //= (!is_null($ws->getCell("D$idx"))) ? $ws->getCell("D$idx")->getCalculatedValue() : "";
                    $routeId    = $this->getNamedCellValue($ws, $idx, $cols, 'drop_id'); //= (!is_null($ws->getCell("E$idx"))) ? $ws->getCell("E$idx")->getCalculatedValue() : "";
                    //$postCode   = (!is_null($ws->getCell("G$idx"))) ? $ws->getCell("G$idx")->getCalculatedValue() : "";
                    $postCode   = "";
                    
                    // if routeid is blank, search up for a valid one
                    $offset = 1;
                    while ($routeId == "")
                    {
                        $routeId    = $this->getNamedCellValue($ws, ($idx-$offset), $cols, 'drop_id'); // = (!is_null($ws->getCell("E".($idx-$offset)))) ? $ws->getCell("E".($idx-$offset))->getCalculatedValue() : "";
                        $offset++;
                    }

                    $routeId = strtoupper(trim($routeId));
                    $roundId = trim($roundId);

                    if(!in_array($routeId, $updatedRoutes))
                    {
                        // flag route as updated
                        $route = SecondaryRoute::model()->find('SecondaryRouteId=:rid', array(':rid'=>$routeId));
                        if (!isset($route))
                        {
                            $route = new SecondaryRoute();
                            $route->SecondaryRouteId = $routeId;
                            $route->BundleSize = 80;
                        }
                        $route->AreaId = $this->area;
                        $route->DateUpdated = new CDbExpression('NOW()');
                        $route->save();


                        // flag rounds as DISABLED for the route, will ENABLE individually
                        SecondaryRouteRound::model()->updateAll(array('Enabled'=>0), 'SecondaryRouteId=:rid', array(':rid'=>$routeId));

                        // add to list to avoid updating route info again
                        $updatedRoutes[] = $routeId;
                    }


                    $quantity   = $this->getNamedCellValue($ws, $idx, $cols, 'quantity'); //= (!is_null($ws->getCell("G$idx"))) ? $ws->getCell("G$idx")->getCalculatedValue() : "";
                    $comments   = $this->getNamedCellValue($ws, $idx, $cols, 'comments'); //= (!is_null($ws->getCell("H$idx"))) ? $ws->getCell("H$idx")->getCalculatedValue() : "";

                    // update round information
                    // round exist?
                    $round = SecondaryRound::model()->find('SecondaryRoundId=:rid', array(':rid'=>$roundId));
                    if (!isset($round))
                    {
                        $round = new SecondaryRound();
                        $round->SecondaryRoundId = $roundId;
                        $round->Name = $name;
                        $round->Surname = $surname;
                        $round->Address = $address;
                        $round->PostCode = $postCode;
                        $round->DateUpdated = new CDbExpression('NOW()');
                        $round->save();
                        $newRounds = true;
                    }
                    else
                    {
                        $round->Name = $name;
                        $round->Surname = $surname;
                        $round->Address = $address;
                        $round->DateUpdated = new CDbExpression('NOW()');
                        $round->save();
                    }

                    // update quantities on mapping table
                    $mapping = SecondaryRouteRound::model()->find('SecondaryRouteId=:route AND SecondaryRoundId=:round', array(':route'=> $routeId, ':round'=> $roundId));
                    if (!isset($mapping))
                    {
                        $mapping = new SecondaryRouteRound();
                        $mapping->SecondaryRouteId = $routeId;
                        $mapping->SecondaryRoundId = $roundId;
                        $mapping->SortOrder = 9999;
                        $mapping->Quantity = $quantity;
                        $mapping->Comments = $comments;
                        $mapping->Enabled = 1;
                        $mapping->DateUpdated = new CDbExpression('NOW()');
                        $mapping->save();
                        $newRounds = true;
                    }
                    else
                    {
                        $mapping->Quantity = $quantity;
                        $mapping->Comments = $comments;
                        $mapping->Enabled = 1; // enable to include in listing
                        $mapping->DateUpdated = new CDbExpression('NOW()');
                        $mapping->save();
                    }

                    //if(!in_array($routeId, $updatedRoutes))
                    //    $updatedRoutes[] = $routeId;

                    $idx++;
                }
            }

            //echo '|'.implode("|", $updatedRoutes).'|';

            /*foreach($updatedRoutes as $routeId)
            {
                $route = SecondaryRoute::model()->find('SecondaryRouteId=:rid', array(':rid'=>$routeId));
                $route->DateUpdated = new CDbExpression('NOW()');
                $route->save();
            }
             *
             */
        }

        // Once we have finished using the library, give back the
        // power to Yii...
        spl_autoload_register(array('YiiBase','autoload'));

        if ($axn == 'init')
            return array('rows'=>$rows, 'routes'=>$reinitRoutes);
        else
            return array('rows'=>$rows, 'newRounds'=>$newRounds);
    }

    public function getRoutes()
    {
        $criteria = new CDbCriteria();
        $criteria->order = "SecondaryRouteId ASC";
        return SecondaryRoute::model()->findAll($criteria);
    }

    public function saveRoutes($routes)
    {
        foreach($routes as $routeId => $details)
        {
            $sr = SecondaryRoute::model()->findByPk($routeId);
            if(isset($sr) && 
                    (($sr->BundleSize != $details['BundleSize']) || ($sr->BundleWeight != $details['BundleWeight'])))
            {
                $sr->BundleSize = $details['BundleSize'];
                $sr->BundleWeight = $details['BundleWeight'];
                $sr->DateUpdated = new CDbExpression('NOW()');
                $sr->save();
            }
        }
    }
    
    public function getOptionsArea()
    {
        $result = array();
        $crit = new CDbCriteria();
        $crit->order = 'Name ASC';
        $areas = Area::model()->findAll($crit);
        foreach ($areas as $a) {
            $result[$a->Id] = $a->Name;
        }
        return $result;
    }
}
?>
