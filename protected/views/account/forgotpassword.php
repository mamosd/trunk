<?php
/* @var $this AccountController */
/* @var $model LoginForm */
?>

<?php
$this->pageTitle=Yii::app()->name . ' - Login';
?>

<div id="login">
    <h1>Password Reset</h1>

    <?php $form=$this->beginWidget('CActiveForm', array(
            'id'=>'pw-form',
            'enableAjaxValidation'=>true,
            'errorMessageCssClass'=>'formError',
    )); ?>

    <?php echo $form->errorSummary($model, "", "", array('class'=>'errorBox')); ?>

    
    <?php if(Yii::app()->user->hasFlash('PWResetLinkSent')):?>
        <div class="successBox" id="login-form_es_">
        <?php echo Yii::app()->user->getFlash('PWResetLinkSent'); ?>
        </div>
    <?php endif; ?>
    
    <?php if(Yii::app()->user->hasFlash('userOrEmail')):?>
        <div class="errorBox" id="login-form_es_">
        <ul>
        <?php
        //foreach(Yii::app()->user->getFlashes() as $key => $message) 
        //{
            //echo '<li>' . $message . "</li>";
        //}
        ?>
            
            <li><?php echo Yii::app()->user->getFlash('userOrEmail'); ?></li>
        </ul>
        </div>
    <?php endif; ?>
    
    <div>
        <?php echo CHtml::label('Username or Email', false); ?>
        <?php echo CHtml::textField('usernameORemail', '', array('onkeyup'=>'validateUser(this.value)','onkeydown'=>'validateUser(this.value)','onclick'=>'validateUser(this.value)','onchange'=>'validateUser(this.value)'));?>
    
        <div id="checkUserExist" class="checkUserExist">
            <?php
            //echo CHtml::image('img/icons/accept.png');
            ?>
        </div>        
        
        
    </div>

   
    <br/>
    
        <div id="loginFormSubmitWrap"><!--We need to use this div as a IE6 fix to prevent and mis-floats-->
            <div class="loginFormSubmit"><!--We need to use this div to keep the "submit button" and the "loader image" together.-->
                    <?php echo CHtml::submitButton('Submit'); ?>
            </div>
        </div>
    <?php $this->endWidget(); ?>
</div>





<script>
function validateUser(userdata) {
    // 1. Create XHR instance - Start
    var xhr;
    if (window.XMLHttpRequest) {
        xhr = new XMLHttpRequest();
    }
    else if (window.ActiveXObject) {
        xhr = new ActiveXObject("Msxml2.XMLHTTP");
    }
    else {
        throw new Error("Ajax is not supported by this browser");
    }
    // 1. Create XHR instance - End
    
    // 2. Define what to do when XHR feed you the response from the server - Start
    xhr.onreadystatechange = function () {
        if (xhr.readyState === 4) {
            if (xhr.status == 200 && xhr.status < 300) {
                document.getElementById('checkUserExist').innerHTML = xhr.responseText;
            }
        }
    }
    // 2. Define what to do when XHR feed you the response from the server - Start

    var userdata = userdata;

    // 3. Specify your action, location and Send to the server - Start 
    xhr.open('POST', '<?php echo $this->createUrl('account/checkuserexist');?>');
    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    xhr.send("userdata=" + userdata);
    // 3. Specify your action, location and Send to the server - End
}
</script>