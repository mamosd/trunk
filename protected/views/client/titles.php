<?php

$this->breadcrumbs=array(
                array('label'=> Yii::app()->user->name , 'url'=>array(Yii::app()->user->role->HomeUrl)),
                array('label'=>'Titles'),
            );
?>

<div class="titleWrap">
    <h1>Titles Listing</h1>
</div>

<?php $form=$this->beginWidget('CActiveForm', array(
            'id'=>'titles-form',
            'errorMessageCssClass'=>'formError',
    )); ?>

<div class="errorBox" style="display:none;">
<ul>
    <li>There are errors in the form, make sure all highlighted fields are completed correctly and try again.</li>
    <li>Also, all titles selected need to have delivery points defined to be considered a valid order.</li>
</ul>
</div>

<table class="listing fluid" cellpadding="0" cellspacing="0">
<tbody>
    <tr>
        <th width="12%">Actions</th>
        <th>Name</th>
    </tr>

    <?php
    $titles = $model->getTitles(Yii::app()->user->loginId);
    $count = count($titles);
    for($i = 0; $i < $count; $i++):
        $details = $model->getLastOrder(Yii::app()->user->loginId, $titles[$i]->TitleId);
        if ($details['orderStatus'] == "")
            unset($details['orderStatus']);
    ?>
        <tr class="row<?php echo (($i%2)+1) ?>">
          <td>
              <button class="formButton order-button">
                  <div class="order" <?php echo ($details['orderStatus'] != Order::STATUS_DRAFT) ? '' : 'style="display:none;"'; ?>>
                      <img src="img/icons/add.png" alt="order" width="16" height="16" /> Add
                  </div>
                <div class="cancel" <?php echo ($details['orderStatus'] != Order::STATUS_DRAFT) ? 'style="display:none;"' : ''; ?>>
                      <img src="img/icons/cancel.png" alt="order" width="16" height="16" /> Cancel
                  </div>
              </button>
          </td>

          <td>
              <div class="titleName"><?php echo $titles[$i]->Name; ?></div>

            <?php
                if (isset($details['orderStatus']) && ($details['orderStatus'] != Order::STATUS_DRAFT)):
            ?>
              <div class="infoSmall">
                  Last order: <?php echo Yii::app()->dateFormatter->format('dd/MM/yyyy', $details['dateCreated']); ?> <br />
                  Pagination: <?php echo $details['pagination']; ?> <br />
                  Bundle Size: <?php echo $details['bundleSize']; ?> <br />
                  Publication Date: <?php echo $details['publicationDate']; ?> <br />
                  Delivery Date: <?php echo $details['deliveryDate']; ?> <br />

                  <table class="fluid">
                      <tr>
                          <th>Delivery Point</th>
                          <th>Copies</th>
                          <th>Bundles</th>
                          <th>Odds</th>
                      </tr>
                      <?php
                        $cnt = count($details['orderDetails']);
                        $totalCopies = 0;
                        for ($j = 0; $j < $cnt; $j++):
                            $od = $details['orderDetails'][$j];
                            $copies = intval($od['copies']);
                            $totalCopies += $copies;
                            $bundleSize = intval($details['bundleSize']);
                        ?>

                      <tr>
                          <td><?php echo $od['descdelpoint']; ?></td>
                          <td><?php echo $copies; ?></td>
                          <td><?php echo floor($copies / $bundleSize); ?></td>
                          <td><?php echo ($copies % $bundleSize); ?></td>
                      </tr>
                      <?php
                        endfor;
                      ?>
                      <tr>
                          <td><strong>Total Copies</strong></td>
                          <td><?php echo $totalCopies; ?></td>
                          <td></td>
                          <td></td>
                      </tr>
                  </table>
                  
              </div>
            <?php endif; ?>
          </td>
        </tr>
        <tr class="order-container" <?php echo ($details['orderStatus'] != Order::STATUS_DRAFT) ? 'style="display:none;"' : ''; ?>>
            <td></td>
            <td>
                <div class="infoRight">
                    <?php
                    if (isset($details['orderStatus']))
                    {
                        echo ($details['orderStatus'] != Order::STATUS_DRAFT) ? 'Last Order Date: ' : 'Saved on: ';
                        echo CHtml::textField("OrderDetails[".$titles[$i]->TitleId."][dateCreated]", Yii::app()->dateFormatter->format('dd/MM/yyyy', $details['dateCreated']), array('size'=>'10', 'class'=>'readOnlyField', 'tabindex'=>'-1', 'readonly'=>'readonly'));
                    }
                    ?>
                </div>
                <table class="listing fluid" cellpadding="0" cellspacing="0">
                    <tr>
                        <th>Pagination</th>
                        <th>Bundle Size</th>
                        <th>Publication Date</th>
                        <th>Delivery Date</th>
                    </tr>
                    <tr>
                        <td>
                            <?php echo CHtml::textField("OrderDetails[".$titles[$i]->TitleId."][pagination]", $details['pagination'], array('size'=>'5', 'class' => 'recalc')); ?>
                        </td>
                        <td>
                            <?php echo CHtml::textField("OrderDetails[".$titles[$i]->TitleId."][bundleSize]", $details['bundleSize'], array('size'=>'5', 'class' => 'recalc')); ?>
                        </td>
                        <td>
                            <?php echo CHtml::textField("OrderDetails[".$titles[$i]->TitleId."][publicationDate]", $details['publicationDate'], array('size'=>'10', 'class' => 'dpicker')); ?>
                        </td>
                        <td>
                            <?php echo CHtml::textField("OrderDetails[".$titles[$i]->TitleId."][deliveryDate]", $details['deliveryDate'], array('size'=>'10', 'class' => 'dpicker')); ?>
                        </td>
                    </tr>
                </table>
                
                <h2>Delivery Points Entry</h2>

                <div class="infoRight">
                    <div>
                        <label>Total Copies</label>
                        <?php echo CHtml::textField("OrderDetails[".$titles[$i]->TitleId."][totalCopies]", "", array('size'=>'5', 'class'=>'readOnlyField', 'tabindex'=>'-1', 'readonly'=>'readonly')); ?>
                    </div>
                    <!--div>
                        <label>Order Weight</label>
                        <input type="text" class="readOnlyField" tabindex="-1" readonly="readonly" size="10" />
                    </div-->
                </div>

                <table id="tblDetails_<?php echo $titles[$i]->TitleId; ?>" class="listing fluid" cellpadding="0" cellspacing="0">
                <tbody>
                    <tr>
                      <th width="30%">Delivery Point</th>
                      <th>Copies</th>
                      <th>Bundles</th>
                      <th>Odds</th>
                      <th>Actions</th>
                    </tr>
                    <?php
                        $cnt = count($details['orderDetails']);
                        for ($j = 0; $j < $cnt; $j++):
                            $od = $details['orderDetails'][$j];
                        ?>
                    <tr>
                        <td width="30%">
                            <?php echo CHtml::hiddenField("OrderDetails[".$titles[$i]->TitleId."][delpoint".($j+1)."]", $od['delpoint'], array('id'=>'txtRowDelPoint'.($j+1))); ?>
                            <?php echo CHtml::textField("OrderDetails[".$titles[$i]->TitleId."][descdelpoint".($j+1)."]", $od['descdelpoint'], array('id'=>'txtRowDescDelPoint'.($j+1), 'size'=>'25', 'class'=>'readOnlyField', 'tabindex'=>'-1', 'readonly'=>'readonly')); ?>
                        </td>
                        <td>
                            <?php echo CHtml::textField("OrderDetails[".$titles[$i]->TitleId."][copies".($j+1)."]", $od['copies'], array('id'=>'txtRowCopies'.($j+1), 'size'=>'5', 'class' => 'recalc')); ?>
                        </td>
                        <td>
                            <?php echo CHtml::textField("OrderDetails[".$titles[$i]->TitleId."][bundles".($j+1)."]", "", array('id'=>'txtRowBundles'.($j+1), 'size'=>'5', 'class'=>'readOnlyField', 'tabindex'=>'-1', 'readonly'=>'readonly')); ?>
                        </td>
                        <td>
                            <?php echo CHtml::textField("OrderDetails[".$titles[$i]->TitleId."][odds".($j+1)."]", "", array('id'=>'txtRowOdds'.($j+1), 'size'=>'5', 'class'=>'readOnlyField', 'tabindex'=>'-1', 'readonly'=>'readonly')); ?>
                        </td>
                        <td>
                            <a href="#" id="lnkDelete" tabindex="-1" class="delete-link">
                                <img height="16" width="16" alt="add" src="img/icons/delete.png">
                                Delete
                            </a>
                        </td>
                    </tr>
                    <?php
                        endfor;
                    ?>
                </tbody>
                </table>
                <hr />
                <table class="listing fluid">
                <tbody>
                    <tr>
                        <td colspan="5">
                            <?php echo CHtml::dropDownList("ddlDummyDelPoint[".$titles[$i]->TitleId."]", "", $model->getOptionsDeliveryPoint(), array('empty'=>'Select a Delivery Point to Add ->', 'class'=>'dummy-del-point')); ?>
                        </td>
                    </tr>
                </tbody>
                </table>
                <?php echo CHtml::hiddenField("OrderDetails[".$titles[$i]->TitleId."][titleId]", $titles[$i]->TitleId); ?>
                <?php echo CHtml::hiddenField("OrderDetails[".$titles[$i]->TitleId."][orderStatus]", $details['orderStatus']); ?>
                <?php echo CHtml::hiddenField("OrderDetails[".$titles[$i]->TitleId."][orderId]", (isset($details['orderStatus']) && ($details['orderStatus'] != Order::STATUS_DRAFT)) ? "" : $details['orderId']); ?>
                <?php echo CHtml::hiddenField("OrderDetails[".$titles[$i]->TitleId."][save]", (($details['orderStatus'] != Order::STATUS_DRAFT) ? '0' : '1')); ?>
            </td>
        </tr>
    <?php
    endfor;

    if ($count === 0) :
    ?>
        <tr class="row1">
            <td colspan="4">
                <div class="errorBox">There are no titles setup on the system for your login.</div>
            </td>
        </tr>
    <?php
    endif;
    ?>
