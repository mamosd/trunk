<?php
    $this->breadcrumbs=array(
                array('label'=>'Home', 'url'=>array(Yii::app()->user->role->HomeUrl)),
                array('label'=>'LSC', 'url' => '#'),
                array('label'=>'Control')
            );
    
    $baseUrl = Yii::app()->request->baseUrl;
    $readOnlyAttrs = array('class' => 'readOnlyField', 'readOnly' => 'readOnly');
    $smallReadOnlyAttrsBase = array_merge(array('size' => 5), $readOnlyAttrs);
    $totalReadOnlyAttrsBase = array_merge(array('size' => 7), $readOnlyAttrs);
    
    $cs = Yii::app()->getClientScript();
    $cs->registerScriptFile($baseUrl.'/js/sorttable.js');
    
    $anythingNotAck = array('DTC' => FALSE, 'DTR' => FALSE);
    
    $contractors = FinanceContractorDetails::model()->findAll();
    $contractorList = array();
    foreach($contractors as $c)
        $contractorList[$c->ContractorId] = $c;
?>
<style>
    .route-link {
        cursor: pointer;
    }
    
    #mainWrap {
        width: 95% !important;
    }
    .vtop th {
        vertical-align: middle;
    }
    .white {
        background-color: #FFF;
    }
    .red {
        background-color: #f46464;
    }
    .blue {
        background-color: #6af56e;
    }
    .sortable-hand {
        cursor: pointer;
    }
    table.sortable th:not(.sorttable_sorted):not(.sorttable_sorted_reverse):not(.sorttable_nosort):after { 
    content: " \25B4\25BE" 
    }
    
    .selected-type {
        /*color: red;*/
    }
    
    .unselected-type {
        font-weight: normal !important;
        text-decoration: none !important;
    }
    
    .route-listing tr:hover td { background: #A3FFA3; }
    .highlighted-row td { background: #FFFF99 !important; }
    .ui-state-active, .ui-widget-content .ui-state-active, .ui-widget-header .ui-state-active {
        background: #21E621 !important;
    }
    .ui-state-active a, .ui-state-active a:link, .ui-state-active a:visited {
        color: #fff !important;
    }
    
    .row-50 {
        width: 50px;
    }
</style>

<?php if ($model->baseEdit) : ?>
    <h1>LSC - Edit <?php echo $model->editingCategoryBase; ?> Base Routing Plan</h1>
<?php else: ?>
    <h1>LSC Control Screen</h1>
<?php endif; ?>
<div class="standardForm">
<?php $form=$this->beginWidget('CActiveForm', array(
        'id'=>'finance-form',
        'errorMessageCssClass'=>'formError',
        'method' => 'GET',
        'action'=>$this->createUrl($this->route)
)); 

echo CHtml::errorSummary($model, "", "", array('class'=>'errorBox'));
echo $form->hiddenField($model, 'doInitialize');

if (!$model->baseEdit) :
?>
    <table width="20%">
        <tr>
            <td>
                <div class="field">
                    <?php echo $form->labelEx($model,'weekStarting'); ?>
                    <?php echo $form->textField($model, 'weekStarting', array('size' => '10', 'readonly' => 'readonly', 'class' => 'dpicker')); ?>
                </div>
            </td>
            <td style="vertical-align: bottom;">
                <button id="btnLoadRoutes">Load Routes</button>
            </td>
        </tr>
    </table>   
    <br/>
<?php
else:
    echo $form->hiddenField($model, 'weekStarting');
    ?>
    <div class="infoBox">
        Please note that editing <?php echo $model->editingCategoryBase ?> base routing will affect the routing for week starting <?php echo $model->weekStarting ?> onwards. 
        <br/>If you wish to alter future dated base routing, the routing for that week should be initialized before doing so.
    </div>
    <?php
endif;
$this->endWidget(); ?>
</div>
    
<div id="dialog-loading" title="Please wait" style="display:none;">
    <div class="infoBox">Loading information...</div>
</div>
<script>
    function showLoader() {
        $( "#dialog-loading" ).dialog({
                resizable: false,
                height:100,
                modal: true
        });
    }
</script>

<?php
if (isset($model->routes)):
    if (Login::checkPermission(Permission::PERM__FUN__LSC__DTR)):
        if (!$model->baseEdit && !$model->dtrRoutes): ?>
        <?php if (Login::checkPermission(Permission::PERM__FUN__LSC__INITIALIZE)): ?>
            <div class="warningBox">
                No DTR routes entered for selected week. <a id="lnkInitializeDTR" class="initialize-link" href="#" rel="DTR">Create DTR schedule using base routing plan &raquo;</a>
            </div>
            <!--div class="infoBox">
                <strong>Note:</strong> This will not copy any adjustments or exception routes created specifically for last week, but will create a clean copy of the base routing plan for this week.
            </div-->
        <?php else: ?>
            <div class="warningBox">
                No DTR routes entered for selected week.
            </div>
        <?php endif; ?>
    <?php endif; 
    endif;
    ?>

    <?php
    if (Login::checkPermission(Permission::PERM__FUN__LSC__DTC)):
        if (!$model->baseEdit && !$model->dtcRoutes): ?>
        <?php if (Login::checkPermission(Permission::PERM__FUN__LSC__INITIALIZE)): ?>
            <div class="warningBox">
                No DTC routes entered for selected week. <a id="lnkInitializeDTC" class="initialize-link" href="#" rel="DTC">Create DTC schedule using base routing plan &raquo;</a>
            </div>
            <!--div class="infoBox">
                <strong>Note:</strong> This will not copy any adjustments or exception routes created specifically for last week, but will create a clean copy of the base routing plan for this week.
            </div-->
        <?php else: ?>
            <div class="warningBox">
                No DTC routes entered for selected week.
            </div>
        <?php endif; ?>
    <?php endif; 
    endif;
endif;
?>
    
<?php if ((isset($model->routes) && !empty($model->routes)) || ($model->baseEdit && !$model->baseDataAvailable)): ?>    
<div class="titleWrap">
    <?php if (!$model->baseEdit) : ?>
    <h2>Week Starting <?php echo $model->weekStarting; ?></h2>
    <?php endif; ?>
    
    <ul>
        <li class="seperator">
            <img src="<?php echo $baseUrl; ?>/img/icons/add.png" alt="add" />
            <a href="#" id="lnkNewRoute" date="<?php echo $model->weekStarting ?>">
                Add <?php echo (!$model->baseEdit) ? 'exception ' : ''; ?>route
            </a>
        </li>
    </ul>
</div>
            
<div class="titleWrap">
    <ul>
        <?php if (!$model->baseEdit) : ?>
        <li>
            Filters: 
            
            <?php if (Login::checkPermission(Permission::PERM__FUN__LSC__DTR) && $model->dtrRoutes): ?>
            <a href="#" id="lnkDtr" class="category-filter">DTR</a>  |
            <?php endif; ?>
            <?php if (Login::checkPermission(Permission::PERM__FUN__LSC__DTC) && $model->dtcRoutes): ?>
            <a href="#" id="lnkDtc" class="category-filter">DTC</a> |
            <?php endif; ?>
            
            <select id="ddlFilter">
                <option value="*">Show All</option>
                <option value="NACK">Show Unconfirmed only</option>
            </select>
        </li>
        <?php if (Login::checkPermission(Permission::PERM__FUN__LSC__PO)) : ?>
            <?php if ($model->dtcRoutes && Login::checkPermission(Permission::PERM__FUN__LSC__DTC)): ?>
            <li class="seperator">
            <button id="btnOutputDtcPOs">
                DTC POs
                <img src="<?php echo $baseUrl; ?>/img/icons/report.png" />
            </button>
            </li>    
            <?php endif; ?>
            <?php if ($model->dtrRoutes && Login::checkPermission(Permission::PERM__FUN__LSC__DTR)): ?>
            <li class="seperator">
            <button id="btnOutputDtrPOs">
                DTR POs
                <img src="<?php echo $baseUrl; ?>/img/icons/report.png" />
            </button>
            </li>
            <?php endif; ?>
        <?php endif; ?>
        <?php if (Login::checkPermission(Permission::PERM__FUN__LSC__INVOICES)) : ?>
            <?php if ($model->dtcRoutes && Login::checkPermission(Permission::PERM__FUN__LSC__DTC)): ?>
            <li class="seperator">
            <button id="btnOutputDtcInvoice">
                DTC Invoices
                <img src="<?php echo $baseUrl; ?>/img/icons/page_white_acrobat.png" />
            </button>
            </li>
            <?php endif; ?>
            <?php if ($model->dtrRoutes && Login::checkPermission(Permission::PERM__FUN__LSC__DTR)): ?>
            <li class="seperator">
            <button id="btnOutputDtrInvoice">
                DTR Invoices
                <img src="<?php echo $baseUrl; ?>/img/icons/page_white_acrobat.png" />
            </button>
            </li>
            <?php endif; ?>
        <?php endif; ?>
        <?php endif; ?>
    </ul>
</div>
            
<?php if ($model->baseEdit && !$model->baseDataAvailable): ?>
    <div class="warningBox">No Routing Data available in system. Click "add route" to begin.</div>
<?php else: ?>
    <div id="loader" class="infoBox">
        Loading routes ...
    </div>
    <script>
        showLoader();
    </script>
    
    <div id="tabs" style="display:none;">
    <ul>
        <?php foreach($model->routes as $catId => $routes): 
                $data = array_values($routes);
                $data = array_values($data[0]);
            ?>
        <li class="cat-<?php echo strtolower($data[0]->CategoryType); ?>">
            <a href="#tab-<?php echo $catId; ?>">
                <?php echo $data[0]->CategoryType.' '.$data[0]->Category; ?>
            </a>
        </li>
        <?php endforeach;?>
    </ul>
    
    <?php foreach($model->routes as $catId => $routes): 
        
        $allTotalValue = 0;
        $excTotalValue = 0;
        
        ?>
    <div id="tab-<?php echo $catId?>">
    
        <table id="table-<?php echo $catId?>" class="listing fluid vtop sortable route-listing">
        <thead>
        <tr>
            <th width="10" class="sorttable_nosort">Actions</th>
            <th width="125" class="sortable-hand">Route</th>
            <th class="sortable-hand">Contractor</th>
            <?php $datesToShow = array();
            $wst = CDateTimeParser::parse($model->weekStarting, "dd/MM/yyyy");
            // Regular entry types
            foreach(range(0,6) as $delta)
                $datesToShow[] = strtotime("+$delta day", $wst);
            
            foreach ($datesToShow as $date):
                ?>
            <th class="sorttable_nosort row-50">
                <?php echo $model->baseEdit ? '' : date('d/m', $date).'<br/>';?>
                <?php echo date('D', $date);?>
            </th>
            <?php endforeach; ?>
            <?php
            // special entry types
            $regularEntryType = FinanceRouteInstanceDetails::$REGULAR_ENTRY_TYPE;
            $specialEntryTypes = FinanceRouteInstanceDetails::$SPECIAL_ENTRY_TYPES;
            foreach ($specialEntryTypes as $entryType) :  // not required at phase 1 (empty array) ?>
            <th class="sorttable_nosort row-50">
                <?php echo $entryType;?>
            </th>    
            <?php endforeach; ?>
            <?php if (!$model->baseEdit): ?>
            <th class="sorttable_nosort row-50">
                Exceptions
            </th>
            <?php endif; ?>
            <th class="sorttable_nosort row-50">
                All
            </th>
        </tr>
        </thead>

        <tbody>
        <?php
            $rowCount=0;
            foreach ($routes as $routeId => $routeDates): 
                $routeInfo = array_values($routeDates);
                $routeInfo = $routeInfo[0];
                
                $this->renderPartial('control_row', array(
                    'model' => $model,
                    'routeInfo' => $routeInfo,
                    'rowCount' => $rowCount,
                    'routeDates' => $routeDates,
                    'contractorList' => $contractorList,
                    'datesToShow' => $datesToShow
                        ));

                $rowCount=$rowCount+1;
            endforeach;
        ?>
        </tbody>
        <tfoot>
        <tr>
            <td colspan="9">&nbsp;</td>
            <th class="sorttable_nosort">Totals</th>
            <?php if (!$model->baseEdit): ?>
            <td>
                <?php
                    $totalReadOnlyAttrsBase['class'] = $readOnlyAttrs['class'].' exc-overall-total';
                    echo CHtml::textField("dt-exc-total-$catId", sprintf("%01.2f", $excTotalValue), $totalReadOnlyAttrsBase); 
                ?>
            </td>
            <?php endif; ?>
            <td>
                <?php
                    $totalReadOnlyAttrsBase['class'] = $readOnlyAttrs['class'].' fee-overall-total';
                    echo CHtml::textField("dt-fee-total-$catId", sprintf("%01.2f", $allTotalValue), $totalReadOnlyAttrsBase); 
                ?>
            </td>
        </tr>
        </tfoot>
        </table>
        
    </div>
    <?php endforeach;?>
</div>
<?php endif; ?>
<?php endif; ?>

<script>
    
var currentTab = "";
var currentFilter = "";
var currentCategoryFilter = "";
var editingBase = <?php echo $model->baseEdit ? 'true' : 'false' ?>;

var exceptions = {
        dtr : <?php echo $anythingNotAck['DTR'] ? 'true' : 'false' ?>,
        dtc : <?php echo $anythingNotAck['DTC'] ? 'true' : 'false' ?>
    };
    
$(function(){
    
    var hash = window.location.hash;
    processHash(hash);
    
    $("#tabs").tabs({
        select: function( event, ui ) {
            //window.location.hash = ui.tab.hash; 
            currentTab = ui.tab.hash;
            updateHash();
        }
    }).tabs("select", currentTab);
    //}).tabs("select",window.location.hash);
        
    applyFilter(currentFilter);
    
    $(".dpicker").datepicker({
        dateFormat: 'dd/mm/yy',
        onSelect: function() {
            $(this).removeClass("error");
            $(this).change();
        },
        beforeShowDay: function(date){ return [date.getDay() == 1,""]},
        maxDate: '7'
    });
    
    //$(".route-link").click(showInstanceDetails);
    //$(".lnkDelete").click(dropRoute);
    //$(".contractor-earnings").click(showEarnings);
    //$('.route-listing td').click(highlightRow);
    bindRowEvents();
    
    $("#btnLoadRoutes").click(showLoader);
    
    $("#btnOutputDtcPOs").click(function(){
        if (exceptions.dtc)
            alert("Not Allowed, please have all DTC exceptions acknowledged first.");
        else {
            $("#Report_category").val('DTC');
            $("#accReport").submit();
        }
        return false;
    });
    
    $("#btnOutputDtrPOs").click(function(){
        if (exceptions.dtr)
            alert("Not Allowed, please have all DTR exceptions acknowledged first.");
        else {
            $("#Report_category").val('DTR');
            $("#accReport").submit();
        }
        return false;
    });
    
    $("#btnOutputDtcInvoice").click(function(){
        if (exceptions.dtc) 
            alert("Not Allowed, please have all DTC exceptions acknowledged first.");
        else {
            var url = "<?php echo $this->createUrl('finance/invoices', array('ui'=>'popUp', 'date' => $model->weekStarting, 'category' => 'DTC'));?>";
            $.colorbox({href: url, width:"800px", height:"450px", iframe:true});
        }
        return false;
    });
    
    $("#btnOutputDtrInvoice").click(function(){
        if (exceptions.dtr)
            alert("Not Allowed, please have all DTR exceptions acknowledged first.");
        else {
            var url = "<?php echo $this->createUrl('finance/invoices', array('ui'=>'popUp', 'date' => $model->weekStarting, 'category' => 'DTR'));?>";
            $.colorbox({href: url, width:"800px", height:"450px", iframe:true});
        }
        return false;
    });
    
    $(".initialize-link").click(function(){
        var category = $(this).attr('rel');
        
        var message = "Please confirm you wish to initialize the " + category + " routing for week starting <?php echo $model->weekStarting ?>.";
        message += "\n\nNote:This will not copy any adjustments or exception routes created specifically for last week, but will create a clean copy of the base routing plan for this week."
        
        if (!confirm(message))
            return false;
        
        $("#FinanceControl_doInitialize").val(category);
        $("#finance-form").submit();
        return false;
    });
    
    $("#lnkNewRoute").click(function(){
        var url = "<?php echo $this->createUrl('finance/route', array('ui'=>'popUp'));?>";
        url += (url.indexOf("?") == -1) ? "?" : "&";
        url += "date=" + $(this).attr('date');
        <?php if ($model->baseEdit): ?>
        url += "&base=1";
        url += "&filter=<?php echo $model->editingCategoryBase ?>";
        <?php else: ?>
        url += "&filter=" + currentCategoryFilter;  
        <?php endif; ?>
        $.colorbox({href: url, width:"800px", height:"500px", iframe:true, onClosed:reloadDropDowns});
        return false;
    });
    
    $("#ddlFilter").change(function(){
        var val = $(this).val();
        applyFilter(val);        
        updateHash();
    });
    
    $("#lnkDtr").click(function(){
        selectDtr('#tab-1');
        return false;
    });
    
    $("#lnkDtc").click(function(){
        selectDtc('#tab-18');
        return false;
    });
    
    var selTab = currentTab;
    
    <?php if ($model->baseEdit): // #129
        $tab = ($model->editingCategoryBase == 'DTR') ? 'tab-1' : 'tab-18';
        ?>
    if (selTab == "")
        selTab = "<?php echo $tab ?>";
        //$("#tabs").tabs('select', '<?php echo $tab ?>');
    <?php endif; ?>
    
    if (selTab != "") {
        $("#tabs").tabs("select", selTab);
        var selected = $(".ui-state-active", $("#tabs"));
        if (selected.hasClass("cat-dtr"))
            selectDtr();
        else if (selected.hasClass("cat-dtc"))
            selectDtc();
    }
    else {
        if ($(".category-filter").length == 1)
            $(".category-filter:first").click();
        else
            $("#lnkDtc").click();
    }
        
    calculateTotals();
    
    
    $("#loader").hide();
    $("#tabs").show();
    
    $( "#dialog-loading" ).dialog('destroy');
});

function clearTypeClasses()
{
    $("#lnkDtr").removeClass("selected-type");
    $("#lnkDtr").removeClass("unselected-type");
    $("#lnkDtc").removeClass("selected-type");
    $("#lnkDtc").removeClass("unselected-type");
}

function applyFilter(filter)
{
    currentFilter = filter;
    
    $("#ddlFilter").val(filter);
    $(".route-listing tbody tr").hide();
    
    switch(filter)
    {
        case "*":
            $(".route-listing tbody tr").show();
            break;
        case "NACK":
            $(".route-listing tbody tr").find('.red').each(function(){
                $(this).parents("tr:first").show();
            });
            break;
    }
}

function reloadDropDowns()
{
    showLoader();
    location.reload();
}

function processHash(hash)
{
    var filter = "*";
    var tab = "";
    
    if (hash != "")
    {
        var contents = hash.substring(1);
        var aOptions = contents.split("|");
        if (aOptions.length == 2)
        {
            tab = aOptions[0];
            filter = aOptions[1];
        }
    }
    
    currentTab = tab;
    currentFilter = filter;
}

function updateHash()
{
    var hash = currentTab+"|"+currentFilter;
    window.location.hash = hash;
}

function reloadRouteDetails(routeId, catId) {
    var data = {
            routeId : routeId,
            catId : catId
        };
        
    $( "#dialog-loading" ).dialog({
            resizable: false,
            height:100,
            modal: true
    });
        
    $.get("<?php echo $this->createUrl('finance/controlupdate', array(
                                            'weekStarting' => $model->weekStarting,
                                            'baseEdit' => $model->baseEdit,
                                            'editingCategoryBase' => $model->editingCategoryBase
            )); ?>",
        data,
        function(data) {
            if (data != '') {
                var $table;
                $("tr[rel='"+routeId+"'][cat='"+catId+"']").each(function(idx) {
                    if (idx == 0) {
                        $table = $(this).parents('table:first');
                        $(this).before(data);
                    }
                    $(this).remove();
                });
                
                calculateTotals($("tr[rel='"+routeId+"'][cat='"+catId+"']"));
                bindRowEvents();
                
                $( "#dialog-loading" ).dialog('destroy');
            }
        },
        'html');
}

