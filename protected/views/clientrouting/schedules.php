<?php
    $this->breadcrumbs=array(
                array('label'=>'Home', 'url'=>array(Yii::app()->user->role->HomeUrl)),
                array('label'=>'Client Routing', 'url' => '#'),
                array('label'=>'Schedules')
            );
    
    $baseUrl = Yii::app()->request->baseUrl;
    $smallReadOnlyAttrsBase = array('size' => 5, 'class' => 'readOnlyField', 'readOnly' => 'readOnly');
    
?>
<style>
    .route-link {
        cursor: pointer;
    }
    
    #mainWrap {
        width: 95% !important;
    }
    
    .good {
        background-color: greenyellow;
    }
    .warning {
        background-color: yellow;
    }
    .err {
        background-color: red !important;
        color: white;
    }
</style>


<h1>Schedules Control Screen</h1>

<div class="standardForm">
<?php $form=$this->beginWidget('CActiveForm', array(
        'id'=>'schedules-form',
        'errorMessageCssClass'=>'formError',
        'method' => 'GET',
        'action'=>$this->createUrl($this->route)
)); 

echo CHtml::errorSummary($model, "", "", array('class'=>'errorBox'));

?>
    <table width="50%">
        <tr>
            <td>
                <div class="field">
                    <?php echo $form->labelEx($model,'deliveryDate'); ?>
                    <?php echo $form->textField($model, 'deliveryDate', array('size' => '10', 'class' => 'dpicker')); ?>
                </div>
            </td>
            <td>
                <div class="field">
                    <?php echo $form->labelEx($model,'clientId'); ?>
                    <?php echo $form->dropDownList($model,'clientId', $model->getClientOptions()); //, array('empty' => '-- select one') ?>
                </div>                
            </td>
            <td>
                <div class="field">
                    <?php echo $form->labelEx($model,'printCentreId'); ?>
                    <?php echo $form->dropDownList($model,'printCentreId', $model->getPrintCentreOptions(), array('empty' => '-- select one')); ?>
                </div>
            </td>
        </tr>
    </table>
    
    <br/>
    
    <button>Load Schedule</button>
<?php
$this->endWidget(); ?>
</div>

<?php 
if (isset($_GET['RoutingScheduleForm']) && !$model->hasErrors()): 
    if (!isset($model->scheduleInfo)): ?>
        <div class="warningBox">
            There are no schedules for selected criteria.
            <a href="#" id="lnkBlankRoute">Generate Blank Route &raquo;</a>
        </div>
    <?php
    else:
        $dropsIdx = 0;
        $all = $model->scheduleInfo;        
    
        // group by route+wholesaler
        $info = array();
        foreach($all as $line)
        {
            if (!isset($info[$line->RouteId]))
                    $info[$line->RouteId] = array();
            
            if (!isset($info[$line->RouteId][$line->WholesalerId]))
                    $info[$line->RouteId][$line->WholesalerId] = array();
            
            $info[$line->RouteId][$line->WholesalerId][] = $line;
        }
        
//        var_dump($info);
//        die;
    
        ?>

<h2>
    Schedule for <?php echo $model->deliveryDate; ?>, <?php echo $all[0]->ClientName; ?>, <?php echo $all[0]->PrintCentreName; ?>
</h2>

<div class="titleWrap">
    <ul>
        <li class="seperator">
            <button id="btnBoomSheet">
                Boom Sheet
                <img src="<?php echo $baseUrl; ?>/img/icons/report.png" />
            </button>
        </li>
        
        <li class="seperator">
            <button id="btnWholesalerReport">
                Wholesaler Report
                <img src="<?php echo $baseUrl; ?>/img/icons/report.png" />
            </button>
        </li>
        
        <li class="seperator">
            <button id="btnPrintSchedule">
                Daily Schedule
                <img src="<?php echo $baseUrl; ?>/img/icons/printer.png" />
            </button>
        </li>
        
        <li class="seperator">
            <button id="btnPrintLoadSheets">
                All Load Sheets
                <img src="<?php echo $baseUrl; ?>/img/icons/printer.png" />
            </button>
        </li>
        
        <li class="seperator">
            <button id="btnPrintNotes">
                All Delivery Notes
                <img src="<?php echo $baseUrl; ?>/img/icons/printer.png" />
            </button>
        </li>
        
    </ul>
</div>

