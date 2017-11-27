<?php
    $lockarray = array();
    if($model->status != RouteInstance::STATUS_ACTIVE)
        $lockarray = array('class'=>'readOnlyField', 'tabindex'=>'-1', 'readonly'=>'readonly');
?>
<h1>Delivery Information</h1>

<div class="errorBox" style="display:none;"></div>

<?php $form=$this->beginWidget('CActiveForm', array(
            'id'=>'route-form',
            'errorMessageCssClass'=>'formError',
    )); ?>

<table class="listing fluid" cellpadding="0" cellspacing="0">
<tbody>
    <tr class="header">
        <th width="75">Date</th>
        <th width="1" >Route</th>
        <td>&nbsp;</td>
    </tr>
    <tr>
        <td><?php echo $form->textField($model,'date', array('size'=>'10', 'class'=>'readOnlyField', 'tabindex'=>'-1', 'readonly'=>'readonly')); ?></td>
        <td><?php echo $form->textField($model,'routeName', array('size'=>'35', 'class'=>'readOnlyField', 'tabindex'=>'-1', 'readonly'=>'readonly')); ?></td>
        <td>&nbsp;</td>
    </tr>
</tbody>
</table>


<table class="listing fluid" cellpadding="0" cellspacing="0" width="60%">
<tbody>
    <tr>
        <?php if ($model->status != RouteInstance::STATUS_ACTIVE): ?>
        <!--th>Departure Time</th>
        <td colspan="4"><?php echo $model->departureTime; ?></td-->
        <td colspan="5">&nbsp;</td>
        <?php else: ?>
        <td colspan="5">&nbsp;</td>
        <?php endif; ?>
        <?php if ($model->showDetailed != "0") : ?>
        <th colspan="2">Pallets</th>
        <?php endif; ?>
    </tr>
    <tr>
        <th>Title</th>
        <th colspan="2">Destination</th>
        <?php if ($model->showDetailed != "0") : ?>
        <td width="10"></td>
        <th width="125">Delivery Time</th>
        <th width="75">Collected</th>
        <th width="75">Delivered</th>
        <?php endif; ?>
    </tr>

    <?php
    $idx = 0;

    $hh = array(''=>'HH');
    for($h = 0; $h < 24; $h++)
        $hh[str_pad($h, 2, "0", STR_PAD_LEFT)] = ($h >= 12) ? str_pad((($h==12)?$h:($h-12)), 2, "0", STR_PAD_LEFT).' PM' : (($h==0) ? 12 : str_pad($h, 2, "0", STR_PAD_LEFT)).' AM';
    $mm = array(''=>'MM');
    for($m = 0; $m < 60; $m++)
        $mm[str_pad($m, 2, "0", STR_PAD_LEFT)] = str_pad($m, 2, "0", STR_PAD_LEFT);

    foreach ($model->details as $detail): ?>
    <tr class="row<?php echo ($idx%2)+1; ?>">
        <td><?php echo $detail->TitleName; ?></td>
        <td><?php echo $detail->DeliveryPointName.'<br/>'.str_replace("\n", '<br />', $detail->DeliveryPointAddress); ?></td>
        <td><?php echo $detail->DeliveryPointPostalCode; ?></td>
        <?php if ($model->showDetailed != "0") : ?>
        <td>&nbsp;</td>
        <td>
            <?php
            if ($model->status != RouteInstance::STATUS_ACTIVE)
            {
                echo CHtml::textField("Details[$detail->RouteInstanceDetailsId][deliveryTime]", $detail->DeliveryTime, array_merge(array('size'=>12, 'class'=>'req'), $lockarray) );
            }
            else
            {
                $adt = explode(':',$detail->DeliveryTime);
                $dthh = '';
                $dtmm = '';
                if (count($adt) > 1)
                {
                    $dthh = $adt[0];
                    $dtmm = $adt[1];
                }
                
                if (($idx == 0) || (($idx > 0) && ($model->showDetailed != "0")))
                {
                    echo CHtml::dropDownList("Details[$detail->RouteInstanceDetailsId][deliveryTimeHH]", $dthh, $hh);
                    echo ':';
                    echo CHtml::dropDownList("Details[$detail->RouteInstanceDetailsId][deliveryTimeMM]", $dtmm, $mm);
                }
            }
            ?>
        </td>
        <td>
            <?php
            if (($idx == 0) || (($idx > 0) && ($model->showDetailed != "0")))
            echo CHtml::textField("Details[$detail->RouteInstanceDetailsId][palletsCollected]", $detail->PalletsCollected, array_merge(array('size'=>5, 'class'=>'req'), $lockarray)); ?>
        </td>
        <td>
            <?php
            if (($idx == 0) || (($idx > 0) && ($model->showDetailed != "0")))
            echo CHtml::textField("Details[$detail->RouteInstanceDetailsId][palletsDelivered]", $detail->PalletsDelivered, array_merge(array('size'=>5,  'class'=>'req'), $lockarray)); ?>
        </td>
        <?php endif; ?>
    </tr>
    <?php $idx++;
    endforeach; ?>

