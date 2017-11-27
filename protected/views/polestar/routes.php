<?php
    $this->breadcrumbs=array(
                array('label'=>'Home', 'url'=>array(Yii::app()->user->role->HomeUrl)),
                array('label'=>'Polestar', 'url' => '#'),
                array('label'=>'Route Management')
            );
    $baseUrl = Yii::app()->request->baseUrl;
?>
<style>
#mainWrap {
    width: 95% !important;
}
.confirmed td {
    background-color: lightgreen !important;
}
.booked td {
    background-color: rgb(231, 231, 231) !important
}
.cancelled td {
    /*background-color: gray !important;*/
    color: gray !important;
}
.lateadvice td {
    background-color: yellow !important;
}
.drag-load {
    cursor: move !important;
}

</style>

<h1>Route Management</h1>
<div class="infoBox">
Screen purpose: <br/>
- route uploading for date/print centre combinations - print centre would be readonly as per permissions/entry point <br/>
- route management (route/load deletion - manual entry)
</div>

<div class="standardForm">
    <table width="50%">
    <tr>
        <td>
            <div class="field">
                <?php echo CHtml::label('Delivery Date', FALSE) ?>
                <?php echo CHtml::textField('deliveryDate', date('d/m/Y'), array('size' => '10', 'readonly' => 'readonly', 'class' => 'dpicker')); ?>
            </div>
        </td>
        <td>
            <div class="field">
                <?php echo CHtml::label('Print Centre', FALSE) ?>
                <?php echo CHtml::dropDownList('printCentre', '1', array(
                    '1' => 'Bicester',
                    '2' => 'Leeds',
                    '3' => 'Polestar Stones',
                    '4' => 'Sheffield',
                    '5' => 'Wakefield',
                    '6' => 'Web Offset Sheffield'
                ), array('empty' => 'select one --')); ?>
            </div>
        </td>
        <td style="vertical-align: bottom;">
            <button id="btnLoadRoutes">Load Routes</button>
        </td>
    </tr>
    </table>
</div>
<br/>

<hr/>

<div class="infoBox">
    Scenario 1 : no routes yet entered for selected date/print centre
</div>

<div class="titleWrap">
    <h2>Routes for <?php echo date('d/m/Y') ?> - Bicester</h2>

    <ul>
        <li class="seperator">
            <img src="<?php echo $baseUrl; ?>/img/icons/add.png" alt="add" />
            <a href="#" class="new-route">
                Add route
            </a>
        </li>
        <li class="seperator">
            <img src="<?php echo $baseUrl; ?>/img/icons/table_row_insert.png" alt="add" />
            <a href="#" class="route-upload">
                Upload Routes
            </a>
        </li>
    </ul>
</div>

<div class="warningBox">
    There are no routes entered for selected date and print centre combination yet. Route can be initialized/built using the add route / upload options above.
</div>

<br/>
<hr/>

<div class="infoBox">
    Scenario 2 : routes already entered for selected date/print centre
</div>

<div class="titleWrap">
    <h2>Routes for <?php echo date('d/m/Y') ?> - Bicester</h2>

    <ul>
        <li class="seperator">
            <img src="<?php echo $baseUrl; ?>/img/icons/add.png" alt="add" />
            <a href="#" class="new-route">
                Add route
            </a>
        </li>
        <li class="seperator">
            <img src="<?php echo $baseUrl; ?>/img/icons/table_row_insert.png" alt="add" />
            <a href="#" class="route-upload">
                Upload Routes
            </a>
        </li>
    </ul>
</div>

<div class="warningBox">DEV NOTE: shall we display publication + scheduled coll/del times on this screen?</div>

