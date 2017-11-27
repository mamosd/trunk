<?php
    $this->breadcrumbs=array(
                array('label'=>'Home', 'url'=>array(Yii::app()->user->role->HomeUrl)),
                array('label'=>'Client Routing', 'url' => '#'),
                array('label'=>'Import Order File')
            );
?>

<h1>Import Order File</h1>

<div class="standardForm">
    <?php echo CHtml::form('','post',array('enctype'=>'multipart/form-data'));

    echo CHtml::errorSummary($model, "", "", array('class'=>'errorBox'));
    ?>
    
    <?php if (!empty($message)): ?>
    <div class="successBox">
        <?php echo $message; ?>
    </div>
    <?php endif; ?>

    <div>
        <?php echo CHtml::activeLabelEx($model,'clientId'); ?>
        <?php echo CHtml::activeDropDownList($model,'clientId', $model->getClientOptions()); //, array('empty' => '-- select one') ?>
    </div>
    
    <div>
        <?php echo CHtml::activeLabelEx($model,'orderFile'); ?>
        <?php echo CHtml::activeFileField($model,'orderFile'); ?>
    </div>
    
    <div class="titleWrap">
        <?php echo CHtml::submitButton('Upload', array('class'=>'formButton')); ?>
    </div>

    <?php echo CHtml::endForm(); ?>
</div>