<table class="listing fluid vtop">
    <tr>
        <td colspan="3">&nbsp;</td>
        <th colspan="3">Departure Time</th>
        <th colspan="3">Arrival Time</th>
        <th colspan="2">NPA</th>
        <th colspan="2">Pallets Delivered</th>
        <th colspan="2">Pallets Collected</th>
    </tr>
    <tr>
        <th>Wholesaler</th>
        <th>Route Id</th>
        <th>Vehicle</th>
        <th>SCH</th>
        <th>ACT</th>
        <th>+/-</th>
        <th>SCH</th>
        <th>ACT</th>
        <th>+/-</th>
        <th>TIME</th>
        <th>VAR</th>
        <th>Plastic</th>
        <th>Wooden</th>
        <th>Plastic</th>
        <th>Wooden</th>        
    </tr>
    
    <?php foreach ($info as $routeId => $drops): 
    
            $routeEditableRowDisplayed = FALSE;
    
            foreach($drops as $wsId => $titles):
        
                $item = $titles[0];
            
                $hasMagazines = FALSE;
                foreach($titles as $title)
                    if ($title->TitleType == 'M')
                        $hasMagazines = TRUE;

                $noTitles = ($item->ClientTitleId == '');
            
                $smallReadOnlyAttrs = array_merge($smallReadOnlyAttrsBase, array(
                                        'rel' => $item->ClientRouteInstanceId,
//                                        'class' => $smallReadOnlyAttrsBase['class'].' route-link'
                                    ));
                
                $isEmpty = empty($item->ClientRouteInstanceDetailsId);
                if (!$isEmpty)
                {
                    // make time boxes selectable
                    $smallReadOnlyAttrs = array_merge($smallReadOnlyAttrs, array(
                                        'class' => $smallReadOnlyAttrsBase['class'].' route-link'
                                    ));
                }
            
        ?>
    <tr class="row<?php echo (($dropsIdx++ % 2) +1); ?>">
        <td>
            <?php if (!$isEmpty): ?>
            
            <a href="<?php echo $this->createUrl('clientrouting/dropmove', array('ui'=>'popUp', 'id' => $item->ClientRouteInstanceDetailsId));?>"
               class="drop-link">
                <img title="Move to another route"
                        src="<?php echo $baseUrl; ?>/img/icons/arrow_switch.png" /></a>
            |
            <a href="<?php echo $this->createUrl('clientrouting/wholesaler', array('ui'=>'popUp', 'wsid' => $item->ClientWholesalerId));?>"
               class="drop-link">
                <img title="Edit Wholesaler"
                        src="<?php echo $baseUrl; ?>/img/icons/pencil.png" /></a>
            |
            <a href="<?php echo $this->createUrl('clientrouting/routeinstancedrop', array('ui'=>'popUp', 'rid' => $item->ClientRouteInstanceId, 'wsid' => $item->ClientWholesalerId));?>"
               class="drop-link">
            <?php echo $item->WholesalerAlias; ?>
            </a>
            <img src="<?php echo $baseUrl; ?>/img/icons/bullet_arrow_down.png" />
            <?php if($hasMagazines): ?>
            <img src="<?php echo $baseUrl; ?>/img/icons/book_next.png" title="Magazines/Supplements have been added to this drop" />
            <?php endif; ?>
            <?php if($noTitles): ?>
            <img src="<?php echo $baseUrl; ?>/img/icons/book_error.png" title="There are no titles in this drop" />
            <?php endif; ?>
            
            <?php else:  // EMPTY ROUTE ?>
            
                <div class="warningBox">
                    (empty route)
                    <a href="#" class="route-delete" rel="<?php echo $item->ClientRouteInstanceId; ?>" route="<?php echo $item->RouteId; ?>">
                        <img src="<?php echo $baseUrl; ?>/img/icons/delete.png" title="Delete Route" />
                    </a>
                </div>
                
            <?php endif; ?>
        </td>
        <?php if (!$routeEditableRowDisplayed): 
                $routeEditableRowDisplayed = TRUE; ?>
        <td>
            <?php echo $item->RouteId; ?>
        </td>
        <td>
            <?php echo $item->VehicleDescription; ?>
        </td>
        <td width="5">
            <?php echo CHtml::textField('dt', $item->DepartureTime, $smallReadOnlyAttrs); ?>
        </td>
        <td>
            <?php echo CHtml::textField('dta', $item->DepartureTimeActual, $smallReadOnlyAttrs); ?>
        </td>
        <td>
            <?php 
                $depVar = RoutingScheduleForm::getDepartureVariance($item);
                $fDepVar = RoutingScheduleForm::formatHHMM($depVar);
                if ($depVar === FALSE)
                    echo CHtml::textField('dtv', 'N/A', array_merge($smallReadOnlyAttrs, array('class' => $smallReadOnlyAttrs['class'].' warning')));
                else if ($depVar < 0)
                    echo CHtml::textField('dtv', "-{$fDepVar}m", array_merge($smallReadOnlyAttrs, array('class' => $smallReadOnlyAttrs['class'].' good')));
                else if ($depVar > 0)
                    echo CHtml::textField('dtv', "+{$fDepVar}m", array_merge($smallReadOnlyAttrs, array('class' => $smallReadOnlyAttrs['class'].' err')));
                else
                    echo CHtml::textField('dtv', "-", array_merge($smallReadOnlyAttrs, array('class' => $smallReadOnlyAttrs['class'].' good')));
            ?>
        </td>
        <?php else: ?>
        <td colspan="5">&nbsp;</td>    
        <?php endif; ?>
        <td>
            <?php echo CHtml::textField('at', $item->ArrivalTime, $smallReadOnlyAttrs); ?>
        </td>
        <td>
            <?php echo CHtml::textField('ata', $item->ArrivalTimeActual, $smallReadOnlyAttrs); ?>
        </td>
        <td>
            <?php 
                $arrVar = RoutingScheduleForm::getArrivalVariance($item);
                $fArrVar = RoutingScheduleForm::formatHHMM($arrVar);
                if ($arrVar === FALSE)
                    echo CHtml::textField('atv', 'N/A', array_merge($smallReadOnlyAttrs, array('class' => $smallReadOnlyAttrs['class'].' warning')));
                else if ($arrVar < 0)
                    echo CHtml::textField('atv', "-{$fArrVar}m", array_merge($smallReadOnlyAttrs, array('class' => $smallReadOnlyAttrs['class'].' good')));
                else if ($arrVar > 0)
                    echo CHtml::textField('atv', "+{$fArrVar}m", array_merge($smallReadOnlyAttrs, array('class' => $smallReadOnlyAttrs['class'].' err')));
                else
                    echo CHtml::textField('atv', "-", array_merge($smallReadOnlyAttrs, array('class' => $smallReadOnlyAttrs['class'].' good')));
            ?>
        </td>
        <td>
            <?php echo CHtml::textField('npa', $item->NPATime, $smallReadOnlyAttrs); ?>
        </td>
        <td>
            <?php 
                $npaVar = RoutingScheduleForm::getNPAVariance($item);
                $fNpaVar = RoutingScheduleForm::formatHHMM($npaVar);                
                if ($npaVar === FALSE)
                    echo CHtml::textField('npav', 'N/A', array_merge($smallReadOnlyAttrs, array('class' => $smallReadOnlyAttrs['class'].' warning')));
                else if ($npaVar <= 0)
                    echo CHtml::textField('npav', "-", array_merge($smallReadOnlyAttrs, array('class' => $smallReadOnlyAttrs['class'].' good')));
                else
                    echo CHtml::textField('npav', "+{$fNpaVar}m", array_merge($smallReadOnlyAttrs, array('class' => $smallReadOnlyAttrs['class'].' err')));
            ?>
        </td>
        <td>
            <?php echo CHtml::textField('ppd', $item->PlasticPalletsDelivered, $smallReadOnlyAttrs); ?>
        </td>
        <td>
            <?php echo CHtml::textField('wpd', $item->WoodenPalletsDelivered, $smallReadOnlyAttrs); ?>
        </td>
        <td>
            <?php echo CHtml::textField('ppc', $item->PlasticPalletsCollected, $smallReadOnlyAttrs); ?>
        </td>
        <td>
            <?php echo CHtml::textField('wpc', $item->WoodenPalletsCollected, $smallReadOnlyAttrs); ?>
        </td>
        <td width="15">
            <?php if (!$isEmpty): ?>
            <button title="Print Delivery Note" class="note-print" rel="<?php echo $item->ClientWholesalerId; //ClientRouteInstanceDetailsId; ?>">
                <img src="<?php echo $baseUrl; ?>/img/icons/printer.png" />
            </button>
            <?php endif; ?>
        </td>
    </tr>
    <?php endforeach; 
        // Route Separator
    ?>
    <tr>
        <td colspan="16">
            <hr />
        </td>
    </tr>
    <?php endforeach; ?>