function bindRowEvents() {
    $('.route-link').unbind('click', showInstanceDetails).click(showInstanceDetails);
    $('.route-link').unbind('mouseenter', tooltipHoverIn).mouseenter(tooltipHoverIn);
    $('.route-link').unbind('mouseleave', tooltipHoverOut).mouseleave(tooltipHoverOut);
    $('.route-link').unbind('mousemove', tooltipMouseMove).mousemove(tooltipMouseMove);
    $(".lnkDelete").unbind('click', dropRoute).click(dropRoute);
    $(".contractor-earnings").unbind('click', showEarnings).click(showEarnings);
    $('.route-listing td').unbind('click', highlightRow).click(highlightRow);
}

function calculateTotals(_rows) {
    if (_rows == undefined) 
        $(".route-listing").each(function(){
            $("tr:not(:first)", $(this)).each(function(){
                recalculateTotalsByObj($('.route-link:first', $(this)));
            });
        });
    else
        _rows.each(function(){
            recalculateTotalsByObj($('.route-link:first', $(this)));
        });
}

function recalculateTotalsByObj($obj) {
    var $row = $obj.parents('tr:first');
    var $table = $row.parents('table:first');
    
    // recalculate row totals
    var totalFee = 0;
    var totalExc = 0;
    $('.route-link', $row).each(function(){
        var fee = parseFloat($(this).attr('fee'));
        var exc = parseFloat($(this).attr('exception'));
        if (!isNaN(fee))
            totalFee += fee;
        if (!isNaN(exc))
            totalExc += exc;
    });
    $('.fee-total', $row).val(totalFee.toFixed(2));
    $('.exc-total', $row).val(totalExc.toFixed(2));
    
    // recalculate table totals (exceptions / total)
    totalFee = 0;
    totalExc = 0;
    $('.fee-total', $table).each(function(){
        var fee = parseFloat($(this).val());
        if (!isNaN(fee))
            totalFee += fee;
    });
    $('.exc-total', $table).each(function(){
        var exc = parseFloat($(this).val());
        if (!isNaN(exc))
            totalExc += exc;
    });
    //totalExc = -0.20+0.15+0.05;
    if ( Math.abs(totalExc) < 0.00000001 )
    {
        totalExc = Math.abs(totalExc);
    }
    //totalFee = -0.20+0.15+0.05;
    if ( Math.abs(totalFee) < 0.00000001 )
    {
        totalFee = Math.abs(totalFee);
    }
    $('.fee-overall-total', $table).val(totalFee.toFixed(2));
    $('.exc-overall-total', $table).val(totalExc.toFixed(2));
    
    exceptions.dtr = ($(".route-link[exc-dtr='1']").length > 0);
    exceptions.dtc = ($(".route-link[exc-dtc='1']").length > 0);
}

