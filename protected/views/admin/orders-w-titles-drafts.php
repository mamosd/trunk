<?php
    $this->breadcrumbs=array(
                array('label'=> Yii::app()->user->name , 'url'=>array(Yii::app()->user->role->HomeUrl)),
                array('label'=>'Order Entry'),
            );

    $titles = $model->getTitles();
    $titlescount = count($titles);

$orderHeaderDummy = <<<HTML
<tr class="order-header {class}">
    <td><input id="select-{titleid}" name="OrderDetails[{titleid}][isSelected]" class="select-title" value="1" type="checkbox" /></td>
    <td>{titlename}</td>
    <td>{printcentrename}</td>
    <td>
        <button class="formButton submit-button" titleid="{titleid}">
            <img src="img/icons/accept.png" alt="order" width="16" height="16" /> Submit
        </button>
    </td>
</tr>
HTML;

$orderIdField = CHtml::hiddenField("OrderDetails[{titleid}][orderId]", '{orderid}');
$orderStatusField = CHtml::hiddenField("OrderDetails[{titleid}][status]", '{status}');
$paginationField = CHtml::textField("OrderDetails[{titleid}][pagination]", '{pagination}', array('size'=>'5', 'class' => 'recalc'));
$bundleSizeField = CHtml::textField("OrderDetails[{titleid}][bundleSize]", '{bundlesize}', array('size'=>'5', 'class' => 'recalc'));
$pubDateField = CHtml::textField("OrderDetails[{titleid}][publicationDate]", '{publicationdate}', array('size'=>'10', 'class' => 'dpicker'));
$delDateField = CHtml::textField("OrderDetails[{titleid}][deliveryDate]", '{deliverydate}', array('size'=>'10', 'class' => 'dpicker'));
$totalCopiesField = CHtml::textField("OrderDetails[{titleid}][totalCopies]", '', array('size'=>'5', 'class'=>'readOnlyField', 'tabindex'=>'-1', 'readonly'=>'readonly'));
$orderDummy = <<<HTML
<tr class="order-container">
    <td>&nbsp;
        $orderIdField $orderStatusField
    </td>
    <td colspan="3">
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

    <h2>Delivery Points Entry</h2>

    <div class="infoRight">
        <div>
            <label>Total Copies</label>
            $totalCopiesField
        </div>
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
    <h1>Current Draft</h1>
    <ul>
        <li class="seperator">
            <img src="img/icons/add.png" alt="add" />
            <input id="txtTitle">
        </li>
    </ul>
</div>

<?php $form=$this->beginWidget('CActiveForm', array(
            'id'=>'orders-form',
            'errorMessageCssClass'=>'formError',
    )); ?>

<div id="msgBox" class="errorBox" style="display:none;" ></div>

<table id="draft-table" class="listing fluid" cellpadding="0" cellspacing="0">
<tbody>
    <tr class="header">
        <th width="25"><input class="select-all" type="checkbox" /></th>
        <th>Name</th>
        <th>Print Centre</th>
        <th width="12%">Actions</th>
    </tr>
<?php
    $orders = $model->getDraft();
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
?>
    <tr class="header empty-row" <?php echo ($idx > 0) ? "style='display:none;'" : ''; ?>>
        <td colspan="4">
            <div class="warningBox">Add titles to the draft from the list above.</div>
        </td>
    </tr>
</tbody>
</table>

<button class="formButton save-draft">
    <img src="img/icons/disk.png" alt="order" width="16" height="16" /> Save Draft
</button>
<button class="formButton submit-selected">
    <img src="img/icons/accept.png" alt="order" width="16" height="16" /> Submit Selected
</button>

<?php
echo CHtml::hiddenField("task", 'SAVE-DRAFT');
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

var titles = [
<?php for($i = 0; $i < $titlescount; $i++): ?>
    {value: "<?php echo $titles[$i]->TitleId ?>", label: "<?php echo $titles[$i]->Name; ?>"}
<?php
    if ($i != ($titlescount-1))
        echo ",";    
    endfor; ?>
];

