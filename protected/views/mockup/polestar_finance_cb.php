<?php
    $this->breadcrumbs=array(
                array('label'=>'Home', 'url'=>'#'),
                array('label'=>'Polestar', 'url' => '#'),
                array('label'=>'Finance')
            );
    $baseUrl = Yii::app()->request->baseUrl;
    $readOnlyAttrs = array('class' => 'readOnlyField info-entry', 'readOnly' => 'readOnly');
    
?>
<style>
select {
    height: 28px;
}
hr {
    color: orange;
    background-color: orange;
    height: 3px;
    border-style: none;
}
table.listing th {
    white-space: nowrap !important;
}
.nowrap {
    white-space: nowrap;
}

.charge input {
    background-color: #DFF2BF !important;
}

.cost input {
    background-color: #FFBABA !important;
}

.total input {
    font-weight: bolder !important;
    font-size: 1.5em;
    vertical-align: middle;
}
</style>
<h1>Polestar Finance Screen</h1>

<div class="standardForm">
    <table width="50%">
    <tr>
    <td>
        <div class="field">
            <?php echo CHtml::label('Week Ending', FALSE); ?>
            <?php 
            $dtdefault = strtotime('last sunday');
            echo CHtml::textField('wending', date('d/m/Y', $dtdefault), array('size' => '10', 'readonly' => 'readonly', 'class' => 'dpicker')); ?>
        </div>
    </td>
    <td>
        <div class="field">
            <?php echo CHtml::label('Print Centre', FALSE); ?>
            <?php echo CHtml::dropDownList('pc', '', PolestarPrintCentre::getAllAsOptions(), array('empty' => '-- show all --')); ?>
        </div>
    </td>
    <td style="vertical-align: bottom;">
        <button>Load Jobs</button>
    </td>
    </tr>
    </table>
</div>


<h2>Week Ending [Date] - [Print Centre]</h2>

<?php 
$jobs = array(
    array(
        'Id' => 'WKF/15.04.15/018',
        'DeliveryDate' => '15/04/2015',
        'Vehicle' => 'Van',
        'Supplier' => 'Green Group Logistics',
        'Mileage' => '245',
        'WaitingTime' => '00:30',
        'WaitingTimeCost' => '0.24',
        'LateAdviseTime' => '-',
        'LateAdviseCost' => '-',
        'SDAdviseTime' => '02:34',
        'SDAdviseCost' => '2.34',
        'CancelledTime' => '-',
        'CancelledCost' => '-',
        'TotalCost' => '2.58',
        'WaitingTimeCharge' => '66.00',
        'LateAdviseCharge' => '-',
        'SDAdviseCharge' => '109.25',
        'CancelledCharge' => '-',
        'TotalCharge' => '836.50',
        'MileageBandCharge' => '437.00',
        'AgreedPrice' => '25.00',
        'Surcharge' => '109.25',
        'CollectionsCharge' => '45.00',
        'DropOffsCharge' => '45.00',
        'Loads' => array(
            array(
                'Ref' => '0018/1',
                'ColPostcode' => 'AA0 0AA',
                'DelPostcode' => 'CC0 0CC',
                'DelAddress' => 'Sample Delivery Address, Sample Town',
                'Mileage' => '210',
                'WaitingTime' => '00:15',
                'WaitingTimeCost' => '0.12',
                'WaitingTimeCharge' => '33.00',
                'Note' => 'eg DRIVER ARRIVED LATE',
            ),
            array(
                'Ref' => '0018/2',
                'ColPostcode' => 'BB0 0BB',
                'DelPostcode' => 'DD0 0DD',
                'DelAddress' => 'Sample Delivery Address, Sample Town',
                'Mileage' => '25',
                'WaitingTime' => '00:00',
                'WaitingTimeCost' => '0.00',
                'WaitingTimeCharge' => '0.00',
                'Note' => '',
            )
        ),
        'ColPoints' => array(
            array(
                'Postcode' => 'AA0 0AA',
                'Address' => 'Sample Delivery Address, Sample Town',
                'Mileage' => '0',
                'WaitingTime' => '00:00',
                'WaitingTimeCost' => '0.00',
                'WaitingTimeCharge' => '0.00',
                'Note' => ''
            ),
            array(
                'Postcode' => 'BB0 0BB',
                'Address' => 'Sample Delivery Address, Sample Town',
                'Mileage' => '10',
                'WaitingTime' => '00:15',
                'WaitingTimeCost' => '0.12',
                'WaitingTimeCharge' => '33.00',
                'Note' => 'eg DRIVER ARRIVED LATE'
            ),
        )
    )
)
?>

