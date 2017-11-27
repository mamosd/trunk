<?php
    $this->breadcrumbs=array(
                array('label'=> Yii::app()->user->name , 'url'=>array(Yii::app()->user->role->HomeUrl)),
                array('label'=>'Order Entry'),
            );

$orderHeaderDummy = <<<HTML
<tr class="order-header {class}">
    <td>{titlename}</td>
    <td>{printcentrename}</td>
    <td>
        <button class="formButton remove-button" titleid="{titleid}">
            <img src="img/icons/delete.png" alt="order" width="16" height="16" /> Remove
        </button>
    </td>
</tr>
HTML;

$orderIdField = CHtml::hiddenField("OrderDetails[{titleid}][orderId]", '{orderid}');
$orderIdField = CHtml::hiddenField("OrderDetails[{titleid}][routeId]", '{routeid}');
$orderStatusField = CHtml::hiddenField("OrderDetails[{titleid}][status]", '{status}');
$orderSelectedField = CHtml::hiddenField("OrderDetails[{titleid}][isSelected]", '1');
$paginationField = CHtml::textField("OrderDetails[{titleid}][pagination]", '{pagination}', array('size'=>'5', 'class' => 'recalc'));
$bundleSizeField = CHtml::textField("OrderDetails[{titleid}][bundleSize]", '{bundlesize}', array('size'=>'5', 'class' => 'recalc'));
$pubDateField = CHtml::textField("OrderDetails[{titleid}][publicationDate]", '{publicationdate}', array('size'=>'10', 'class' => 'dpicker'));
$delDateField = CHtml::textField("OrderDetails[{titleid}][deliveryDate]", '{deliverydate}', array('size'=>'10', 'class' => 'dpicker'));
$totalCopiesField = CHtml::textField("OrderDetails[{titleid}][totalCopies]", '', array('size'=>'5', 'class'=>'readOnlyField', 'tabindex'=>'-1', 'readonly'=>'readonly'));
$orderDummy = <<<HTML
<tr class="order-container">
    <td colspan="3">
$orderIdField $orderStatusField
$orderSelectedField
    <table class="listing fluid" cellpadding="0" cellspacing="0">
    <tr>
        <th>Pagination</th>
        <th>Bundle Size</th>
        <th>Publication Date</th>
        <th>Delivery Date</th>
    </tr>
    <tr>
        <td>$paginationField</td>
        <td>$bundleSizeField</td>
        <td>$pubDateField</td>
        <td>$delDateField</td>
    </tr>
    </table>

    <div class="titleWrap">
        <h3>Delivery Points</h3>

        <ul>
            <li>
                <label>Total Copies</label>
                $totalCopiesField
            </li>
        </ul>
    </div>

    <table id="tblDetails_{titleid}" class="listing fluid" cellpadding="0" cellspacing="0">
    <tbody>
    <tr>
        <th width="30%">Delivery Point</th>
        <th>Copies</th>
        <th>Bundles</th>
        <th>Odds</th>
    </tr>
    {order-lines}
    </tbody>
    </table>
</td></tr>
HTML;

$delPointField = CHtml::hiddenField("OrderDetails[{titleid}][delpoint{idx}]", '{delpointid}', array('id'=>'txtRowDelPoint_{titleid}_{idx}'));
$delPointDescField = CHtml::textField("OrderDetails[{titleid}][descdelpoint{idx}]", '{delpointdesc}', array('id'=>'txtRowDescDelPoint_{titleid}_{idx}', 'size'=>'25', 'class'=>'readOnlyField', 'tabindex'=>'-1', 'readonly'=>'readonly'));
$copiesField = CHtml::textField("OrderDetails[{titleid}][copies{idx}]", '{copies}', array('id'=>'txtRowCopies_{titleid}_{idx}', 'size'=>'5', 'class' => 'recalc'));
$bundlesField = CHtml::textField("OrderDetails[{titleid}][bundles{idx}]", '', array('id'=>'txtRowBundles_{titleid}_{idx}', 'size'=>'5', 'class'=>'readOnlyField', 'tabindex'=>'-1', 'readonly'=>'readonly'));
$oddsField = CHtml::textField("OrderDetails[{titleid}][odds{idx}]", '', array('id'=>'txtRowOdds_{titleid}_{idx}', 'size'=>'5', 'class'=>'readOnlyField', 'tabindex'=>'-1', 'readonly'=>'readonly'));
$orderLineDummy = <<<HTML
<tr>
    <td width="30%">$delPointField$delPointDescField</td>
    <td>$copiesField</td>
    <td>$bundlesField</td>
    <td>$oddsField</td>
