<?php
    $this->breadcrumbs=array(
                array('label'=>'Home', 'url'=>array(Yii::app()->user->role->HomeUrl)),
                array('label'=>'Polestar', 'url' => '#'),
                array('label'=>'Route Control')
            );
    $baseUrl = Yii::app()->request->baseUrl;
    
    $readOnlyAttrs = array('class' => 'readOnlyField', 'readOnly' => 'readOnly');
    $smallReadOnlyAttrsBase = array_merge(array('size' => 5), $readOnlyAttrs);
    $smallReadOnlyAttrs = $smallReadOnlyAttrsBase;
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
</style>

<h1>Route Control</h1>
<div class="infoBox">
Screen purpose: <br/>
- view route/load statuses<br/>
- send advice sheets and confirm routes <br/>
- add supplier information (driver/reg no/etc) <br />

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

<div class="infoBox">
    Scenario 1 : no routes yet entered for selected date/print centre
</div>

<div class="titleWrap">
    <h2>Routes for <?php echo date('d/m/Y') ?> - Bicester</h2>
</div>

<div class="warningBox">
    There are no routes entered for selected date and print centre combination yet. Routes must be initialized/built using the Route Management screens.
</div>

<br/>
<hr/>

<div class="infoBox">
    Scenario 2 : routes already entered for selected date/print centre
</div>

<div class="titleWrap">
    <h2>Routes for <?php echo date('d/m/Y') ?> - Bicester</h2>
</div>

<div class="warningBox">DEV NOTE: shall we display publication on this screen ? </div>
<div class="warningBox">DEV NOTE 2: where would comments/special instructions apply ? </div>
<div class="warningBox">DEV NOTE 3: is route mileage entered ? where ? </div>

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
        <td colspan="5"></td>
        <th colspan="4">Collection</th>
        <th colspan="4">Delivery</th>
        <td></td>
        <td colspan="2">
            <img src="<?php echo $baseUrl; ?>/img/icons/information.png" alt="add" />
            <a href="#" class="supplier-info">
                <strong>Supplier information</strong>
            </a>
        </td>
        <td>
            <img src="<?php echo $baseUrl; ?>/img/icons/note_go.png" alt="add" />
            <a href="#" class="send-advise">
                <strong>Send advice sheet</strong>
            </a>
        </td>
    </tr>
<tr>
    <td width="1%"></td>
    <th width="5%">Job Type</th>
    <th width="5%">Job/Load</th>
    <th width="5%">Pallets</th>
    <th width="5%">Vehicle</th>
    <th width="5%">Postcode</th>
    <th width="1%">Sched.</th>
    <th width="1%">Arrival</th>
    <th width="1%">Departure</th>
    <th width="5%">Postcode</th>
    <th width="1%">Sched.</th>
    <th width="1%">Arrival</th>
    <th width="1%">Departure</th>
    <th width="15%">Area</th>
    <th width="10%">Company</th>
    <th width="5%">Booking Ref</th>
    <th width="10%">Job Status</th>
</tr>
<?php foreach ($loads as $load): 
        $class = strtolower(preg_replace("/[^a-z]/i", '', $load['status']));
    ?>
<tr class="<?php echo $class ?>">
    <td style="white-space: nowrap">
        <a href="#" class="edit-load"><img src="<?php echo $baseUrl; ?>/img/icons/page_edit.png" title="[edit load details]" /></a>
        <a href="#" class="drop-load"><img src="<?php echo $baseUrl; ?>/img/icons/delete.png" title="[drop load]" /></a>
    </td>
    <td><?php echo $load['type']; ?></td>
    <td><?php echo $load['loadId']; ?></td>
    <td><span title="[pallet breakdown on tooltip]"><?php echo $load['pallets']; ?>*</span></td>
    <td><?php echo $load['vehicle']; ?></td>
    <td><span title="[full collection address on tooltip]"><?php echo $load['collPostcode']; ?>*</span></td>
    <td>
        <?php echo CHtml::textField("dt", '-', $smallReadOnlyAttrs); ?>
    </td>
    <td>
        <?php echo CHtml::textField("dt", '-', $smallReadOnlyAttrs); ?>
    </td>
    <td>
        <?php echo CHtml::textField("dt", '-', $smallReadOnlyAttrs); ?>
    </td>
    <td><span title="[full delivery address on tooltip]"><?php echo $load['delPostcode']; ?>*</span></td>
    <td>
        <?php echo CHtml::textField("dt", '-', $smallReadOnlyAttrs); ?>
    </td>
    <td>
        <?php echo CHtml::textField("dt", '-', $smallReadOnlyAttrs); ?>
    </td>
    <td>
        <?php echo CHtml::textField("dt", '-', $smallReadOnlyAttrs); ?>
    </td>
    <td><?php echo $load['area']; ?></td>
    <td><?php echo $load['company']; ?></td>
    <td><?php echo $load['bookingRef']; ?></td>
    <td>
        <?php echo $load['status']; ?>
    </td>
</tr>
<?php endforeach; ?>
</table>

<?php endforeach; ?>
