<?php
    $this->breadcrumbs=array(
                array('label'=>'Home', 'url'=>array(Yii::app()->user->role->HomeUrl)),
                array('label'=>'Control Report'),
            );
?>

<div class="titleWrap">
    <h1>Control Report > <?php echo ((!$model->showArchived) ? 'All' : 'Archived'); ?></h1>
    <ul>
        <li>
            <a href="#" id="lnkReferences" title="Reference">colour reference</a>
        </li>
        <li class="seperator">
            View
            <a
                class="<?php echo ((!$model->showArchived) ? 'highlighted' : ''); ?>"
                href="<?php echo $this->createUrl('admin/reportingcontrol');?>">all</a> |
            <a class="<?php echo (($model->showArchived) ? 'highlighted' : ''); ?>"
                href="<?php echo $this->createUrl('admin/reportingcontrol', array('archived' => '1'));?>">archived</a>
        </li>
    </ul>
</div>

<div class="standardForm">
<?php $form=$this->beginWidget('CActiveForm', array(
        'id'=>'pallet-report-form',
        'errorMessageCssClass'=>'formError',
)); ?>

    <?php
    echo $form->errorSummary($model, "", "", array('class'=>'errorBox'));
    ?>

    <table class="fluid">
        <tr>
            <td width="30%">
                <div>
                    <?php echo $form->labelEx($model,'printCentre'); ?>
                    <?php echo $form->dropDownList($model, 'printCentre', $model->getOptionsPrintCentre(), array('empty'=>'select one ->')); ?>
                </div>
            </td>
            <td width="30%">
                <div>
                    <label>Week of</label>
                    <input type="text" id="week" name="week" value="<?php echo $model->from." - ".$model->to; ?>" class="dpicker" size="25" >
                    <?php
                    echo $form->hiddenField($model,'from');
                    echo $form->hiddenField($model,'to');
                    ?>
                    <?php echo CHtml::submitButton('Go', array('class'=>'formButton', 'id'=>'btnSubmit')); ?>
                </div>
            </td>            
        </tr>
    </table>
<?php
$this->endWidget(); ?>
</div>
<br/>

<table class="listing fluid">
        <tr>
            <th width="1">Print Site</th>
            <th width="5">Day</th>
            <th>Title / Route</th>
            <th width="5">Off Press Time</th>
            <th wdith="5">Delivery Date</th>
            <th width="5">Weight tonnes</th>
            <th width="5">Delivery notes printed</th>
            <th width="5">Pallets out</th>
            <th width="5">Pallets in</th>
            <th width="5">Delivery times entered</th>
            <th width="75">Supplier</th>
            <td></td>
        </tr>
        <?php
        $details = $model->details;
        $detailscount = count($details);

				if ($detailscount == 0):
				?>
				<tr>
					<td colspan="11">
						<div class="infoBox">No results match your defined criteria.</div>
					</td>
				</tr>
				<?php
				endif;

        for ($i = 0; $i < $detailscount; $i++):
            $d = $details[$i];

            $cssclass = 'greenbg';
            // date parse
            $anp = explode('-', $d->NextPublication);
            $nextPublication = mktime(0, 0, 0, $anp[1], $anp[2], $anp[0]);
            $now = time();
            $dateDiff = $nextPublication - $now;
            $daysDiff = floor($dateDiff/(60*60*24));
            if(!isset($d->RouteInstanceId))
            {
                if ($daysDiff < 1)
                    $cssclass = 'redbg';
                else
                    $cssclass = 'amberbg';
            }
            if (!isset($d->RouteName))
                $cssclass = 'dimmedbg';
        ?>

        <tr class="<?php echo $cssclass; ?>">
            <td><?php echo $d->PrintCentreName ?></td>
            <td><?php echo Yii::app()->locale->getWeekDayName($d->PrintDay); ?></td>
            <td>
                <?php echo "<strong>$d->TitleName</strong><br/>$d->RouteName";?>
            </td>
            <td><?php echo $d->OffPressTime ?></td>
        <?php
        if(isset($d->RouteInstanceId)): ?>
            <td><?php echo $d->DeliveryDate ?></td>
            <td><?php 
                $titleweight = round(($d->TotalWeight)/(1000*1000), 3);
                $routeinstanceweight = round(($d->TotalRouteInstanceWeight)/(1000), 2);
                if ($routeinstanceweight > $d->VehicleCapacity)
                {
                    $msg = "Total Order weight ($routeinstanceweight Kgs) over vehicle capacity ({$d->VehicleCapacity} Kgs)";
                    echo '<div class="errorBox" title="'. $msg .'">'.$titleweight.'</div>';
                }
                else
                    echo $titleweight;  ?></td>
            <td><?php echo ($d->IsPrinted == '1') ? "YES" : "NO"; ?></td>

            <?php
            $add = explode('/', $d->DeliveryDate);
            $delDate = mktime(0, 0, 0, $add[1], $add[0], $add[2]);
            $now = time();
            $dateDiffDel = $delDate - $now;
            $daysDiffDel = floor($dateDiffDel/(60*60*24));
            if (($d->DeliveryTimeEntered != '1') && ($daysDiffDel < -1)) : ?>
            <td colspan="3">
                <div class="errorBox" title="Delivery Date is more than 24 hours in the past.">Not Entered</div>
            </td>
            <?php else : ?>
            <td><?php echo $d->TotalPalletsDelivered ?></td>
            <td><?php echo $d->TotalPalletsCollected ?></td>
            <td><?php echo ($d->DeliveryTimeEntered == '1') ? "YES" : "NO" ?></td>
            <?php endif; ?>
        <?php
        else: ?>
            <td colspan="6">
            <?php if (!isset($d->RouteId)): ?>
                <div class="warningBox">
                    No Route Linked
                    <a href="<?php echo $this->createUrl('admin/route'); ?>">add now &raquo;</a>
                </div>
            <?php else:
                $class = 'warningBox';
                if($daysDiff < 1)
                    $class = 'errorBox';
                ?>
                <div class="<?php echo $class ?>">
                    Date next publication: <?php echo date('d/m/Y', $nextPublication); ?>
                    <a href="<?php echo $this->createUrl('admin/orders', array('rid'=>$d->RouteId)) ?>">enter order now &raquo;</a>
                </div>
            <?php endif; ?>
            </td>
        <?php
        endif; ?>
            <td><?php echo $d->SupplierName ?></td>

        <?php
        if(isset($d->RouteInstanceId) && ($d->Status != RouteInstance::STATUS_ACTIVE)): ?>
            <td>
                <button title="View delivery summary" class="delivery-button"
                    rid="<?php echo $d->RouteInstanceId ?>"
                    href="<?php echo $this->createUrl('supplier/routedeliveryinfo', array('id'=>$d->RouteInstanceId));?>">
                <img src="img/icons/table.png">
                </button>
                <button title="Activate Route" class="archive-button"
                    prompt="The route instance will be re-activated for the supplier to edit. Are you sure you wish to proceed?"
                    href="<?php echo $this->createUrl('supplier/routearchive', array('id'=>$d->RouteInstanceId, 'status'=> RouteInstance::STATUS_ACTIVE));?>"
                    rid="<?php echo $d->RouteInstanceId ?>">
                <img src="img/icons/door_out.png">
                </button>
            </td>
            <?php endif; ?>
        </tr>
        <?php endfor; ?>
    </table>

