<?php
    $this->breadcrumbs=array(
                array('label'=>'Home', 'url'=>array(Yii::app()->user->role->HomeUrl)),
                array('label'=>'Finance', 'url' => '#'),
                array('label'=>'Contractor Earnings')
            );
    
    $baseUrl = Yii::app()->request->baseUrl;
    $readOnlyAttrs = array('class' => 'readOnlyField', 'readOnly' => 'readOnly');
    $data = $model->data;
    $contractorInfo = $model->getContractorInfo();
    $contractorName = trim($contractorInfo->FirstName.' '.$contractorInfo->LastName);
    $emailAvailable = (!empty($contractorInfo->Email));
    
    $cs = Yii::app()->getClientScript();
    $cs->registerScriptFile($baseUrl.'/js/sorttable.js');
    
?>

<style>
    .btn {
        height: 50px;
        padding: 10px;
    }
    #mainWrap {
        width: 95% !important;
    }
    table.sortable th:not(.sorttable_sorted):not(.sorttable_sorted_reverse):not(.sorttable_nosort):after { 
        content: " \25B4\25BE" 
    }
    table.sortable tbody tr:nth-child(2n) td {
        background: #dfdfdf;
    }
    table.sortable tbody tr:nth-child(2n+1) td {
        background: #fff;
    }    
</style>


<h1>
    <?php echo $contractorName ?> - Week Starting <?php echo $model->weekStarting; ?>
</h1>

<fieldset>
    <br/>
    
<table id="tblListing" class="listing fluid sortable" cellpadding="0" cellspacing="0">
    <thead>
    <tr>
        <th width="10%">Date</th>
        <th width="40%">Route</th>
        <th width="20%">Contractor</th>
        <th class="sorttable_nosort" width="10%">Fee</th>
        <th class="sorttable_nosort" width="10%">Expenses / Deductions</th>
        <th class="sorttable_nosort" width="10%">Total</th>
    </tr>
    </thead>
    <?php
    $i = 0;
    $runningTotal = 0;
    $anyNotConfirmed = FALSE;
    foreach ($data as $row):

        $runningTotal += $row['total'];
        $isConfirmed = ($row['confirmed'] == 1);
        if (!$isConfirmed)
            $anyNotConfirmed = TRUE;        
        
        $replText = "";
        if (!empty($row['replaces']))
            $replText = ' (replacing '.$row['replaces'].') ';
        
        if ($row['fee']!=0 || $row['miscfee']!=0)
        {
        ?>
    <tr class="row<?php echo ($i++%2)+1; ?>">
        <td style="white-space: nowrap;"><?php echo $row['date'] ?></td>
        <td><?php echo $row['category'].' / '.$row['route'].$replText.((!$isConfirmed) ? " **" : ""); ?></td>
        <td><?php echo $row['contractor']; ?></td>
        <td style="text-align: right;"><?php echo $row['fee']; ?></td>
        <td style="text-align: right;"><?php echo $row['miscfee']; ?></td>
        <td style="text-align: right;"><?php echo sprintf("%01.2f", $row['total']); ?></td>
    </tr>
    <?php 
        }
    endforeach; 
    ?>
    
<!--
    <tr>
        <td colspan="4">&nbsp;</td>
        <th>Total</th>
        <td style="text-align: right;"><strong><?php //echo sprintf("%01.2f", $runningTotal); ?></strong></td>
    </tr>

    -->
</table>
    
<table id="tblListing" class="listing fluid sortable" cellpadding="0" cellspacing="0">

    <tr>
        <td></td>
        <td width="60%"></td>
        <td></td>
        <td width="15%"></td>
        <th>Total</th>
        <td style="text-align: right;"><strong><?php echo sprintf("%01.2f", $runningTotal); ?></strong></td>
    </tr>
    
</table>    
    
    
    
<?php if ($anyNotConfirmed): ?>
    <em><strong>**</strong> <font style="color: red;font-weight: bold">denotes adjustment not yet acknowledged.</font></em>

<?php else: ?>
<form id="form-action" method="post" action="<?php echo $this->createUrl('finance/earnings', array('id'=> $model->contractorId, 'date' => $model->weekStarting));?>">

    <input type="hidden" id="axn" name="axn" value="export" />
    
    <div class="titleWrap">
        <ul>
            <?php if ($emailAvailable): ?>
            <li>
                <button class="btn" id="btnEmail">
                    <img src="<?php echo $baseUrl; ?>/img/icons/email_go.png" />
                    Send e-mail
                </button>
            </li>
            <?php endif; ?>
            <li>
                <button class="btn" id="btnExport">
                    <img src="<?php echo $baseUrl; ?>/img/icons/bullet_disk.png" />
                    Export
                </button>
            </li>
            <li>
                <button class="btn" id="btnExportPdf">
                    <img src="<?php echo $baseUrl; ?>/img/icons/page_white_acrobat.png" />
                    Export to PDF
                </button>
            </li>
        </ul>
    </div>

    <div class="errorBox" id="divEmailFail" style="display:none;">An error occurred while sending the email.</div>
    <div class="successBox" id="divEmailSuccess" style="display:none;">Email sent successfully.</div>
    
</form>
<?php endif; ?>    
</fieldset>

<script>
$(function(){
    $("#btnExport").click(function(){
        $("#axn").val('export');
    });
    
    $("#btnEmail").click(function(){
        $("#axn").val('email');
        $("#divEmailFail").hide();
        $("#divEmailSuccess").hide();
        
        $.post($("#form-action").attr('action'),
                $("#form-action").serialize(),
                function(data){
                    var $divToShow = $("#divEmailFail");
                    if (data.result == 'success')
                        $divToShow = $("#divEmailSuccess")
                    $divToShow.show().delay(5000).fadeOut();
                    alert($divToShow.text());
                    //buildRouteListing(data);
                },
                'json');
        
        return false;
    });
    
    $("#btnExportPdf").click(function(){
        alert("Not yet implemented - awaiting template.");
        return false;
    });
});
</script>