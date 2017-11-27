<?php
    $newRoute =  ($model->Id == NULL);

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
?>
<style>
    select {
        height: 28px;
    }
    .drag-point {
        cursor: move !important;
    }
</style>
<h1>
    <?php echo $model->getPrintCentre()->Name ?> - <?php echo ($newRoute ? "Add Route" : "Route edit") ?>
</h1>

<?php $form=$this->beginWidget('CActiveForm', array(
    'id'=>'job-form',
    'errorMessageCssClass'=>'formError',
));
    echo $form->errorSummary($model, "", "", array('class'=>'errorBox'));
?>

<?php echo $form->hiddenField($model, 'PrintCentreId'); ?>
<?php echo $form->hiddenField($model, 'Id'); ?>
<?php echo $form->hiddenField($model, 'ClearHighlighting'); ?>

<fieldset>
    <legend>General Details</legend>
    <table class="listing fluid">
        <tr>
            <td>
                <div class="stackedForm">
                    <div class="field">
                        <?php
                        echo $form->labelEx($model,'AktrionJobRef', FALSE);
                        //if ($newRoute)
                        //    echo $form->textField($model,'Ref', array('size' => 15));
                        //else
                        // *** Always read only for auto generation only
                            echo $form->textField($model,'Ref', array_merge(array('size' => 15), $readOnlyAttrs));
                        ?>
                    </div>
                    <div class="field">
                        <?php echo $form->labelEx($model, 'DeliveryDate'); ?>
                        <?php echo $form->textField($model, 'DeliveryDate', array('size' => '10', 'readonly' => 'readonly', 'class' => 'dpicker')); ?>
                    </div>
                    <div class="field">
                        <?php
                        echo $form->labelEx($model, 'ProviderId');
                        echo $form->dropDownList($model, 'ProviderId', $model->GetSupplierOptions(), array('empty' => 'select one --'));
                        ?>
                    </div>
                    <!--div class="field">
                        <?php
                        echo $form->labelEx($model, 'SupplierId');
                        echo $form->dropDownList($model, 'SupplierId', $model->GetSupplierOptions(), array('empty' => 'select one --'));
                        ?>
                    </div-->
                    <div class="field">
                        <?php
                        echo $form->labelEx($model, 'VehicleId');
                        echo $form->dropDownList($model, 'VehicleId', $model->getVehicleOptions(), array('empty' => 'select one --'));
                        ?>
                    </div>
                    <div class="field">
                        <?php
                        echo $form->labelEx($model, 'NewComment', FALSE);
                        echo $form->textField($model, 'NewComment', array('size' => 50));
                        ?>
                    </div>

                </div>
            </td>
            <td>
                <?php if (!empty($model->Comments)): ?>
                <fieldset>
                    <legend>Comments</legend>

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
                </fieldset>
                <?php endif; ?>
            </td>
        </tr>
    </table>
</fieldset>

<table id="points-list" class="listing fluid sortable">
    <?php 
    $noPoints = count($model->CollPostcode);
    for($idx = 0; $idx < $noPoints; $idx++): ?>
    <tr> 
        <td>
        <fieldset class="point-container">
            <legend>
                <img class="drag-point" style="display:none;" src="<?php echo $baseUrl; ?>/img/icons/arrow_switch.png" title="[drag point]"> 
                Collection Point #<span class="sequence">1</span>
            </legend>
            <div class="stackedForm">
                <?php echo $form->hiddenField($model, "CollSeq[$idx]", array('class' => 'sequence' )); ?>
                <?php echo $form->hiddenField($model, "CollId[$idx]", array('class' => 'id' )); ?>
                <div class="field">
                    <?php
                    echo $form->labelEx($model, 'CollPostcode');
                    echo $form->textField($model, "CollPostcode[$idx]", array('size' => 7, 'class' => 'postcode' ));
                    ?>
                    <button type="button" class="postcode_lookup">Find Address</button><br/>
                    <div class="geotoolsselector" >&nbsp;</div>
                </div>
                <div class="field">
                    <?php
                    echo $form->labelEx($model, 'CollAddress');
                    echo $form->textField($model, "CollAddress[$idx]", array('size' => 75, 'class' => 'address'));
                    ?>
                </div>
                <div class="field">
                    <?php
                    echo $form->labelEx($model, 'Company', FALSE);
                    echo $form->textField($model, "CollCompany[$idx]", array('size' => 25, 'class' => 'company'));
                    ?>
                </div>
                <div class="field">
                    <?php
                    echo $form->labelEx($model, 'CollScheduledTime');
                    echo $form->dropDownList($model, "CollScheduledTime[$idx]", $arrayTime, array('empty' => 'TBC', 'class' => 'time'));
                    ?>
                </div>
                <div class="field">
                    <?php
                    echo $form->labelEx($model, 'SpecialInstructions', FALSE);
                    echo $form->textField($model, "SpecialInstructions[$idx]", array('size' => 50, 'class' => 'instructions'));
                    ?>
                </div>
            </div>
        </fieldset>
        </td>
    </tr>
    <?php endfor; ?>