</table>

<form action="<?php echo $this->createUrl('clientrouting/printdeliverynotes'); ?>"
      target="_blank"
      id="formNotePrint"
      method="post">
    <?php echo CHtml::hiddenField('Print[DeliveryDate]', $model->scheduleInfo[0]->DeliveryDate); ?>
    <?php echo CHtml::hiddenField('Print[PrintCentreId]', $model->scheduleInfo[0]->PrintCentreId); ?>
    <?php //echo CHtml::hiddenField('Print[ClientRouteInstanceDetailsId]', '');
            echo CHtml::hiddenField('Print[ClientWholesalerId]', '');?>
</form>

<form action="<?php echo $this->createUrl('clientrouting/printschedule'); ?>"
      target="_blank"
      id="schedulePrint"
      method="post">
    <?php echo CHtml::hiddenField('Print[DeliveryDate]', $model->scheduleInfo[0]->DeliveryDate); ?>
    <?php echo CHtml::hiddenField('Print[PrintCentreId]', $model->scheduleInfo[0]->PrintCentreId); ?>
</form>

<?php endif; 
endif;
?>

<form action="<?php echo $this->createUrl('clientrouting/blankroute'); ?>"
      target="_blank"
      id="newRoute"
      method="post">
    <?php echo CHtml::hiddenField('Route[clientId]', $model->clientId); ?>
    <?php echo CHtml::hiddenField('Route[deliveryDate]', $model->deliveryDate); ?>
    <?php echo CHtml::hiddenField('Route[printCentreId]', $model->printCentreId); ?>
