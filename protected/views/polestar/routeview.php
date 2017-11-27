<?php
    $this->breadcrumbs=array(
                array('label'=>'Home', 'url'=>array(Yii::app()->user->role->HomeUrl)),
                array('label'=>'Polestar', 'url' => '#'),
                array('label'=>'Routes')
            );
    $baseUrl = Yii::app()->request->baseUrl;

    $readOnlyAttrs = array('class' => 'readOnlyField info-entry', 'readOnly' => 'readOnly');
    $smallReadOnlyAttrsBase = array_merge(array('size' => 5), $readOnlyAttrs);
    $smallReadOnlyAttrs = $smallReadOnlyAttrsBase;

    $cs=Yii::app()->getClientScript();
    $cs->registerScriptFile($baseUrl.'/js/jquery.multiselect.min.js',CClientScript::POS_HEAD,array(
        "charset" => "ISO-8859-1"
    ));
    $cs->registerCssFile($baseUrl.'/css/jquery.multiselect.css');
    $cs->registerCssFile($baseUrl.'/css/polestar_status_formatting.css');

    $allowedPCOptions = PolestarPrintCentre::getAllForLoginAsOptions();
?>
<style>
#mainWrap
{
    display: table;
}
table.listing th {
    white-space: nowrap !important;
}
table.listing td {
    border-color: transparent !important;
}
select {
    height: 28px;
}
.job-container:nth-of-type(odd) {
    background: #E5E5E5;
}
.job-details {
    font-size: 1.1em !important;
    max-width: 80% !important;
}
.job-points-details {
    font-size: 0.95em !important;
    max-width: 60% !important;
}
.load-details {
    font-size: 0.95em !important;
}
hr {
    color: orange;
    background-color: orange;
    height: 3px;
    border-style: none;
}
.drag-load {
    cursor: move !important;
}
.info-entry {
    cursor: pointer !important;
}
input.map-job {
    cursor: pointer !important;
    background-color: #F49933 !important;
}
.tooltip {
    display:none;
    position:absolute;
    border:1px solid #ccc;
    background-color:#fff;
    border-radius:5px;
    padding:10px;
    color:#000;
    font-size:11px Arial;
    max-width: 350px;
    z-index: 9999;
}
    .highlight {
        background-color: #FA8991 !important;
    }
    
.cancelled-load {
    /*opacity: 0.3;*/
}
.cancelled-load td {
    background-color: red;
    opacity: 0.3;
    color: white !important;
    text-shadow: none !important;
}
.actions {
    min-width: 60px !important;
    text-align: right;
    padding-right: 5px !important;
}
.cancelled-load .actions {
    opacity: 1 !important;
    background-color: inherit !important;
}
.nowrap {
    white-space: nowrap;
}
#activitylog-container {
    font-size: 0.8em;
}
#activitylog-container .action-icon {
    height: 12px;
}
#activitylog-container h1 {
    display: none;
}
#activitylog-container .titleWrap ul {
    margin: 0 0 2px 0 !important
}
#activitylog-container .titleWrap li {
    padding-left: 0px !important;
}
#activitylog-container select {
    height: 20px;
}
#activitylog-container input {
    height: 5px;
}
#activitylog-jobs-container {
    height: 125px;
    overflow-y: scroll;
}
.titleWrap ul {
    margin: 5px 0 10px 0 !important;
}
h2 {
    margin: 5px 0 9px 0 !important;
}
</style>

