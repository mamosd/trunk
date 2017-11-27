<style type="text/css">
    .dropdown1 {  margin-left: 2em; width: 300px; }
    .formButton { margin-top: 32px; }
</style>
<?php
    $this->breadcrumbs=array(
                array('label'=>'Home', 'url'=>array(Yii::app()->user->role->HomeUrl)),
                array('label'=>'Pallet Report'),
            );
?>

<div class="titleWrap">
    <h1>Pallet Report</h1>
</div>

<div class="standardForm">
<?php $form=$this->beginWidget('CActiveForm', array(
        'id'=>'pallet-report-form',
        'method' => 'get',
        'action' => $this->createUrl('admin/reportingpallet'),
        'errorMessageCssClass'=>'formError',
)); ?>

    <?php
    echo $form->errorSummary($model, "", "", array('class'=>'errorBox'));
    ?>

    <table class="fluid">
        <tr>
            <td>
                <div>
<!-- json_decode('{"mr":"Overview Report (all print centres, all suppliers)"}')-->
<!--"by Print Centre"}'));-->
<!--"by Route"}')); ?><br>-->
<!--"by Delivery Point"}')                    -->
                    <?php
                        echo $form->labelEx($model,'reportType');
                        echo CHtml::radioButton('AdminReportingPallet[reportType]', $model->reportType == 'mr', array('id' => 'AdminReportingPallet_reportType0', 'value' => 'mr'));
                        echo CHtml::label('Overview Report (all print centres, all suppliers)', 'AdminReportingPallet_reportType0') . '<br>';

                        echo CHtml::radioButton('AdminReportingPallet[reportType]', $model->reportType == 'pc', array('id' => 'AdminReportingPallet_reportType1', 'value' => 'pc'));
                        echo CHtml::label('by Print Centre', 'AdminReportingPallet_reportType1') . '<br>';
                        echo CHtml::radioButton('AdminReportingPallet[reportType]', $model->reportType == 'rt', array('id' => 'AdminReportingPallet_reportType2', 'value' => 'rt'));
                        echo CHtml::label('by Route', 'AdminReportingPallet_reportType2') . '<br>';

                        echo CHtml::radioButton('AdminReportingPallet[reportType]', $model->reportType == 'dp', array('id' => 'AdminReportingPallet_reportType3', 'value' => 'dp'));
                        echo CHtml::label('by Delivery Point', 'AdminReportingPallet_reportType3') . '<br>';
                        echo $form->dropDownList($model, 'deliveryPoint',
                            CHtml::listData(DeliveryPoint::model()->findAll(array('order' => 'Name')), 'DeliveryPointId', 'Name'),
                            array('empty'=>'select one ->', 'class' => 'dropdown1')) . '<br>';

                        echo CHtml::label('Supplier', 'AdminReportingPallet_supplier');
                        echo $form->dropDownList($model, 'supplier',
                            CHtml::listData(Supplier::model()->findAll(array('order' => 'Name')), 'SupplierId', 'Name'),
                            array('empty'=>'select one ->', 'class' => 'dropdown1')) . '<br>';
                    ?>
                </div>
            </td>
            <td width="50%">
                <div>
                    <?php echo $form->labelEx($model,'from'); ?>
                    <?php echo $form->textField($model,'from', array('size'=>'10', 'class'=>'dpicker', 'autocomplete' => 'off')); ?>
                </div>

                <div>
                    <?php echo $form->labelEx($model,'to'); ?>
                    <?php echo $form->textField($model,'to', array('size'=>'10', 'class'=>'dpicker', 'autocomplete' => 'off')); ?>
                </div>
                <div class="titleWrap">
                    <?php echo CHtml::submitButton('Submit', array('class'=>'formButton', 'id'=>'btnSubmit')); ?>
                </div>
            </td>
        </tr>
    </table>
    

<?php
$this->endWidget(); ?>
</div>

