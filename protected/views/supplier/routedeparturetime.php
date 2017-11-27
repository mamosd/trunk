<h1>Departure Time</h1>
<p>Enter the departure time for the route</p>

<div class="standardForm">
<?php $form=$this->beginWidget('CActiveForm', array(
            'id'=>'route-form',
            'errorMessageCssClass'=>'formError',
    )); ?>

    <?php
    echo $form->hiddenField($model, 'routeInstanceId');
    echo $form->errorSummary($model, "", "", array('class'=>'errorBox'));
    ?>

    <div>
        <?php echo $form->labelEx($model,'routeName'); ?>
        <?php echo $form->textField($model,'routeName', array('size'=>'35', 'class'=>'readOnlyField', 'tabindex'=>'-1', 'readonly'=>'readonly')); ?>
    </div>

    <div>
        <?php echo $form->labelEx($model,'date'); ?>
        <?php echo $form->textField($model,'date', array('size'=>'10', 'class'=>'readOnlyField', 'tabindex'=>'-1', 'readonly'=>'readonly')); ?>
    </div>

    <div>
        <?php echo $form->labelEx($model,'departureTime'); ?>
        <?php echo $form->textField($model,'departureTime', array('size'=>'15')); ?>
    </div>

    <div class="titleWrap">
        <?php echo CHtml::submitButton('Submit', array('class'=>'formButton')); ?>
        <ul>
            <li class="seperator">
                <img height="16" width="16" alt="add" src="img/icons/cancel.png">
                <a href="#"
                   onclick="parent.$.colorbox.close(); return false;">Cancel</a>
            </li>
        </ul>

    </div>

<?php
$this->endWidget(); ?>
</div>

<script type="text/javascript">
$(document).ready(function(){
    $("#SupplierDepartureTimeForm_departureTime").select();
});
</script>