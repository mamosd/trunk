<?php    
    $baseUrl = Yii::app()->request->baseUrl;
    $readOnlyAttrs = array('class' => 'readOnlyField', 'readOnly' => 'readOnly');
    
    $smallReadOnlyAttrsBase = array_merge(array('size' => 5), $readOnlyAttrs);
    $smallReadOnlyAttrs = $smallReadOnlyAttrsBase;
    
    $mileageAttrs = (Login::checkPermission(Permission::PERM__FUN__POLESTAR__MILEAGE_EDIT))
            ? array('size' => 5, 'class' => 'mileage')
            : $smallReadOnlyAttrs;
    
    $start=strtotime('00:00');
    $end=strtotime('23:45');
    $arrayTime=array();
    for ($halfhour=$start;$halfhour<=$end;$halfhour=$halfhour+15*60) {
        $time =    date('H:i',$halfhour);
        $timeStr = date('H:i:00'  ,$halfhour);
        $arrayTime[$timeStr]=$time;
    }
    
    $jobInfo = $model->jobInfo;
    $adviceSheetSent = $jobInfo->isAdviceSheetSent();
?>
<style>
    table.listing th {
        white-space: nowrap !important;
    }
    select {
        height: 28px;
    }
</style>

<h1>
    <?php echo $jobInfo->Ref ?>
</h1>

<?php $form=$this->beginWidget('CActiveForm', array(
    'id'=>'info-entry-form',
    'errorMessageCssClass'=>'formError',
));
    echo $form->errorSummary($model, "", "", array('class'=>'errorBox'));
    echo $form->hiddenField($model, 'jobId');
    echo $form->hiddenField($model, 'clearHighlighting');
?>

<fieldset>
    <legend>General Details</legend>
    <table class="listing fluid">
        <tr>
            <td>
                <div class="stackedForm">
                    <div class="field">
                        <?php echo $form->labelEx($model, 'mileage');
                        echo $form->textField($model, 'mileage', $smallReadOnlyAttrs);
                        ?>
                    </div>
                    <div class="field">
                        <?php echo $form->labelEx($model, 'specialInstructions');
                        echo $form->textField($model, 'specialInstructions', array('size' => 50));
                        ?>
                    </div>
                    <div class="field">
                        <?php echo $form->labelEx($model, 'newComment');
                        echo $form->textField($model, 'newComment', array('size' => 50));
                        ?>
                    </div>

                </div>
            </td>
            <td>
                <?php if (!empty($jobInfo->Comments)): ?>
                <fieldset>
                    <legend>Comments</legend>

                    <div class="comment-thread-container">
                        <?php foreach($jobInfo->Comments as $comment): ?>
                        <div class="comment-footer">
                            <div class="user">
                                by <?php echo $comment->Login->FriendlyName; ?>
                            </div>
                            <div class="time">
                                <?php echo $comment->CreatedDate; ?>
                            </div>
                        </div>
                        <br style="clear:both"/>
                        <div id="cmt-<?php echo $comment->Id ?>" class="comment-box">
                            <div class="text">
                                <?php echo nl2br($comment->Comment) ; ?>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <br/>
                </fieldset>
                <?php endif; ?>
            </td>
        </tr>
    </table>
    
</fieldset>

