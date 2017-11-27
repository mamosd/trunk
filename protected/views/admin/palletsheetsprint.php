<?php
    $this->breadcrumbs=array(
                array('label'=>'Home', 'url'=>array(Yii::app()->user->role->HomeUrl)),
                array('label'=>'Pallets', 'url'=>'#'),
                array('label'=>'Print Supplier Sheets'),
            );
?>
    
<h1>Print Pallet Sheets per Supplier</h1>

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
        <?php echo $form->labelEx($model,'supplierId'); ?>
        <?php echo $form->dropDownList($model, 'supplierId', $model->getOptionsSupplier(), array('empty'=>'select one ->')); ?>
        
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
        if ($("#PalletsSheetPrint_supplierId").val() == "")
            {
                alert("Please select a supplier to continue.");
                return false;
            }
    });
});

</script>