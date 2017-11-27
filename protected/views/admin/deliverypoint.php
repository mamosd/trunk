<?php
    $this->breadcrumbs=array(
                array('label'=>'Home', 'url'=>array(Yii::app()->user->role->HomeUrl)),
                array('label'=>'Delivery Points', 'url'=>array('admin/deliverypoints')),
                array('label'=>'Delivery Point'),
            );
?>

<?php if (isset($model->deliveryPointId)) : ?>
<h1>Edit Delivery Point</h1>
<?php else : ?>
<h1>Add new Delivery Point</h1>
<?php endif; ?>

<div class="standardForm">
<?php $form=$this->beginWidget('CActiveForm', array(
            'id'=>'delivery-point-form',
            'errorMessageCssClass'=>'formError',
    )); ?>

    <?php
    echo $form->hiddenField($model, 'deliveryPointId');
    echo $form->errorSummary($model, "", "", array('class'=>'errorBox'));
    ?>

    <div>
        <?php echo $form->labelEx($model,'name'); ?>
        <?php echo $form->textField($model,'name', array('size'=>'50')); ?>
    </div>

    <div>
        <?php echo $form->labelEx($model,'address'); ?>
        <?php echo $form->textArea($model,'address', array('rows'=>'4', 'cols'=>'40')); ?>
    </div>

    <div>
        <?php echo $form->labelEx($model,'postalCode'); ?>
        <?php echo $form->textField($model,'postalCode', array('size'=>'10')); ?>
    </div>

    <div>
        <?php echo $form->labelEx($model,'county'); ?>
        <?php echo $form->textField($model,'county', array('size'=>'25')); ?>
    </div>

    <div>
        <?php echo $form->labelEx($model,'telephoneNumber'); ?>
        <?php echo $form->textField($model,'telephoneNumber', array('size'=>'25')); ?>
    </div>

    <div>
        <?php echo $form->labelEx($model,'deliveryComments'); ?>
        <?php echo $form->textArea($model,'deliveryComments', array('rows'=>'4', 'cols'=>'40')); ?>
    </div>
    
    <div>
        <?php echo $form->labelEx($model,'nq'); ?>
        <?php
        $accountStatus = array('Primary'=>'Primary', 'Secondary'=>'Secondary', 'Both'=>'Both');
        //echo $form->radioButtonList($model,'nq',$accountStatus,array('separator'=>' '));
        if ($model->nq == "Primary")
        {
            echo $form->checkBox($model,'isNQPrimary',array('checked'=>'checked'));
            echo " <label>NQ Primary</label>";

            echo $form->checkBox($model,'isNQSecondary');
            echo " <label>NQ Secondary</label>";
        }
        else if ($model->nq == "Secondary")
        {
            echo $form->checkBox($model,'isNQPrimary');
            echo " <label>NQ Primary</label>";

            echo $form->checkBox($model,'isNQSecondary',array('checked'=>'checked'));
            echo " <label>NQ Secondary</label>";            
        }
        else if ($model->nq == "All")
        {
            echo $form->checkBox($model,'isNQPrimary',array('checked'=>'checked'));
            echo " <label>NQ Primary</label>";

            echo $form->checkBox($model,'isNQSecondary',array('checked'=>'checked'));
            echo " <label>NQ Secondary</label>";            
        }
        else if ($model->nq == "")
        {
            echo $form->checkBox($model,'isNQPrimary');
            echo " <label>NQ Primary</label>";

            echo $form->checkBox($model,'isNQSecondary');
            echo " <label>NQ Secondary</label>";            
        }
        
        
        ?>
    </div>    
    

    <h1>Operating Times</h1>
    <table width="100%">
        <thead>
            <tr height="40px">
            <th></th>
            <th>Mon</th>
            <th>Tue</th>
            <th>Wed</th>
            <th>Thu</th>
            <th>Fri</th>
            <th>Sat</th>
            <th>Sun</th>
            </tr>
        </thead>
        
        <tbody>
            <tr height="40px">
                <?php
                    $start=strtotime('00:00');
                    $end=strtotime('23:30');
                    $arrayTime=array();
                    for ($halfhour=$start;$halfhour<=$end;$halfhour=$halfhour+30*60) {
                        $time = date('g:i a',$halfhour);
                        $arrayTime[$time]=$time;
                    }
                ?>
                <td><b>NPA Cut Off</b></td>
                <td>
                    <?php
                    echo $form->dropDownList($model,'NPAMon',$arrayTime); 
                    ?>
                </td>
                <td>
                    <?php
                    echo $form->dropDownList($model,'NPATue',$arrayTime); 
                    ?>
                </td>
                <td>
                    <?php
                    echo $form->dropDownList($model,'NPAWed',$arrayTime); 
                    ?>
                </td>
                <td>
                    <?php
                    echo $form->dropDownList($model,'NPAThu',$arrayTime); 
                    ?>
                </td>
                <td>
                    <?php
                    echo $form->dropDownList($model,'NPAFri',$arrayTime); 
                    ?>
                </td>
                <td>
                    <?php
                    echo $form->dropDownList($model,'NPASat',$arrayTime); 
                    ?>
                </td>
                <td>
                    <?php
                    echo $form->dropDownList($model,'NPASun',$arrayTime); 
                    ?>
                </td>
            </tr>
            
            <!--Opening hours-->
            <tr height="90px">
                <?php
                    $start=strtotime('00:00');
                    $end=strtotime('23:30');
                    $arrayTime=array();
                    for ($halfhour=$start;$halfhour<=$end;$halfhour=$halfhour+30*60) {
                        $time = date('g:i a',$halfhour);
                        $arrayTime[$time]=$time;
                    }
                ?>
                <td><b>Opening Hours</b></td>
                <td>