<?php foreach ($jobs as $job): 
    $jobId = $job['Id'];
    ?>
<hr />
<table class="listing fluid job-details">
    <tr>
        <td colspan="3">
            <span style="font-size: 1.5em; font-weight: bold;">JobRef: <?php echo $jobId ?></span>
        </td>
        <th colspan="2">
            Supplier Invoice
        </th>
        <td colspan="2"></td>
        <th colspan="2">
            Waiting Time
        </th>
        <th colspan="2">
            Late Advise
        </th>
        <th colspan="2">
            Same Day Advise
        </th>
        <th colspan="2">
            Cancelled
        </th>
        <th rowspan="2">
            Total<br/>Job Cost
        </th>
        <th colspan="5">
            Charges
        </th>
    </tr>
    <tr>
        <td></td>
        <th width='1'>Col. Date</th>
        <th width='250'>Supplier</th>
        <th width='1'>Number</th>
        <th width='1'>Date Received</th>
        <th>Vehicle</th>
        <th>Mileage</th>
        <th>Time</th>
        <th>Cost</th>
        <th>Time</th>
        <th>Cost</th>
        <th>Time</th>
        <th>Cost</th>
        <th>Time</th>
        <th>Cost</th>
        <th>Wait Time</th>
        <th>Late Adv.</th>
        <th>Same Day</th>
        <th>Cancelled</th>
        <th>Total</th>
        <th>Comments</th>
    </tr>
    <tr>
        <td width='1'>
            <a href="javascript:void(0)">
                <img src="<?php echo $baseUrl; ?>/img/icons/magnifier.png" title="[see job details]" />
            </a>
        </td>
        <td>
            <?php echo $job['DeliveryDate']; ?>
        </td>
        <td class='nowrap center'>
            <?php echo $job['Supplier'] ?>
        </td>
        <td>
            <?php echo CHtml::textField("dt", '', array_merge(array('size' => 10), $readOnlyAttrs)); ?>
        </td>
        <td>
            <?php echo CHtml::textField("dt", '', array_merge(array('size' => 10), $readOnlyAttrs)); ?>
        </td>
        <td class="center">
            <?php echo $job['Vehicle'] ?>
        </td>
        <td class="center">
            <?php echo $job['Mileage'] ?>
        </td>
        <td class="center">
            <?php echo CHtml::textField("dt", $job['WaitingTime'], array_merge(array('size' => 6), $readOnlyAttrs)); ?>
        </td>
        <td class="center cost">
            <?php echo CHtml::textField("dt", $job['WaitingTimeCost'], array_merge(array('size' => 6), $readOnlyAttrs)); ?>
        </td>
        <td class="center">
            <?php echo CHtml::textField("dt", $job['LateAdviseTime'], array_merge(array('size' => 6), $readOnlyAttrs)); ?>
        </td>
        <td class="center cost">
            <?php echo CHtml::textField("dt", $job['LateAdviseCost'], array_merge(array('size' => 6), $readOnlyAttrs)); ?>
        </td>
        <td class="center">
            <?php echo CHtml::textField("dt", $job['SDAdviseTime'], array_merge(array('size' => 6), $readOnlyAttrs)); ?>
        </td>
        <td class="center cost">
            <?php echo CHtml::textField("dt", $job['SDAdviseCost'], array_merge(array('size' => 6), $readOnlyAttrs)); ?>
        </td>
        <td class="center">
            <?php echo CHtml::textField("dt", $job['CancelledTime'], array_merge(array('size' => 6), $readOnlyAttrs)); ?>
        </td>
        <td class="center cost">
            <?php echo CHtml::textField("dt", $job['CancelledCost'], array_merge(array('size' => 6), $readOnlyAttrs)); ?>
        </td>
        <td class="center cost total">
            <?php echo CHtml::textField("dt", $job['TotalCost'], array_merge(array('size' => 6), $readOnlyAttrs)); ?>
        </td>
        <td class="center charge">
            <?php echo CHtml::textField("dt", $job['WaitingTimeCharge'], array_merge(array('size' => 6), $readOnlyAttrs)); ?>
        </td>
        <td class="center charge">
            <?php echo CHtml::textField("dt", $job['LateAdviseCharge'], array_merge(array('size' => 6), $readOnlyAttrs)); ?>
        </td>
        <td class="center charge">
            <?php echo CHtml::textField("dt", $job['SDAdviseCharge'], array_merge(array('size' => 6), $readOnlyAttrs)); ?>
        </td>
        <td class="center charge">
            <?php echo CHtml::textField("dt", $job['CancelledCharge'], array_merge(array('size' => 6), $readOnlyAttrs)); ?>
        </td>
        <td class="center charge total">
            <?php echo CHtml::textField("dt", $job['TotalCharge'], array_merge(array('size' => 6), $readOnlyAttrs)); ?>
        </td>
        <td class="center">
            <img class="tooltiped" title="[comments on tooltip]" src="<?php echo $baseUrl; ?>/img/icons/comments.png"/>
        </td>
    </tr>
    
