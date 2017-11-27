<?php
$baseUrl = Yii::app()->request->baseUrl;
$jobs = $model->getData();

if ($model->fullBlown) :
    $cs=Yii::app()->getClientScript();
    $cs->registerScriptFile($baseUrl.'/js/jquery.multiselect.min.js',CClientScript::POS_HEAD,array(
        "charset" => "ISO-8859-1"
    ));
    $cs->registerCssFile($baseUrl.'/css/jquery.multiselect.css');
    $cs->registerCssFile($baseUrl.'/css/polestar_status_formatting.css');
?>
<style>
select {
    height: 28px;
}
</style>
<?php endif; ?>

<?php if (!$ajax): ?>
<div id="activitylog-container">
<?php endif; ?>
    
    <div class="titleWrap">
    <h1>Activity Log</h1>
    <ul>
        <li>
            Filters
        </li>
        <li>
            <?php echo CHtml::textField('alDateFrom', $model->dateFrom
                    , array('size' => '10', 'placeholder' => 'Created From')); ?>
        </li>
        <li>
            <?php echo CHtml::textField('alDateTo', $model->dateTo
                    , array('size' => '10', 'placeholder' => 'To')); ?>
        </li>
        <li>
            <?php echo CHtml::dropDownList('alHourFilter', $model->hourRange
                    , array('24' => '24hs', '48' => '48hs', '72' => '72hs')
                    , array('empty' => 'hours')); ?>
        </li>
        <li>
            <?php echo CHtml::dropDownList('alPrintCentreFilter', NULL, PolestarPrintCentre::getAllForLoginAsOptions(), array('empty' => 'All Print Centres')); ?>
        </li>
        <li>
            <?php echo CHtml::dropDownList('alUserFilter', NULL, PolestarPrintCentre::getUsersAsOptions(), array('empty' => 'All Users')); ?>
        </li>
        <li>
            <?php echo CHtml::dropDownList('alStatusFilter', NULL, PolestarStatus::getAllAsOptions(), array('empty' => 'All Statuses')); ?>
        </li>
        <li>
            <button id="alBtnApply">Apply</button>
        </li>
    </ul>
    </div>

    <div id="activitylog-jobs-container">
<?php if (empty($jobs)): ?>
<div class="warningBox">
    No jobs to list under the selected criteria.
</div>
<?php else: ?>
<table class="listing fluid">
    <tr>
        <th>Print Centre</th>
        <th>AktrionJobRef</th>
        <th>Col. Date</th>
        <th>Provider</th>
        <th>Supplier</th>
        <th>Job Status</th>
        <th>Date Edited</th>
        <th>Edited By</th>
        <td width="1"></td>
    </tr>
<?php foreach ($jobs as $job): ?>
    <tr class="<?php echo cycle("row1","row2") ?>">
        <td><?php echo @$job->PrintCentre->Name; ?></td>
        <td><?php echo $job->Ref; ?></td>
        <td><?php echo $job->formatDate('DeliveryDate','d/m/Y'); ?></td>
        <td><?php echo @$job->Provider->Name; ?></td>
        <td><?php echo @$job->Supplier->Name; ?></td>
        <td class="status-cell <?php echo @$job->Status->Code ?>">
            <?php echo @$job->Status->Name; ?>
        </td>
        <td><?php echo (empty($job->EditedDate)) ? $job->formatDate('CreatedDate','d/m/Y H:i') : $job->formatDate('EditedDate','d/m/Y H:i'); ?></td>
        <td><?php echo (empty($job->EditedBy)) ? @$job->CreatedByLogin->FriendlyName : @$job->EditedByLogin->FriendlyName; ?></td>
        <td>
            <a target="_blank" href="<?php echo $job->getPermalink() ?>">
                <img src="<?php echo $baseUrl; ?>/img/icons/magnifier.png" alt="view" class="action-icon"/>
            </a>
        </td>
    </tr>
<?php endforeach; ?>        
</table>
<?php endif; ?>
    </div>