<?php
$jobs = array(
    'BIC/'.date('d.m.y').'/001' => array(
        array(
            'type' => '-',
            'loadId' => '9821/1',
            'pallets' => '40',
            'vehicle' => 'DD',
            'collPostcode' => 'S9 1RF',
            'delPostcode' => 'OX26 4QZ',
            'area' => 'Bicester, Oxon',
            'company' => 'Polestar Bicester',
            'bookingRef' => '',
            'status' => 'CONFIRMED'
        )
    ),
    'BIC/'.date('d.m.y').'/002' => array(
        array(
            'type' => 'PRIORITY',
            'loadId' => '9821/1',
            'pallets' => '19',
            'vehicle' => 'SD',
            'collPostcode' => 'S9 1RF',
            'delPostcode' => 'OX26 4QZ',
            'area' => 'Bicester, Oxon',
            'company' => 'Polestar Bicester',
            'bookingRef' => '',
            'status' => 'CANCELLED'
        )
    ),
    'BIC/'.date('d.m.y').'/003' => array(
        array(
            'type' => '-',
            'loadId' => '9870/1',
            'pallets' => '1',
            'vehicle' => 'SD',
            'collPostcode' => 'OX26 4QZ',
            'delPostcode' => 'S9 1RF',
            'area' => 'Sheffield, South Yorkshire',
            'company' => 'Polestar Sheffield Ltd',
            'bookingRef' => '34076',
            'status' => 'BOOKED'
        ),
        array(
            'type' => '-',
            'loadId' => '9870/2',
            'pallets' => '8',
            'vehicle' => 'SD',
            'collPostcode' => 'OX26 4QZ',
            'delPostcode' => 'S9 1RF',
            'area' => 'Sheffield, South Yorkshire',
            'company' => 'Polestar Sheffield Ltd',
            'bookingRef' => '',
            'status' => 'BOOKED'
        ),
        array(
            'type' => '-',
            'loadId' => '9870/3',
            'pallets' => '1',
            'vehicle' => 'SD',
            'collPostcode' => 'OX26 4QZ',
            'delPostcode' => 'S9 1RF',
            'area' => 'TINSLEY, SHEFFIELD',
            'company' => 'PUPL (SHEFFIELD WEB)',
            'bookingRef' => '',
            'status' => 'BOOKED'
        ),
    ),
    'BIC/'.date('d.m.y').'/004' => array(
        array(
            'type' => '-',
            'loadId' => '98075/1',
            'pallets' => '1',
            'vehicle' => 'SD',
            'collPostcode' => 'OX26 4QZ',
            'delPostcode' => 'SP5 3HU',
            'area' => 'Salisbury, Wilts',
            'company' => 'PRIORITY NEWSTRADE',
            'bookingRef' => '',
            'status' => 'LATE ADVICE'
        )
    ),
);

foreach ($jobs as $jobId => $loads):
?>
<hr/>
<h3>Job Ref No: <?php echo $jobId ?></h3>

<table class="listing fluid">
<tr>
    <td width="1%"></td>
    <th width="5%">Job Type</th>
    <th width="5%">Job/Load</th>
    <th width="5%">Pallets</th>
    <th width="5%">Vehicle Req.</th>
    <th width="5%">Coll. Postcode</th>
    <th width="5%">Postcode</th>
    <th width="25%">Area</th>
    <th width="20%">Company</th>
    <th width="10%">Booking Ref</th>
    <th width="10%">Job Status</th>
</tr>
<?php foreach ($loads as $load):
        $class = strtolower(preg_replace("/[^a-z]/i", '', $load['status']));
    ?>
<tr class="<?php echo $class ?>">
    <td style="white-space: nowrap">
        <a href="#" class="edit-load"><img src="<?php echo $baseUrl; ?>/img/icons/page_edit.png" title="[edit load details]" /></a>
        <a href="#" class="drop-load"><img src="<?php echo $baseUrl; ?>/img/icons/delete.png" title="[drop load]" /></a>
        <a href="#" class="drag-load"><img src="<?php echo $baseUrl; ?>/img/icons/arrow_switch.png" title="[to be used for dragging loads within route]"></a>
    </td>
    <td><?php echo $load['type']; ?></td>
    <td><?php echo $load['loadId']; ?></td>
    <td><span title="[pallet breakdown on tooltip]"><?php echo $load['pallets']; ?>*</span></td>
    <td><?php echo $load['vehicle']; ?></td>
    <td><span title="[full collection address on tooltip]"><?php echo $load['collPostcode']; ?>*</span></td>
    <td><span title="[full delivery address on tooltip]"><?php echo $load['delPostcode']; ?>*</span></td>
    <td><?php echo $load['area']; ?></td>
    <td><?php echo $load['company']; ?></td>
    <td><?php echo $load['bookingRef']; ?></td>
    <td>
        <?php echo $load['status']; ?>
    </td>
</tr>
<?php endforeach; ?>
<tr>
    <td colspan="5">
        <img src="<?php echo $baseUrl; ?>/img/icons/add.png" alt="add" />
        <a href="#" class="add-load">
            <strong>Add load</strong>
        </a>
    </td>
</tr>
</table>

<?php endforeach; ?>

<script>
$(function() {
    $(".new-route").click(function(){
        showLoadPopup();
        return false;
    });
    $(".drop-load").click(function(){
        if (confirm("Are you sure you wish to cancel this load?"))
            alert("functionality not implemented in mockup");
        return false;
    });
    $(".drag-load").click(function(){
        alert("functionality not implemented in mockup");
        return false;
    });
    $(".edit-load").click(function(){
        showLoadPopup(999);
        return false;
    });
    $(".add-load").click(function(){
        showLoadPopup(-1);
        return false;
    });
    $(".route-upload").click(function(){
        var url = "<?php echo $this->createUrl('polestar/upload', array('ui'=>'popUp'));?>";
        $.colorbox({href: url, width:"650px", height:"650px", iframe:true});
        return false;
    });
});

function showLoadPopup(existing) {
    var url = "<?php echo $this->createUrl('polestar/load', array('ui'=>'popUp'));?>";
    if (existing != undefined) {
        url += (url.indexOf("?") == -1) ? "?" : "&";
        url += "id=" + existing;
    }
    $.colorbox({href: url, width:"1000px", height:"650px", iframe:true});
    return false;
}
</script>