<table class="listing fluid">
    <tr>
        <td width="30%">
            <h1>Routes</h1>
            
            <div class="infoBox">
                <strong>Planning Date:</strong><br/>
                Jobs returned will be:
                <ul>
                    <li>- Jobs for delivery on planning date (only Same Day Advice / Late Advice)</li>
                    <li>- Jobs for delivery on planning date + 1 (regardless status) </li>
                </ul>
            </div>
            
            <div class="standardForm">
            <?php $form=$this->beginWidget('CActiveForm', array(
                    'id'=>'route-form',
                    'errorMessageCssClass'=>'formError',
                    'method' => 'GET',
                    'action'=>$this->createUrl($this->route)
            ));

            echo CHtml::errorSummary($model, "", "", array('class'=>'errorBox'));
            ?>
                <table class="listing fluid">
                <tr>
                    <td>
                        <div class="field">
                            <?php echo $form->labelEx($model, 'planningDate'); ?>
                            <?php echo $form->textField($model, 'planningDate', array('size' => '10', 'readonly' => 'readonly', 'class' => 'dpicker change-clear')); ?>
                        </div>
                    </td>
                    <td>
                        <div class="field">
                            <?php echo $form->labelEx($model, 'printCentreId'); ?>
                            <?php echo $form->dropDownList($model, 'printCentreId',
                                        $allowedPCOptions,
                                        array('empty'=>'select one ->', 'class' => 'change-clear')); ?>
                        </div>
                    </td>
                    <td style="vertical-align: bottom;">
                        <button id="btnLoadRoutes">Load Control Screen</button>
                    </td>
                </tr>
                </table>
            <?php $this->endWidget(); ?>
            </div>
        </td>
        <td>
            <fieldset style="min-height: 175px;">
                <legend>Activity Log</legend>
                <?php
                $almodel = new PolestarActivityLogForm();
                $criteria = Yii::app()->session['activitylog-criteria'];
                if (isset($criteria))
                    $almodel->setAttributes($criteria, false);
                $almodel->fullBlown = FALSE;
                $this->renderPartial('activitylog', array('model' => $almodel, 'ajax' => FALSE));
                ?>
            </fieldset>
            
        </td>
    </tr>
</table>



<div id="dialog-loading" title="Please wait" style="display:none;">
    <div class="infoBox">Loading information...</div>
</div>
<div id="dialog-sending-advice-sheet" title="Sending" style="display:none;">
    <div class="infoBox sending">Sending advice sheet...</div>
    <div class="successBox ok">Sent</div>
    <div class="errorBox error">Error sending the email</div>
</div>

<script>
    function showLoader() {
        $( "#dialog-loading" ).dialog({
                resizable: false,
                height:100,
                modal: true
        });
    }

    function hideLoader() {
        $( "#dialog-loading" ).dialog('destroy');
    }
</script>

<br/>

<div id="results">

<?php if (isset($model->jobsData)): ?>
    <script>
        showLoader();
    </script>


<div class="titleWrap">
    <h2>Routes for <?php echo $model->planningDate ?></h2>

    <ul>
        <li class="seperator">
            Filter by status:
            <?php echo CHtml::dropDownList('ddlStatusFilter', NULL, PolestarStatus::getAllAsOptions(), array('empty' => 'Show All')); ?>
        </li>
        <li class="seperator">
            <img src="<?php echo $baseUrl; ?>/img/icons/add.png" alt="add" />
            <?php
                $dt = DateTime::createFromFormat('d/m/Y', $model->planningDate);
                $dt = $dt->modify('+1 day');
                $nextDay = $dt->format('d/m/Y');
            ?>
            <a href="#" class="new-job" dt="<?php echo $nextDay ?>">Add route</a>
        </li>
        <li class="seperator">
            <img src="<?php echo $baseUrl; ?>/img/icons/page_excel.png" alt="export" />
            <a href="#" 
               target="_blank"
               id="btnExportRoutes">Export Routes</a>
        </li>
        <li class="seperator">
            <img src="<?php echo $baseUrl; ?>/img/icons/table_row_insert.png" alt="add" />
            <a href="#" class="route-upload">Upload Routes</a>
        </li>
    </ul>
</div>


<div id="pc-tabs">
  <ul>
      <?php
        $activeIdx = 0;
        $idx = 0;
        foreach ($allowedPCOptions as $pcid => $pcname) {
            if ($pcid == $model->getPrintCentre()->Id) {
                echo '<li><a href="#tabs-1">'.$pcname.'</a></li>';
                $activeIdx = $idx;
            }
            else {
                $url = $this->createUrl('polestar/routeview', array('PolestarRouteViewForm[planningDate]' => $model->planningDate, 'PolestarRouteViewForm[printCentreId]' => $pcid));
                echo '<li><a href="'.$url.'">'.$pcname.'</a></li>';
            }
            $idx++;
        }
      ?>
  </ul>
    <div id="tabs-1">

<?php if (empty($model->jobsData)): ?>
<div class="warningBox">
    There are no routes entered for selected date and print centre combination yet. Route can be initialized/built using the add route / upload options above.
</div>
<?php else: ?>

<?php
        foreach ($model->jobsData as $jobInfo):
            $this->renderPartial('routeview_job', array(
                            'model' => $model,
                            'jobInfo' => $jobInfo,
                                ));
        endforeach;

    endif;
