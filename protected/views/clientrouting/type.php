<?php
    $this->breadcrumbs=array(
                array('label'=>'Home', 'url'=>array(Yii::app()->user->role->HomeUrl)),
                array('label'=>'Titles', 'url'=>array('clientrouting/titles')),
                array('label'=>'Title'),
            );
?>

<?php if (isset($model->titleId)) : ?>
<h1>Edit Title</h1>
<?php else : ?>
<h1>Add new Title</h1>
<?php endif; ?>

<div class="standardForm">
<?php $form=$this->beginWidget('CActiveForm', array(
            'id'=>'title-form',
            'errorMessageCssClass'=>'formError',
    )); ?>

    <?php
    echo $form->hiddenField($model, 'titleId');
    echo $form->errorSummary($model, "", "", array('class'=>'errorBox'));
    ?>

    <div>
        <?php echo $form->labelEx($model,'name'); ?>
        <?php echo $form->textField($model,'name', array('size'=>'35')); ?>
    </div>

    <div>
        <?php echo $form->labelEx($model,'isLive'); ?>
        <?php echo $form->checkBox($model,'isLive'); ?>
    </div>

<br/>

    <div class="titleWrap">
        <?php echo CHtml::submitButton('Submit', array('class'=>'formButton')); ?>
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