</table>

<br/>

<table class="listing fluid load-details" style="max-width: 60% !important;">
    <tr>
        <td></td>
        <th>Collection</th>
        <th colspan="3">
            Delivery
        </th>
        <th colspan="2">
            Waiting Time
        </th>
    </tr>
    <tr>
        <th width="100">PolestarLoadRef</th>
        <th width="1">Postcode</th>
        <th width="1">Postcode</th>
        <th width="350">Address</th>
        <th>Mileage</th>
        <th>Time</th>
        <th>Cost</th>
        <th>Comments</th>
    </tr>
    <?php foreach ($job['Loads'] as $load): ?>
    <tr class="<?php echo cycle("row1","row2") ?>">
        <td><?php echo $load['Ref']; ?></td>
        <td><?php echo $load['ColPostcode']; ?></td>
        <td><?php echo $load['DelPostcode']; ?></td>
        <td><?php echo $load['DelAddress']; ?></td>
        <td><?php echo $load['Mileage']; ?></td>
        <td class="center">
            <?php echo CHtml::textField("dt", $load['WaitingTime'], array_merge(array('size' => 6), $readOnlyAttrs)); ?>
        </td>
        <td class="center cost">
            <?php echo CHtml::textField("dt", $load['WaitingTimeCost'], array_merge(array('size' => 6), $readOnlyAttrs)); ?>
        </td>
        <td class="center">
            <img class="tooltiped" title="[comments on tooltip]" src="<?php echo $baseUrl; ?>/img/icons/comments.png"/>
        </td>
    </tr>
    <?php endforeach; ?>
</table>

<?php    
endforeach;
?>

<h2>Alternative mockup</h2>

<?php foreach ($jobs as $job): 
    $jobId = $job['Id'];
    ?>
<hr />
<table class="listing fluid job-details">
    <tr>
        <td colspan="3">
            <span style="font-size: 1.5em; font-weight: bold;">JobRef: <?php echo $jobId ?></span>
        </td>
        <th colspan="2">
            Supplier Invoice
        </th>
        <td></td>
        <th colspan="2">
            Total Waiting Time
        </th>
        <th colspan="2">
            Late Advise
        </th>
        <th colspan="2">
            Same Day Advise
        </th>
        <th colspan="2">
            Cancelled
        </th>
        <th rowspan="2">
            Total<br/>Job Cost
        </th>
    </tr>
    <tr>
        <td></td>
        <th width='1'>Col. Date</th>
        <th width='250'>Supplier</th>
        <th width='1'>Number</th>
        <th width='1'>Date Received</th>
        <td width="100"></td>
        <th>Time</th>
        <th>Cost</th>
        <th>Time</th>
        <th>Cost</th>
        <th>Time</th>
        <th>Cost</th>
        <th>Time</th>
        <th>Cost</th>
    </tr>
    <tr>
        <td width='1'>
            <a href="javascript:void(0)">
                <img src="<?php echo $baseUrl; ?>/img/icons/magnifier.png" title="[see job details]" />
            </a>
        </td>
        <td>
            <?php echo $job['DeliveryDate']; ?>
        </td>
        <td class='nowrap center'>
            <?php echo $job['Supplier'] ?>
        </td>
        <td>
            <?php echo CHtml::textField("dt", '', array_merge(array('size' => 10), $readOnlyAttrs)); ?>
        </td>
        <td>
            <?php echo CHtml::textField("dt", '', array_merge(array('size' => 10), $readOnlyAttrs)); ?>
        </td>
        
        <td><!-- separator --></td>
        
        <td class="center">
            <?php echo CHtml::textField("dt", $job['WaitingTime'], array_merge(array('size' => 6), $readOnlyAttrs)); ?>
        </td>
        <td class="center cost">
            <?php echo CHtml::textField("dt", $job['WaitingTimeCost'], array_merge(array('size' => 6), $readOnlyAttrs)); ?>
        </td>
        <td class="center">
            <?php echo CHtml::textField("dt", $job['LateAdviseTime'], array_merge(array('size' => 6), $readOnlyAttrs)); ?>
        </td>
        <td class="center cost">
            <?php echo CHtml::textField("dt", $job['LateAdviseCost'], array_merge(array('size' => 6), $readOnlyAttrs)); ?>
        </td>
        <td class="center">
            <?php echo CHtml::textField("dt", $job['SDAdviseTime'], array_merge(array('size' => 6), $readOnlyAttrs)); ?>
        </td>
        <td class="center cost">
            <?php echo CHtml::textField("dt", $job['SDAdviseCost'], array_merge(array('size' => 6), $readOnlyAttrs)); ?>
        </td>
        <td class="center">
            <?php echo CHtml::textField("dt", $job['CancelledTime'], array_merge(array('size' => 6), $readOnlyAttrs)); ?>
        </td>
        <td class="center cost">
            <?php echo CHtml::textField("dt", $job['CancelledCost'], array_merge(array('size' => 6), $readOnlyAttrs)); ?>
        </td>
        <td class="center cost total">
            <?php echo CHtml::textField("dt", $job['TotalCost'], array_merge(array('size' => 6), $readOnlyAttrs)); ?>
        </td>
    </tr>
    