<script>
$(document).ready(function(){

<?php
$days=array('Mon','Tue','Wed','Thu','Fri','Sat','Sun');
foreach ( $days as $day )
{
?>
    $("#openingShowHide<?=$day?>").click(function(){
        
        if( $('#openingShowHide<?=$day?>:checked').length > 0 ) 
        {
            $("#openingDiv<?=$day?>1").html("<input type='hidden' name='DeliveryPointForm[OpeningStart<?=$day?>]' value='' />\n\
                                <input type='hidden' name='DeliveryPointForm[OpeningEnd<?=$day?>]' value='' />");
            $("#openingDiv<?=$day?>").hide();
        }
        else
        {
            $("#openingDiv<?=$day?>1").html("");
            $("#openingDiv<?=$day?>").show();            
        }
    });

    $('#openingShowHide<?=$day?>').each(function() {
        if( $('#openingShowHide<?=$day?>:checked').length > 0 ) {
            $("#openingDiv<?=$day?>").hide();
            $("#openingDiv<?=$day?>1").html("<input type='hidden' name='DeliveryPointForm[OpeningStart<?=$day?>]' value='' />\n\
                                <input type='hidden' name='DeliveryPointForm[OpeningEnd<?=$day?>]' value='' />");
        } else {
            
        }
    }); 

<?php
}
?>
});
</script>                    
                    <?php
                    if ( !empty( $model->OpeningStartMon ) && !empty ( $model->OpeningEndMon ) )
                    {echo "<input type='checkbox' id='openingShowHideMon' /> 24 Hours";}
                    else
                    {echo "<input type='checkbox' id='openingShowHideMon' checked='checked'/> 24 Hours";}
                    ?>
                    <br>
                    <div id="openingDivMon">
                    <?php
                    echo $form->dropDownList($model,'OpeningStartMon',$arrayTime, array('id'=>'OpeningStartMon'));
                    echo "<br> to <br>";
                    echo $form->dropDownList($model,'OpeningEndMon',$arrayTime, array('id'=>'OpeningEndMon'));
                    ?>
                    </div>
                    <div id="openingDivMon1">
                    </div>
                </td>
                <td>
                    <?php
                    if ( !empty( $model->OpeningStartTue ) && !empty ( $model->OpeningEndTue ) )
                    {echo "<input type='checkbox' id='openingShowHideTue' /> 24 Hours";}
                    else
                    {echo "<input type='checkbox' id='openingShowHideTue' checked='checked'/> 24 Hours";}
                    ?>
                    <br>
                    <div id="openingDivTue">
                    <?php
                    echo $form->dropDownList($model,'OpeningStartTue',$arrayTime, array('id'=>'OpeningStartTue'));
                    echo "<br> to <br>";
                    echo $form->dropDownList($model,'OpeningEndTue',$arrayTime, array('id'=>'OpeningEndTue'));
                    ?>
                    </div>
                    <div id="openingDivTue1">
                    </div>
                </td>
                <td>
                    <?php
                    if ( !empty( $model->OpeningStartWed ) && !empty ( $model->OpeningEndWed ) )
                    {echo "<input type='checkbox' id='openingShowHideWed' /> 24 Hours";}
                    else
                    {echo "<input type='checkbox' id='openingShowHideWed' checked='checked'/> 24 Hours";}
                    ?>
                    <br>
                    <div id="openingDivWed">
                    <?php
                    echo $form->dropDownList($model,'OpeningStartWed',$arrayTime, array('id'=>'OpeningStartWed'));
                    echo "<br> to <br>";
                    echo $form->dropDownList($model,'OpeningEndWed',$arrayTime, array('id'=>'OpeningEndWed'));
                    ?>
                    </div>
                    <div id="openingDivWed1">
                    </div>
                </td>
                <td>
                    <?php
                    if ( !empty( $model->OpeningStartThu ) && !empty ( $model->OpeningEndThu ) )
                    {echo "<input type='checkbox' id='openingShowHideThu' /> 24 Hours";}
                    else
                    {echo "<input type='checkbox' id='openingShowHideThu' checked='checked'/> 24 Hours";}
                    ?>
                    <br>
                    <div id="openingDivThu">
                    <?php
                    echo $form->dropDownList($model,'OpeningStartThu',$arrayTime, array('id'=>'OpeningStartThu'));
                    echo "<br> to <br>";
                    echo $form->dropDownList($model,'OpeningEndThu',$arrayTime, array('id'=>'OpeningEndThu'));
                    ?>
                    </div>
                    <div id="openingDivThu1">
                    </div>
                </td>
                <td>
                    <?php
                    if ( !empty( $model->OpeningStartFri ) && !empty ( $model->OpeningEndFri ) )
                    {echo "<input type='checkbox' id='openingShowHideFri' /> 24 Hours";}
                    else
                    {echo "<input type='checkbox' id='openingShowHideFri' checked='checked'/> 24 Hours";}
                    ?>
                    <br>
                    <div id="openingDivFri">
                    <?php
                    echo $form->dropDownList($model,'OpeningStartFri',$arrayTime, array('id'=>'OpeningStartFri'));
                    echo "<br> to <br>";
                    echo $form->dropDownList($model,'OpeningEndFri',$arrayTime, array('id'=>'OpeningEndFri'));
                    ?>
                    </div>
                    <div id="openingDivFri1">
                    </div>
                </td>
                <td>
                    <?php
                    if ( !empty( $model->OpeningStartSat ) && !empty ( $model->OpeningEndSat ) )
                    {echo "<input type='checkbox' id='openingShowHideSat' /> 24 Hours";}
                    else
                    {echo "<input type='checkbox' id='openingShowHideSat' checked='checked'/> 24 Hours";}
                    ?>
                    <br>
                    <div id="openingDivSat">
                    <?php
                    echo $form->dropDownList($model,'OpeningStartSat',$arrayTime, array('id'=>'OpeningStartSat'));
                    echo "<br> to <br>";
                    echo $form->dropDownList($model,'OpeningEndSat',$arrayTime, array('id'=>'OpeningEndSat'));
                    ?>
                    </div>
                    <div id="openingDivSat1">
                    </div>
                </td>
                <td>
                    <?php
                    if ( !empty( $model->OpeningStartSun ) && !empty ( $model->OpeningEndSun ) )
                    {echo "<input type='checkbox' id='openingShowHideSun' /> 24 Hours";}
                    else
                    {echo "<input type='checkbox' id='openingShowHideSun' checked='checked'/> 24 Hours";}
                    ?>
                    <br>
                    <div id="openingDivSun">
                    <?php
                    echo $form->dropDownList($model,'OpeningStartSun',$arrayTime, array('id'=>'OpeningStartSun'));
                    echo "<br> to <br>";
                    echo $form->dropDownList($model,'OpeningEndSun',$arrayTime, array('id'=>'OpeningEndSun'));
                    ?>
                    </div>
                    <div id="openingDivSun1">
                    </div>
                </td>
            </tr>
            
            <!--Closed to-->
            <tr>
                <?php
                    $start=strtotime('00:00');
                    $end=strtotime('23:30');
                    $arrayTime=array();
                    for ($halfhour=$start;$halfhour<=$end;$halfhour=$halfhour+30*60) {
                        $time = date('g:i a',$halfhour);
                        $arrayTime[$time]=$time;
                    }
                ?>
                <td><b>Closed time</b></td>
                <td>
