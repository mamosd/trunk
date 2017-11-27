<?php
    $this->breadcrumbs=array(
                array('label'=>'Home', 'url'=>array(Yii::app()->user->role->HomeUrl)),
                array('label'=>'Finance', 'url' => '#'),
                array('label'=>'Contractor Invoices')
            );
    
    $baseUrl = Yii::app()->request->baseUrl;
    
?>

<h1>
    <?php echo $model->category; ?> Invoices - Week Starting <?php echo $model->weekStarting; ?>
</h1>

<fieldset>
    <br/>
    
    <div class="stackedForm">
<?php $form=$this->beginWidget('CActiveForm', array(
            'id'=>'invoicing-form',
            'errorMessageCssClass'=>'formError',
    )); ?>

    <?php
    echo $form->hiddenField($model, 'weekStarting');
    echo $form->hiddenField($model, 'category');
    echo $form->errorSummary($model, "", "", array('class'=>'errorBox'));
    ?>

    <div class="field">
        <?php echo $form->labelEx($model,'contractor'); 
        echo CHtml::activeListBox($model,'contractor', $model->getContractorOptions(TRUE, $model->category), array('empty' => '(all)', 'size' => 8, 'multiple' => 'multiple'));
        ?>
    </div>
        
    <div class="field">
        <?php echo $form->labelEx($model,'sendOverEmail'); 
        echo CHtml::activeCheckBox($model,'sendOverEmail');
        ?>
    </div>
        
    <br/><br/>
        
    <div class="titleWrap">
        <button class="btn" id="btnGenerate">
            <img src="<?php echo $baseUrl; ?>/img/icons/page_white_acrobat.png" />
            Generate Invoice/s
        </button>
        <div class="infoBox" id="divGenerating" style="display:none;">
            Please wait while generation completes and download the file when prompted. <br/>
            If you wish to continue generating invoices, please change your selection once download is completed.
        </div>
    </div>
<?php
$this->endWidget(); ?>
</div>
    
    
</fieldset>

<script>

$(function(){
    $("#btnGenerate").click(function(){
        //$(this).html('Please wait...').attr('disabled', 'disabled');
        $(this).hide();
        $('#divGenerating').show();
        $("#invoicing-form").submit();
    });
    
    $("#FinanceInvoicing_contractor").change(enableButton);
    $("#FinanceInvoicing_sendOverEmail").change(enableButton);
});

function enableButton() {
    $('#divGenerating').hide();
    $("#btnGenerate").show();
}
</script>