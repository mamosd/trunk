<?php
    $baseUrl = Yii::app()->request->baseUrl;

    $readOnlyAttrs = array('class' => 'readOnlyField info-entry', 'readOnly' => 'readOnly');
    $smallReadOnlyAttrsBase = array_merge(array('size' => 5), $readOnlyAttrs);
    $smallReadOnlyAttrs = $smallReadOnlyAttrsBase;
    
    $smallReadOnlyAttrsMap = $smallReadOnlyAttrs;
    $smallReadOnlyAttrsMap['class'] = str_replace('info-entry', 'map-job', $smallReadOnlyAttrsMap['class']);

    $id = $jobInfo->Id;
    $jobId = $jobInfo->Ref;
    $class = @$jobInfo->Status->Code;
    $loads = $jobInfo->Loads;
    $collectionPoints = $jobInfo->CollectionPoints;
    $loadCount = 0;

    $aktrionSupplierId = Setting::get('polestar', 'aktrion-supplier-id');
?>
<div class="job-container" rel="<?php echo $id ?>" status="<?php echo $jobInfo->StatusId ?>">
    <a id="job-<?php echo $id ?>"></a>
    <hr />

    <table class="listing fluid job-details">
        <tr>
            <td colspan="5">
                <span style="font-size: 1.5em; font-weight: bold;">AktrionJobRef: <?php echo $jobId ?></span>
            </td>
            <th colspan="3">Driver</th>
            <th width="1%" rowspan="2">Total <br/>Mileage</th>
            <td colspan="3"></td>
        </tr>
        <tr>
            <td width="1%"></td>
            <th width="1%">Col. Date</th>
            <th width="8%">Provider</th>
            <th width="5%">Supplier</th>
            <th width="1%">Vehicle</th>
            <th width="10%">Name</th>
            <th width="1%">Reg.</th>
            <th width="7%">Phone</th>
            <th width="7%">Job Status</th>
            <th width="1%">Comments</th>
            <?php if (Login::checkPermission(Permission::PERM__FUN__POLESTAR__COSTING)): ?>
            <th width="1%">Agreed Price</th>
            <?php endif; ?>
        </tr>
        <tr>
            <td class="nowrap actions">
                <a href="javascript:void(0)" class="edit-job"><img src="<?php echo $baseUrl; ?>/img/icons/page_edit.png" title="[edit job details]" /></a>
                <a href="javascript:void(0)" class="drop-job"><img src="<?php echo $baseUrl; ?>/img/icons/delete.png" title="[cancel job]" /></a>
                <a href="javascript:void(0)" class="map-job"><img src="<?php echo $baseUrl; ?>/img/icons/map.png" title="[see/update mileage information]" /></a>
                <?php if (Login::checkPermission(Permission::PERM__FUN__POLESTAR__ROUTE_CLONE)): ?>
                <a href="javascript:void(0)" class="clone-job"><img src="<?php echo $baseUrl; ?>/img/icons/application_double.png" title="[clone job for a different date]" /></a>
                <?php endif; ?>  
            </td>
            <td>
                <?php 
                echo $jobInfo->getUiTag('DeliveryDate', 
                            $jobInfo->formatDate('DeliveryDate','d/m/Y')
                        );
                ?>
            </td>
            <td>
                <?php 
                echo $jobInfo->getUiTag('ProviderId', 
                                @$jobInfo->Provider->Name
                            );
                ?>
            </td>
            <td>
                <?php
                if ($jobInfo->ProviderId == $aktrionSupplierId)
                    echo CHtml::textField("dt", @$jobInfo->Supplier->Name, $readOnlyAttrs);
                else
                    echo $jobInfo->getUiTag('SupplierId', 
                                @$jobInfo->Supplier->Name
                            );
                ?>
            </td>
            <td>
                <?php echo $jobInfo->getUiTag('VehicleId', 
                                @$jobInfo->Vehicle->Name
                            ); ?>
            </td>
            <td>
                <?php echo $jobInfo->getUiTag('DriveName'); ?>
            </td>
            <td class="nowrap">
                <?php echo $jobInfo->getUiTag('VehicleRegNo'); ?>
            </td>
            <td class="nowrap">
                <?php echo $jobInfo->getUiTag('ContactNo'); ?>
            </td>
            <td>
                <?php 
                if ($jobInfo->TotalMileage == '')
                    echo CHtml::textField("dt", $jobInfo->TotalMileage, $smallReadOnlyAttrsMap); 
                else
                    echo $jobInfo->TotalMileage;
                ?>
            </td>
            <td style="text-align: center;" class="status-cell <?php echo $class ?>">
                <?php
                    $tooltip = PolestarStatusUpdater::getAllJobRevisionChangesHtml($jobInfo);
                ?>
                <span class="tooltiped" title="<?php echo htmlentities($tooltip); ?>"><?php echo @$jobInfo->Status->Name; ?></span>
            </td>
            <td style="text-align: center;">
                <?php if (!empty($jobInfo->Comments)): ?>
                <?php $comments = ''; ?>
                <?php for ($i = 0; $i < 3 && $i < count($jobInfo->Comments); $i++) {
                    $comment = $jobInfo->Comments[$i];
                    $comments .= ($i>0?'<hr/>':'').sprintf('<strong>By %s @ %s</strong><br/><em>%s</em>', $comment->Login->FriendlyName, $comment->CreatedDate, $comment->Comment) ;
                }?>
                <img class="tooltiped" title="<?php echo htmlentities($comments); ?>" src="<?php echo $baseUrl; ?>/img/icons/comments.png"/>
                <?php endif; ?>
            </td>
            <?php if (Login::checkPermission(Permission::PERM__FUN__POLESTAR__COSTING)): ?>
            <td>
                <?php echo $jobInfo->getUiTag('AgreedPrice'); ?>
            </td>
            <?php endif; ?>
        </tr>
    </table>

    <br/>
    
    <table class="listing fluid job-points-details sortable_cps">
        <thead>
        <tr>
            <td></td>
            <th colspan="5">Collection</th>
            <td colspan="2"></td>
        </tr>
        <tr>
            <td></td>
            <th width="2%">Postcode</th>
            <th>Address</th>
            <th width="1%">Sched.</th>
            <th width="1%">Arrival</th>
            <th width="1%">Departure</th>
            <th width="1%">Mileage</th>
            <th width="25%">Special Instructions</th>
        </tr>
        </thead>
        <tbody>
    <?php 
      $points = array_merge(array($jobInfo), $collectionPoints);
      foreach ($points as $pInfo):
    ?>
        <tr data-id="<?php echo $pInfo->Id ?>" data-jid="<?php echo isset($pInfo->JobId) ? $pInfo->JobId : 0 ?>" data-seq="<?php echo isset($pInfo->Sequence) ? $pInfo->Sequence : 0 ?>">
            <td class="nowrap actions">
                <a href="javascript:void(0)" class="edit-job"><img src="<?php echo $baseUrl; ?>/img/icons/page_edit.png" title="[edit job details]" /></a>
                <?php if (count($points) > 1): ?>
                <a href="javascript:void(0)" class="drop-cp"><img src="<?php echo $baseUrl; ?>/img/icons/delete.png" title="[cancel collection point]" /></a>
                <a href="javascript:void(0)" class="drag-cp"><img src="<?php echo $baseUrl; ?>/img/icons/arrow_switch.png" title="[drag collection point]"></a>
                <?php endif; ?>
            </td>
            <td class="nowrap">
                <?php echo $pInfo->CollPostcode; ?>
            </td>
            <td class="nowrap">
                <?php echo $pInfo->CollAddress; ?>
            </td>
            <td style="text-align: center;">
                <?php echo $pInfo->formatTime('CollScheduledTime','H:i','TBC'); ?>
            </td>
            <td>
                <?php echo CHtml::textField("dt", $pInfo->formatTime('CollArrivalTime','H:i'), $smallReadOnlyAttrs); ?>
            </td>
            <td>
                <?php echo CHtml::textField("dt", $pInfo->formatTime('CollDepartureTime','H:i'), $smallReadOnlyAttrs); ?>
            </td>
            <td>
                <?php 
                if ($jobInfo->TotalMileage == '')
                    echo CHtml::textField("dt", $pInfo->Mileage, $smallReadOnlyAttrsMap); 
                else
                    echo $pInfo->Mileage;
                ?>
            </td>
            <td class="nowrap">
                <span class="tooltiped" title="<?php echo htmlentities($pInfo->SpecialInstructions); ?>">
                    <?php echo excerpt($pInfo->SpecialInstructions, 50); ?>
                </span>
            </td>
        </tr>
    <?php endforeach; ?>
        </tbody>
        <tfoot>
        <tr>
            <td colspan="5">
                <img src="<?php echo $baseUrl; ?>/img/icons/add.png" alt="add" />
                <a href="#" class="add-coll-point" job="<?php echo $jobInfo->Id ?>">
                    <strong>Add collection point</strong>
                </a>
            </td>
        </tr>
        </tfoot>
    </table>
    
    <br/>

    <table class="listing fluid load-details sortable_loads">
    <thead>
        <tr>
            <td colspan="3"></td>
            <th colspan="4">Pallets</th>
            <td colspan="3"></td>
            <th>Collection</th>
            <th colspan="7">Delivery</th>
            <td colspan="5"></td>
        </tr>
    <tr>
        <td width="1%"></td>
        <th width="5%">Job Type</th>
        <th width="5%">PolestarLoadRef</th>
        <th width="1%">T</th>
        <th width="1%">F</th>
        <th width="1%">H</th>
        <th width="1%">Q</th>
        <th width="1%">Qty</th>
        <th width="1%">Weight</th>
        <th>Publication</th>
        <th width="1%">Postcode</th>
        <th width="1%">Date</th>
        <th width="1%">Postcode</th>
        <th width="1%">Sched.</th>
        <th width="1%">Time Code</th>
        <th width="1%">Arrival</th>
        <th width="1%">Departure</th>
        <th width="1%">Mileage</th>
        <th width="12%">Address</th>
        <th width="10%">Company</th>
        <th width="1%">Booking Ref</th>
        <th width="1%">Job/Load Ref. No.</th>
        <th width="1%">Comments</th>
    </tr>
    </thead>
    <?php if (empty($loads)) : ?>
    <tr>
        <td></td>
        <td colspan="15">
            <div class="warningBox">
                There are no loads added to this job yet. Please continue to do so by clicking the Add Load hyperlink below.
            </div>
        </td>
    </tr>
    <?php
    else:
        foreach ($loads as $load):
            $loadCount += ($load->StatusId == PolestarStatus::CANCELLED_ID) ? 0 : 1;
        ?>
    <tr
        class="load_container <?php echo ($load->StatusId == PolestarStatus::CANCELLED_ID)?'cancelled-load':''?>"
        data-seq="<?php echo $load->Sequence?>"
        data-id="<?php echo $load->Id ?>">

        <td class="actions nowrap">
            <?php if ($load->StatusId == PolestarStatus::CANCELLED_ID): ?>
                <a href="javascript:void(0)" class="activate-load"><img src="<?php echo $baseUrl; ?>/img/icons/arrow_refresh.png" title="[activate load]" /></a>
            <?php else: ?>
                <a href="javascript:void(0)" class="edit-load"><img src="<?php echo $baseUrl; ?>/img/icons/page_edit.png" title="[edit load details]" /></a>
                <a href="javascript:void(0)" class="drop-load"><img src="<?php echo $baseUrl; ?>/img/icons/delete.png" title="[cancel load]" /></a>
            <?php endif; ?>
            <a href="javascript:void(0)" class="drag-load"><img src="<?php echo $baseUrl; ?>/img/icons/arrow_switch.png" title="[drag load within route]"></a>
        </td>
        <td>
            <?php 
                echo $load->getUiTag('JobTypeId', 
                            (!empty($load->JobTypeId) ? $load->JobType->Name : '')
                        );
            ?>
        </td>
        <td>
            <?php echo $load->getUiTag('Ref'); ?>
        </td>
        <td><?php echo $load->getUiTag('PalletsTotal'); ?></td>
        <td>
            <?php 
                echo $load->getUiTag('PalletsFull', 
                            ((empty($load->PalletsFull)) ? '-' : $load->PalletsFull)
                        );
            ?>
        </td>
        <td>
            <?php 
                echo $load->getUiTag('PalletsHalf', 
                            ((empty($load->PalletsHalf)) ? '-' : $load->PalletsHalf)
                        );
            ?>
        </td>
        <td>
            <?php 
                echo $load->getUiTag('PalletsQtr', 
                            ((empty($load->PalletsQtr)) ? '-' : $load->PalletsQtr)
                        );
            ?>
        </td>
        <td>
            <?php echo $load->getUiTag('Quantity'); ?>
        </td>
        <td>
            <?php echo $load->getUiTag('Kg'); ?>
        </td>
        <td>
            <?php echo $load->getUiTag('Publication'); ?>
        </td>
        <td class="nowrap">
            <?php if (empty($load->CollectionSequence)) : ?>
            <span class="tooltiped" title="<?php echo htmlentities($jobInfo->CollAddress); ?>"><?php echo $jobInfo->CollPostcode; ?></span>
            <?php else: 
                echo $load->getUiTag('CollectionSequence');
            endif; ?>
        </td>
        <td>
            <?php 
                echo $load->getUiTag('DeliveryDate', 
                            $load->formatDate('DeliveryDate', 'd/m/Y')
                        );
            ?>
        </td>
        <td class="nowrap"><?php echo $load->getUiTag('DelPostcode'); ?></td>
        <td style="text-align: center;">
            <?php 
                echo $load->getUiTag('DelScheduledTime', 
                            $load->formatTime('DelScheduledTime','H:i','TBC')
                        );
            ?>
        </td>
        <td>
            <?php echo $load->getUiTag('DelTimeCode'); ?>
        </td>
        <td>
            <?php echo CHtml::textField("dt", $load->formatTime('DelArrivalTime','H:i'), $smallReadOnlyAttrs); ?>
        </td>
        <td>
            <?php echo CHtml::textField("dt", $load->formatTime('DelDepartureTime','H:i'), $smallReadOnlyAttrs); ?>
        </td>
        <td>
            <?php 
            if ($jobInfo->TotalMileage == '')
                echo CHtml::textField("dt", $load->Mileage, $smallReadOnlyAttrsMap); 
            else
                echo $load->Mileage;
            ?>
        </td>
        <td><?php echo $load->getUiTag('DelAddress'); ?></td>
        <td><?php echo $load->getUiTag('DelCompany'); ?></td>
        <td><?php echo $load->getUiTag('BookingRef'); ?></td>
        <td class="nowrap">
            <span class="tooltiped" title="<?php echo htmlentities($load->SpecialInstructions); ?>">
                <?php 
                    echo $load->getUiTag('SpecialInstructions', 
                                excerpt($load->SpecialInstructions, 25)
                            );
                ?>
            </span>
        </td>
        <td style="text-align: center;">
            <?php if (!empty($load->Comments)): ?>
            <?php $comments = ''; ?>
            <?php for ($i = 0; $i < 3 && $i < count($load->Comments); $i++) {
                $comment = $load->Comments[$i];
                $comments .= ($i>0?'<hr/>':'').sprintf('<strong>By %s @ %s</strong><br/><em>%s</em>', $comment->Login->FriendlyName, $comment->CreatedDate, $comment->Comment) ;
                }?>
            <img class="tooltiped" title="<?php echo htmlentities($comments); ?>" src="<?php echo $baseUrl; ?>/img/icons/comments.png"/>
            <?php endif; ?>
        </td>
    </tr>
    <?php endforeach;
    endif;?>
    <tfoot>
    <tr>
        <td colspan="5">
            <img src="<?php echo $baseUrl; ?>/img/icons/add.png" alt="add" />
            <a href="#" class="add-load" job="<?php echo $jobInfo->Id ?>">
                <strong>Add load</strong>
            </a>
        </td>
    </tr>
    </tfoot>
    </table>

    <?php 
    if ($loadCount > 0) :
        if (!empty($jobInfo->SupplierId) && ($jobInfo->StatusId != PolestarStatus::CANCELLED_ID)): 
            $sent = $jobInfo->isAdviceSheetSent();
            $icon = ($sent) ? 'email_open' : 'email_go';
            ?>
    <div style="text-align: right;">
        <img src="<?php echo $baseUrl; ?>/img/icons/<?php echo $icon ?>.png" alt="add" />
        <a href="javascript:void(0)" class="send-advise">
            <strong>Send advice sheet</strong>
        </a>
        <?php if ($sent): ?>
        &nbsp;
        <span class="successBox">SENT&nbsp;&nbsp;</span>
        <?php endif; ?>
    </div>
    <?php endif; ?>
    <div style="text-align: right;">
        <img src="<?php echo $baseUrl; ?>/img/icons/page_white_acrobat.png" alt="add" />
        <a href="<?php echo Yii::app()->createUrl('polestar/advice_sheet/', array( 'id' => $id )); ?>" class="generate-advise">
            <strong>Generate advice sheet</strong>
        </a>
    </div>
    <?php endif; ?>

</div>