</tr>
HTML;
?>

<div class="titleWrap">
    <h1>Order Entry</h1>
    <ul>
        <li class="seperator">
            Select Route
            <img src="img/icons/add.png" alt="add" />
            <input id="txtRoute" size="50" style="display: none;">
            <?php echo CHtml::dropDownList("ddlRoute", "", $model->getOptionsRoutes(), array("empty"=>"-- Select One --")) ?>
        </li>
        <li><a href="#" id="lnkLoadOldValues" style="display:none;">load last values</a></li>
    </ul>
</div>

<?php $form=$this->beginWidget('CActiveForm', array(
            'id'=>'orders-form',
            'errorMessageCssClass'=>'formError',
    )); ?>

<div id="lastOrderInfo" class="infoBox" style="display:none;"></div>
<div id="msgBox" class="errorBox" style="display:none;" ></div>

<table id="draft-table" class="listing fluid" cellpadding="0" cellspacing="0">
<tbody>
    <tr class="header">
        <th width="50%">Title</th>
        <th width="35%">Print Centre</th>
        <th width="15%">Actions</th>
    </tr>
<?php
/*    $orders = $model->getDraft();
    $idx = 0;
    foreach($orders as $titleId => $o)
    {
        $idx++;
        $html = $orderHeaderDummy;
        $html = str_replace('{class}', 'row'.($idx%2), $html);
        $html = str_replace('{titleid}', $titleId, $html);
        $html = str_replace('{titlename}', $o['titlename'], $html);
        $html = str_replace('{printcentrename}', $o['printcentrename'], $html);

        echo $html;

        $html = $orderDummy;
        $html = str_replace('{titleid}', $titleId, $html);
        $html = str_replace('{orderid}', $o['orderid'], $html);
        $html = str_replace('{status}', $o['status'], $html);
        $html = str_replace('{pagination}', $o['pagination'], $html);
        $html = str_replace('{bundlesize}', $o['bundlesize'], $html);
        $html = str_replace('{publicationdate}', $o['publicationdate'], $html);
        $html = str_replace('{deliverydate}', $o['deliverydate'], $html);

        $countlines = count($o['details']);
        $lines = $o['details'];
        $lineshtml = "";
        for ($i = 0; $i < $countlines; $i++)
        {
            $lineshtml .= $orderLineDummy;
            $lineshtml = str_replace('{idx}', $i, $lineshtml);
            $lineshtml = str_replace('{titleid}', $titleId, $lineshtml);
            $lineshtml = str_replace('{delpointid}', $lines[$i]['delpoint'], $lineshtml);
            $lineshtml = str_replace('{delpointdesc}', $lines[$i]['descdelpoint'], $lineshtml);
            $lineshtml = str_replace('{copies}', $lines[$i]['copies'], $lineshtml);
        }
        $html = str_replace('{order-lines}', $lineshtml, $html);

        echo $html;
        
    }
 *
 */
?>
    <tr class="header empty-row" <?php //echo ($idx > 0) ? "style='display:none;'" : ''; ?>>
        <td colspan="4">
            <div class="warningBox">Add Titles to the order selecting a Route from the list above.</div>
        </td>
    </tr>
</tbody>
</table>

<!--button class="formButton save-draft">
    <img src="img/icons/disk.png" alt="order" width="16" height="16" /> Save Draft
</button-->

<div class="titleWrap">
    <button class="formButton submit-orders">
        <img src="img/icons/accept.png" alt="order" width="16" height="16" /> Submit
    </button>

    <ul>
        <li class="seperator">
            <a href="<?php echo $this->createUrl(Yii::app()->user->role->HomeUrl); ?>">cancel</a>
        </li>
    </ul>
</div>



