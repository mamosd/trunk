<?php
    $newLoad =  ($model->Id == NULL);

    $baseUrl = Yii::app()->request->baseUrl;
    $readOnlyAttrs = array('class' => 'readOnlyField', 'readOnly' => 'readOnly');

    $start=strtotime('00:00');
    $end=strtotime('23:45');
    $arrayTime=array();
    for ($halfhour=$start;$halfhour<=$end;$halfhour=$halfhour+15*60) {
        $time =    date('H:i',$halfhour);
        $timeStr = date('H:i:00'  ,$halfhour);
        $arrayTime[$timeStr]=$time;
    }
    $cs=Yii::app()->getClientScript();
    $cs->registerScriptFile($baseUrl.'/js/geotools-loader.js',CClientScript::POS_HEAD,array(
        "charset" => "ISO-8859-1"
    ));
    
    $jobInfo = $model->getJob();
?>
<style>
    select {
        height: 28px;
    }
</style>
<h1>
    <?php echo $model->getJob()->Ref.' - '.($newLoad ? "Add Load to Route" : "Load edit") ?>
</h1>

<?php $form=$this->beginWidget('CActiveForm', array(
    'id'=>'load-form',
    'errorMessageCssClass'=>'formError',
));
    echo $form->errorSummary($model, "", "", array('class'=>'errorBox'));
    echo $form->hiddenField($model, 'Id');
    echo $form->hiddenField($model, 'JobId');
    echo $form->hiddenField($model, 'Sequence');
?>

<?php //echo $form->textField($model, 'Sequence', array('size' => 15)); ?>
<fieldset>
<br/>
<div class="stackedForm">
    <table class="listing fluid">
        <tr>
            <td>
                <div class="field">
                    <?php echo CHtml::label('Aktrion Job Ref', FALSE);
                    echo CHtml::textField('jobId', $jobInfo->Ref, $readOnlyAttrs);
                    ?>
                </div>

                <div class="field">
                    <?php echo CHtml::label('Collection Date', FALSE);
                    $dt = new DateTime($model->getJob()->DeliveryDate);
                    echo CHtml::textField('delDate', $dt->format('d/m/Y'), $readOnlyAttrs);
                    ?>
                </div>

                <div class="field">
                    <?php echo $form->labelEx($model, 'Ref');
                    echo $form->textField($model, 'Ref', array('size' => 15));
                    ?>
                </div>

                <div class="field">
                    <?php echo $form->labelEx($model, 'JobType', FALSE);
                        echo $form->dropDownList($model, 'JobType', PolestarJobType::getAllAsOptions(), array('empty' => '(blank/normal)'));
                    ?>
                </div>

                <div class="field">
                    <?php echo $form->labelEx($model, 'Publication');
                    echo $form->textField($model, 'Publication', array('size' => 35));
                    ?>
                </div>

                <div class="field">
                    <?php echo $form->labelEx($model, 'Quantity');
                    echo $form->textField($model, 'Quantity', array('size' => 7));
                    ?>
                </div>

                <div class="field">
                    <?php echo $form->labelEx($model, 'PalletsFull');
                    echo $form->textField($model, 'PalletsFull', array('size' => 3));
                    ?>
                </div>
                <div class="field">
                    <?php echo $form->labelEx($model, 'PalletsHalf');
                    echo $form->textField($model, 'PalletsHalf', array('size' => 3));
                    ?>
                </div>
                <div class="field">
                    <?php echo $form->labelEx($model, 'PalletsQtr');
                    echo $form->textField($model, 'PalletsQtr', array('size' => 3));
                    ?>
                </div>
                <div class="field">
                    <?php echo $form->labelEx($model, 'Kg');
                    echo $form->textField($model, 'Kg', array('size' => 5));
                    ?>
                </div>
                
                <fieldset>
                    <legend>Collect From</legend>
                    <?php if (empty($jobInfo->CollectionPoints)) : // display readonly 
                        echo $jobInfo->CollCompany."<br/>";
                        echo $jobInfo->CollAddress."<br/>";
                        echo $jobInfo->CollPostcode;
                    else: 
                        $points = array_merge(array($jobInfo), $jobInfo->CollectionPoints);
                    ?>
                    <table class="listing fluid">
                        <tr>
                            <td width="1"></td>
                            <th width="1">Postcode</th>
                            <th>Address</th>
                        </tr>
                        <?php foreach ($points as $p): ?> 
                        <tr>
                            <td>
                                <?php echo CHtml::activeCheckBox($model, "CollectionSequence[{$p->CollPostcode}]", array("value" => $p->CollPostcode, 'class' => 'one-required')); ?>
                            </td>
                            <td style="white-space: nowrap;">
                                <?php echo $p->CollPostcode ?>
                            </td>
                            <td>
                                <?php echo $p->CollAddress ?>
                            </td>    
                        </tr>
                        <?php endforeach; ?>
                    </table>
                    <?php 
                    endif; ?>
                </fieldset>
            </td>
            <td>
                <fieldset>
                    <legend>Delivery</legend>

                    <div class="field">
                        <?php echo $form->labelEx($model, 'Postcode');
                        echo $form->textField($model, 'Postcode', array('size' => 7));
                        ?>
                        <button type="button" id="postcode_lookup">Find Address</button><br/>
                        <div id="geotoolsselector" >&nbsp;</div>
                    </div>
                    <div class="field">
                        <?php echo $form->labelEx($model, 'Area');
                        echo $form->textField($model, 'Area', array('size' => 30));
                        ?>
                    </div>
                    <div class="field">
                        <?php echo $form->labelEx($model, 'Company');
                        echo $form->textField($model, 'Company', array('size' => 20));
                        ?>
                    </div>
                    <div class="field">
                        <?php echo $form->labelEx($model, 'DeliveryDate');
                        echo $form->textField($model, 'DeliveryDate', array('size' => 10, 'class' => 'dpicker'));
                        ?>
                    </div>
                    <div class="field">
                        <?php
                        echo $form->labelEx($model, 'ScheduledTime', FALSE);
                        echo $form->dropDownList($model, 'ScheduledTime', $arrayTime, array('empty' => 'TBC'));
                        ?>
                    </div>
                    <div class="field">
                        <?php echo $form->labelEx($model, 'TimeCode');
                        echo $form->textField($model, 'TimeCode', array('size' => 5));
                        ?>
                    </div>
                    <div class="field">
                        <?php echo $form->labelEx($model, 'BookingRef');
                        echo $form->textField($model, 'BookingRef', array('size' => 10));
                        ?>
                    </div>
                    <div class="field">
                        <?php echo $form->labelEx($model, 'SpecialInstructions');
                        echo $form->textField($model, 'SpecialInstructions', array('size' => 25));
                        ?>
                    </div>
                     <div class="field">
                        <?php echo $form->labelEx($model, 'LoadSpecialInstructions');
                        echo $form->textField($model, 'LoadSpecialInstructions', array('size' => 25));
                        ?>
                    </div>
                </fieldset>
                
                <fieldset>
                    <legend>Comments</legend>
                    
                <?php if (!empty($model->Comments)): ?>
                    <div class="comment-thread-container">
                        <?php foreach($model->Comments as $comment): ?>
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
                <?php endif; ?>
                    
                    <div class="field">
                        <?php echo $form->labelEx($model, 'NewComment');
                        echo $form->textField($model, 'NewComment', array('size' => 25));
                        ?>
                    </div>
                </fieldset>
            </td>
        </tr>
    </table>