<table class="listing fluid">
    <tr>
        <td>
            <fieldset>
                <legend>Supplier Information</legend>
                <div class="stackedForm">
                    <div class="field">
                        <?php echo CHtml::label('Provider', FALSE); 
                        $provider = isset($jobInfo->ProviderId) 
                                        ? PolestarSupplier::model()->findByPk($jobInfo->ProviderId)->Name
                                        : '';
                        echo CHtml::textField('provider', $provider, $readOnlyAttrs);
                        ?>
                    </div>
                    <div class="field">
                        <?php
                        echo $form->labelEx($model, 'supplier');
                        echo $form->dropDownList($model, 'supplier', PolestarSupplier::getAllAsOptions(), array('empty' => '- none selected -'));
                        ?>
                    </div>
                    <?php
                    $driverAttrs = array();
                    if (!$adviceSheetSent) {
                        echo "<em>Driver information below cannot be entered until advice sheet is sent</em><br/><br/>";
                        $driverAttrs = $readOnlyAttrs;
                    }
                    ?>
                    <div class="field">
                        <?php echo $form->labelEx($model, 'driverName');
                        echo $form->textField($model, 'driverName', array_merge(array('size' => 35), $driverAttrs));
                        ?>
                    </div>
                    <div class="field">
                        <?php echo $form->labelEx($model, 'vehicleRegNo');
                        echo $form->textField($model, 'vehicleRegNo', array_merge(array('size' => 10), $driverAttrs));
                        ?>
                    </div>
                    <div class="field">
                        <?php echo $form->labelEx($model, 'contactNo');
                        echo $form->textField($model, 'contactNo', array_merge(array('size' => 15), $driverAttrs));
                        ?>
                    </div>
                    <?php if (Login::checkPermission(Permission::PERM__FUN__POLESTAR__COSTING)): ?>
                    <div class="field">
                        <?php echo $form->labelEx($model, 'agreedPrice');
                        echo $form->textField($model, 'agreedPrice', array('size' => 10));
                        ?>
                    </div>
                    <?php endif; ?>
                </div>
            </fieldset>
        </td>
        <td>
            <fieldset>
                <legend>Collection Information</legend>
                <div class="stackedForm">
                    <div class="field">
                        <?php echo CHtml::label('Date', FALSE); 
                        echo CHtml::textField('collDate', $jobInfo->formatDate('DeliveryDate','d/m/Y'), $readOnlyAttrs);
                        ?>
                    </div>
                    
                    <table class="listing fluid">
                        <tr>
                            <th>Postcode</th>
                            <th>Sched.</th>
                            <th>Arrival</th>
                            <th>Departure</th>
                            <th>Mileage</th>
                        </tr>
                    <?php
                    $points = array($jobInfo);
                    foreach ($jobInfo->CollectionPoints as $p)
                        $points[$p->Id] = $p;
                    
                    foreach ($points as $pointId => $point):
                    ?>
                        <tr>
                            <td style="white-space: nowrap;">
                               <span title="<?php echo htmlentities($point->CollAddress); ?>"><?php echo $point->CollPostcode; ?></span> 
                            </td>
                            <td>
                               <?php echo CHtml::textField('collSchedTime', $point->formatTime('CollScheduledTime','H:i', 'TBC'), $smallReadOnlyAttrs); ?>
                            </td>
                            <td>
                                <?php echo $form->dropDownList($model, "arrivalTime[$pointId]", $arrayTime, array('empty' => '-')); ?>
                            </td>
                            <td>
                                <?php echo $form->dropDownList($model, "departureTime[$pointId]", $arrayTime, array('empty' => '-')); ?>
                            </td>
                            <td>
                               <?php echo $form->textField($model, "collMileage[$pointId]", $mileageAttrs); ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </table>
                </div>
            </fieldset>
        </td>
    </tr>
</table>