function showInstanceDetails(){
    var routeId = $(this).attr('rel');
    var instanceId = $(this).attr('rel-instance');
    var dateIdx = $(this).attr('date');
    var $row = $(this).parents('tr:first');
    var catId = $row.attr('cat');

    var url = "<?php echo $this->createUrl('finance/routeinstance', array('ui'=>'popUp','scenario'=>Yii::app()->controller->action->id));?>";
    url += (url.indexOf("?") == -1) ? "?" : "&";
    url += "id=" + routeId;
    url += "&instanceid=" + instanceId;
    url += "&date=" + dateIdx;
    <?php if ($model->baseEdit): ?>
    url += "&base=1";
    url += "&filter=<?php echo $model->editingCategoryBase ?>";
    <?php else: ?>
    url += "&filter=" + currentCategoryFilter;    
    <?php endif; ?>

    $.colorbox({href: url, width:"800px", height:"600px", iframe:true, 
        onClosed: function() {
            reloadRouteDetails(routeId, catId);
        }
    });

    return false;
}
    
function dropRoute(){
    if (confirm("Please confirm you wish to DELETE the selected route."))
    $.post("<?php echo $this->createUrl("finance/droproute") ?>",
        {id : $(this).attr('rel')},
        function(data){
            if (data.result == 'fail')
                alert('Error : ' + data.error);
            else
                reloadDropDowns();
        },
        'json'
        );
    return false;
}

