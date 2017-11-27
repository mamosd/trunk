<?php
    $this->breadcrumbs=array(
                array('label'=>'Home', 'url'=>array(Yii::app()->user->role->HomeUrl)),
                array('label'=>'Titles', 'url'=>array('admin/titles')),
                array('label'=>'Title'),
            );
?>

<?php if (isset($model->titleId)) : ?>
<h1>Edit Contact</h1>
<?php else : ?>
<h1>Add new Contact</h1>
<?php endif; ?>

<div class="standardForm">
<?php $form=$this->beginWidget('CActiveForm', array(
            'id'=>'PrintCentreContactForm',
            'errorMessageCssClass'=>'formError',
    )); ?>

    <?php
    if(isset ( $_GET['PrintCentreId'] ))
    {echo $form->hiddenField($model, 'PrintCentreId',array('value'=>$_GET['PrintCentreId']));}
    else if(isset ( $_GET['DeliveryPointId'] ))
    {echo $form->hiddenField($model, 'DeliveryPointId',array('value'=>$_GET['DeliveryPointId']));}
    ?>
    <?php
    //echo $form->hiddenField($model, 'PrintCentreId',array('value'=>$_GET['PrintCentreId']));
    echo $form->errorSummary($model, "", "", array('class'=>'errorBox'));
    ?>

    <div>
        <?php echo $form->labelEx($model,'type'); ?>
        <?php echo $form->dropDownList($model, 'type', array('day'=>'Day','night'=>'Night'), array('empty'=>'select one ->')); ?>
    </div>
    
    <div>
        <?php echo $form->labelEx($model,'department'); ?>
        <?php echo $form->textField($model,'department', array('size'=>'35')); ?>
    </div>
   
    <div>
        <?php echo $form->labelEx($model,'name'); ?>
        <?php echo $form->textField($model,'name', array('size'=>'20')); ?>
    </div>
    
    <div>
        <?php echo $form->labelEx($model,'surname'); ?>
        <?php echo $form->textField($model,'surname', array('size'=>'15')); ?>
    </div>
    
    <div>
        <?php echo $form->labelEx($model,'telNumber'); ?>
        <?php echo $form->textField($model,'telNumber', array('size'=>'35')); ?>
    </div>    

    <div>
        <?php echo $form->labelEx($model,'mobileNumber'); ?>
        <?php echo $form->textField($model,'mobileNumber', array('size'=>'35')); ?>
    </div>    
    
    <div>
        <?php echo $form->labelEx($model,'email'); ?>
        <?php echo $form->textField($model,'email', array('size'=>'35')); ?>
    </div>    
    
<br/>

    <div class="titleWrap">
        <?php echo CHtml::submitButton('Save', array('class'=>'formButton', 'name'=>'save')); ?>
        
        <ul>
            <li class="seperator">
                <img height="16" width="16" alt="add" src="img/icons/cancel.png">
                <a href="<?php echo $this->createUrl('admin/titles');?>"
                   <?php if("popUp" === $ui):
                       echo "onclick='parent.$.colorbox.close(); return false;'";
                       endif;
                   ?>

                   >Cancel</a>
            </li>
        </ul>

    </div>

<?php
$this->endWidget(); ?>
</div>