<script>
$(document).ready(function(){

<?php
$days=array('Mon','Tue','Wed','Thu','Fri','Sat','Sun');
foreach ( $days as $day )
{
?>
    $("#closingShowHide<?=$day?>").click(function(){
        
        if( $('#closingShowHide<?=$day?>:checked').length > 0 ) 
        {
            $("#closingDiv<?=$day?>1").html("<input type='hidden' name='DeliveryPointForm[ClosingStart<?=$day?>]' value='' />\n\
                                <input type='hidden' name='DeliveryPointForm[ClosingEnd<?=$day?>]' value='' />");
            $("#closingDiv<?=$day?>").hide();
        }
        else
        {
            $("#closingDiv<?=$day?>1").html("");
            $("#closingDiv<?=$day?>").show();            
        }
    });

    $('#closingShowHide<?=$day?>').each(function() {
        if( $('#closingShowHide<?=$day?>:checked').length > 0 ) {
            $("#closingDiv<?=$day?>").hide();
            $("#closingDiv<?=$day?>1").html("<input type='hidden' name='DeliveryPointForm[ClosingStart<?=$day?>]' value='' />\n\
                                <input type='hidden' name='DeliveryPointForm[ClosingEnd<?=$day?>]' value='' />");
        } else {
            
        }
    }); 

<?php
}
?>
});
</script>                  
                    <?php
                    if ( !empty( $model->ClosingStartMon ) && !empty ( $model->ClosingEndMon ) )
                    {echo "<input type='checkbox' id='closingShowHideMon' /> 24 Hours";}
                    else
                    {echo "<input type='checkbox' id='closingShowHideMon' checked='checked'/> 24 Hours";}
                    ?>
                    <br>
                    <div id="closingDivMon">
                    <?php
                    echo $form->dropDownList($model,'ClosingStartMon',$arrayTime, array('id'=>'ClosingStartMon'));
                    echo "<br> to <br>";
                    echo $form->dropDownList($model,'ClosingEndMon',$arrayTime, array('id'=>'ClosingEndMon'));
                    ?>
                    </div>
                    <div id="closingDivMon1">
                    </div>
                </td>
                <td>
                    <?php
                    if ( !empty( $model->ClosingStartTue ) && !empty ( $model->ClosingEndTue ) )
                    {echo "<input type='checkbox' id='closingShowHideTue' /> 24 Hours";}
                    else
                    {echo "<input type='checkbox' id='closingShowHideTue' checked='checked'/> 24 Hours";}
                    ?>
                    <br>
                    <div id="closingDivTue">
                    <?php
                    echo $form->dropDownList($model,'ClosingStartTue',$arrayTime, array('id'=>'ClosingStartTue'));
                    echo "<br> to <br>";
                    echo $form->dropDownList($model,'ClosingEndTue',$arrayTime, array('id'=>'ClosingEndTue'));
                    ?>
                    </div>
                    <div id="closingDivTue1">
                    </div>
                </td>
                <td>
                    <?php
                    if ( !empty( $model->ClosingStartWed ) && !empty ( $model->ClosingEndWed ) )
                    {echo "<input type='checkbox' id='closingShowHideWed' /> 24 Hours";}
                    else
                    {echo "<input type='checkbox' id='closingShowHideWed' checked='checked'/> 24 Hours";}
                    ?>
                    <br>
                    <div id="closingDivWed">
                    <?php
                    echo $form->dropDownList($model,'ClosingStartWed',$arrayTime, array('id'=>'ClosingStartWed'));
                    echo "<br> to <br>";
                    echo $form->dropDownList($model,'ClosingEndWed',$arrayTime, array('id'=>'ClosingEndWed'));
                    ?>
                    </div>
                    <div id="closingDivWed1">
                    </div>
                </td>
                <td>
                    <?php
                    if ( !empty( $model->ClosingStartThu ) && !empty ( $model->ClosingEndThu ) )
                    {echo "<input type='checkbox' id='closingShowHideThu' /> 24 Hours";}
                    else
                    {echo "<input type='checkbox' id='closingShowHideThu' checked='checked'/> 24 Hours";}
                    ?>
                    <br>
                    <div id="closingDivThu">
                    <?php
                    echo $form->dropDownList($model,'ClosingStartThu',$arrayTime, array('id'=>'ClosingStartThu'));
                    echo "<br> to <br>";
                    echo $form->dropDownList($model,'ClosingEndThu',$arrayTime, array('id'=>'ClosingEndThu'));
                    ?>
                    </div>
                    <div id="closingDivThu1">
                    </div>
                </td>
                <td>
                    <?php
                    if ( !empty( $model->ClosingStartFri ) && !empty ( $model->ClosingEndFri ) )
                    {echo "<input type='checkbox' id='closingShowHideFri' /> 24 Hours";}
                    else
                    {echo "<input type='checkbox' id='closingShowHideFri' checked='checked'/> 24 Hours";}
                    ?>
                    <br>
                    <div id="closingDivFri">
                    <?php
                    echo $form->dropDownList($model,'ClosingStartFri',$arrayTime, array('id'=>'ClosingStartFri'));
                    echo "<br> to <br>";
                    echo $form->dropDownList($model,'ClosingEndFri',$arrayTime, array('id'=>'ClosingEndFri'));
                    ?>
                    </div>
                    <div id="closingDivFri1">
                    </div>
                </td>
                <td>
                    <?php
                    if ( !empty( $model->ClosingStartSat ) && !empty ( $model->ClosingEndSat ) )
                    {echo "<input type='checkbox' id='closingShowHideSat' /> 24 Hours";}
                    else
                    {echo "<input type='checkbox' id='closingShowHideSat' checked='checked'/> 24 Hours";}
                    ?>
                    <br>
                    <div id="closingDivSat">
                    <?php
                    echo $form->dropDownList($model,'ClosingStartSat',$arrayTime, array('id'=>'ClosingStartSat'));
                    echo "<br> to <br>";
                    echo $form->dropDownList($model,'ClosingEndSat',$arrayTime, array('id'=>'ClosingEndSat'));
                    ?>
                    </div>
                    <div id="closingDivSat1">
                    </div>
                </td>
                <td>
                    <?php
                    if ( !empty( $model->ClosingStartSun ) && !empty ( $model->ClosingEndSun ) )
                    {echo "<input type='checkbox' id='closingShowHideSun' /> 24 Hours";}
                    else
                    {echo "<input type='checkbox' id='closingShowHideSun' checked='checked'/> 24 Hours";}
                    ?>
                    <br>
                    <div id="closingDivSun">
                    <?php
                    echo $form->dropDownList($model,'ClosingStartSun',$arrayTime, array('id'=>'ClosingStartSun'));
                    echo "<br> to <br>";
                    echo $form->dropDownList($model,'ClosingEndSun',$arrayTime, array('id'=>'ClosingEndSun'));
                    ?>
                    </div>
                    <div id="closingDivSun1">
                    </div>
                </td>
            </tr>
            
            
        </tbody>
        
    </table>
    <br>
    
    <div class="titleWrap">
        <?php echo CHtml::submitButton('Save', array('class'=>'formButton', 'name'=>'save')); ?>
        <?php echo CHtml::submitButton('Save and Exit', array('class'=>'formButton', 'name'=>'saveandexit')); ?>
        <ul>
            <li class="seperator">
                <img height="16" width="16" alt="add" src="img/icons/cancel.png">
                <a href="<?php echo $this->createUrl('admin/deliverypoints');?>"
                   <?php if("popUp" === $ui):
                       echo "onclick='parent.$.colorbox.close(); return false;'";
                       endif;
                   ?>
                   >Cancel</a>
            </li>
        </ul>
    </div>

    <?php
    if ( isset( $_GET['id'] ) )
    {
    ?>
    <h1>Add contact</h1>
    
    <div class="titleWrap">
        <ul>
        <li class="seperator">
            <img height="16" width="16" alt="add" src="img/icons/add.png">
                <a href="<?php echo $this->createUrl('admin/deliverypointContact', array('ui'=>'popUp','DeliveryPointId'=>$_GET['id']));?>" title="Add new Contact" class="colorBoxIFrame">Add Contact</a>
        </li>
    </ul>
    </div>
    <?php
    }
    ?>
    