</table>

<fieldset>
    <legend>Charges Breakdown</legend>
    
    <table class="listing fluid">
        <tr>
            <th colspan="3">
                Mileage
            </th>
            <th rowspan="2">
                Agreed <br/>Price
            </th>
            <th colspan="2">
                Collections
            </th>
            <th colspan="2">
                Drop Offs
            </th>
        </tr>
        <tr>
            <th>Vehicle</th>
            <th>Total</th>
            <th>Charge</th>
            
            <th>#</th>
            <th>Charge</th>
            
            <th>#</th>
            <th>Charge</th>
            
            <th>Surcharge</th>
            
            <th>Wait Time</th>
            <th>Late Adv.</th>
            <th>Same Day</th>
            <th>Cancelled</th>
            <th>Total</th>
            <th>Comments</th>
        </tr>
        <tr>
            <td class="center">
                <?php echo $job['Vehicle'] ?>
            </td>
            <td class="center">
                <?php echo $job['Mileage'] ?>
            </td>
            <td class="center charge">
                <?php echo CHtml::textField("dt", $job['MileageBandCharge'], array_merge(array('size' => 6), $readOnlyAttrs)); ?>
            </td>
            <td class="center charge">
                <?php echo CHtml::textField("dt", $job['AgreedPrice'], array_merge(array('size' => 6), $readOnlyAttrs)); ?>
            </td>
            
            <td class="center">
                <?php echo count($job['ColPoints']); ?>
            </td>
            <td class="center charge">
                <?php echo CHtml::textField("dt", $job['CollectionsCharge'], array_merge(array('size' => 6), $readOnlyAttrs)); ?>
                <img class="tooltiped" title="[expand collections]" src="<?php echo $baseUrl; ?>/img/icons/zoom_in.png"/>
            </td>
            
            <td class="center">
                <?php echo count($job['Loads']); ?>
            </td>
            <td class="center charge">
                <?php echo CHtml::textField("dt", $job['DropOffsCharge'], array_merge(array('size' => 6), $readOnlyAttrs)); ?>
                <img class="tooltiped" title="[expand loads]" src="<?php echo $baseUrl; ?>/img/icons/zoom_in.png"/>
            </td>
            
            <td class="center charge">
                <?php echo CHtml::textField("dt", $job['Surcharge'], array_merge(array('size' => 6), $readOnlyAttrs)); ?>
                <img class="tooltiped" title="[surcharge details on tooltip]" src="<?php echo $baseUrl; ?>/img/icons/information.png"/>
            </td>
            
            <td class="center charge">
                <?php echo CHtml::textField("dt", $job['WaitingTimeCharge'], array_merge(array('size' => 6), $readOnlyAttrs)); ?>
            </td>
            <td class="center charge">
                <?php echo CHtml::textField("dt", $job['LateAdviseCharge'], array_merge(array('size' => 6), $readOnlyAttrs)); ?>
            </td>
            <td class="center charge">
                <?php echo CHtml::textField("dt", $job['SDAdviseCharge'], array_merge(array('size' => 6), $readOnlyAttrs)); ?>
            </td>
            <td class="center charge">
                <?php echo CHtml::textField("dt", $job['CancelledCharge'], array_merge(array('size' => 6), $readOnlyAttrs)); ?>
            </td>
            <td class="center charge total">
                <?php echo CHtml::textField("dt", $job['TotalCharge'], array_merge(array('size' => 6), $readOnlyAttrs)); ?>
            </td>
            <td class="center">
                <img class="tooltiped" title="[comments on tooltip]" src="<?php echo $baseUrl; ?>/img/icons/comments.png"/>
            </td>
        </tr>
    </table>
    