endif;

?>
    </div>
</div>

</div>

<script>
$(function() {

    $('body').on('mousedown','#cboxClose',function(){
        if (!confirm("Are you sure you wish to close this window?\nChanges done (if any) will be lost."))
            return false;
        else
            $(this).click();
    });

    $(".dpicker").datepicker({
        dateFormat: 'dd/mm/yy',
        onSelect: function() {
            $(this).removeClass("error");
            $(this).change();
        }
    });

    $( "#pc-tabs" ).tabs({
      active: <?php echo $activeIdx ?>,
      beforeLoad: function( event, ui ) {
        var $lnk = $('a:first', ui.tab);
        showLoader();
        location.href = $lnk.attr('href');
        return false;
      }
    });

    $("#ddlStatusFilter").multiselect({
        selectedList: 2, // 0-based index
        header: "Choose options below",
        click: function(e){
            $('.job-container').hide();
            var resetAll = false;
            $(this).multiselect("widget").find("input:checked").each(function(){
                if($(this).val() == '')
                    $('.job-container').show();
                else
                    $('.job-container[status="'+$(this).val()+'"]').show();
            });
        }
     });

    $(".change-clear").change(function(){
        $("#results").hide();
    });

    $(".route-upload").click(function(){
        var url = "<?php echo $this->createUrl('polestar/upload', array('pcid' => $model->printCentreId, 'ui'=>'popUp', 'dt' => $model->planningDate));?>";
        $.colorbox({href: url, 
            width:"750px", height:"550px", 
            iframe:true,
            onClosed: function(){
                location.reload();
            }});
        return false;
    });

    $("#btnLoadRoutes").click(function() {
        showLoader();
    });
    
    $("#btnExportRoutes").click(function() {
        var url = "<?php echo $this->createUrl('polestar/export', array('date' => $model->planningDate, 'pc' => $model->printCentreId)); ?>";
        var filter = "";
        $("#ddlStatusFilter").multiselect("widget").find("input:checked").each(function(){
            if($(this).val() == '')
                filter = "*";
            else if (filter != "*")
                filter += ","+$(this).val();
        });
        url += (url.indexOf("?") == -1) ? "?" : "&";
        url += "filter=" + filter;
        $(this).attr('href', url);
    });

    bindJobEvents($('body'));

    bindTooltipEvents();

    hideLoader();


});

function bindJobEvents($ctx) {
    $(".new-job", $ctx).unbind('click', showJobPopup).click(showJobPopup);
    $(".edit-job", $ctx).unbind('click', showJobPopup).click(showJobPopup);
    $(".clone-job", $ctx).unbind('click', showCloneJobPopup).click(showCloneJobPopup);
    $(".drop-job", $ctx).unbind('click', dropJobConfirm).click(dropJobConfirm);
    $(".drop-cp", $ctx).unbind('click', dropCPConfirm).click(dropCPConfirm);
    $(".drop-load", $ctx).unbind('click', dropLoadConfirm).click(dropLoadConfirm);
    $(".activate-load", $ctx).unbind('click', activateLoadConfirm).click(activateLoadConfirm);
    $(".edit-load", $ctx).unbind('click', showLoadPopup).click(showLoadPopup);
    $(".add-load", $ctx).unbind('click', showLoadPopup).click(showLoadPopup);
    $(".add-coll-point", $ctx).unbind('click', showJobCollPointPopup).click(showJobCollPointPopup);
    $(".info-entry", $ctx).unbind('click', showInfoEntryPopup).click(showInfoEntryPopup);
    $(".send-advise", $ctx).unbind('click', showAdviceSheetDialog).click(showAdviceSheetDialog);
    $(".map-job", $ctx).unbind('click', showJobMapPopup).click(showJobMapPopup);

    $(".sortable_loads tbody", $ctx).sortable({
        'stop' : sortLoads
    }).disableSelection();

    $(".sortable_cps tbody", $ctx).sortable({
        'stop' : sortCPS
    }).disableSelection();
}

