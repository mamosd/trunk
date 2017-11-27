<?php
    $this->breadcrumbs=array(
                array('label'=>'Home', 'url'=>array(Yii::app()->user->role->HomeUrl)),
                array('label'=>'Pallets', 'url'=>'#'),
                array('label'=>'Delivery Points'),
            );
?>
<!--style>
    #mainWrap {
        width: 95% !important;
    }  
</style-->

<h1>Pallet - Delivery Point Report</h1>

<div class="standardForm">
<?php $form=$this->beginWidget('CActiveForm', array(
            'id'=>'report-form',
            'errorMessageCssClass'=>'formError'
    )); ?>

    <?php
    echo $form->errorSummary($model, "", "", array('class'=>'errorBox'));
    ?>

    <table class="fluid">
        <tr>
            <td width="50%">
                <div>
                    <?php echo $form->labelEx($model,'supplierId'); ?>
                    <?php echo $form->dropDownList($model, 'supplierId', $model->getOptionsSupplier(), array('empty'=>'(all)')); ?>
                </div>

                <div>
                    <?php echo $form->labelEx($model,'printCentre'); ?>
                    <?php echo $form->dropDownList($model, 'printCentre', $model->getOptionsPrintCentre(), array('empty'=>'(all)')); ?>
                </div> 

                <div>
                    <?php echo $form->labelEx($model,'route'); ?>
                    <?php echo $form->dropDownList($model, 'route', $model->getOptionsRoute(), array('empty'=>'(all)')); ?>
                </div>       
                
                <div>
                    <?php echo $form->labelEx($model,'deliveryPoint'); ?>
                    <?php echo $form->dropDownList($model, 'deliveryPoint', $model->getOptionsDeliveryPoint(), array('empty'=>'(all)')); ?>
                </div>  
            </td>
            <td width="50%">
                <div>
                    <?php echo $form->labelEx($model,'dateFrom'); ?>
                    <?php echo $form->textField($model,'dateFrom', array('size'=>'10', 'class'=>'dpicker', 'autocomplete' => 'off')); ?>
                </div>

                <div>
                    <?php echo $form->labelEx($model,'dateTo'); ?>
                    <?php echo $form->textField($model,'dateTo', array('size'=>'10', 'class'=>'dpicker', 'autocomplete' => 'off')); ?>
                </div>
                
                <div>
                    <?php echo $form->labelEx($model,'byDay'); ?>
                    <?php echo $form->checkBox($model, 'byDay'); ?>
                </div>  
            </td>
        </tr>
    </table>
    
    <br/>
    <div class="titleWrap">
        <?php echo CHtml::submitButton('Submit', array('class'=>'formButton', 'id'=>'btnSubmit')); ?>
        <? if ($dp !== null){
            echo CHtml::submitButton('Export to CSV', array('class'=>'formButton', 'id'=>'btnCsvSubmit', 'name'=>'csv'));
        } ?>
    </div>

<?php
$this->endWidget(); ?>
</div>

<?php if ($dp !== null):
    
    $data = $dp->getData();
    if (!empty($data))
    {
        // collect overall balance
        echo "<h3>Overall Balances: ";
        $pBalance = 0;
        $wBalance = 0;
        foreach($data as $row)
        {
            $pBalance += $row['PlasticBalance'];
            $wBalance += $row['WoodenBalance'];
        }
        echo "Plastic: $pBalance ; ";
        echo "Wooden: $wBalance ";
        echo "</h3>";
        
        echo "<ul>";
        $crit = $model->getSearchCriteria();
        foreach($crit as $key => $value)
                echo "<li>$key : $value</li>";
        echo "</ul>";
        
        $columns = array(
                'DeliveryPointName:text:Delivery Point',
                'Date:text:Date', 
                'PlasticDelivered:text:Plastic Delivered',
                'PlasticCollected:text:Plastic Collected',
                'PlasticBalance:text:Plastic Balance',
                'WoodenDelivered:text:Wooden Delivered',
                'WoodenCollected:text:Wooden Collected',
                'WoodenBalance:text:Wooden Balance',
                //'NoteNumbers:text:Note Numbers',
                );
        
        if (!$model->byDay)
            unset($columns[1]);
        
        // display grid
        $this->widget('zii.widgets.grid.CGridView', array(
            'dataProvider' => $dp,
            'columns' => $columns
            )
        );
    }
    else
    {?>
        <div class="warningBox">There are no results for the selected criteria</div>
<?php
    }
endif; ?>

<script>
$(function(){
    $(".dpicker").datepicker({
        dateFormat: 'dd/mm/yy',
        onSelect: function() {
            $(this).removeClass("error");
            $(this).change();
        }
    });
});
</script>