<script type="text/javascript">
$(document).ready(function(){
    $(".report-type").click(function(){
        $(".report-type").removeClass('highlighted');
        $(".report-type-container").val($(this).attr('value'));
        $(this).addClass('highlighted');
        return false;
    });
    if ($(".report-type-container").val() != '')
        $(".report-type[value='"+$(".report-type-container").val()+"']").click();
    else
        $(".report-type:first").click();

    $(".dpicker").datepicker({
        dateFormat: 'dd/mm/yy',
        onSelect: function() {
            $(this).removeClass("error");
            $(this).change();
        }
    });
    $("#AdminReportingPallet_reportType0").change(function () {
        $("#AdminReportingPallet_supplier").attr('disabled', 'disabled');
        $("#AdminReportingPallet_deliveryPoint").attr('disabled', 'disabled'); }
    )
    $("#AdminReportingPallet_reportType1").change(function () {
        $("#AdminReportingPallet_supplier").removeAttr('disabled');
        $("#AdminReportingPallet_deliveryPoint").attr('disabled', 'disabled'); }
    )
    $("#AdminReportingPallet_reportType2").change(function () {
        $("#AdminReportingPallet_supplier").removeAttr('disabled');
        $("#AdminReportingPallet_deliveryPoint").attr('disabled', 'disabled'); }
    )
    $("#AdminReportingPallet_reportType3").change(function () {
        $("#AdminReportingPallet_supplier").attr('disabled', 'disabled');
        $("#AdminReportingPallet_deliveryPoint").removeAttr('disabled'); }
    )
    if($("#pallet-report-form input[type='radio']:checked").val() == "mr") {
        $("#AdminReportingPallet_supplier").attr('disabled', 'disabled');
        $("#AdminReportingPallet_deliveryPoint").attr('disabled', 'disabled');
    }
});
</script>
<!--<input type="radio" onchange="js:alert("eeeeee");"> -->
<!-- REPORT -->
<?php
if($model->reportType == 'mr' || $model->reportType == 'pc') {
    $this->renderPartial('reportingMrPallet', array('model' => $model));
}
else {
if(isset($model->details)):
    $count = count($model->details);
    if ($count == 0):
?>
    <div class="warningBox">There is no information on the system for the criteria provided.</div>
<?php
    else:
        // get totals
        $pd = 0;
        $pc = 0;
        foreach($model->details as $d)
        {
            $pc += $d->PalletsCollected;
            $pd += $d->PalletsDelivered;
        }
?>

<div class="titleWrap">
    <ul>
        <li>
            <table class="listing">
                <tr>
                    <th colspan="3">Pallets</th>
                </tr>
                <tr>
                    <th>Collected</th>
                    <th>Delivered</th>
                    <th>Balance</th>
                </tr>
                <tr>
                    <td><?php echo CHtml::textField("Report[collected]", $pc, array('size'=>'5', 'class'=>'readOnlyField', 'tabindex'=>'-1', 'readonly'=>'readonly')); ?></td>
                    <td><?php echo CHtml::textField("Report[delivered]", $pd, array('size'=>'5', 'class'=>'readOnlyField', 'tabindex'=>'-1', 'readonly'=>'readonly')); ?></td>
                    <td><?php echo CHtml::textField("Report[balance]", ($pc-$pd), array('size'=>'5', 'class'=>'readOnlyField', 'tabindex'=>'-1', 'readonly'=>'readonly')); ?></td>
                </tr>
            </table>
        </li>
    </ul>
</div>

<table class="listing fluid">
    <tr>
        <th>Date</th>
        <th>
        <?php
        if ($model->reportType == 'mr')
            echo "Overview Report (all print centres, all suppliers)";
        if ($model->reportType == 'pc')
            echo "Print Centre Name";
        if ($model->reportType == 'rt')
            echo "Route Name";
        if ($model->reportType == 'dp')
            echo "Delivery Point Name";
        ?>
        </th>
        <th>Collected</th>
        <th>Returned</th>
        <th>Balance</th>
    </tr>
    <?php
    $control = getControlField($model->reportType, $model->details[0]);
    $pc = 0;
    $pd = 0;
    $itemName = getDescField($model->reportType, $model->details[0]);
    $date = $model->details[0]->DeliveryDate;
    $idx = 0;
    foreach($model->details as $d):
        if (($control == getControlField($model->reportType, $d)) && ($date == $d->DeliveryDate)):
            $pc += $d->PalletsCollected;
            $pd += $d->PalletsDelivered;
        else:
            ?>
            <tr class="row<?php echo ($idx%2)+1 ?>">
                <td><?php echo $date; ?></td>
                <td><?php echo $itemName?></td>
                <td><?php echo $pc; ?></td>
                <td><?php echo $pd; ?></td>
                <td><?php echo (0 + $pc - $pd); ?></td>
            </tr>
            <?php
            $idx++;
            $control = getControlField($model->reportType, $d);
            $pc = $d->PalletsCollected;
            $pd = $d->PalletsDelivered;
            $itemName = getDescField($model->reportType, $d);
            $date = $d->DeliveryDate;
        endif;
    endforeach; 
    ?>
    <tr class="row<?php echo ($idx%2)+1 ?>">
        <td><?php echo $date; ?></td>
        <td><?php echo $itemName?></td>
        <td><?php echo $pc; ?></td>
        <td><?php echo $pd; ?></td>
        <td><?php echo (0 + $pc - $pd); ?></td>
    </tr>
</table>

<?php
    endif;
endif;
}

function getControlField($type, $d)
{
    $result = -1;
    if ($type == 'pc')
        $result = $d->PrintCentreId;
    if ($type == 'rt')
        $result = $d->RouteId;
    if ($type == 'dp')
        $result = $d->DeliveryPointId;
    return $result;
}
function getDescField($type, $d)
{
    $result = '';
    if ($type == 'pc')
        $result = $d->PrintCentreName;
    if ($type == 'rt')
        $result = $d->RouteName;
    if ($type == 'dp')
        $result = $d->DeliveryPointName;
    return $result;
}

?>