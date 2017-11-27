<?php
    $this->breadcrumbs=array(
                array('label'=>'Home', 'url'=>array(Yii::app()->user->role->HomeUrl)),
                array('label'=>'Finance', 'url' => '#'),
                array('label'=>'Route Instance')
            );
    
    $baseUrl = Yii::app()->request->baseUrl;
    $readOnlyAttrs = array('class' => 'readOnlyField', 'readOnly' => 'readOnly');
    $data = $model->instanceData;
?>
<style>
    .red {
        background-color: #f46464;
    }
    .blue {
        background-color: #948df9;
    }
    .btn {
        height: 50px;
        padding: 10px;
    }
    
    #charcounter {
        font-size: 0.75em;
        color: #555;
        padding-left: 360px;
    }
</style>

<h1>
    <?php 
    if (!$model->baseEdit):
        echo $model->editingCategory.' '.$model->routeCategory.' - '.$model->routeCode.' - '.$model->date;
    else:
        echo $model->editingCategory.' '.$model->routeCategory.' - '.$model->routeCode;
    endif;
    ?>
</h1>

<fieldset>
   
    
<?php 
$ackRequired = FALSE;
$ackDone = FALSE;
$canAck = Login::checkPermission(Permission::PERM__FUN__LSC__ACKNOWLEDGE);

if (isset($data) && (!$model->baseEdit)): // vefify adjustments acknowledgement required

        if ((!empty($model->adjustedFee) && ($model->adjustedFee != $model->fee)) || !empty($model->adjustedContractor) || !empty($model->miscFee)): // adjustment found
            if (empty($data->AdjAckDate)): 
                $ackRequired = TRUE;
        ?>
            <div class="warningBox">There are adjustments below that require acknowledgement.</div>
        <?php
        else: 
            $ackDone = TRUE;
            ?>
            <div class="infoBox">The adjustments below were acknowledged by <?php echo $data->AdjAckBy ?> on <?php echo $data->AdjAckDate ?></div>
        <?php
        endif;
    endif;

    if ($data->IsBase != 1):
        if (empty($data->AckDate)): 
            $ackRequired = TRUE;
        ?>
            <div class="warningBox">This is an exception route and requires acknowledgement.</div>
        <?php
        else:
            $ackDone = TRUE;
            ?>
            <div class="infoBox">This exception route was acknowledged by <?php echo $data->AckBy ?> on <?php echo $data->AckDate ?></div>
        <?php
        endif;
    endif;
    
endif;

$allowAdjustment = TRUE;
if ($ackDone)
    if (!Login::checkPermission(Permission::PERM__FUN__LSC__OVERRIDE))
        $allowAdjustment = FALSE;

?>

<br/>    

