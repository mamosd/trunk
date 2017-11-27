<?php

$data = $model->data;
$info = $contractor;

?>

<h1><?php echo trim($info->FirstName.' '.$info->LastName) ?> - Week Starting <?php echo $model->weekStarting ?></h1>

<table>
    <tr>
        <th width="10%">Date</th>
        <th width="40%">Route</th>
        <th width="20%">Contractor</th>
        <th width="10%">Fee</th>
        <th width="10%">Expenses / Deductions</th>
        <th width="10%">Total</th>
    </tr>
    
    <?php
    $i = 0;
    $runningTotal = 0;
    
    foreach ($data as $row):

        $runningTotal += $row['total'];
        $isConfirmed = ($row['confirmed'] == 1);
        
        $replText = "";
        if (!empty($row['replaces']))
            $replText = ' (replacing '.$row['replaces'].') ';
        ?>
    <tr class="row<?php echo ($i++%2)+1; ?>">
        <td><?php echo $row['date'] ?></td>
        <td><?php echo $row['category'].' / '.$row['route'].$replText.((!$isConfirmed) ? " **" : ""); ?></td>
        <td><?php echo $row['contractor'] ?></td>
        <td style="text-align: right;"><?php echo $row['fee']; ?></td>
        <td style="text-align: right;"><?php echo $row['miscfee']; ?></td>
        <td style="text-align: right;"><?php echo sprintf("%01.2f", $row['total']); ?></td>
    </tr>
    <?php endforeach; ?>
    
    <tr>
        <td colspan="4">&nbsp;</td>
        <th>Total</th>
        <td style="text-align: right;"><strong><?php echo sprintf("%01.2f", $runningTotal); ?></strong></td>
    </tr>
</table>
<br/>
<em><strong>**</strong> denotes adjustment not yet acknowledged.</em>