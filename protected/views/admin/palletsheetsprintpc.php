<?php
    $this->breadcrumbs=array(
                array('label'=>'Home', 'url'=>array(Yii::app()->user->role->HomeUrl)),
                array('label'=>'Pallets', 'url'=>'#'),
                array('label'=>'Print Print Centre Sheets'),
            );
?>
    
<h1>Print Pallet Sheets per Print Centre</h1>

<div class="standardForm">
<?php $form=$this->beginWidget('CActiveForm', array(
            'id'=>'sheets-form',
            'errorMessageCssClass'=>'formError',
            'htmlOptions' => array('target' => '_blank')
    )); ?>

    <?php
    echo $form->errorSummary($model, "", "", array('class'=>'errorBox'));
    ?>

    <div>
        <?php echo $form->labelEx($model,'printCentreId'); ?>
        <?php echo $form->dropDownList($model, 'printCentreId', $model->getOptionsPrintCentre(), array('empty'=>'select one ->')); ?>
        
    </div>
    <br/>
    <div class="titleWrap">
        <?php echo CHtml::submitButton('Print', array('class'=>'formButton', 'id'=>'btnSubmit')); ?>
    </div>

<?php
$this->endWidget(); ?>
</div>
    
<script>
$(function(){
    $("#btnSubmit").click(function(){
        if ($("#PalletsSheetPrintPC_printCentreId").val() == "")
            {
                alert("Please select a print centre to continue.");
                return false;
            }
    });
});

</script>