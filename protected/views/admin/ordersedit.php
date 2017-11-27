<?php
    $this->breadcrumbs=array(
                array('label'=> Yii::app()->user->name , 'url'=>array(Yii::app()->user->role->HomeUrl)),
                array('label'=>'Orders'),
            );
?>

<div class="titleWrap">
    <h1>Orders Listing</h1>
</div>

<?php if ($message != ''): ?>
<div class="infoBox"><?php echo $message; ?></div>
<?php endif; ?>

<table class="listing fluid" cellpadding="0" cellspacing="0">
<tbody>
    <tr>
        <td colspan="3"></td>
        <th colspan="3">Date</th>
    </tr>
    <tr>
        <th width="100">Actions</th>
        <th>Title</th>
        <th>Route</th>
        <th width="75">Publication</th>
        <th width="75">Delivery</th>
        <th width="75">Update</th>
    </tr>
<?php
    $count = count($model->orders);
    $deliveryPoints = $model->getOptionsDeliveryPoint();
    for($i = 0; $i < $count; $i++):
        $order = $model->orders[$i];
    ?>

    <tr class="row<?php echo (($i%2)+1) ?>">
        <td>
            <button class="formButton order-button">
                  <div class="details">
                      <img src="img/icons/zoom_in.png" alt="order" width="16" height="16" /> Details
                  </div>
                <div class="hide" style="display:none;">
                      <img src="img/icons/zoom_out.png" alt="order" width="16" height="16" /> Hide
                  </div>
              </button>
        </td>
        <td><?php echo $order->TitleName; ?></td>
        <td><?php echo $order->RouteName; ?></td>
        <td><?php echo $order->PublicationDate; ?></td>
        <td><?php echo $order->DeliveryDate; ?></td>
        <td><?php echo Yii::app()->dateFormatter->format('dd/MM/yyyy', $order->DateUpdated); ?></td>
    </tr>
    <tr class="order-container" style="display:none;">
        <td></td>
        <td colspan="5">
            <?php
            $orderDetails = $model->getOrderDetails($order->OrderId);

            $form=$this->beginWidget('CActiveForm', array(
                'id'=>'order-form-'.$order->OrderId,
                'errorMessageCssClass'=>'formError',
            ));

            ?>

            <div class="infoRight">
                <?php
                echo 'Saved on: ';
                echo CHtml::textField("OrderDetails[".$order->OrderId."][dateCreated]", Yii::app()->dateFormatter->format('dd/MM/yyyy', $order->DateUpdated), array('size'=>'10', 'class'=>'readOnlyField', 'tabindex'=>'-1', 'readonly'=>'readonly'));
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
                    <?php echo CHtml::hiddenField("OrderDetails[".$order->OrderId."][routeId]", $order->RouteId); ?>
                    <?php echo CHtml::textField("OrderDetails[".$order->OrderId."][pagination]", $order->Pagination, array('size'=>'10', 'class'=>'readOnlyField editable recalc', 'tabindex'=>'-1', 'readonly'=>'readonly')); ?>
                </td>
                <td>
                    <?php echo CHtml::textField("OrderDetails[".$order->OrderId."][bundleSize]", $order->BundleSize, array('size'=>'10', 'class'=>'readOnlyField editable recalc', 'tabindex'=>'-1', 'readonly'=>'readonly')); ?>
                </td>
                <td>
                    <?php echo CHtml::textField("OrderDetails[".$order->OrderId."][publicationDate]", $order->PublicationDate, array('size'=>'10', 'class'=>'readOnlyField editable dpicker', 'tabindex'=>'-1', 'readonly'=>'readonly')); ?>
                </td>
                <td>
                    <?php echo CHtml::textField("OrderDetails[".$order->OrderId."][deliveryDate]", $order->DeliveryDate, array('size'=>'10', 'class'=>'readOnlyField', 'tabindex'=>'-1', 'readonly'=>'readonly')); ?>
                </td>
            </tr>
            </table>

            <h2>Delivery Points</h2>

            <div class="infoRight">
                <div>
                    <label>Total Copies</label>
                    <?php
                        $copies = 0;
                        foreach($orderDetails as $od)
                            $copies += $od->Copies;
                        echo CHtml::textField("OrderDetails[".$order->OrderId."][totalCopies]", $copies, array('size'=>'5', 'class'=>'readOnlyField', 'tabindex'=>'-1', 'readonly'=>'readonly'));
                    ?>
                </div>
            </div>

            <table id="tblDetails_<?php echo $order->OrderId; ?>" class="listing fluid" cellpadding="0" cellspacing="0">
            <tbody>
                <tr>
                  <th width="30%">Delivery Point</th>
                  <th>Copies</th>
                  <th>Bundles</th>
                  <th>Odds</th>
                  <!--th class="editable" style="display:none;">Actions</th-->
                </tr>
                <?php
                    $cnt = count($orderDetails);
                    for ($j = 0; $j < $cnt; $j++):
                        $od = $orderDetails[$j];
                    ?>
                <tr>
                    <td width="30%">
                        <?php echo CHtml::hiddenField("OrderDetails[".$order->OrderId."][delpoint".($j+1)."]", $od->DeliveryPointId, array('id'=>'txtRowDelPoint'.($j+1))); ?>
                        <?php echo CHtml::textField("OrderDetails[".$order->OrderId."][descdelpoint".($j+1)."]", $od->DeliveryPointName, array('id'=>'txtRowDescDelPoint'.($j+1), 'size'=>'25', 'class'=>'readOnlyField', 'tabindex'=>'-1', 'readonly'=>'readonly')); ?>
                    </td>
                    <td>
                        <?php echo CHtml::textField("OrderDetails[".$order->OrderId."][copies".($j+1)."]", $od->Copies, array('id'=>'txtRowCopies'.($j+1), 'size'=>'5', 'class'=>'readOnlyField editable recalc', 'tabindex'=>'-1', 'readonly'=>'readonly')); ?>
                    </td>
                    <td>
                        <?php
                            $bundles = floor($od->Copies / $order->BundleSize);
                            echo CHtml::textField("OrderDetails[".$order->OrderId."][bundles".($j+1)."]", $bundles, array('id'=>'txtRowBundles'.($j+1), 'size'=>'5', 'class'=>'readOnlyField', 'tabindex'=>'-1', 'readonly'=>'readonly'));
                        ?>
                    </td>
                    <td>
                        <?php 
                            $odds = $od->Copies % $order->BundleSize;
                            echo CHtml::textField("OrderDetails[".$order->OrderId."][odds".($j+1)."]", $odds, array('id'=>'txtRowOdds'.($j+1), 'size'=>'5', 'class'=>'readOnlyField', 'tabindex'=>'-1', 'readonly'=>'readonly'));
                        ?>
                    </td>
                    <!--<td class="editable" style="display:none;">
                        <a href="#" id="lnkDelete" tabindex="-1" class="delete-link">
                            <img height="16" width="16" alt="add" src="img/icons/delete.png">
                            Delete
                        </a>
                    </td-->
                </tr>
                <?php
                    endfor;
                ?>
            </tbody>
            </table>
            <!--table class="listing fluid editable" style="display:none;">
            <tbody>
                <tr>
                    <td colspan="5">
                        <?php echo CHtml::dropDownList("ddlDummyDelPoint[".$order->OrderId."]", "", $deliveryPoints, array('empty'=>'Select a Delivery Point to Add ->', 'class'=>'dummy-del-point')); ?>
                    </td>
                </tr>
            </tbody>
            </table-->
            <?php
                echo CHtml::hiddenField("OrderDetails[".$order->OrderId."][orderid]", $order->OrderId, array('id'=>'orderId'.$order->OrderId));
                echo CHtml::button('Edit', array('class'=>'formButton edit-button'));
                echo CHtml::submitButton('Save', array('class'=>'formButton save-button', 'style'=>'display:none;'));
            ?>
            <?php
            $this->endWidget(); ?>
        </td>
    </tr>

