<?php
/* @var $this AccountController */
/* @var $model LoginForm */
?>
<?php
$this->pageTitle=Yii::app()->name . ' - Change Password';
$this->breadcrumbs=array(
    'Change Password',
);
?>
<!--<h2>Hi! :v</h2>-->
<div class="form">
    <h2>Change Password</h2>
<?php $form=$this->beginWidget('CActiveForm', array(
    'id'=>'PWReset-form',
    'errorMessageCssClass'=>'formError',
)); ?>
 
    
    <?php echo $form->errorSummary($formmodel, "", "", array('class'=>'errorBox'));?>

    <?php if(Yii::app()->user->hasFlash('PWStrength')):?>
        <div class="errorBox" id="login-form_es_">        
            <?php echo Yii::app()->user->getFlash('PWStrength'); ?>
        </div>
    <?php elseif(Yii::app()->user->hasFlash('PWHints')):?>
        <div class="infoBox" id="login-form_es_">
            <?php echo Yii::app()->user->getFlash('PWHints'); ?>
        </div>
    <?php endif; ?>
    
    <div class="row">
<!--            New Password : <input name="PWReset[password]" id="ContactForm_email" type="password">
            <input name="PWReset[tokenhid]" id="ContactForm_email" type="hidden" value="<?php echo $model->token?>">-->
        
        
        <?php echo $form->hiddenField($formmodel,'tokenhid',array('value'=>$model->token)); ?>
        
        <?php echo $form->labelEx($formmodel,'newPassword'); ?>
        <?php echo $form->passwordField($formmodel,'newPassword'); ?>
        
        <?php echo $form->labelEx($formmodel,'newPassword2'); ?>
        <?php echo $form->passwordField($formmodel,'newPassword2'); ?>
        
    </div>
 
    <div class="row buttons">
        <?php echo CHtml::submitButton('Submit'); ?>
    </div>
 
<?php $this->endWidget(); ?>
 
</div><!-- form -->