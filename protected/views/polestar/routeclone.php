<?php
    $baseUrl = Yii::app()->request->baseUrl;
    $readOnlyAttrs = array('class' => 'readOnlyField', 'readOnly' => 'readOnly');

    $jobInfo = $model->sourceJobInfo;
    
    $start=strtotime('00:00');
    $end=strtotime('23:45');
    $arrayTime=array();
    for ($halfhour=$start;$halfhour<=$end;$halfhour=$halfhour+15*60) {
        $time =    date('H:i',$halfhour);
        $timeStr = date('H:i:00'  ,$halfhour);
        $arrayTime[$timeStr]=$time;
    }
    
    $nextDay = new DateTime($jobInfo->DeliveryDate);
    //$nextDay = $nextDay->modify('+1 day'); // uncomment to NOT allow same day
?>

<h1>
    Cloning <?php echo $jobInfo->Ref ?>
</h1>

<?php $form=$this->beginWidget('CActiveForm', array(
    'id'=>'job-clone-form',
    'errorMessageCssClass'=>'formError',
));
    echo $form->errorSummary($model, "", "", array('class'=>'errorBox'));
?>
<fieldset>
    <legend>General Details</legend>
    <table class="listing fluid">
        <tr>
            <td>
                <div class="stackedForm">
                    <div class="field">
                        <?php
                        echo $form->labelEx($model,'newRef', FALSE);
                        // *** Always read only for auto generation only
                            echo $form->textField($model,'newRef', array_merge(array('size' => 15), $readOnlyAttrs));
                        ?>
                    </div>
                    <div class="field">
                        <?php echo $form->labelEx($model, 'collDate'); ?>
                        <?php echo $form->textField($model, 'collDate', array('size' => '10', 'readonly' => 'readonly', 'class' => 'col-dpicker data-required')); ?>
                    </div>
                    </div>
            </td>
        </tr>
    </table>
</fieldset>

<fieldset>
    <legend>Collection Information</legend>
        <table class="listing fluid">
            <tr>
                <th>Postcode</th>
                <th>Address</th>
                <th>Sched.</th>
            </tr>
        <?php
        $points = array($jobInfo);
        foreach ($jobInfo->CollectionPoints as $p)
            $points[$p->Id] = $p;

        foreach ($points as $pointId => $point):
        ?>
            <tr>
                <td style="white-space: nowrap;">
                   <?php echo $point->CollPostcode; ?>
                </td>
                <td style="white-space: nowrap;">
                    <?php echo $point->CollAddress; ?>
                </td>
                <td>
                    <?php echo $form->dropDownList($model, "collScheduledTime[$pointId]", $arrayTime, array('empty' => 'TBC')); ?>
                </td>
            </tr>
        <?php endforeach; ?>
        </table>
</fieldset>

