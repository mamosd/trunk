<?php
/**
 * DEPRECATED - now using activitylog instead
 */

$baseUrl = Yii::app()->request->baseUrl;
$jobs = $model->getData();

if (empty($jobs)): ?>
<div class="successBox">
    No jobs to list under this print centre.
</div>
<?php else: ?>

<table class="listing fluid">
    <tr>
        <th>AktrionJobRef</th>
        <th>Col. Date</th>
        <th>Provider</th>
        <th>Supplier</th>
        <th>Job Status</th>
        <th>Date Created</th>
        <th>Created By</th>
        <td width="1"></td>
    </tr>
<?php foreach ($jobs as $job): ?>
    <tr class="<?php echo cycle("row1","row2") ?>">
        <td><?php echo $job->Ref; ?></td>
        <td><?php echo $job->formatDate('DeliveryDate','d/m/Y'); ?></td>
        <td><?php echo @$job->Provider->Name; ?></td>
        <td><?php echo @$job->Supplier->Name; ?></td>
        <td class="status-cell <?php echo @$job->Status->Code ?>">
            <?php echo @$job->Status->Name; ?>
        </td>
        <td><?php echo $job->formatDate('CreatedDate','d/m/Y H:i'); ?></td>
        <td><?php echo @$job->CreatedByLogin->FriendlyName; ?></td>
        <td>
            <?php 
            $dtp = new DateTime($job->DeliveryDate);
            $dtp = $dtp->modify('-1 day');
            $dtp = $dtp->format('d/m/Y');
            $url = $this->createUrl('polestar/routeview', array('PolestarRouteViewForm[planningDate]' => $dtp, 'PolestarRouteViewForm[printCentreId]' => $job->PrintCentreId));
            $url .= "#job-{$job->Id}";
            ?>
            <a target="_blank" href="<?php echo $url ?>">
                <img src="<?php echo $baseUrl; ?>/img/icons/magnifier.png" alt="view" />
            </a>
        </td>
    </tr>
<?php endforeach; ?>        
</table>
<?php endif; ?>