<?php
$this->endWidget(); ?>

    
    
  
<?php
if ( isset( $_GET['id'] ) )
{
?>    
<table class="listing fluid" cellpadding="0" cellspacing="0">
<tbody>
    <tr>
      <th>Type</th>  
      <th>Department</th>
      <th>Name</th>
      <th>Surname</th>
      <th>Telephone Number</th>
      <th>Mobile Number</th>
      <th>Email</th>
      <th>Actions</th>
    </tr>

    <?php
    $count = count($contacts);
    for($i = 0; $i < $count; $i++):
    ?>
        <tr class="row<?php echo (($i%2)+1) ?>">
          <td class="route-name"><?php echo $contacts[$i]->type ?></td>
          <td class="route-name"><?php echo $contacts[$i]->department ?></td>
          <td class="route-name"><?php echo $contacts[$i]->name ?></td>
          <td class="route-name"><?php echo $contacts[$i]->surname ?></td>
          <td class="route-name"><?php echo $contacts[$i]->telNumber ?></td>
          <td class="route-name"><?php echo $contacts[$i]->mobileNumber ?></td>
          <td class="route-name"><?php echo $contacts[$i]->email ?></td>
          <td style="white-space: nowrap;">
                <!--              
              <a href="<?php //echo $this->createUrl('admin/route', array('id'=>$contacts[$i]->RouteId));?>">
              <img src="img/icons/page_edit.png" alt="edit" width="16" height="16" />
              Edit</a>
              -->
              
              <a class="lnkDelete"
                  href="<?php echo $this->createUrl('admin/deliveryPointcontactdelete', array('contactid'=>$contacts[$i]->ContactId,'deliverypointid'=>$_GET['id']));?>">
              <img src="img/icons/cancel.png" alt="delete" width="16" height="16" />
              Delete</a>
          </td>
        </tr>
    <?php
    endfor;

    if ($count === 0) :
    ?>
        <tr class="row1">
            <td colspan="4">
                <div class="infoBox">There are no contacts under this print centre.</div>
            </td>
        </tr>
    <?php
    endif;
    ?>
</tbody>
</table>
<?php
}
else
{
    echo '<div class="infoBox">Print centre should be added before assigning contacts to it.</div>';
}
?> 
    
    
    
    
    