</tbody>
</table>

<table id="tblAux" class="listing fluid">
<tbody>
    <tr style="display:none;">
        <td width="30%">
            <?php echo CHtml::hiddenField("txtRowDelPointDummy"); ?>
            <?php echo Chtml::textField('txtRowDescDelPointDummy', '', array('size'=>'25', 'class'=>'readOnlyField', 'tabindex'=>'-1', 'readonly'=>'readonly')); ?>
        </td>
        <td>
            <?php echo Chtml::textField('txtRowCopiesDummy', '', array('size'=>'5', 'class' => 'recalc')); ?>
        </td>
        <td>
            <?php echo Chtml::textField('txtRowBundlesDummy', '', array('size'=>'5', 'class'=>'readOnlyField', 'tabindex'=>'-1', 'readonly'=>'readonly')); ?>
        </td>
        <td>
            <?php echo Chtml::textField('txtRowOddsDummy', '', array('size'=>'5', 'class'=>'readOnlyField', 'tabindex'=>'-1', 'readonly'=>'readonly')); ?>
        </td>
        <td>
            <a href="#" id="lnkDelete" tabindex="-1" class="delete-link">
                <img height="16" width="16" alt="add" src="img/icons/delete.png">
                Delete
            </a>
        </td>
    </tr>
</tbody>
</table>