</table>
<img src="<?php echo $baseUrl; ?>/img/icons/add.png" alt="add" />
<a href="#" class="add-point">
    <strong>Add collection point</strong>
</a>

<br/><br/>

<button class="validate save">Save and Exit</button>
<?php if ($newRoute) : ?>
<button class="validate save" name="save_and_load">Save and Add Load to Job</button>
<?php endif; ?>

<?php $this->endWidget(); ?>

<div id="dialog-clear-highlight" title="Clear Highlighting?" style="display:none;">
  <p>
      <span class="ui-icon ui-icon-alert" style="float:left; margin:0 7px 20px 0;"></span>
      Would you like to clear existing highlighted changes? 
  </p>
</div>

<script>
var originalDate = "<?php echo $model->DeliveryDate ?>";
var originalRef = "<?php echo $model->Ref ?>";
    
$(function(){
    $(".dpicker").datepicker({
        dateFormat: 'dd/mm/yy',
        onSelect: function() {
            $(this).removeClass("error");
            $(this).change();
            
            var newValue = $("#PolestarJobForm_DeliveryDate").val();
            
            // #225 - warn if past date is selected
            <?php if (!empty($model->Id)) : ?>
            var curDt = Date.parse('<?php echo $model->getJob()->DeliveryDate ?>');
            var selDt = $(this).datepicker("getDate");
            if (selDt < curDt) {
                if (!confirm('The date you have selected is in the past, are you sure you want to set it?')) {
                    $(this).val(originalDate);
                    newValue = originalDate;
                }
            }
            <?php endif; ?>
              
            if (newValue != originalDate) {
                // TODO: grab from server
                $("#PolestarJobForm_Ref").val('TBD');
                $.ajax({
                    'url': '<?php echo Yii::app()->createUrl('polestar/routenewref'); ?>/',
                    'data': {
                        'pcid' : <?php echo $model->PrintCentreId ?>,
                        'dt': newValue
                    },
                    'success': function(data_str) {
                        $("#PolestarJobForm_Ref").val(data_str);
                    },
                    'error' : function() {
                        alert('An error has occurred while retrieving new job reference, a valid reference will be generated upon saving the changes.');                   
                    }
                });
            }
            else {
                $("#PolestarJobForm_Ref").val(originalRef);
            }
        }
    });
    
    $(".sortable tbody").sortable({
        'stop' : sortPoints
    });
    
    $(".add-point").click(function(){
        var $newRow = $("#points-list tr:first").clone(false);
        $(".id", $newRow).val('new');
        $(".postcode", $newRow).val('');
        $(".address", $newRow).val('');
        $(".company", $newRow).val('');
        $(".time", $newRow).val('');
        $(".instructions", $newRow).val('');
        $newRow.appendTo($("#points-list"));
        sortPoints();
        return false;
    });
    
    $(".validate").click(function(){
        var error = false;
        
        $(".point-container").each(function(idx){
            $("input", $(this)).removeClass('error');
            $("select", $(this)).removeClass('error');
            
            if ($.trim($(".postcode", $(this)).val()) == '') {
                $(".postcode", $(this)).addClass('error');
                error = true;
            }
            if ($.trim($(".address", $(this)).val()) == '') {
                $(".address", $(this)).addClass('error');
                error = true;
            }
        });        
        
        if (error) {
            alert('Please review your input on collection points as highlighted');
            return false;
        }
    });
    
    $(".save").click(function(){
        <?php 
        $statusToHighlight = array(
            PolestarStatus::AMENDED_ID,
            PolestarStatus::LATE_ADVICE_ID
        );
        $statusId = $model->getJob()->StatusId;
        if (in_array($statusId, $statusToHighlight) &&
                ($model->getJob()->ClearHighlighting != 'Y') &&
                Login::checkPermission(Permission::PERM__FUN__POLESTAR__CLEAR_HIGHLIGHT)):
        ?>
        $( "#dialog-clear-highlight" ).dialog({
            resizable: false,
            height:140,
            modal: true,
            position: { my: "center bottom", at: "center top", of: $(".save:first") },
            buttons: {
                Yes: function() {
                    $("#PolestarJobForm_ClearHighlighting").val('Y');
                    $( this ).dialog( "close" );
                    $("#job-form").submit();
                },
                No: function() {
                    $( this ).dialog( "close" );
                    $("#job-form").submit();
                }
            }
        });
        return false;
        <?php endif; ?>
    });
    
    sortPoints();
    
    <?php if (isset($_GET['add-coll-point']) && ($_GET['add-coll-point'] == '1')): ?>
    $(".add-point").click();
    $("#points-list tr:last").find('.postcode').focus();
    <?php endif; ?>
});