</div>
</fieldset>
<br/>
<button class="validate">Save and Exit</button>

<button name="save_and_load" class="validate">Save and Add New Load</button>

<?php $this->endWidget(); ?>

<script type="text/javascript"> 
$(function(){
    
    $(".dpicker").datepicker({
        dateFormat: 'dd/mm/yy',
        onSelect: function() {
            $(this).removeClass("error");
            
            // #225 - warn if past date is selected
            <?php if (!empty($model->Id)) : ?>
            var curDt = Date.parse('<?php echo $model->getLoad()->DeliveryDate ?>');
            var selDt = $(this).datepicker("getDate");
            if (selDt < curDt) {
                if (!confirm('The date you have selected is in the past, are you sure you want to set it?')) {
                    $(this).val("<?php echo $model->DeliveryDate ?>");
                }
            }
            <?php endif; ?>
        }
    });
    
    $("#postcode_lookup").click(function(){
        var postcode = $("#PolestarLoadForm_Postcode").val();
        postcodeDeliveryPointLookup(postcode);
        return false;
    });
    
    $(".validate").click(function(){
        if ($(".one-required").length > 0) {
            var selected = ($(".one-required:checked").length > 0);
            if (!selected) {
                alert("Please select a delivery point for the load.");
                return false;
            }
        }
        return true;
    });
});

function postcodeDeliveryPointLookup (postcode) {
    $.ajax({
        'url': '<?php echo Yii::app()->createUrl('polestar/postcode_lookup'); ?>/',
        'data': {
            'postcode': postcode,
        },
        'success': function(data_str) {
            var data = JSON.parse(data_str);
            $("#PolestarLoadForm_Postcode").val(data.postcode);    
            $("#PolestarLoadForm_Area").val(data.address);
            $("#PolestarLoadForm_Company").val(data.company);
        },
        'error' : function() {
            geotoolsLookup(postcode);
        },
    });
}

function geotoolsLookup(postcode) {
    var g = new GeoTools("<?php echo Yii::app()->params['geotools_token'] ?>");
    g.retrieve(postcode,function(result){
     $("#geotoolsselector").html("");
     if (result.length == 0){
         $("#geotoolsselector").html("No results");
     } else if (result.length == 1){
         var r = result[0];
         var text = r.Address1;
         if (text.length > 0 && r.Address2.length > 0) text += ", ";
         text += r.Address2;
         if (text.length > 0 && r.Address3.length > 0) text += ", ";
         text += r.Address3;
         if (text.length > 0 && r.Address4.length > 0) text += ", ";
         text += r.Address4;
         $("#PolestarLoadForm_Area").val(text);
     } else {
         var cs = document.createElement("select");
         cs.onchange = function(){
             var r = result[cs.selectedIndex];
             var text = r.Address1;
             if (text.length > 0 && r.Address2.length > 0) text += ", ";
             text += r.Address2;
             if (text.length > 0 && r.Address3.length > 0) text += ", ";
             text += r.Address3;
             if (text.length > 0 && r.Address4.length > 0) text += ", ";
             text += r.Address4;
             $("#PolestarLoadForm_Area").val(text);
             $("#geotoolsselector").html("");
         };
         $("<option/>").attr('value','').text('Postcode OK - select one of the addresses below').appendTo(cs);
         for (var i = 0; i < result.length; i++){
             var opt = document.createElement("option");
             opt.value = i;
             opt.text = result[i]['Description'];
             cs.appendChild(opt);
         }

         $("#geotoolsselector").append(cs);

     }
    });
}
</script>