$(document).ready(function(){
   // autocomplete
   $("#txtTitle").autocomplete({
            minLength: 0,
            source: titles,
            focus: function( event, ui ) {
                    $( "#txtTitle" ).val( ui.item.label );
                    return false;
            },
            select: function( event, ui ) {
                    /*$( "#project" ).val( ui.item.label );
                    $( "#project-id" ).val( ui.item.value );
                    $( "#project-description" ).html( ui.item.desc );
                    $( "#project-icon" ).attr( "src", "images/" + ui.item.icon );
                    */
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
   // autocomplete end
//   $(".add-to-draft").click(function(){
//      addToDraft($(this).attr('titleid'));
//   });
   $(".select-all").change(function(){
      $("#draft-table .select-title").attr('checked', $(this).attr('checked'));
   });
   $(".save-draft").click(function(){
      $("#task").val("SAVE-DRAFT");
      return validateOrders();
   });
   $(".submit-selected").click(submitSelected);
//   $("#ddlTitle").change(addFromList);
//   $("#lnkAdd").click(addFromList);

   // TODO: bind events for existing orders in draft..
   doBinding();
   recalculateTotals();

   if (msg != "")
       showMsg(msg, 'infoBox');
});

function submitThis() {
    // clear all + select this title
    $(".select-all").attr('checked', false);
    $("#draft-table .select-title").attr('checked', false);
    $(this).parents("tr").find(".select-title").attr('checked', true);
    return submitSelected();
}

function submitSelected(){
   // validate some title is selected
   var bFound = false;
   $("#draft-table .select-title").each(function(){
       if($(this).attr('checked') == true)
           bFound = true;
   })
   if (!bFound) {
       showError("At least one title must be checked to use this feature.");
       return false;
   }
   // post submit
   $("#task").val("SUBMIT-SELECTED");
   return validateOrders();
}

function addFromList(evt) {
    var titleId = $("#ddlTitle").val();
    if (titleId != "") {
        addToDraft(titleId);
    }
    $("#ddlTitle").val("");
    return false;
}

function addToDraft(titleId) {
    // validate title is not on draft already
    var bFound = false;
    $(".submit-button").each(function() {
        if ($(this).attr('titleid') == titleId)
            bFound = true;
    });
    if (bFound) {
        showError("The selected title is already in the draft.");
        return false;
    }

    $.getJSON("<?php echo $this->createUrl("admin/titleinfo"); ?>", { id: titleId}, function(json) {
        // validate title information:
        if (json.deliverypoints == undefined)
        {
            showError("There is no route information setup for this title: " + json.title.name);
            return false;
        }

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

        $(window).scrollTop(newRow.offset().top);

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
        $("#draft-table").append(newOrderRow);

        // set order level values
        if (json.order != undefined)
        {
            //$("#OrderDetails_"+ tid +"_orderId").val(json.order.id);
            //$("#OrderDetails_"+ tid +"_status").val(json.order.status);
            $("#OrderDetails_"+ tid +"_pagination").val(json.order.pagination);
            $("#OrderDetails_"+ tid +"_bundleSize").val(json.order.bundleSize);
            //$("#OrderDetails_"+ tid +"_publicationDate").val(json.order.publicationDate);
            //$("#OrderDetails_"+ tid +"_deliveryDate").val(json.order.deliveryDate);
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
                $("#txtRowCopies_"+tid+"_"+i).val(dps[i].copies);
            }
        }
        else
            showError("There is no route information setup for this title: " + json.title.name);

        $("#draft-table .empty-row").hide();

        doBinding();
        recalculateTotals();
    });
}

function doBinding() {
    $("#draft-table .recalc").unbind('change');
    $("#draft-table .recalc").bind('change', recalculateTotals);
    $("#draft-table .submit-button").unbind('click');
    $("#draft-table .submit-button").bind('click', submitThis);

    $("#draft-table .dpicker").datepicker({
        dateFormat: 'dd/mm/yy',
        onSelect: function() {
            $(this).removeClass("error");
        }
    });
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
    $("#draft-table .order-container").each(function(){
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