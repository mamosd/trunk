<?php
    $this->breadcrumbs=array(
                array('label'=>'Home', 'url'=>array(Yii::app()->user->role->HomeUrl)),
                array('label'=>'Client Routing', 'url' => '#'),
                array('label'=>'Clear Data')
            );
    $baseUrl = Yii::app()->request->baseUrl;    
?>

<div class="standardForm">
<?php $form=$this->beginWidget('CActiveForm', array(
        'id'=>'clear-form',
        'errorMessageCssClass'=>'formError',
        'action'=>$this->createUrl($this->route)
)); 

echo CHtml::errorSummary($model, "", "", array('class'=>'errorBox'));

?>
    <?php if (!empty($model->message)): ?>
    <div class="infoBox">
        <?php echo $model->message; ?>
    </div>
    <?php endif; ?>
    
    <table width="50%">
        <tr>
            <td>
                <div class="field">
                    <?php echo $form->labelEx($model,'date'); ?>
                    <?php echo $form->textField($model, 'date', array('size' => '10', 'class' => 'dpicker')); ?>
                </div>
            </td>
            <td>
                <div class="field">
                    <?php echo $form->labelEx($model,'clientId'); ?>
                    <?php echo $form->dropDownList($model,'clientId', $model->getClientOptions()); //, array('empty' => '-- select one') ?>
                </div>                
            </td>
        </tr>
    </table>
    
    <br/>
    
    <button id="btnSubmit">Clear Data</button>
<?php
$this->endWidget(); ?>
</div>


<script>
$(function(){
    $(".dpicker").datepicker({
        dateFormat: 'dd/mm/yy',
        onSelect: function() {
            $(this).removeClass("error");
            $(this).change();
        }
    });
    
    $("#btnSubmit").click(function(){
        return confirm("This will delete all route imported/entered data, keeping only Magazine entries for the selected date.\nPlease confirm you wish to proceed.");
    });
});
</script>