<?php
echo CHtml::hiddenField("task", 'SUBMIT');
//echo CHtml::hiddenField("param", ''); // thought to send title id for individual submits -- using SAVE-SELECTED for now, clearing the rest.
$this->endWidget(); ?>

<table id="order-header-dummy" style="display:none;">
<?php echo $orderHeaderDummy; ?>
</table>

<table id="order-dummy" style="display:none;">
<?php echo $orderDummy; ?>
</table>

<table id="order-line-dummy" style="display:none;">
<?php echo $orderLineDummy; ?>
</table>

<script type="text/javascript">
var msg = "<?php echo $model->message; ?>";


$(document).ready(function(){
   // autocomplete
/*   $("#txtRoute").autocomplete({
            minLength: 0,
            source: titles,
            focus: function( event, ui ) {
                    $( "#txtRoute" ).val( ui.item.label );
                    return false;
            },
            select: function( event, ui ) {
                    addToDraft( ui.item.value );
                    return false;
            }
    })
    .data( "autocomplete" )._renderItem = function( ul, item ) {
            return $( "<li></li>" )
                    .data( "item.autocomplete", item )
                    .append( "<a>" + item.label + "</a>" )
                    .appendTo( ul );
    };

    $("#txtRoute").select();
   // autocomplete end
*/
    $("#ddlRoute").change(function(){
        addToDraft( $(this).val() );
    });

   $(".submit-orders").click(function(){
//       return false;
       return validateOrders();
   })

   // bind events for existing orders.
   doBinding();
   recalculateTotals();

   if (msg != "")
       showMsg(msg, 'infoBox');

   <?php if(isset($routeId))
            echo "addToDraft($routeId);";
       ?>
});

function addToDraft(routeId) {
    
    $.getJSON("<?php echo $this->createUrl("admin/routeinfo"); ?>", { id: routeId}, function(resp) {

        if (resp.error != undefined)
        {
            showError(resp.error);
            return;
        }

        if (resp.route != undefined)
        {
            $("#txtRoute").val(resp.route.name);
            $("#txtRoute").addClass("readOnlyField");
            $("#txtRoute").attr("readonly", "readonly");
            $("#txtRoute").attr("tabindex", "-1");

            $("#ddlRoute").hide();
            $("#txtRoute").show();
        }

        for(var k = 0; k < resp.titles.length; k++)
        {
            var json = resp.titles[k];
            // add row to draft
            var tid = json.title.id;
            var nbrRows = $("#draft-table .order-header").length;

            var newRow = $("#order-header-dummy tr:first").clone();
            // clear default values
                newRow.html(newRow.html().replace(/{class}/gi, ""));
            newRow.addClass("row"+((nbrRows%2)+1));
            newRow.html(newRow.html().replace(/{titlename}/gi, json.title.name));
            newRow.html(newRow.html().replace(/{printcentrename}/gi, json.title.printcentre));
            newRow.html(newRow.html().replace(/{orderid}/gi, '-1'));
            newRow.html(newRow.html().replace(/{titleid}/gi, tid));


            $("#draft-table").append(newRow);

            //$(window).scrollTop(newRow.offset().top);

            var newOrderRow = $("#order-dummy tr:first").clone();
            // clear default values
                newOrderRow.html(newOrderRow.html().replace(/{orderid}/gi, ""));
                newOrderRow.html(newOrderRow.html().replace(/{status}/gi, ""));
                newOrderRow.html(newOrderRow.html().replace(/{pagination}/gi, ""));
                newOrderRow.html(newOrderRow.html().replace(/{bundlesize}/gi, ""));
                newOrderRow.html(newOrderRow.html().replace(/{publicationdate}/gi, ""));
                newOrderRow.html(newOrderRow.html().replace(/{deliverydate}/gi, ""));
                newOrderRow.html(newOrderRow.html().replace(/{order-lines}/gi, ""));
            newOrderRow.html(newOrderRow.html().replace(/{titleid}/gi, tid));
            newOrderRow.html(newOrderRow.html().replace(/{routeid}/gi, routeId));
//            newOrderRow.addClass("row"+((nbrRows%2)+1));
            $("#draft-table").append(newOrderRow);

            // set order level values
            if (json.order != undefined)
            {
                $("#OrderDetails_"+ tid +"_pagination").attr('oldval', json.order.pagination);
                $("#OrderDetails_"+ tid +"_bundleSize").attr('oldval', json.order.bundleSize);


                $("#lnkLoadOldValues").click(function(){
                    $("#draft-table input[type=text]").each(function(){
                        if($(this).attr("oldval") != "")
                            $(this).val($(this).attr("oldval"));
                    });
                    // show last order info
                    $("#lastOrderInfo").html("NOTE: Last order posted was for Publication Date <strong>" + json.order.publicationDate + "</strong> and Delivery Date <strong>" + json.order.deliveryDate + "</strong>")
                    $("#lastOrderInfo").show();

                    recalculateTotals();
                    return false;
                });
                $("#lnkLoadOldValues").show();
            }

            if (json.deliverypoints != undefined)
            {
                var dps = json.deliverypoints;
                for (var i = 0; i < dps.length; i++)
                {
                    var newLineRow = $("#order-line-dummy tr:first").clone();
                    newLineRow.html(newLineRow.html().replace(/{titleid}/gi, tid));
                    newLineRow.html(newLineRow.html().replace(/{idx}/gi, i));
                    $("#tblDetails_" + tid).append(newLineRow);

                    $("#txtRowDelPoint_"+tid+"_"+i).val(dps[i].id);
                    $("#txtRowDescDelPoint_"+tid+"_"+i).val(dps[i].name);
                    $("#txtRowCopies_"+tid+"_"+i).attr('oldval', dps[i].copies);
                }
            }
            else
                showError("There is no route information setup for this title: " + json.title.name);
        }
        
        $("#draft-table .empty-row").hide();
        
        doBinding();
        recalculateTotals();

        $("#draft-table .order-container:first").find("input[type=text]:first").select();

    });
}