<script>
function initMultiSelectSelection() {
    <?php $pcs = explode('|', $model->printCentres);
    if (!empty($pcs[0])): ?>
    $("#alPrintCentreFilter").multiselect("widget").find("input[value='']").click();        
    <?php endif;
    foreach($pcs as $pc): 
        if (!empty($pc)) :?>
    $("#alPrintCentreFilter").multiselect("widget").find("input[value='<?php echo $pc ?>']").click();
    <?php endif;
    endforeach; ?>
            
    <?php $users = explode('|', $model->users);
    if (!empty($users[0])): ?>
    $("#alUserFilter").multiselect("widget").find("input[value='']").click();        
    <?php endif;
    foreach($users as $usr): 
        if (!empty($usr)) :?>
    $("#alUserFilter").multiselect("widget").find("input[value='<?php echo $usr ?>']").click();
    <?php endif;
    endforeach; ?>
    
    <?php $stts = explode('|', $model->statuses);
    if (!empty($stts[0])): ?>
    $("#alStatusFilter").multiselect("widget").find("input[value='']").click();        
    <?php endif;
    foreach($stts as $stt): 
        if (!empty($stt)) :?>
    $("#alStatusFilter").multiselect("widget").find("input[value='<?php echo $stt ?>']").click();
    <?php endif;
    endforeach; ?>
}
</script>
    
<?php if (!$ajax): ?>    
</div>

<script>
var alToday = "<?php echo date('d/m/Y'); ?>";
$(initActivityLogUI);

var pcFilter;
var userFilter;
var sttFilter;
var refreshInterval;

function initActivityLogUI(){
    $("#alStatusFilter").multiselect({
        selectedList: 2, // 0-based index
        header: "Choose",
        minWidth: '150',
        click: gatherSttSel
     });
    
    $("#alPrintCentreFilter").multiselect({
        selectedList: 2, // 0-based index
        header: "Choose",
        minWidth: '150',
        click: gatherPcSel
     });
     
    $("#alUserFilter").multiselect({
        selectedList: 2, // 0-based index
        header: "Choose",
        minWidth: '150',
        click: gatherUserSel
     });
     
    $("#alHourFilter").change(function(){
        var val = $(this).val();
        if (val != '') {
            $("#alDateFrom").val('').hide();
            $("#alDateTo").val('').hide();
        }
        else {
            if ($("#alDateFrom").val() == '') {
                $("#alDateFrom").val(alToday).show();
                $("#alDateTo").val(alToday).show();
            }
            else {
                $("#alDateFrom").show();
                $("#alDateTo").show();
            }
        }
    });
    
    $("#alDateFrom").datepicker({
        dateFormat: 'dd/mm/yy',
        minDate: '-1m',
        maxDate: '0',
        onSelect: function(selectedDate) {
            $("#alDateTo").datepicker("option", 'minDate', selectedDate);
        }
    });
    
    $("#alDateTo").datepicker({
        dateFormat: 'dd/mm/yy',
        minDate: '-1m',
        maxDate: '0'
    });
    
    $("#alBtnApply").click(function(){
        $.post("<?php echo $this->createUrl('polestar/activitylog', array('ajax' => 1)) ?>",
            {
                'alForm[hourRange]' : $("#alHourFilter").val(),
                'alForm[printCentres]' : pcFilter,
                'alForm[users]' : userFilter,
                'alForm[statuses]' : sttFilter,
                'alForm[dateFrom]' : $("#alDateFrom").val(),
                'alForm[dateTo]' : $("#alDateTo").val()
            },
            function(data) {
                $("#activitylog-container").html(data);
                initActivityLogUI();
            },
            'html');
    });
    
    $("#alHourFilter").change();
    
    initMultiSelectSelection();
    gatherPcSel();
    gatherUserSel();
    gatherSttSel();
    
    clearInterval(refreshInterval);
    refreshInterval = setInterval(function() {
        $("#alBtnApply").click();
    }, 15000);
}

function gatherPcSel(){
    var pcSel = [];
    $("#alPrintCentreFilter").multiselect("widget").find("input:checked").each(function(){
        if($(this).val() != '')
            pcSel.push($(this).val());
    });
    pcFilter = pcSel.join('|');
}

function gatherUserSel(){
    var userSel = [];
    $("#alUserFilter").multiselect("widget").find("input:checked").each(function(){
        if($(this).val() != '')
            userSel.push($(this).val());
    });
    userFilter = userSel.join('|');
}

function gatherSttSel(){
    var sttSel = [];
    $("#alStatusFilter").multiselect("widget").find("input:checked").each(function(){
        if($(this).val() != '')
            sttSel.push($(this).val());
    });
    sttFilter = sttSel.join('|');
}
</script>
<?php endif;?>