function showJobMapPopup() {
    var jobContainer = $(this).parents('.job-container:first');
    if (jobContainer != undefined) {
        var jobId = jobContainer.attr('rel');
        var url = "<?php echo $this->createUrl('polestar/jobmap', array('ui'=>'popUp'));?>";
        url += (url.indexOf("?") == -1) ? "?" : "&";
        url += "id=" + jobId;
        $.colorbox({href: url, width:"90%", height:"95%",
            iframe:true,
            onClosed: function(){
                reloadJob(jobId);
            }
        });
    }
    else
        alert('The job information is not being properly displayed on the screen, please reload the page and retry.');
    return false;
}

function showInfoEntryPopup(){
    var jobContainer = $(this).parents('.job-container:first');
    if (jobContainer != undefined) {
        var jobId = jobContainer.attr('rel');
        var url = "<?php echo $this->createUrl('polestar/infoentry', array('ui'=>'popUp'));?>";
        url += (url.indexOf("?") == -1) ? "?" : "&";
        url += "id=" + jobId;
        $.colorbox({href: url, width:"90%", height:"95%",
            iframe:true,
            onClosed: function(){
                reloadJob(jobId);
            }
        });
    }
    else
        alert('The job information is not being properly displayed on the screen, please reload the page and retry.');
    return false;
}

function dropCPConfirm() {
    if (confirm("Are you sure you wish to cancel this collection point?")) {
        var id = $(this).parents('tr').attr('data-id');
        var jid = $(this).parents('tr').attr('data-jid');
        dropCP(id,jid);
    }
    return false;
}

function dropCP(id,jid) {
    $.ajax({
        'url' : '<?php echo Yii::app()->createUrl('polestar/collection_point_drop')?>',
        'data' : {
            'id'    : id,
            'jid'   : jid,
        },
        'type' : 'POST',
        'complete': function(){
            reloadJob(jid);
        }
    });
}

function dropJobConfirm() {
    if (confirm("Are you sure you wish to cancel this job?")) {
        var jid = $(this).parents('.job-container').attr('rel');
        dropJob(jid);
    }
    return false;
}

function dropJob(jid) {
    $.ajax({
        'url' : '<?php echo Yii::app()->createUrl('polestar/route_drop')?>',
        'data' : {
            'id'   : jid,
        },
        'type' : 'POST',
        'complete': function(){
            reloadJob(jid);
        }
    });
}

function dropLoadConfirm(){
    //console.log($(this).parents('.load_container'));
    if (confirm("Are you sure you wish to cancel this load?")) {
        dropLoad($(this).parents('.load_container').attr('data-id'), $(this));
    }
    return false;
}
function dropLoad(lid, pthis) {
    var jid = pthis.parents('.job-container').attr('rel');
    $.ajax({
        'url' : '<?php echo Yii::app()->createUrl('polestar/load_drop')?>',
        'data' : {
            'id'   : lid,
        },
        'type' : 'POST',
        'complete': function(){
            reloadJob(jid);
        }
    });
}

function activateLoadConfirm(){
    if (confirm("Are you sure you wish to activate this load?")) {
        activateLoad($(this).parents('.load_container').attr('data-id'), $(this));
    }
    return false;
}

function activateLoad(lid,pthis){
    var jid = pthis.parents('.job-container').attr('rel');
    $.ajax({
        'url' : '<?php echo Yii::app()->createUrl('polestar/load_activate')?>',
        'data' : {
            'id'   : lid,
        },
        'type' : 'POST',
        'complete': function(){
            reloadJob(jid);
        }
    });
}

function sortLoads(event, ui) {
    var pthis = $(this);
    var jid = pthis.parents('.job-container').attr('rel');

    var params = [];
    pthis.find("tr").each(function(pos, item) {
        var pitem = $(item);
        var sequence = pitem.attr('data-seq');
        if ((pos + 1) != sequence) {
            var id = pitem.attr('data-id');
            params.push({ 'id' : id, 'seq': pos + 1 })
            pitem.attr('data-seq', pos + 1);
        }
    });
    if (params.length > 0) {
        $.ajax({
            'url' : '<?php echo Yii::app()->createUrl('polestar/route_reorder')?>',
            'data' : {
                'jid'   : jid,
                'items' : params,
            },
            'type' : 'POST',
            'complete' : function() {
                reloadJob(jid);
            }
        });
    }
}