</div>


<script>

    $(document).ready(function(){
    
        $(".colorBoxIFrame").colorbox({width:"500px", height:"600px", iframe:true, onClosed:reloadDropDowns});

    });

    function reloadDropDowns() {
        $.getJSON("<?php echo $this->createUrl('admin/route', array('json'=>'dds')) ?>", function(data){
            $("#ddlDummyTitle option:gt(0)").remove();
            $.each(data.titles, function(idx, title){
               $("<option>").attr("value", title.value).text(title.text).appendTo("#ddlDummyTitle");
            });

            $("#ddlDummyDelPoint option:gt(0)").remove();
            $.each(data.delpoints, function(idx, delpoint){
               $("<option>").attr("value", delpoint.value).text(delpoint.text).appendTo("#ddlDummyDelPoint");
            });

            $("select[name^='RouteDetails\\[title']").each(function(idx){
               var prevVal = $(this).val();
               var curList = $(this);
               $(this).find("option:gt(0)").remove();
               $("#ddlDummyTitle option:gt(0)").each(function(idx){
                    $(this).clone().appendTo(curList);
               });
               $(this).val(prevVal);
            });

            $("select[name^='RouteDetails\\[delpoint']").each(function(idx){
               var prevVal = $(this).val();
               var curList = $(this);
               $(this).find("option:gt(0)").remove();
               $("#ddlDummyDelPoint option:gt(0)").each(function(idx){
                    $(this).clone().appendTo(curList);
               });
               $(this).val(prevVal);
            });
            
            location.reload();
        });
    }

</script>