</fieldset>

<fieldset>
    <legend>
        <img class="tooltiped" title="[collapse]" src="<?php echo $baseUrl; ?>/img/icons/zoom_out.png"/>
        Collection details
    </legend>
    
<table class="listing fluid load-details" style="max-width: 70% !important;">
    <tr>
        <td colspan="2"></td>
        <th colspan="3">
            Waiting Time
        </th>
    </tr>
    <tr>
        <th width="1">Postcode</th>
        <th width="350">Address</th>
        <th>Mileage</th>
        <th>Time</th>
        <th>Cost</th>
        <th>Charge</th>
        <th width="250">Note</th>
        <th>Comments</th>
    </tr>
    <?php foreach ($job['ColPoints'] as $point): ?>
    <tr class="<?php echo cycle("row1","row2") ?>">
        <td><?php echo $point['Postcode']; ?></td>
        <td><?php echo $point['Address']; ?></td>
        <td><?php echo $point['Mileage']; ?></td>
        <td class="center">
            <?php echo CHtml::textField("dt", $point['WaitingTime'], array_merge(array('size' => 6), $readOnlyAttrs)); ?>
        </td>
        <td class="center cost">
            <?php echo CHtml::textField("dt", $point['WaitingTimeCost'], array_merge(array('size' => 6), $readOnlyAttrs)); ?>
        </td>
        <td class="center charge">
            <?php echo CHtml::textField("dt", $point['WaitingTimeCharge'], array_merge(array('size' => 6), $readOnlyAttrs)); ?>
        </td>
        <td><?php echo $point['Note']; ?></td>
        <td class="center">
            <img class="tooltiped" title="[comments on tooltip]" src="<?php echo $baseUrl; ?>/img/icons/comments.png"/>
        </td>
    </tr>
    <?php endforeach; ?>
</table>    
    
</fieldset>

<fieldset>
    <legend>
        <img class="tooltiped" title="[collapse]" src="<?php echo $baseUrl; ?>/img/icons/zoom_out.png"/>
        Loads (Drop Offs) details
    </legend>

<table class="listing fluid load-details" style="max-width: 70% !important;">
    <tr>
        <td></td>
        <th>Collection</th>
        <th colspan="3">
            Delivery
        </th>
        <th colspan="3">
            Waiting Time
        </th>
    </tr>
    <tr>
        <th width="100">PolestarLoadRef</th>
        <th width="1">Postcode</th>
        <th width="1">Postcode</th>
        <th width="350">Address</th>
        <th>Mileage</th>
        <th>Time</th>
        <th>Cost</th>
        <th>Charge</th>
        <th width="250">Note</th>
        <th>Comments</th>
    </tr>
    <?php foreach ($job['Loads'] as $load): ?>
    <tr class="<?php echo cycle("row1","row2") ?>">
        <td><?php echo $load['Ref']; ?></td>
        <td><?php echo $load['ColPostcode']; ?></td>
        <td><?php echo $load['DelPostcode']; ?></td>
        <td><?php echo $load['DelAddress']; ?></td>
        <td><?php echo $load['Mileage']; ?></td>
        <td class="center">
            <?php echo CHtml::textField("dt", $load['WaitingTime'], array_merge(array('size' => 6), $readOnlyAttrs)); ?>
        </td>
        <td class="center cost">
            <?php echo CHtml::textField("dt", $load['WaitingTimeCost'], array_merge(array('size' => 6), $readOnlyAttrs)); ?>
        </td>
        <td class="center charge">
            <?php echo CHtml::textField("dt", $load['WaitingTimeCharge'], array_merge(array('size' => 6), $readOnlyAttrs)); ?>
        </td>
        <td><?php echo $load['Note']; ?></td>
        <td class="center">
            <img class="tooltiped" title="[comments on tooltip]" src="<?php echo $baseUrl; ?>/img/icons/comments.png"/>
        </td>
    </tr>
    <?php endforeach; ?>
</table>
    
</fieldset>

<?php    
endforeach;
?>



<script>
$(function(){
    $(".dpicker").datepicker({
        dateFormat: 'dd/mm/yy',
        onSelect: function() {
            $(this).removeClass("error");
            $(this).change();
        },
        beforeShowDay: function(date){ return [date.getDay() == 0,""]},
        maxDate: '0'
    });
});
</script>