function sortCPS(event, ui) {
    var pthis = $(this);
    var jid = pthis.parents('.job-container').attr('rel');

    var params = [];
    pthis.find("tr").each(function(pos, item) {
        var pitem = $(item);
        var sequence = pitem.attr('data-seq');
        if (pos != sequence) {
            var id = pitem.attr('data-id');
            var jid =  pitem.attr('data-jid');
            params.push({ 'id' : id, 'seq': pos, 'jid': jid })
            pitem.attr('data-seq', pos );
        }
    });
    if (params.length > 0) {
        $.ajax({
            'url' : '<?php echo Yii::app()->createUrl('polestar/route_reorder_cps')?>',
            'data' : {
                'jid'   : jid,
                'items' : params,
            },
            'type' : 'POST',
            'complete' : function() {
                reloadJob(jid);
            }
        });
    }
}

function showJobPopup() {
    var jid = $(this).parents('.job-container').attr('rel');
    var url = "<?php echo $this->createUrl('polestar/route', array('ui'=>'popUp'));?>";
    url += (url.indexOf("?") == -1) ? "?" : "&";
    if (jid != undefined) {
        url += "id=" + jid;
    }
    else {
        url += "dt=" + $(this).attr('dt');
        url += "&pc=" + $("#PolestarRouteViewForm_printCentreId").val();
    }
    $.colorbox({
        href: url,
        width:"1000px",
        height:"95%",
        iframe:true,
        onClosed: function(){
            if (jid != undefined)
                reloadJob(jid);
            else
                location.reload();
        }
    });
    return false;
}

function showJobCollPointPopup() {
    var jid = $(this).parents('.job-container').attr('rel');
    var url = "<?php echo $this->createUrl('polestar/route', array('ui'=>'popUp', 'add-coll-point' => 1));?>";
    url += (url.indexOf("?") == -1) ? "?" : "&";
    if (jid != undefined) {
        url += "id=" + jid;
    }
    else {
        url += "dt=" + $(this).attr('dt');
        url += "&pc=" + $("#PolestarRouteViewForm_printCentreId").val();
    }
    $.colorbox({
        href: url,
        width:"1000px",
        height:"95%",
        iframe:true,
        onClosed: function(){
            if (jid != undefined)
                reloadJob(jid);
            else
                location.reload();
        }
    });
    return false;
}

function showAdviceSheetDialog() {
    var pthis = $(this);
    var jid = $(this).parents('.job-container').attr('rel');

    $.ajax({
        'url': '<?php echo Yii::app()->createUrl('polestar/job_suppliers/'); ?>/',
        'data': {
            'id': jid
        },
        'success': function(data) {
            if (data.length > 0) {
                var contacts = $('<table class="listing fluid" cellpadding="0" cellspacing="0"><thead><tr><th>Select</th><th>Sent</th><th>D/N</th><th>Dept</th><th>Name</th><th>Landline</th><th>Mobile</th><th>Email</th></tr></thead></table>');
                for (var i = 0; i < data.length; i++) {
                    var contact = data[i];
                    if (!contact.receiveAdviceEmails) {
                        continue;
                    }
                    var c = $('<tr/>');
                    c.append( $('<td/>').append( $('<input type="checkbox" value="1"/>').attr('ref',contact.id) ) );
                    var iconName = contact.sent ? 'accept' : 'cancel';
                    var iconPath = "<?php echo $baseUrl ?>/img/icons/"+iconName+".png";
                    c.append( $('<td/>').append( $('<img/>').attr('src', iconPath).addClass('tooltiped').attr('title', contact.sent ? 'Advice sheet already sent to this contact' : 'Advice sheet not yet sent') ) );
                    c.append( $('<td/>').append(contact.type) );
                    c.append( $('<td/>').append(contact.department) );
                    c.append( $('<td/>').append(contact.name) );
                    c.append( $('<td/>').append(contact.landline) );
                    c.append( $('<td/>').append(contact.mobile) );
                    c.append( $('<td/>').append(contact.email) );
                    c.appendTo(contacts);
                }
                var send = $('<button/>').append('Send to selected');
                var contactsDialog = $("<div/>")
                                        .append($("<em/>").text("Please select contacts to send the advice sheet"))
                                        .append($("<br/>")).append($("<br/>"))
                                        .append(contacts)
                                        .append($("<br/>"))
                                        .append(send);
                contactsDialog.dialog({
                    resizalble: false,
                    height: 300,
                    width: 800,
                    modal: true,
                });
                send.click(function(){
                    var ids = contactsDialog.find('input:checked').map(function(){
                        return $(this).attr('ref');
                    }).get();
                    if (ids.length == 0) {
                        alert("Please select at least one contact to send the advice sheet to.");
                        return false;
                    }
                    contactsDialog.dialog('destroy');
                    sendAdviceSheet(jid,ids);
                });
            }
            else {
                var dialog = $( "#dialog-sending-advice-sheet" ).dialog({
                    resizable: false,
                    height:150,
                    width:500,
                    modal: true
                });
                dialog.find("div").hide();
                dialog.find(".error").text("There are no contacts selected for sending under the supplier, please review the supplier configuration.").show();
                setTimeout(function(){ dialog.dialog('destroy'); }, 3000);
            }
        },
        'dataType' : 'json'
    });
};

