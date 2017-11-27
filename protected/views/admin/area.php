<?php
    $this->breadcrumbs=array(
                array('label'=>'Home', 'url'=>array(Yii::app()->user->role->HomeUrl)),
                array('label'=>'Areas', 'url'=>array('admin/areas')),
                array('label'=>'Area'),
            );
?>

<?php if (isset($model->areaId)) : ?>
<h1>Edit Area</h1>
<?php else : ?>
<h1>Add new Area</h1>
<?php endif; ?>

<div class="standardForm">
<?php $form=$this->beginWidget('CActiveForm', array(
            'id'=>'area-form',
            'errorMessageCssClass'=>'formError',
    )); ?>

    <?php 
    echo $form->hiddenField($model, 'areaId');
    echo $form->errorSummary($model, "", "", array('class'=>'errorBox'));
    ?>

    <div>
        <?php echo $form->labelEx($model,'name'); ?>
        <?php echo $form->textField($model,'name', array('size'=>'50')); ?>
    </div>

    <div class="titleWrap">
        <?php echo CHtml::submitButton('Submit', array('class'=>'formButton')); ?>
        <ul>
            <li class="seperator">
                <img height="16" width="16" alt="add" src="img/icons/cancel.png">
                <a href="<?php echo $this->createUrl('admin/areas');?>">Cancel</a>
            </li>
        </ul>
    </div>
<?php
$this->endWidget(); ?>
</div>