function findPostcodeEvent(){
    var $container = $(this).parents(".point-container:first");
    var postcode = $('.postcode', $container).val();
    postcodeDeliveryPointLookup(postcode, $container);
    return false;
}

function sortPoints() {
    if ($(".point-container").length > 1) 
        $(".drag-point").show();
    else
        $(".drag-point").hide();

    $(".point-container").each(function(idx){
        $("input.sequence", $(this)).val(idx);
        $("span.sequence", $(this)).text(idx+1);
        $(".id", $(this))
            .attr('name', 'PolestarJobForm[CollId]['+idx+']')
            .attr('id', 'PolestarJobForm_CollId_'+idx);
        $(".postcode", $(this))
                .attr('name', 'PolestarJobForm[CollPostcode]['+idx+']')
                .attr('id', 'PolestarJobForm_CollPostcode_'+idx);
        $(".address", $(this))
                .attr('name', 'PolestarJobForm[CollAddress]['+idx+']')
                .attr('id', 'PolestarJobForm_CollAddress_'+idx);
        $(".company", $(this))
                .attr('name', 'PolestarJobForm[CollCompany]['+idx+']')
                .attr('id', 'PolestarJobForm_CollCompany_'+idx);
        $(".time", $(this))
                .attr('name', 'PolestarJobForm[CollScheduledTime]['+idx+']')
                .attr('id', 'PolestarJobForm_CollScheduledTime_'+idx);
        $(".instructions", $(this))
                .attr('name', 'PolestarJobForm[SpecialInstructions]['+idx+']')
                .attr('id', 'PolestarJobForm_SpecialInstructions_'+idx);
    });
    
    $(".postcode_lookup").unbind('click', findPostcodeEvent).click(findPostcodeEvent);
}

function postcodeDeliveryPointLookup (postcode, $container) {
    $(".geotoolsselector", $container).html("");
    $.ajax({
        'url': '<?php echo Yii::app()->createUrl('polestar/postcode_lookup'); ?>/',
        'data': {
            'postcode': postcode,
        },
        'success': function(data_str) {
            var data = JSON.parse(data_str);
            $(".postcode", $container).val(data.postcode);
            $(".address", $container).val(data.address);
            $(".company", $container).val(data.company);
        },
        'error' : function() {
            geotoolsLookup(postcode, $container);
        },
    });
}

function geotoolsLookup(postcode, $container) {
    var g = new GeoTools("<?php echo Yii::app()->params['geotools_token'] ?>");
    g.retrieve(postcode,function(result){
     $(".geotoolsselector", $container).html("");
     if (result.length == 0){
         $(".geotoolsselector", $container).html("No results");
     } else if (result.length == 1){
         var r = result[0];
         var text = r.Address1;
         if (text.length > 0 && r.Address2.length > 0) text += ", ";
         text += r.Address2;
         if (text.length > 0 && r.Address3.length > 0) text += ", ";
         text += r.Address3;
         if (text.length > 0 && r.Address4.length > 0) text += ", ";
         text += r.Address4;
         $(".address", $container).val(text);
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
             $(".address", $container).val(text);
             $(".geotoolsselector", $container).html("");
         };
         $("<option/>").attr('value','').text('Postcode OK - select one of the addresses below').appendTo(cs);
         for (var i = 0; i < result.length; i++){
             var opt = document.createElement("option");
             opt.value = i;
             opt.text = result[i]['Description'];
             cs.appendChild(opt);
         }
         $(".geotoolsselector", $container).append(cs);
     }
    });
}

</script>