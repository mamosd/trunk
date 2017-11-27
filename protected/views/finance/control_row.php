<?php

$baseUrl = Yii::app()->request->baseUrl;
$regularEntryType = FinanceRouteInstanceDetails::$REGULAR_ENTRY_TYPE;
$specialEntryTypes = FinanceRouteInstanceDetails::$SPECIAL_ENTRY_TYPES;

$readOnlyAttrs = array('class' => 'readOnlyField', 'readOnly' => 'readOnly');
$smallReadOnlyAttrsBase = array_merge(array('size' => 5), $readOnlyAttrs);
$totalReadOnlyAttrsBase = array_merge(array('size' => 7), $readOnlyAttrs);

$anythingNotAck = array('DTC' => FALSE, 'DTR' => FALSE);

?>
<tr cat="<?php echo $routeInfo->RouteCategoryId ?>"
    rel="<?php echo $routeInfo->RouteId ?>">
    <td>
    <?php if ($model->baseEdit || ($routeInfo->IsBase == 0)) : ?>

        <a href="#" class="lnkDelete" 
           rel="<?php echo $routeInfo->RouteInstanceId; ?>">
            <img src="<?php echo $baseUrl; ?>/img/icons/delete.png" title="drop route" />
        </a>
    <?php endif; ?>
    </td>
    <td>
        <?php 
            if ($routeInfo->IsBase != 1):?>
                <div class="warningBox">
                    <?php echo $routeInfo->Route;?>
                </div>
                <?php
            else:
                echo $routeInfo->Route;
            endif;
        ?>
    </td>
    <td id="<?php echo $rowCount."-".$routeInfo->RouteId;?>">
            <?php 
            $instances = $routeDates;
            $contractorIds = array();
            foreach ($instances as $d => $i)
            {
                $contractorId = $i->ContractorId;

                if (!isset($contractorIds[$contractorId])) // ensure main contractor always show (eg when single date available and adjusted)
                    $contractorIds[$contractorId] = array();
                
                if (!$model->baseEdit && !empty($i->AdjContractorId))
                    $contractorId = $i->AdjContractorId;

                if (!isset($contractorIds[$contractorId]))
                    $contractorIds[$contractorId] = array();

                if ($contractorId == $i->AdjContractorId)
                    if (!in_array($i->RouteDate, $contractorIds[$contractorId]))
                        $contractorIds[$contractorId][] = date('d/m', CDateTimeParser::parse($i->RouteDate, "yyyy-MM-dd"));
            }
            // list contractors for route
            foreach (array_keys($contractorIds) as $cid)
            {
                $c = NULL;
                if (isset($contractorList[$cid]))
                    $c = $contractorList[$cid];
                else
                    $c = FinanceContractorDetails::model()->find("ContractorId = :cid", array(':cid' => $cid)); 
                
                ?>

                <?php if (!$model->baseEdit) : 
                if (!empty($contractorIds[$cid])):
                    echo "(".implode(', ', $contractorIds[$cid]).")";
                endif;
                ?>

            <a href="#" class="contractor-earnings" rel="<?php echo $cid; ?>"  date="<?php echo $model->weekStarting ?>">
        <?php endif; ?>
                <?php echo trim($c->Data.' - '.$c->FirstName." ".$c->LastName); ?>
        <?php if (!$model->baseEdit) : ?> 
            </a>
        <?php endif; ?>

        <?php
                if (!empty($c->ParentContractorId)): ?>
                    <a href="#" class="contractor-earnings" rel="<?php echo $c->ParentContractorId; ?>"  date="<?php echo $model->weekStarting ?>">
                        <?php echo ' (<strong>'.trim($c->ParentFirstName." ".$c->ParentLastName).'</strong>)'; ?>
                    </a>
                <?php
                endif;
                echo '<br/>';
            }
            ?>
    </td>
    <?php
    $allValue = 0;
    $excValue = 0;
    $indexes = array();
    // regular entry types
    foreach ($datesToShow as $date): 
        $routeDate = date('Y-m-d', $date);
        $indexes[] = "$routeDate-$regularEntryType";
    endforeach;
    // special entry types
    foreach ($specialEntryTypes as $entryType): // not required at phase 1 (empty array)
        $routeDate = date('Y-m-d', $wst);
        $indexes[] = "$routeDate-$entryType";
    endforeach;

    //foreach ($datesToShow as $date): 
    foreach ($indexes as $index): ?>
    <td>
        <?php 
        $fee = 'N/A';
        $exception = 0;
        $cssClass = 'white';
        $instanceId = -1;
        $tooltip = '';

        if (isset($routeDates[$index])) {

            //$cssClass = '';
            $instance = $routeDates[$index];
            $instanceId = $instance->RouteInstanceId;
            
            $instanceDetails = $model->getInstanceUIDetails($instance, $model->baseEdit);

            $allValue += floatval($instanceDetails['fee']);
            $excValue += floatval($instanceDetails['exception']);

            $fee = sprintf("%01.2f", $instanceDetails['fee']);
            $exception = sprintf("%01.2f", $instanceDetails['exception']);
            $cssClass = $instanceDetails['cssClass'];
            $tooltip = $instanceDetails['tooltip'];
            $anythingNotAck = array_merge($anythingNotAck, $instanceDetails['nack']);
        }
        
        if ( isset( $_GET['FinanceControl']['weekStarting'] ) )
        {
        $smallReadOnlyAttrs = array_merge($smallReadOnlyAttrsBase, array(
                        'rel' => $routeInfo->RouteId, //$instance->RouteId,
                        'contractor' => $routeInfo->ContractorId,
                        'rel-instance' => $instanceId,
                        'date' => $index,
                        'class' => $smallReadOnlyAttrsBase['class']." $cssClass route-link",
                        'title' => $tooltip,
                        'fee' => $fee,
                        'exception' => $exception,
                        'exc-dtr' => $anythingNotAck['DTR'],
                        'exc-dtc' => $anythingNotAck['DTC'],
                    ));
        }
        else
        {
        $smallReadOnlyAttrs = array_merge($smallReadOnlyAttrsBase, array(
                        'rel' => $routeInfo->RouteId, //$instance->RouteId,
                        'contractor' => $routeInfo->ContractorId,
                        'rel-instance' => $instanceId,
                        'date' => $index,
                        'class' => $smallReadOnlyAttrsBase['class']." $cssClass route-link",
                        'title' => $tooltip,
                        'fee' => $fee,
                        'exception' => $exception,
                        'exc-dtr' => $anythingNotAck['DTR'],
                        'exc-dtc' => $anythingNotAck['DTC'],
                    ));                    
        }

        ?>
        <?php echo CHtml::textField("dt[{$routeInfo->RouteId}][{$routeInfo->ContractorId}][$index]", $fee, $smallReadOnlyAttrs); ?>

    </td> 
    <?php endforeach; ?>
    <?php if (!$model->baseEdit): ?>
    <td>
        <?php 
            $totalReadOnlyAttrsBase['class'] = $readOnlyAttrs['class'].' exc-total';
            echo CHtml::textField("dte-{$routeInfo->RouteId}-$rowCount", sprintf("%01.2f", $excValue), $totalReadOnlyAttrsBase); 
        ?>
    </td>
    <?php endif; ?>
    <td>
        <?php 
            $totalReadOnlyAttrsBase['class'] = $readOnlyAttrs['class'].' fee-total';
            echo CHtml::textField("dta-{$routeInfo->RouteId}-$rowCount", sprintf("%01.2f", $allValue), $totalReadOnlyAttrsBase); 
        ?>
    </td>
</tr>