<div class="stackedForm">
<?php $form=$this->beginWidget('CActiveForm', array(
            'id'=>'instance-form',
            'errorMessageCssClass'=>'formError',
    )); ?>

    <?php
    echo $form->hiddenField($model, 'instanceId');
    echo $form->hiddenField($model, 'routeId');
    echo $form->hiddenField($model, 'date');
    echo $form->hiddenField($model, 'entryType');
    echo $form->hiddenField($model, 'acknowledge');
    echo $form->hiddenField($model, 'editingCategory');
    echo $form->errorSummary($model, "", "", array('class'=>'errorBox'));
    ?>
    <table width="100%">
        <tr>
            <td>
                <div class="field">
                    <?php echo $form->labelEx($model,'contractorId'); ?>
                    <?php 
                    if (isset($data) && !$model->baseEdit):
                        $attrs = $readOnlyAttrs;
                        $newAttrs = array('size' => 30);
                        $contractorName = $data->ContractorFirstName.' '.$data->ContractorLastName;

                        if (isset($model->adjustedContractorId))
                        {
                            $contractorName = $data->AdjContractorFirstName.' '.$data->AdjContractorLastName;
                            if (empty($data->AdjAckDate))
                                $newAttrs['class'] = "{$attrs['class']} red";
                            else
                                $newAttrs['class'] = "{$attrs['class']} blue";
                        }
                        $attrs = array_merge($attrs, $newAttrs);

                        echo CHtml::textField('contractor', $contractorName, $attrs); 
                        echo $form->hiddenField($model, 'contractorId');
                    ?>
                    <?php if ($allowAdjustment): ?>
                    <a href="#" class="adjust-contractor">
                        <img title="Adjust Contractor"
                             src="<?php echo $baseUrl; ?>/img/icons/pencil.png" /></a>
                        <?php echo CHtml::activeDropDownList($model,'adjustedContractorId', $model->getContractorOptions($model->contractorId), array('empty' => '('.$data->ContractorFirstName.' '.$data->ContractorLastName.')', 'style' => 'display:none; width:235px;')); ?>
                    <?php
                        endif;
                    else:
                        echo CHtml::activeDropDownList($model,'contractorId', $model->getContractorOptions(), array('empty' => '-- select one', 'style' => 'width:235px;'));
                    endif;
                    ?>
                </div>

                <div class="field">
                    <?php echo $form->labelEx($model,'fee'); ?>
                    <?php 
                    if (isset($data) && (!$model->baseEdit)):
                        $attrs = $readOnlyAttrs;
                        $newAttrs = array('size' => 10);
                        $fee = $model->fee;

                        if (isset($model->adjustedFee) && ($model->adjustedFee != $model->fee))
                        {
                            $fee = $model->adjustedFee;
                            if (empty($data->AdjAckDate))
                                $newAttrs['class'] = "{$attrs['class']} red";
                            else
                                $newAttrs['class'] = "{$attrs['class']} blue";
                        }
                        $attrs = array_merge($attrs, $newAttrs);

                        echo CHtml::textField('fee', $fee, $attrs);
                        echo $form->hiddenField($model, 'fee');
                        ?>
                    <?php if ($allowAdjustment): ?>
                    <a href="#" class="adjust-fee">
                        <img title="Adjust Fee"
                             src="<?php echo $baseUrl; ?>/img/icons/pencil.png" /></a>
                    <?php endif; ?>
                        <?php echo $form->textField($model, 'adjustedFee', array('size' => 10, 'style' => 'display:none;')); ?>
                    <?php
                    else:
                        echo $form->textField($model, 'fee', array('size' => '10')); 
                    endif; ?>
                </div>

                <?php if (!$model->baseEdit): ?>
                <fieldset>
                    <!--legend>New Comment</legend-->
                    <br/>
                    <div class="field">
                        <?php echo $form->labelEx($model,'newComment'); ?>
                        <?php echo $form->textField($model, 'newComment', array('size' => '30', 'maxlength' => '80')); ?>
                        <div id="charcounter"></div>
                    </div>
                    
                    <div class="field">
                        <?php echo $form->labelEx($model,'newCommentOnInvoice'); ?>
                        <?php echo $form->checkBox($model, 'newCommentOnInvoice'); ?>
                    </div>
                    
                </fieldset>
                
                <fieldset>
                    <legend>Expenses / Deds</legend>
                
                    <br/>
                    
                    <table class="listing fluid" id="expense-list">
                        <tr>
                            <th width="50">Amount</th>
                            <th>Comment</th>
                            <td width="5"></td>
                        </tr>
                        <?php if (!empty($model->expenseThread)): 
                                foreach ($model->expenseThread as $expense):
                                    $expenseId = $expense->AdjustmentExpenseId;
                            ?>
                        <tr>
                            <td>
                                <?php echo CHtml::hiddenField("Expense[$expenseId][visible]", '1', array('class' => 'expense-visible')); ?>
                                
                                <?php echo CHtml::textField("Expense[$expenseId][amount]"
                                                    , $expense->Amount
                                                    , array('class' => 'readOnlyField expense-amount'
                                                        , 'readonly' => 'readOnly'
                                                        , 'size' => 10)); ?>
                            </td>
                            <td>
                                <em><?php echo $expense->Comment; ?></em>
                            </td>
                            <td style="vertical-align: middle;">
                                <a href="#" class="delete-expense">
                                <img src="<?php echo $baseUrl; ?>/img/icons/delete.png" /></a>
                            </td>
                        </tr>
                        <?php endforeach; 
                            endif; ?>
                        <tr id="dummy-expense" style="display:none;">
                            <td>
                                <?php echo CHtml::hiddenField("Expense[-999][visible]", '1', array('class' => 'expense-visible')); ?>
                                <?php echo CHtml::textField("Expense[-999][amount]"
                                                    , ''
                                                    , array('class' => 'expense-amount'
                                                        , 'size' => 7)); ?>
                            </td>
                            <td>
                                <?php echo CHtml::textField("Expense[-999][comment]"
                                                    , ''
                                                    , array('class' => 'expense-comment'
                                                        , 'size' => 30)); ?>
                            </td>
                            <td style="vertical-align: middle;">
                                <a href="#" class="delete-expense">
                                    <img src="<?php echo $baseUrl; ?>/img/icons/delete.png" /></a>
                            </td>
                        </tr>
                    </table>
                    <div style="text-align: right;">
                        <a href="#" class="add-expense">
                                <img src="<?php echo $baseUrl; ?>/img/icons/add.png"
                                     title="Click to add an expense/deduction"/></a>
                    </div>
                    
                    <!--
                    <div class="field">
                        <?php echo $form->labelEx($model,'miscFee'); ?>
                        <?php 
                            $attrs = array('size' => '10');
                            if (!$allowAdjustment)
                                $attrs = array_merge ($attrs, $readOnlyAttrs);
                        echo $form->textField($model, 'miscFee', $attrs); ?>
                    </div>
                    -->
                    
                </fieldset>
                
                
                <?php else: 
                    echo $form->hiddenField($model, 'newComment');
                endif; ?>                
            </td>
            <td width="250" style="padding-left:10px">
                <?php if (!$model->baseEdit): ?>
                <fieldset>
                    <legend>Comments</legend>
                    <?php if (!empty($model->commentThread)): ?>
                    <div class="comment-thread-container">
                        <?php foreach($model->commentThread as $comment): 
                            $attrs = $model->getCommentUIAttrs($comment);
                            $cssClass = $attrs['cssClass'];
                            $iconPath = $attrs['iconPath'];
                            $tooltip = $attrs['tooltip']
                            ?>
                        <div class="comment-footer">
                            <div class="user">
                                <a href="#" 
                                   class="toggle-comment" 
                                   rel="<?php echo $comment->CommentId ?>">
                                <img src="<?php echo $iconPath ?>"
                                 title="<?php echo $tooltip ?>" /></a>
                                by <?php echo $comment->login->FriendlyName; ?>
                            </div>
                            <div class="time">
                                <?php echo $comment->CreatedDate; ?>
                            </div>
                        </div>
                        <br style="clear:both"/>
                        <div id="cmt-<?php echo $comment->CommentId ?>" class="comment-box <?php echo $cssClass ?>">
                            <div class="text">
                                <?php echo nl2br($comment->Comment) ; ?>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <br style="clear:both"/>
                    <?php else: ?>
                    <em>No comments posted.</em>
                    <?php endif; ?>
                </fieldset>
                <?php endif; ?>
            </td>
        </tr>
    </table>
    
    <div class="titleWrap">
        <ul>
            <?php if ($ackRequired && $canAck): ?>
            <li>
                <button class="btn" id="btnAck">
                    <img src="<?php echo $baseUrl; ?>/img/icons/accept.png" />
                    Acknowledge
                </button>
            </li>
            <?php endif; ?>
            <li>
                <button class="btn">
                    <img src="<?php echo $baseUrl; ?>/img/icons/bullet_disk.png" />
                    Save changes
                </button>
            </li>
        </ul>
    </div>
