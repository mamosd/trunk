<?php
    $this->breadcrumbs=array(
                array('label'=>'Home', 'url'=>array(Yii::app()->user->role->HomeUrl)),
                array('label'=>'Suppliers', 'url'=>array('admin/suppliers')),
                array('label'=>'Supplier'),
            );
?>

<?php if (isset($model->supplierId)) : ?>
<h1>Edit Supplier</h1>
<?php else : ?>
<h1>Add new Supplier</h1>
<?php endif; ?>

<div class="standardForm">
<?php $form=$this->beginWidget('CActiveForm', array(
            'id'=>'supplier-form',
            'errorMessageCssClass'=>'formError',
    )); ?>

    <?php 
    echo $form->hiddenField($model, 'supplierId');
    echo $form->errorSummary($model, "", "", array('class'=>'errorBox'));
    ?>

    <div>
        <?php echo $form->labelEx($model,'name'); ?>
        <?php echo $form->textField($model,'name', array('size'=>'50')); ?>
    </div>

    <div>
        <?php echo $form->labelEx($model,'contactPerson'); ?>
        <?php echo $form->textField($model,'contactPerson', array('size'=>'50')); ?>
    </div>

    <div>
        <?php echo $form->labelEx($model,'telephoneNumber'); ?>
        <?php echo $form->textField($model,'telephoneNumber', array('size'=>'10')); ?>
    </div>

    <div>
        <?php echo $form->labelEx($model,'LandlineNumber'); ?>
        <?php echo $form->textField($model,'LandlineNumber', array('size'=>'10')); ?>
    </div>

    <div>
        <?php echo $form->labelEx($model,'Email'); ?>
        <?php echo $form->textField($model,'Email', array('size'=>'25')); ?>
    </div>
    
    <div>
        <?php echo $form->labelEx($model,'isLive'); ?>
        <?php echo $form->checkBox($model,'isLive'); ?>
    </div>

    <div class="titleWrap">
        <?php echo CHtml::submitButton('Submit', array('class'=>'formButton')); ?>
        <ul>
            <li class="seperator">
                <img height="16" width="16" alt="add" src="img/icons/cancel.png">
                <a href="<?php echo $this->createUrl('admin/suppliers');?>">Cancel</a>
            </li>
        </ul>

    </div>


<?php
$this->endWidget(); ?>
</div>