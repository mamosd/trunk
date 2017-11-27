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
    echo $form->hiddenField($model, 'ClientTitleId');
    echo $form->errorSummary($model, "ClientTitleId", "", array('class'=>'errorBox'));
    ?>

    <div>
        <?php echo $form->labelEx($model,'ClientId'); ?>
        <?php 
        echo $form->dropDownList($model, 'ClientId', ClientTitleForm::getOptionsClientId());        
        ?>
    </div>

    <div>
        <?php echo $form->labelEx($model,'TitleId'); ?>
        <?php echo $form->textField($model,'TitleId', array('size'=>'35')); ?>
    </div>
    
    <div>
        <?php echo $form->labelEx($model,'TitleType'); ?>
        <?php 
        echo $form->dropDownList($model, 'TitleType', array(
                                    'M' => 'Supplement/Magazine',
                                    'S' => 'Standard',
                                ));        
        ?>
        
    </div>    

    <div>
        <?php echo $form->labelEx($model,'Name'); ?>
        <?php echo $form->textField($model,'Name', array('size'=>'45')); ?>
    </div>

    <div>
        <?php echo $form->labelEx($model,'IsLive'); ?>
        <?php echo $form->checkBox($model,'IsLive'); ?>
    </div>

<br/>

    <div class="titleWrap">
        <?php echo CHtml::submitButton('Submit', array('class'=>'formButton')); ?>
        <ul>
            <li class="seperator">
                <img height="16" width="16" alt="add" src="img/icons/cancel.png">
                <a href="<?php echo $this->createUrl('clientrouting/titles');?>"
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