function showEarnings(){
    var url = "<?php echo $this->createUrl('finance/earnings', array('ui'=>'popUp'));?>";
    url += (url.indexOf("?") == -1) ? "?" : "&";
    url += "date=" + $(this).attr('date');
    url += "&id=" + $(this).attr('rel');
    $.colorbox({href: url, width:"1000px", height:"600px", iframe:true});
    return false;
}

function highlightRow(){
    var cssClass = 'highlighted-row';
    var $row = $(this).parents('tr:first');
    if ($row.hasClass(cssClass))
        $row.removeClass(cssClass);
    else
        $row.addClass(cssClass);
}

function tooltipHoverIn() {
    // Hover over code
    var title = $(this).attr('title');
    if (title)
    {
        $(this).data('tipText', title).removeAttr('title');
        $('<p class="tooltip"></p>')
        .html(title)
        .appendTo('body')
        .fadeIn('slow');
    }
}

function tooltipHoverOut() {
    // Hover out code
    $(this).attr('title', $(this).data('tipText'));
    $('.tooltip').remove();
}

function tooltipMouseMove(e) {
    var mousex = e.pageX + 20; //Get X coordinates
    var mousey = e.pageY + 10; //Get Y coordinates
    $('.tooltip').css({ top: mousey, left: mousex })
}