</form>

<form action="<?php echo $this->createUrl('report/wholesalerreport'); ?>"
      target="_blank"
      id="wsReport"
      method="post">
    <?php echo CHtml::hiddenField('Report[clientId]', $model->clientId); ?>
    <?php echo CHtml::hiddenField('Report[deliveryDate]', $model->deliveryDate); ?>
    <?php echo CHtml::hiddenField('Report[printCentreId]', $model->printCentreId); ?>
</form>

<form action="<?php echo $this->createUrl('report/boomsheet'); ?>"
      target="_blank"
      id="boomReport"
      method="post">
    <?php echo CHtml::hiddenField('Report[clientId]', $model->clientId); ?>
    <?php echo CHtml::hiddenField('Report[deliveryDate]', $model->deliveryDate); ?>
    <?php echo CHtml::hiddenField('Report[printCentreId]', $model->printCentreId); ?>
</form>

<form action="<?php echo $this->createUrl('clientrouting/printloadsheets'); ?>"
      target="_blank"
      id="loadSheetsReport"
      method="post">
    <?php echo CHtml::hiddenField('Print[clientId]', $model->clientId); ?>
    <?php echo CHtml::hiddenField('Print[deliveryDate]', $model->deliveryDate); ?>
    <?php echo CHtml::hiddenField('Print[printCentreId]', $model->printCentreId); ?>
</form>

<script>
$(function(){
    $(".dpicker").datepicker({
        dateFormat: 'dd/mm/yy',
        onSelect: function() {
            $(this).removeClass("error");
            $(this).change();
        }
    });
    
    $(".route-link").click(function(){
        var url = "<?php echo $this->createUrl('clientrouting/routeinstance', array('ui'=>'popUp'));?>";
        url += (url.indexOf("?") == -1) ? "?" : "&";
        url += "id=" + $(this).attr('rel');
        $.colorbox({href: url, width:"1000px", height:"600px", iframe:true, onClosed:reloadDropDowns});
        return false;
    });
    
    $(".drop-link").colorbox({width:"900px", height:"600px", iframe:true, onClosed:reloadDropDowns});
    
    $(".note-print").click(function(){
        printDeliveryNotes($(this).attr('rel'));
        return false;
    });
    
    $("#btnPrintNotes").click(function(){
        printDeliveryNotes(); // all notes for date/pc
        return false;
    });
    
    $("#btnPrintLoadSheets").click(function(){
        $("#loadSheetsReport").submit();
        return false;
    });
    
    $("#btnPrintSchedule").click(function(){
        $("#schedulePrint").submit();
        return false;
    });
    
    $("#btnWholesalerReport").click(function(){
        $("#wsReport").submit();
        return false;
    });
    
    $("#btnBoomSheet").click(function(){
        $("#boomReport").submit();
        return false;
    });
    
    $("#lnkBlankRoute").click(function(){
        // generate blank route (remote)
        $.post($("#newRoute").attr("action"),
                $("#newRoute").serialize(),
                function(data){
                    if (data.result == 0)
                        location.reload();
                    else
                        alert("An error has occurred. Please verify there is a route in the system for the same weekday as the selected date, within the previous week.");
                },
            'json');
        // reload when success
        return false;
    });
    
    $(".route-delete").click(function(){
        if (!confirm("This will delete route " + $(this).attr('route') + ". Please confirm you wish to proceed." ))
            return false;
        
        var url = "<?php echo $this->createUrl('clientrouting/routedelete', array('ui'=>'popUp'));?>";
        $.post(url,
                {"instanceid" : $(this).attr('rel')},
                function (data) {
                    if (data.error == 0)
                        reloadDropDowns();
                    else
                        alert('An error has occurred.');
                },
                'json');
                
        return false;
    });
});

function printDeliveryNotes(id)
{
    id = (id == undefined) ? '' : id;
    //$("#Print_ClientRouteInstanceDetailsId").val(id);
    $("#Print_ClientWholesalerId").val(id);
    $("#formNotePrint").submit();
}

function reloadDropDowns()
{
    location.reload();
}
</script>