<?php
    endfor;
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
        <!--td>
            <a href="#" id="lnkDelete" tabindex="-1" class="delete-link">
                <img height="16" width="16" alt="add" src="img/icons/delete.png">
                Delete
            </a>
        </td-->
    </tr>
</tbody>
</table>

<?php
//$this->endWidget(); ?>

<script type="text/javascript" >
$(document).ready(function(){
    $(".order-button").click(function(){
        var next = $(this).parents("tr").next("tr");

        if(next.is(":hidden"))
        {
            $(this).find(".details").hide();
            $(this).find(".hide").show();
            next.fadeIn();
        }
        else
        {
            $(this).find(".hide").hide();
            $(this).find(".details").show();
            next.fadeOut();
        }
        return false; //avoid submit;
    });

    $(".edit-button").click(function(){
        var container = $(this).parents(".order-container");
        makeOrderEditable(container);

        $(this).fadeOut(function(){
            container.find(".save-button").fadeIn();
        });
    });

    $(".dummy-del-point").change(function(){
        if ($(this).val() != "")
            addRow($(this).val(), $(this).find("option:selected").text(), $("#tblAux tr:first").clone(), $(this).parents(".order-container"));
    });
});

function makeOrderEditable(container)
{
    container.find(".editable").each(function(){
        $(this).fadeIn();
        $(this).removeAttr("readonly");
        $(this).removeClass("readOnlyField");
        $(this).removeAttr("tabindex");
    });

    container.find(".recalc").change(recalculateTotals);
    container.find(".delete-link").click(deleteRow);

    container.find(".dpicker").datepicker({
        dateFormat: 'dd/mm/yy',
        onSelect: function() {
            $(this).removeClass("error");
        }
    });

/*    container.find(".dummy-del-point").change(function(){
        if ($(this).val() != "")
            addRow($(this).val(), $(this).find("option:selected").text(), $("#tblAux tr:first").clone(), $(this).parents(".order-container"));
    });
*/
    container.addClass("editing");
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
        var orderId = $(this).find('input[id^="orderId"]').val();
        $(this).find('table[id^="tblDetails"] tr').each(function(idx){
            if(idx > 0)
            {
                renameField($(this).find('input[id^="txtRowDelPoint"]'),
                    "txtRowDelPoint"+idx,
                    "OrderDetails["+orderId+"][delpoint"+idx+"]");

                renameField($(this).find('input[id^="txtRowDescDelPoint"]'),
                    "txtRowDescDelPoint"+idx,
                    "OrderDetails["+orderId+"][descdelpoint"+idx+"]");

                renameField($(this).find('input[id^="txtRowCopies"]'),
                    "txtRowCopies"+idx,
                    "OrderDetails["+orderId+"][copies"+idx+"]");

                renameField($(this).find('input[id^="txtRowBundles"]'),
                    "txtRowBundles"+idx,
                    "OrderDetails["+orderId+"][bundles"+idx+"]");

                renameField($(this).find('input[id^="txtRowOdds"]'),
                    "txtRowOdds"+idx,
                    "OrderDetails["+orderId+"][odds"+idx+"]");
            }
        });

        if($(this).hasClass("editing"))
            makeOrderEditable($(this));
    });
}

function renameField(fld, newId, newName){
    fld.attr("id", newId);
    fld.attr("name", newName);
}
</script>