function selectDtr(_default) {
    clearTypeClasses();
    currentCategoryFilter = "DTR";
    $("#lnkDtr").addClass("selected-type");
    $("#lnkDtc").addClass("unselected-type");
    $(".cat-dtr").show();
    $(".cat-dtc").hide();
    //$("#tabs").tabs("select", $(".cat-dtr a:first").attr("href"));
    if (_default != undefined)
        currentTab = _default;
    $("#tabs").tabs("select", currentTab); // #129
}
    
function selectDtc(_default) {
    clearTypeClasses();
    currentCategoryFilter = "DTC";
    $("#lnkDtc").addClass("selected-type");
    $("#lnkDtr").addClass("unselected-type");
    $(".cat-dtr").hide();
    $(".cat-dtc").show();
    //$("#tabs").tabs("select", $(".cat-dtc a:first").attr("href"));
    if (_default != undefined)
        currentTab = _default;
    $("#tabs").tabs("select", currentTab); // #129  
}
</script>

<form action="<?php echo $this->createUrl('finance/accountingreport'); ?>"
      target="_blank"
      id="accReport"
      method="post">
    <?php echo CHtml::hiddenField('Report[weekStarting]', $model->weekStarting); ?>
    <?php echo CHtml::hiddenField('Report[category]', ''); ?>
</form>

<!--tooltip-->
<style type="text/css">
.tooltip {
	display:none;
	position:absolute;
	border:1px solid #ccc;
	background-color:#fff;
	border-radius:5px;
	padding:10px;
	color:#000;
	font-size:11px Arial;
}
</style>