<fieldset>
    <legend>Load Information</legend>

    <table class="listing fluid">
        <tr>
            <td colspan="3"></td>
            <th colspan="6">Delivery</th>
        </tr>
        <tr>
            <th>PolestarLoadRef</th>
            <th>Pallets</th>
            <th width="5%">Postcode</th>
            <th width="1%">Date</th>
            <th width="1%">Sched.</th>
            <th width="1%">Time Code</th>
            <th width="1%">Arrival</th>
            <th width="1%">Departure</th>
            <th width="1%">Mileage</th>
            <th width="5%">Special Instructions</th>
            <th width="1%">Comments</th>
        </tr>
        <?php foreach($jobInfo->Loads as $load): 
            if ($load->StatusId == PolestarStatus::CANCELLED_ID) // do not show cancelled loads
                continue;
            $fieldPrefix = sprintf('PolestarInfoEntryForm[loads][%s]', $load->Id);
            echo CHtml::hiddenField($fieldPrefix."[id]", $load->Id);
            
            $exists = isset($model->loads[$load->Id]);
            $entry = $exists ? $model->loads[$load->Id] : array();
            $arrival = $exists ? $entry['arrival'] : $load->DelArrivalTime;
            $departure = $exists ? $entry['departure'] : $load->DelDepartureTime;
            $mileage = $exists ? $entry['mileage'] : $load->Mileage;
            $instructions = $exists ? $entry['instructions'] : $load->SpecialInstructions;
            $comment = $exists ? $entry['comment'] : '';
        ?>
        <tr>
            <td>
                <span title="<?php echo htmlentities($load->Publication); ?>">
                    <?php echo $load->Ref; ?>
                </span>
            </td>
            <td>
                <?php $palletBreakdown = sprintf('%s Full, %s Half, %s Quarter', $load->PalletsFull, $load->PalletsHalf, $load->PalletsQtr); ?>
                <span title="<?php echo htmlentities($palletBreakdown); ?>"><?php echo $load->PalletsTotal; ?>*</span>
            </td>
            <td style="white-space: nowrap;">
                <span title="<?php echo htmlentities($load->DelAddress); ?>"><?php echo $load->DelPostcode; ?></span>
            </td>
            <td>
                <?php echo $load->formatDate('DeliveryDate', 'd/m/Y'); ?>
            </td>
            <td>
                <?php echo CHtml::textField("dt", $load->formatTime('DelScheduledTime','H:i','TBC'), $smallReadOnlyAttrs); ?>
            </td>
            <td>
                <?php echo $load->DelTimeCode; ?>
            </td>
            <td>
                <?php echo CHtml::dropDownList($fieldPrefix."[arrival]", $arrival, $arrayTime, array('empty' => '-')); ?>
            </td>
            <td>
                <?php echo CHtml::dropDownList($fieldPrefix."[departure]", $departure, $arrayTime, array('empty' => '-')); ?>
            </td>
            <td>
                <?php echo CHtml::textField($fieldPrefix."[mileage]", $mileage, $mileageAttrs); ?>
            </td>
            <td>
                <?php echo CHtml::textField($fieldPrefix."[instructions]", $instructions); ?>
            </td>
            <td style="white-space: nowrap;">
                <?php echo CHtml::textField($fieldPrefix."[comment]", $comment); ?>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>
    
</fieldset>
<br/>
<button id="btnSave">Save and Exit</button>

<?php $this->endWidget(); ?>

<div id="dialog-clear-highlight" title="Clear Highlighting?" style="display:none;">
  <p>
      <span class="ui-icon ui-icon-alert" style="float:left; margin:0 7px 20px 0;"></span>
      Would you like to clear existing highlighted changes? 
  </p>
</div>

<script>
$(function(){
    $(".mileage").change(updateMileTotal);
    if ($(".mileage").length > 0)
        recalculateMileTotal();
    
    $("#btnSave").click(function(){
        <?php 
        $statusToHighlight = array(
            PolestarStatus::AMENDED_ID,
            PolestarStatus::LATE_ADVICE_ID
        );
        if (in_array($jobInfo->StatusId, $statusToHighlight) && 
                ($jobInfo->ClearHighlighting != 'Y') &&
                Login::checkPermission(Permission::PERM__FUN__POLESTAR__CLEAR_HIGHLIGHT)):
        ?>
        $( "#dialog-clear-highlight" ).dialog({
            resizable: false,
            height:140,
            modal: true,
            position: { my: "center bottom", at: "center top", of: $("#btnSave") },
            buttons: {
                Yes: function() {
                    $("#PolestarInfoEntryForm_clearHighlighting").val('Y');
                    $( this ).dialog( "close" );
                    $("#info-entry-form").submit();
                },
                No: function() {
                    $( this ).dialog( "close" );
                    $("#info-entry-form").submit();
                }
            }
        });
        return false;
        <?php endif; ?>
    });
});

function updateMileTotal(){
    var value = $(this).val();
    if(isNaN(value)) {
        $(this).addClass('error');
        alert('Please enter a numeric value');
        return false;
    }
    $(this).removeClass('error');
    
    recalculateMileTotal();
}

function recalculateMileTotal() {
    var total = 0.0;
    $(".mileage").each(function(){
        var value = parseFloat($(this).val());
        var newValue = Math.ceil(value);
        if (!isNaN(newValue)) {
            $(this).val(newValue);
            total += newValue;
        }
    });
    $("#PolestarInfoEntryForm_mileage").val(total);
}
</script>
    