<div style="display:none;">
<div id="divReferences">
    <div class="dimmedbg padded-5 light-border">No Route for this title</div>
    <br />
    <div class="amberbg padded-5">Next order required.</div>
    <br/>
    <div class="redbg padded-5">Next order required urgently.</div>
    <br/>
    <div class="greenbg padded-5">No issues found for this title.</div>
</div>
</div>
<script type="text/javascript">
    $(document).ready(function() {

        $(".cboxlink").colorbox({
            width:"900px",
            height:"600px",
            iframe:true,
            href: function(){
                return $(this).attr('href');
            }
        });

        $("#lnkReferences").colorbox({inline:true, href:"#divReferences"}
        );

        $(".delivery-button").colorbox({
            width:"900px",
            height:"600px",
            iframe:true,
            onLoad: addPrintButton,
            href: function(){
                return $(this).attr('href');
            }
        });

        $(".archive-button").click(function(){
            var bContinue = true;
            if ($(this).attr("prompt") != "")
                bContinue = confirm($(this).attr("prompt"));

            if (bContinue)
                $.getJSON($(this).attr("href"), function(data){
                    if (data.result == "OK")
                        location.reload();
                    else
                        alert(data.result);
                });
        });

        $.datepicker.setDefaults( $.datepicker.regional[ "en-GB" ] );
        $(".dpicker").datepicker({
            dateFormat: 'dd/mm/yy',
            maxDate: 0,
            onSelect: function(selectedDate) {
                var selDate = $(this).datepicker( "getDate" );
                // week: monday to sunday
                var diff = (selDate.getDay() == 0) ? 6 : selDate.getDay() - 1; // 1: monday
                var mon = new Date(selDate - (diff*24*60*60*1000));
                var fmon = $.datepicker.formatDate($(this).datepicker("option", "dateFormat"), mon);
                diff = 7 - selDate.getDay();
                var sun = new Date(selDate.getTime() + (diff*24*60*60*1000));
                var fsun = $.datepicker.formatDate($(this).datepicker("option", "dateFormat"), sun);
                $("#week").val(fmon + " - " + fsun);
                $("#AdminReportingControl_from").val(fmon);
                $("#AdminReportingControl_to").val(fsun);
            }
        });
    });

    function addPrintButton()
    {
        if ($("#cboxCurrent a").length == 0)
        {
            $("<a />").attr('href', '#').text('print').click(function(){
                printIframe("cboxIframe");
            }).appendTo("#cboxCurrent");
        }
        $("#cboxCurrent").attr('style', 'left:0;width:100%;').show();
    }

    function printIframe(id)
    {
        var iframe = document.frames ? document.frames[id] : document.getElementById(id);
        var ifWin = iframe.contentWindow || iframe;
        iframe.focus();
        ifWin.print();
        return false;
    }

</script>