</tbody>
</table>

<?php if ($model->showDetailed == "0") : ?>
<table class="listing">
    <tr>
        <td>&nbsp;</td>
        <th colspan="2">Pallets</th>
    </tr>
    <tr>
        <th>Last Delivery Time</th>
        <th>Collected</th>
        <th>Delivered</th>
    </tr>
    <tr>
        <td>
            <?php
            $detail = $model->details[0];
            if ($model->status != RouteInstance::STATUS_ACTIVE)
            {
                echo CHtml::textField("Details[$detail->RouteInstanceDetailsId][deliveryTime]", $detail->DeliveryTime, array_merge(array('size'=>12, 'class'=>'req'), $lockarray) );
            }
            else
            {
                $adt = explode(':',$detail->DeliveryTime);
                $dthh = '';
                $dtmm = '';
                if (count($adt) > 1)
                {
                    $dthh = $adt[0];
                    $dtmm = $adt[1];
                }

                echo CHtml::dropDownList("Details[$detail->RouteInstanceDetailsId][deliveryTimeHH]", $dthh, $hh);
                echo ':';
                echo CHtml::dropDownList("Details[$detail->RouteInstanceDetailsId][deliveryTimeMM]", $dtmm, $mm);
            }
            ?>
        </td>
        <td>
            <?php
            echo CHtml::textField("Details[$detail->RouteInstanceDetailsId][palletsCollected]", $detail->PalletsCollected, array_merge(array('size'=>5, 'class'=>'req'), $lockarray));
            ?>
        </td>
        <td>
            <?php
            echo CHtml::textField("Details[$detail->RouteInstanceDetailsId][palletsDelivered]", $detail->PalletsDelivered, array_merge(array('size'=>5,  'class'=>'req'), $lockarray)); ?>
        </td>
    </tr>
</table>
<?php endif; ?>

<div class="titleWrap">
    <?php
    if ($model->status == RouteInstance::STATUS_ACTIVE)
        echo CHtml::submitButton('Submit', array('class'=>'formButton submit-button')); ?>
    <ul>
        <li class="seperator">
            <img height="16" width="16" alt="add" src="img/icons/cancel.png">
            <a href="#"
               onclick="parent.$.colorbox.close(); return false;">Cancel</a>
        </li>
    </ul>

</div>

<?php
$this->endWidget(); ?>

<script type="text/javascript">
$(document).ready(function(){
   $(".submit-button").click(function(){
      // validate required fields
      $(".req").removeClass("error");
      $(".errorBox").hide();
      var error = false;
      $(".req").each(function(){
          if($.trim($(this).val()) == "")
          {
               error = true;
               $(this).addClass("error");
          }
      });
      if (error)
      {
        $(".errorBox").html("There are errors in the data entered, please make sure you fill all the highlighted fields.").show();
        return false;
      }
   });
});
</script>