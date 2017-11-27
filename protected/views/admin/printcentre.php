<?php
    $this->breadcrumbs=array(
                array('label'=>'Home', 'url'=>array(Yii::app()->user->role->HomeUrl)),
                array('label'=>'Print Centres', 'url'=>array('admin/printcentres')),
                array('label'=>'Print Centre'),
            );
$baseUrl = Yii::app()->request->baseUrl;
    ?>

<?php if (isset($model->printCentreId)) : ?>
<h1>Edit Print Centre</h1>
<?php else : ?>
<h1>Add new Print Centre</h1>
<?php endif; ?>

<div class="standardForm">
<!--    if editing print centre, then show confirmation popup-->
<?php
if ( isset( $_GET['id'] ) )
{
    $form=$this->beginWidget('CActiveForm', array(
                'id'=>'print-centre-form',
                "errorMessageCssClass"=>'formError',
                 'htmlOptions'=>array('onsubmit'=>"return confirm('Do you want to save the changes?')")
        )); 
}
else
{
    $form=$this->beginWidget('CActiveForm', array(
                'id'=>'print-centre-form',
                "errorMessageCssClass"=>'formError'
        ));    
}
?>

    <?php
    echo $form->hiddenField($model, 'printCentreId');
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
        <?php echo $form->labelEx($model,'postalcode'); ?>
        <?php echo $form->textField($model,'postalcode', array('size'=>'10')); ?>
    </div>
    
    <div>
        <?php echo $form->labelEx($model,'county'); ?>
        <?php echo $form->textField($model,'county', array('size'=>'20')); ?>
    </div>
    
    <div>
        <?php echo $form->labelEx($model,'enabled'); ?>
        <?php echo $form->checkBox($model,'enabled'); ?>
    </div>
    
    <div class="titleWrap">
        <?php echo CHtml::submitButton('Save', array('class'=>'formButton', 'name'=>'save')); ?>
        <?php echo CHtml::submitButton('Save and Exit', array('class'=>'formButton', 'name'=>'saveandexit')); ?>
        <ul>
            <li class="seperator">
                <img height="16" width="16" alt="add" src="img/icons/cancel.png">
                <a href="<?php echo $this->createUrl('admin/printcentres');?>">Cancel</a>
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
                <a href="<?php echo $this->createUrl('admin/contact', array('ui'=>'popUp','PrintCentreId'=>$_GET['id']));?>" title="Add new Contact" class="colorBoxIFrame">Add Contact</a>
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
      <th width="10%">Actions</th>
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
                  href="<?php echo $this->createUrl('admin/contactdelete', array('contactid'=>$contacts[$i]->ContactId,'printcentreid'=>$_GET['id']));?>">
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