<div class="titleWrap">
    <?php echo CHtml::hiddenField("txtSaveStatus", Order::STATUS_DRAFT); ?>
    <?php echo CHtml::submitButton('Save Order', array('class'=>'formButton save-draft')); ?>
    <?php echo CHtml::submitButton('Submit Order', array('class'=>'formButton save-final')); ?>
    <ul>
        <li class="seperator">
            <img height="16" width="16" alt="add" src="img/icons/cancel.png">
            <a href="<?php echo $this->createUrl('client/titles');?>">Cancel</a>
        </li>
    </ul>

</div>

<?php
$this->endWidget(); ?>

<script type="text/javascript" >

var statusDraft = '<?php echo Order::STATUS_DRAFT; ?>';
var statusFinal = '<?php echo Order::STATUS_SUBMITTED; ?>';

$(document).ready(function(){
    $(".save-draft").click(function(){
        if(validateOrders())
            $("#txtSaveStatus").val(statusDraft);
        else
            return false;
    });
    $(".save-final").click(function(){
        if(validateOrders())
            $("#txtSaveStatus").val(statusFinal);
        else
            return false;
    });

    $(".order-button").click(function(){
        var next = $(this).parents("tr").next("tr");
        
        if(next.is(":hidden"))
        {
            $(this).find(".order").hide();
            $(this).find(".cancel").show();
            next.find('input[id$="save"]').val(1);
            next.fadeIn();
        }
        else
        {
            $(this).find(".cancel").hide();
            $(this).find(".order").show();
            next.find('input[id$="save"]').val(0);
            next.fadeOut();
        }
        return false; //avoid submit;
    });

    $(".recalc").change(recalculateTotals);
    $(".delete-link").click(deleteRow);

    $(".dpicker").datepicker({
        dateFormat: 'dd/mm/yy',
        onSelect: function() {
            $(this).removeClass("error");
        }
    });
    recalculateTotals();

    $(".dummy-del-point").change(function(){
        if ($(this).val() != "")
            addRow($(this).val(), $(this).find("option:selected").text(), $("#tblAux tr:first").clone(), $(this).parents(".order-container"));
    });
});

