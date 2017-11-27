<?php 

$rowCount = rand(999, 9999);

$datesToShow = array();
$wst = CDateTimeParser::parse($model->weekStarting, "dd/MM/yyyy");
foreach(range(0,6) as $delta)
    $datesToShow[] = strtotime("+$delta day", $wst);

foreach($model->routes as $catId => $routes): 

    foreach ($routes as $routeId => $routeDates): 
                $routeInfo = array_values($routeDates);
                $routeInfo = $routeInfo[0];
                
                $this->renderPartial('control_row', array(
                    'model' => $model,
                    'routeInfo' => $routeInfo,
                    'rowCount' => $rowCount,
                    'routeDates' => $routeDates,
                    'contractorList' => array(),
                    'datesToShow' => $datesToShow
                        ));

            $rowCount ++;
        endforeach;

endforeach;


?>
