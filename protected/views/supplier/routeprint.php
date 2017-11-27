<?php
$GLOBALS['details'] = $model->routeDetails;
global $details;
$detailscount = count($model->routeDetails);
?>

<style>
.delnotes {
    margin: 5px;
}
.delnotes th, .delnotes td{
    border: 2px solid black;
    padding: 10px;
}
.open-left {
    border-left: 0px;
}

.delnotessummary {
}
.delnotessummary th, .delnotessummary td{
    border:2px solid black;
    padding:10px;
}

.blank {
    border:0px !important
}
.open-left {
    border-left: 0px !important;
}
.open-right {
    border-right: 0px !important;
}
.center {
    text-align: center;
}
.break {
    page-break-after: always;
}
</style>

<?php
$GLOBALS['orderHeader'] = <<<HTML
<table width="80%">
<tr>
    <td valign="top">
        {titlename}<br/>
        {printcentre}<br/>
        Off Press Time: {offpresstime}
    </td>
    <td>

        <table class="delnotessummary">
            <tr>
                <td class="blank">&nbsp;</td>
                <th>MAIN</th>
                <th>PROPERTY</th>
            </tr>
            <tr>
                <th>TOTAL COPIES</th>
                <td>{totalcopies}</td>
                <td>&nbsp;</td>
            </tr>
            <tr>
                <th>BULK COUNT</th>
                <td>{bundlesize}</td>
                <td>&nbsp;</td>
            </tr>
            <tr>
                <th>PAGINATION</th>
                <td>{pagination}</td>
                <td>&nbsp;</td>
            </tr>
            <tr>
                <th>DELIVERY DATE</th>
                <td colspan="2" class="center">{deliverydate}</td>
            </tr>
        </table>

    </td>
</tr>
</table>
<br/>
HTML;

$GLOBALS['orderSummary'] = <<<HTML
<table class="delnotes" width="90%">
    <tr>
        <td class="blank">&nbsp;</td>
        <th colspan="2">
            MAIN <br/>
            (bundles of {bundlesize})
        </th>
        <td colspan="4" class="blank">&nbsp;</td>
        <th colspan="2">PALLETS</th>
    </tr>
    <tr>
        <th>COPIES</th>
        <th>BULK</th>
        <th>ODDS</th>
        <th colspan="2" class="open-right">DESTINATION</th>
        <th class="open-left">AREA</th>
        <th>DELIVERY TIME</th>
        <th>COLL</th>
        <th>DEL</th>
    </tr>
    {orderlines}
</table>
HTML;

$GLOBALS['orderSummaryLine'] = <<<HTML
<tr>
    <td>{copies}</td>
    <td>{bulk}</td>
    <td>{odds}</td>
    <td>{address}<br/>{county}</td>
    <td>{postalcode}<br/>{phonenumber}</td>
    <td>{area}<br/>{comments}</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
</tr>
HTML;

$GLOBALS['orderSummaryFooter'] = <<<HTML
<tr>
    <td>{totalcopies}</td>
    <td>{totalcopies}</td>
    <td>{totalodds}</td>
    <td class="blank">&nbsp;</td>
</tr>
HTML;


$titleId = $details[0]->TitleId;
$orderLines = '';
$totalCopies = 0;
$totalOdds = 0;
for($i = 0; $i < $detailscount; $i++) {
    
    if ($titleId != $details[$i]->TitleId)
    {
        // print out header + summary
        printSummary(($i-1), $totalCopies, $totalOdds, $orderLines, true);
        $orderLines = '';
        $totalCopies = 0;
        $totalOdds = 0;
        $titleId = $details[$i]->TitleId;
    }
    
    $orderLines .= $GLOBALS['orderSummaryLine'];
    $orderLines = str_replace('{copies}', $details[$i]->Copies, $orderLines);
    $orderLines = str_replace('{bulk}', floor($details[$i]->Copies / $details[$i]->BundleSize) , $orderLines);
    $orderLines = str_replace('{odds}', ($details[$i]->Copies % $details[$i]->BundleSize) , $orderLines);
    $totalOdds += ($details[$i]->Copies % $details[$i]->BundleSize);
    $orderLines = str_replace('{address}', str_replace("\n", '<br/>', $details[$i]->DeliveryPointAddress), $orderLines);
    $orderLines = str_replace('{county}', $details[$i]->DeliveryPointCounty, $orderLines);
    $orderLines = str_replace('{postalcode}', $details[$i]->DeliveryPointPostalCode, $orderLines);
    $orderLines = str_replace('{phonenumber}', $details[$i]->DeliveryPointTelephoneNumber, $orderLines);
    $orderLines = str_replace('{area}', $details[$i]->DeliveryPointAccountNumber, $orderLines);
    $orderLines = str_replace('{comments}', $details[$i]->DeliveryPointComments, $orderLines);
    $totalCopies += $details[$i]->Copies;
}
printSummary(($detailscount-1), $totalCopies, $totalOdds, $orderLines, false);


function printSummary($idx, $totalCopies, $totalOdds, $orderLines, $insertPageBreak)
{
    global $details, $orderHeader, $orderSummaryFooter, $orderSummary;

    $header = $orderHeader;
    $header = str_replace('{titlename}', $details[$idx]->TitleName, $header);
    $header = str_replace('{printcentre}', $details[$idx]->PrintCentreName, $header);
    $header = str_replace('{offpresstime}', $details[$idx]->OffPressTime, $header);

    $header = str_replace('{totalcopies}', $totalCopies, $header);
    $header = str_replace('{bundlesize}', $details[$idx]->BundleSize, $header);
    $header = str_replace('{pagination}', $details[$idx]->Pagination, $header);
    $header = str_replace('{deliverydate}', $details[$idx]->DeliveryDate, $header);

    $orderLines .= $orderSummaryFooter;
    $orderLines = str_replace('{totalcopies}', $totalCopies, $orderLines);
    $orderLines = str_replace('{totalodds}', $totalOdds, $orderLines);

    $summary = $orderSummary;
    $summary = str_replace('{bundlesize}', $details[$idx]->BundleSize, $summary);
    $summary = str_replace('{orderlines}', $orderLines, $summary);

    echo $header;
    echo $summary;
    echo "<br/><br/>";
    if ($insertPageBreak)
        echo '<div class="break">&nbsp;</div>';
}
?>