function deleteRow(evt){
    $(this).parents("tr:first").remove(); // delete row
    redrawDetails();
    recalculateTotals();
    return false;
}

function addRow(dpId, dpDesc, newRow, orderContainer){
    newRow.removeAttr("style");

    newRow.find("#txtRowDelPointDummy").val(dpId);
    newRow.find("#txtRowDescDelPointDummy").val(dpDesc);

    newRow.appendTo(orderContainer.find('table[id^="tblDetails"]'));

    redrawDetails();
}

function redrawDetails(){
    $(".order-container").each(function(){
        var titleId = $(this).find('input[id$="titleId"]').val();
        $(this).find('table[id^="tblDetails"] tr').each(function(idx){
            if(idx > 0)
            {
                renameField($(this).find('input[id^="txtRowDelPoint"]'),
                    "txtRowDelPoint"+idx,
                    "OrderDetails["+titleId+"][delpoint"+idx+"]");

                renameField($(this).find('input[id^="txtRowDescDelPoint"]'),
                    "txtRowDescDelPoint"+idx,
                    "OrderDetails["+titleId+"][descdelpoint"+idx+"]");

                renameField($(this).find('input[id^="txtRowCopies"]'),
                    "txtRowCopies"+idx,
                    "OrderDetails["+titleId+"][copies"+idx+"]");

                renameField($(this).find('input[id^="txtRowBundles"]'),
                    "txtRowBundles"+idx,
                    "OrderDetails["+titleId+"][bundles"+idx+"]");

                renameField($(this).find('input[id^="txtRowOdds"]'),
                    "txtRowOdds"+idx,
                    "OrderDetails["+titleId+"][odds"+idx+"]");
            }
        });
    });
    $(".recalc").change(recalculateTotals);
    $(".delete-link").click(deleteRow);
}

function renameField(fld, newId, newName){
    fld.attr("id", newId);
    fld.attr("name", newName);
}

function recalculateTotals(){
    $(".order-container").each(function(){
        $(this).find(".recalc").each(function(){
            var curVal = parseInt($(this).val(), 10);
            if (isNaN(curVal))
            {
                $(this).val("");
                $(this).addClass("error");
            }
            else
            {
                $(this).removeClass("error");
                $(this).val(curVal);
            }
        });

        var bundleSize = $(this).find('input[id$="bundleSize"]').val();
        bundleSize = (isNaN(parseInt(bundleSize, 10))) ? 1 : parseInt(bundleSize, 10);
        var totalCopies = 0;

        //totalCopies
        $(this).find('table[id^="tblDetails"] tr').each(function(){
            var copies = $(this).find('input[id^="txtRowCopies"]').val();
            if (copies != undefined)
            {
                $(this).find('input[id^="txtRowBundles"]').val( Math.floor(copies / bundleSize) );
                $(this).find('input[id^="txtRowOdds"]').val(copies % bundleSize);
                totalCopies += isNaN(parseInt(copies, 10)) ? 0 : parseInt(copies, 10);
            }
        });

        $(this).find('input[id$="totalCopies"]').val(totalCopies);
    });
}

function validateOrders()
{
    var bError = false;
    $(".order-container").each(function(){
        if ($(this).find('input[id$="save"]').val() == "1")
        {
            $(this).find(".recalc").each(function(){
                var curVal = parseInt($(this).val(), 10);
                if (isNaN(curVal))
                {
                    $(this).val("");
                    $(this).addClass("error");
                    bError = true;
                }
                else
                {
                    $(this).removeClass("error");
                    $(this).val(curVal);
                }
            });

            $(this).find(".dpicker").each(function(){
                if($(this).val() == "")
                {
                    $(this).addClass("error");
                    bError = true;
                }
                else
                {
                    $(this).removeClass("error");
                }
            });

            if($(this).find('table[id^="tblDetails"] tr').length == 1)
                bError = true;
        }
    });

    $(".errorBox").css('display', ((bError) ? "block" : "none"));
    if (bError)
        window.scrollTo(0, 0);

    return !bError;
}

</script>