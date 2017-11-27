<?php
/* @var $this AccountController */
/* @var $model LoginForm */
$this->pageTitle=Yii::app()->name . ' - Login';
?>

<div id="login">
    <h1>Login</h1>

    <?php $form=$this->beginWidget('CActiveForm', array(
            'id'=>'login-form',
            'enableAjaxValidation'=>true,
            'errorMessageCssClass'=>'formError',
    )); ?>

    <?php echo $form->errorSummary($model, "", "", array('class'=>'errorBox')); ?>

    <div>
        <?php echo $form->labelEx($model,'username'); ?>
        <?php echo $form->textField($model,'username'); ?>
    </div>
    
    <div>
        <?php echo $form->labelEx($model,'password'); ?>
        <?php echo $form->passwordField($model,'password'); ?>
    </div>

    <div class="cbcontainer">
        <?php echo $form->checkBox($model,'rememberMe'); ?>
        <?php echo $form->label($model,'rememberMe', array('class'=>'inline')); ?>
    </div>

    <div class="cbcontainer">
        <?php echo CHtml::link('Forgot my password', $this->createAbsoluteUrl('account/forgotpassword')); ?>
    </div> 
    
        <div id="loginFormSubmitWrap"><!--We need to use this div as a IE6 fix to prevent and mis-floats-->
            <div class="loginFormSubmit"><!--We need to use this div to keep the "submit button" and the "loader image" together.-->
                    <?php echo CHtml::submitButton('Login'); ?>
            </div>
        </div>
    <?php $this->endWidget(); ?>
</div>

<script>
$(function(){
    $("#login-form").attr('action', $(location).attr('href'));
})
</script>