<?php
$this->endWidget(); ?>
</div>

</fieldset>    

<div id="dialog-confirm" title="Confirm adjustments?" style="display:none;">
  <p><span class="ui-icon ui-icon-alert" style="float:left; margin:0 7px 20px 0;"></span>This will confirm entered adjustments, please confirm you wish to proceed.</p>
</div>

<script>
    var idxAdded = 0;
    
$(function(){
    
    $(".adjust-contractor").click(function(){
        $("#contractor").hide();
        $("#FinanceRouteInstanceForm_adjustedContractorId").show();
        $(".adjust-contractor").hide();
    });
    
    $(".adjust-fee").click(function(){
        $("#fee").hide();
        $("#FinanceRouteInstanceForm_adjustedFee").show();
        $(".adjust-fee").hide();
    });
    
    $(".toggle-comment").click(function(){
        var id = $(this).attr('rel');
        var $img = $('img', $(this));
        var url = "<?php echo $this->createUrl('finance/commentvisibility') ?>";
        var data = {id : id};
        $.get(url,
            data,
            function(data){
                if (data.toInvoice != undefined) {
                    $("#cmt-"+id).removeClass('blue').addClass(data.cssClass);
                    $img.attr('src', data.iconPath);
                    $img.attr('title', data.tooltip);
                }
            },
            'json'
            );
        return false;
    });
    
    $("#btnAck").click(function(){
        //if (!confirm('This will confirm entered adjustments, please confirm you wish to proceed.'))
        //    return false;
        
        //$("#FinanceRouteInstanceForm_acknowledge").val(1);
        $( "#dialog-confirm" ).dialog({
            resizable: false,
            height:140,
            modal: true,
            position: { my: "center bottom", at: "center top", of: $("#btnAck") },
            buttons: {
                "Confirm": function() {
                    $("#FinanceRouteInstanceForm_acknowledge").val(1); 
                    $( this ).dialog( "close" );
                    $("#instance-form").submit();
                },
                Cancel: function() {
                    $( this ).dialog( "close" );
                }
            }
        });
        return false;
    });
    
    $("#FinanceRouteInstanceForm_newComment").keyup(function(){
        refreshCounter($(this));
    });
    refreshCounter($("#FinanceRouteInstanceForm_newComment"));
    
    
    $(".add-expense").click(function(){
        var $newRow = $('#dummy-expense').clone();
        idxAdded++;
        $('input', $newRow).each(function(){
            var id = $(this).attr('id');
            id = id.replace('999', idxAdded);
            $(this).attr('id', id);
            var name = $(this).attr('name');
            name = name.replace('999', idxAdded);
            $(this).attr('name', name);
        });
        $newRow.appendTo("#expense-list").show();
        
        $('.delete-expense').unbind('click').click(deleteExpense);
        $('.expense-amount').unbind('blur').blur(formatExpense);
        
        return false;
    });
    
    $('.delete-expense').click(deleteExpense);
    $('.expense-amount').blur(formatExpense);
});

function formatExpense() {
    var curValue = $(this).val().trim();
    if (curValue == '')
        return;
        
    if (!isNaN(curValue)) {
        curValue = parseFloat(curValue);
        $(this).val(curValue.toFixed(2));
    }
    else {
        $(this).val('');
        alert('Please enter a valid number');
        $(this).focus();
    }   
}

function deleteExpense() {
    var $row = $(this).parents('tr:first');
    $('.expense-visible', $row).val(0);
    $row.hide();
    return false;
}

function refreshCounter($field) {
    var max = parseInt($field.attr('maxlength'),10);
    var len = $field.val().length;
    var rem = max - len;
    
    $("#charcounter").text(rem);
}
</script>