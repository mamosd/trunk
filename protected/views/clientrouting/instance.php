<?php
    $this->breadcrumbs=array(
                array('label'=>'Home', 'url'=>array(Yii::app()->user->role->HomeUrl)),
                array('label'=>'Client Routing', 'url' => '#'),
                array('label'=>'Instance')
            );
    
    $baseUrl = Yii::app()->request->baseUrl;

    $routeInfo = $model->details[0];
    
    // group details by wholesaler
    $details = $model->details;
    $items = array();
    foreach($details as $detail)
    {
        if (!isset($items[$detail->ClientWholesalerId]))
                $items[$detail->ClientWholesalerId] = $detail; //array();
        //$items[$detail->ClientWholesalerId][] = $detail;
    }
    
    $deliveryDate = CTimestamp::formatDate("d/m/Y",CDateTimeParser::parse($routeInfo->DeliveryDate, "yyyy-MM-dd"))
?>
<style>
    .time-col
    {
        text-align: center;
    }
    
    .time-col input
    {
        width: 90%;
        text-align: center;
    }
</style>

<h1><?php echo $routeInfo->ClientName.' ('.$routeInfo->PrintCentreName.') - '.$deliveryDate.' - '.$routeInfo->RouteId;  ?></h1>

<div class="standardForm">
<?php $form=$this->beginWidget('CActiveForm', array(
            'id'=>'instance-form',
            'errorMessageCssClass'=>'formError',
    )); ?>

    <?php
    echo $form->hiddenField($model, 'routeInstanceId');
    echo $form->errorSummary($model, "", "", array('class'=>'errorBox'));
    ?>

    <table width="100%">
        <tr>
            <td>
                <div>
                    <label>Vehicle *</label>
                    <?php //echo CHtml::textField("RtData[Vehicle]", $routeInfo->VehicleId); ?>
                    <?php echo CHtml::dropDownList("RtData[Vehicle]", $routeInfo->VehicleId, $model->getVehicleOptions(), array('empty' => '-- select one')); ?>
                </div>                
            </td>
            <td>
                <div>
                    <label>Departure Time (SCH) *</label>
                    <?php echo CHtml::textField("RtData[DepartureTime]", $routeInfo->DepartureTime, array('class' => 'hhmm')); ?>
                </div>                
            </td>
            <td>
                <div>
                    <label>Departure Time (ACT)</label>
                    <?php echo CHtml::textField("RtData[DepartureTimeActual]", $routeInfo->DepartureTimeActual, array('class' => 'hhmm')); ?>
                </div>                
            </td>
        </tr>
    </table>
    
    <br/>
    
    <table class="listing fluid vtop">
    <tr>
        <td>&nbsp;</td>
        <th colspan="2">Arrival Time</th>
        <th>NPA</th>
        <th colspan="2">Pallets Delivered</th>
        <th colspan="2">Pallets Collected</th>
    </tr>
    <tr>
        <th>Wholesaler</th>
        <th>SCH *</th>
        <th>ACT</th>
        <th>TIME *</th>
        <th>Plastic</th>
        <th>Wooden</th>
        <th>Plastic</th>
        <th>Wooden</th>
    </tr>
    <?php 
    $dropsIdx = 0;
    foreach($items as $wsId => $item): ?>
    <tr class="row<?php echo (($dropsIdx++ % 2) +1); ?>">
        <td style="white-space: nowrap">
            <?php echo $item->WholesalerAlias; ?>
        </td>
        <td class="time-col">
            <?php echo CHtml::textField("WsData[$wsId][ArrivalTime]", $item->ArrivalTime, array('class' => 'hhmm')); ?>
        </td>
        <td class="time-col">
            <?php echo CHtml::textField("WsData[$wsId][ArrivalTimeActual]", $item->ArrivalTimeActual, array('class' => 'hhmm')); ?>
        </td>
        <td class="time-col">
            <?php echo CHtml::textField("WsData[$wsId][NPATime]", $item->NPATime, array('class' => 'hhmm')); ?>
        </td>
        <td class="time-col">
            <?php echo CHtml::textField("WsData[$wsId][PlasticDelivered]", $item->PlasticPalletsDelivered, array('class' => 'nbr')); ?>
        </td>
        <td class="time-col">
            <?php echo CHtml::textField("WsData[$wsId][WoodenDelivered]", $item->WoodenPalletsDelivered, array('class' => 'nbr')); ?>
        </td>
        <td class="time-col">
            <?php echo CHtml::textField("WsData[$wsId][PlasticCollected]", $item->PlasticPalletsCollected, array('class' => 'nbr')); ?>
        </td>
        <td class="time-col">
            <?php echo CHtml::textField("WsData[$wsId][WoodenCollected]", $item->WoodenPalletsCollected, array('class' => 'nbr')); ?>
        </td>
    </tr>    
    <?php endforeach; ?>
    </table>

    <div class="titleWrap">
        <?php echo CHtml::submitButton('Submit', array('class'=>'formButton btn-submit')); ?>
        <ul>
            <li class="seperator">
                <img height="16" width="16" alt="add" src="img/icons/cancel.png">
                <a href="#" onclick="parent.$.colorbox.close(); return false;">Cancel</a>
            </li>
        </ul>

    </div>
    
    <em><strong>*</strong> : altering these fields value will affect route for future imports.</em>

<?php
$this->endWidget(); ?>
</div>

<script>
$(function(){
    $(".btn-submit").click(function(){
        var bError = false;
        var toFocus = null;
        var msg = "";
        $(".hhmm").each(function(){
            if (!bError){
                var val = $.trim($(this).val());
                bError = (val != "") && !val.match(/^([0-9]|0[0-9]|1[0-9]|2[0-3]):[0-5][0-9]$/);
                toFocus = (bError) ? $(this) : null;
                msg = (bError) ? "Only values expressed in HH:MM format are allowed." : "";
            }
        });
        $(".nbr").each(function(){
            if (!bError){
                var val = $.trim($(this).val());
                bError = (val != "") && (parseInt(val,10) != val);
                toFocus = (bError) ? $(this) : null;
                msg = (bError) ? "Only numeric values are allowed." : "";
            }
        });        
        if (bError)
        {
            alert(msg);
            toFocus.select();
            return false;
        }
    });
});
</script>