<fieldset>
    <legend>Load Information</legend>

    <table class="listing fluid" id="load-listing">
        <tr>
            <td colspan="2"></td>
            <th colspan="3">Pallets</th>
            <td colspan="20"></td>
        </tr>
        <tr>
            <th>PolestarLoadRef</th>
            <th>Publication</th>
            <th>Full</th>
            <th>Half</th>
            <th>Qtr</th>
            <th>Quantity</th>
            <th>Weight</th>
            <th width="1%">Postcode</th>
            <th width="1%">Date</th>
            <th width="1%">Sched.</th>
            <th>Job/Load Ref. No.</th>
            <th>Booking Ref</th>
        </tr>
        <?php foreach($jobInfo->Loads as $load): 
            if ($load->StatusId == PolestarStatus::CANCELLED_ID) // do not show cancelled loads
                continue;
        ?>
        <tr>
            <td style="white-space: nowrap;">
                <?php echo $form->textField($model, "ref[{$load->Id}]", array('size' => 15, 'class' => 'data-required')); ?>
            </td>
            <td style="white-space: nowrap;">
                <?php echo $load->Publication; ?>
            </td>
            <td>
                <?php echo $form->textField($model, "fullPallets[{$load->Id}]", array('size' => 2, 'class' => 'pallets-required')); ?>
            </td>
            <td>
                <?php echo $form->textField($model, "halfPallets[{$load->Id}]", array('size' => 2, 'class' => 'pallets-required')); ?>
            </td>
            <td>
                <?php echo $form->textField($model, "qtrPallets[{$load->Id}]", array('size' => 2, 'class' => 'pallets-required')); ?>
            </td>
            <td>
                <?php echo $form->textField($model, "quantity[{$load->Id}]", array('size' => 5)); ?>
            </td>
            <td>
                <?php echo $form->textField($model, "kg[{$load->Id}]", array('size' => 5, 'class' => 'data-required')); ?>
            </td>
            <td style="white-space: nowrap;">
                <span title="<?php echo htmlentities($load->DelAddress); ?>">
                    <?php echo $load->DelPostcode; ?>
                </span>
            </td>
            <td>
                <?php echo $form->textField($model, "delDate[{$load->Id}]", array('size' => '10', 'readonly' => 'readonly', 'class' => 'del-dpicker data-required')); ?>
            </td>
            <td>
                <?php echo $form->dropDownList($model, "delScheduledTime[{$load->Id}]", $arrayTime, array('empty' => 'TBC')); ?>
            </td>
            <td>
                <?php echo $form->textField($model, "specialInstructions[{$load->Id}]", array('size' => 15, 'class' => 'data-required')); ?>
            </td>
            <td>
                <?php echo $form->textField($model, "bookingRef[{$load->Id}]", array('size' => 7)); ?>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>
    
</fieldset>

<br/>
<br/>
<button class="validate">Save and Exit</button>
<?php $this->endWidget(); ?>

<script>
var originalDate = "<?php echo $model->collDate ?>";
var originalRef = "<?php echo $model->newRef ?>";

$(function(){
    $(".col-dpicker").datepicker({
        dateFormat: 'dd/mm/yy',
        minDate: '<?php echo $nextDay->format('d/m/Y') ?>',
        onSelect: function() {
            $(this).removeClass("error");
            $(this).change();
            var newValue = $("#PolestarRouteCloneForm_collDate").val();
            if (newValue != originalDate) {
                $("#PolestarRouteCloneForm_newRef").val('TBD');
                $.ajax({
                    'url': '<?php echo Yii::app()->createUrl('polestar/routenewref'); ?>/',
                    'data': {
                        'pcid' : <?php echo $jobInfo->PrintCentreId ?>,
                        'dt': newValue
                    },
                    'success': function(data_str) {
                        $("#PolestarRouteCloneForm_newRef").val(data_str);
                    },
                    'error' : function() {
                        alert('An error has occurred while retrieving new job reference, a valid reference will be generated upon saving the changes.');                   
                    }
                });
            }
            else {
                $("#PolestarRouteCloneForm_newRef").val(originalRef);
            }
        }
    });
    
    $(".del-dpicker").datepicker({
        dateFormat: 'dd/mm/yy',
        minDate: '<?php echo $nextDay->format('d/m/Y') ?>',
    });
    
    $(".validate").click(function(){
        $(".data-required").removeClass('error');
        $(".pallets-required").removeClass('error');
        
        $(".data-required").each(function(){
            var val = $.trim($(this).val());
            if (val == '')
                $(this).addClass('error');
        });
        
        $("#load-listing tr").each(function(){
            var total = 0;
            $(".pallets-required", $(this)).each(function(){
                var val = $.trim($(this).val());
                total += (val == '') ? 0 : parseInt(val, 10);
            });
            if (isNaN(total) || (total == 0))
                $(".pallets-required", $(this)).addClass('error');
        });
        
        if ($('.error').length > 0) {
            alert('There are errors on the information entered, please revise.');
            return false;
        }
        return true;
    });
});
</script>