function doBinding() {
    $("#draft-table .recalc").unbind('change');
    $("#draft-table .recalc").bind('change', recalculateTotals);

    $("#draft-table .dpicker").datepicker({
        dateFormat: 'dd/mm/yy',
        onSelect: function() {
            $(this).removeClass("error");
            $(this).change();
        }
    });

    $("#draft-table input[id$='publicationDate']:first").change(function(){
        var curVal = $(this).val();
        $("#draft-table input[id$='publicationDate']").each(function(){
            //if($.trim($(this).val()) == "") // only if blank?
                $(this).val(curVal);
                $(this).removeClass("error");
        });
    });

    $("#draft-table input[id$='deliveryDate']:first").change(function(){
        var curVal = $(this).val();
        $("#draft-table input[id$='deliveryDate']").each(function(){
            //if($.trim($(this).val()) == "") // only if blank?
                $(this).val(curVal);
                $(this).removeClass("error");
        });
    });

    showHideRemoveButtons();
}

function showHideRemoveButtons() {
    if($("#draft-table .remove-button:visible").length < 2)
        $("#draft-table .remove-button:visible").hide();
    else {
        $("#draft-table .remove-button").unbind('click');
        $("#draft-table .remove-button").click(removeTitle);
    }
}

function removeTitle() {
    if(confirm("Are you sure you wish to remove this title from this order?"))
    {
         //alert($(this).attr('titleid'));
         $("#OrderDetails_"+ $(this).attr('titleid') +"_isSelected").val("0");
         $(this).parents("tr:first").next().hide();
         $(this).hide();
         showHideRemoveButtons();
    }

    return false;
}


function recalculateTotals(){
    $("#draft-table .order-container").each(function(){
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
    $("#draft-table .order-container:visible").each(function(){
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
    });

    if (bError) 
        showError("There are errors in the form, make sure all highlighted fields are completed correctly and try again.");
    
    return !bError;
}

function showError(sMsg) {
    showMsg(sMsg, 'errorBox');
}

var toError;
function showMsg(sMsg, sClass) {
    clearTimeout(toError);
    if (sMsg != "")
    {
        $("#msgBox").text(sMsg);
        $("#msgBox").removeClass().addClass(sClass).fadeIn();
        window.scrollTo(0, 0);
        toError = setTimeout(showError, 3000, "");
    }
    else
    {
        $("#msgBox").fadeOut();
    }
}
</script>