function sendAdviceSheet (jid, ids) {
     var dialog = $( "#dialog-sending-advice-sheet" ).dialog({
         resizable: false,
         height:100,
         width:500,
         modal: true
     });
     dialog.find("div").hide();
     dialog.find(".sending").show();

     $.ajax({
        'url': '<?php echo Yii::app()->createUrl('polestar/send_advice_sheet/'); ?>/',
         'data': {
             'id': jid,
             'contacts' : ids,
         },
         'success': function(data) {
             if (data.success) {
                 dialog.find("div").hide();
                 dialog.find(".ok").show();
                 setTimeout(function(){ dialog.dialog('destroy'); reloadJob(jid); }, 3000);
             }
             else {
                 dialog.find("div").hide();
                 dialog.find(".error").text(data.reason).show();
                 setTimeout(function(){ dialog.dialog('destroy'); }, 3000);
             }
         },
         'error' : function() {
             dialog.find("div").hide();
             dialog.find(".error").show();
             setTimeout(function(){ dialog.dialog('destroy'); }, 3000);
         },
         'dataType' : 'json'
     });
}

function showLoadPopup() {
    var existing = $(this).parents('.load_container').attr('data-id');
    var jid = $(this).parents('.job-container').attr('rel');
    var url = "<?php echo $this->createUrl('polestar/load', array('ui'=>'popUp'));?>";
    url += (url.indexOf("?") == -1) ? "?" : "&";
    if (existing != undefined) {
        url += "id=" + existing;
    }
    else {
        url += "jid=" + $(this).attr('job');
    }
    $.colorbox({
        href: url,
        width:"1000px",
        height:"95%",
        iframe:true,
        onClosed: function(){
            if (jid != undefined)
                reloadJob(jid);
            else
                location.reload();
        }
    });
    return false;
}

function bindTooltipEvents() {
    $('body').on('mouseenter','.tooltiped',tooltipHoverIn);
    $('body').on('mouseleave','.tooltiped',tooltipHoverOut);
    $('body').on('mousemove','.tooltiped',tooltipHoverMove);
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

function tooltipHoverMove(e) {
    var mousex = e.pageX - $('.tooltip').width() - 10; //Get X coordinates
    var mousey = e.pageY + 10; //Get Y coordinates

	if (mousey - $(window).scrollTop() + $('.tooltip').height() > $(window).height() - 30)
	{
		mousey = mousey - (mousey - $(window).scrollTop() + $('.tooltip').height() - $(window).height() + 30);
		mousex -= 30;
	}

    $('.tooltip').css({ top: mousey, left: mousex })
}

function reloadJob(jobId) {
    showLoader();
    var $container = $('.job-container[rel='+jobId+']');
    if ($container != undefined) {
        var data = {
            id : jobId
        };
        $.get("<?php echo $this->createUrl('polestar/routeview_job'); ?>",
            data,
            function(data) {
                if (data != '') {
                    $container.before(data).remove();
                    $container = $('.job-container[rel='+jobId+']');
                    bindJobEvents($container);
                    hideLoader();
                }
            },
            'html');
    }
    else
        location.reload();
}

function showCloneJobPopup() {
    var jid = $(this).parents('.job-container').attr('rel');
    var url = "<?php echo $this->createUrl('polestar/routeclone', array('ui'=>'popUp'));?>";
    url += (url.indexOf("?") == -1) ? "?" : "&";
    url += "id=" + jid;
    $.colorbox({
        href: url,
        width:"90%",
        height:"95%",
        iframe:true,
        onClosed: function(){
            // redirect handled by popup
        }
    });
    return false;
}
</script>