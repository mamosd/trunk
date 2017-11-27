<?php
    $this->breadcrumbs=array(
                array('label'=>'Home', 'url'=>array(Yii::app()->user->role->HomeUrl)),
                array('label'=>'Finance', 'url' => '#'),
                array('label'=>'Route')
            );
    
    $baseUrl = Yii::app()->request->baseUrl;
    $readOnlyAttrs = array('class' => 'readOnlyField', 'readOnly' => 'readOnly');
?>

<h1>
    <?php if (!$model->baseEdit): ?> 
        New <?php echo $model->editingCategory ?> Exception Route for week starting <?php echo $model->weekStarting ?>
    <?php else: ?>
        New Route for <?php echo $model->editingCategory ?> Base Routing Plan
    <?php endif; ?>
</h1>

<fieldset>
<br/>
<div class="stackedForm">
<?php $form=$this->beginWidget('CActiveForm', array(
            'id'=>'route-form',
            'errorMessageCssClass'=>'formError',
    )); ?>

    <?php
    echo $form->hiddenField($model, 'weekStarting');
    echo $form->errorSummary($model, "", "", array('class'=>'errorBox'));
    ?>

    <div class="field">
        <?php echo $form->labelEx($model,'routeCategory'); 
        echo CHtml::activeDropDownList($model,'routeCategory', $model->getCategoryOptions(), array('empty' => '-- select one'));
        ?>
    </div>
    
    <div class="field">
        <?php echo $form->labelEx($model,'routeCode');
        echo $form->textField($model, 'routeCode', array('size' => '10')); ?>
    </div>
    
    <div class="field">
        <?php echo $form->labelEx($model,'contractorId'); 
        echo CHtml::activeDropDownList($model,'contractorId', $model->getContractorOptions(), array('empty' => '-- select one'));
        ?>
    </div>
    
    <fieldset>
        <legend>Fees</legend>
    <table class="listing fluid vtop">
    <tr>
        <?php $datesToShow = array();
        $wst = CDateTimeParser::parse($model->weekStarting, "dd/MM/yyyy");

        foreach(range(0,6) as $delta)
            $datesToShow[] = strtotime("+$delta day", $wst);

        foreach ($datesToShow as $date):
            ?>
        <th width="50">
            <?php if (!$model->baseEdit) echo date('d/m', $date)."<br/>";?>
            <?php echo date('D', $date);?>
        </th>
        <?php endforeach; ?>
        <th width="50">
            All
        </th>
    </tr>
    <tr>
        <?php $first = TRUE;
        foreach ($datesToShow as $date):
            $feeClass = 'fee';
            $feeClass .= ($first) ? ' first' : '';
            $first = FALSE;
            ?>
        <td>
            <?php echo CHtml::textField('Fee['.date('Y-m-d', $date).']', $model->getFee(date('Y-m-d', $date)), array('size' => 5, 'class' => $feeClass)); ?>
        </td>
        <?php endforeach; ?>
        <td>
            <?php echo CHtml::textField('Fee[all]', $model->getFee('all'), array('size' => 5, 'class' => 'readOnlyField fee-all', 'readOnly' => 'readOnly')); ?>
        </td>
    </tr>
    </table>
    </fieldset>
    
    <br/>
    
    <div class="titleWrap">
        <button class="btn" id="btnSave">
            <img src="<?php echo $baseUrl; ?>/img/icons/bullet_disk.png" />
            Save
        </button>
    </div>
<?php
$this->endWidget(); ?>
</div>
    
</fieldset>

<script>

var formChanged = false;
var formSave = false;
   
$(function(){
    $('.fee').change(function(){
        $(this).removeClass('error');
        var val = parseFloat($(this).val());
        if (isNaN(val))
        {
            $(this).addClass('error');
            val = "";
        }
        $(this).val(val);
        
        if ($(this).hasClass('first'))
        {
            $('.fee').each(function(){
                if ($.trim($(this).val())  == '')
                    $(this).val(val);
            })
        }
        
        updateTotal();
    })
    
    $("#btnSave").click(function(){
        // validate all fees have values (0 allowed)
        formSave = true;
        var bError = false;
        $('.fee').each(function(){
            if (($.trim($(this).val())  == '') || isNaN($(this).val()) || (parseFloat($(this).val()) < 0))
            {
                bError = true;
                $(this).addClass('error');
            }
        });
        
        if (bError)
        {
            alert('There are errors on the data entered, please review.\n\t- Fees must contain numeric values (0 allowed).');
            return false;
        }
    });
    
    
    
    $('#route-form').change(function(){
        formChanged = true;        
    });
    
    
    //confirm popup close if form changed
    var originalClose = parent.jQuery.colorbox.close;

    parent.jQuery.colorbox.close = function(){          
        
    if ( formSave == false )
    {
        if ( formChanged == true)
        {
            if (confirm("Are you sure you want to close this window?\nInformation entered will not be saved."))
            {
                originalClose();
            }
        }
        else if ( formChanged == false)
        {
            originalClose();
        }
    }
    else
    {
      originalClose();
    }

//    if (confirm("Are you sure you want to close this window?"+formChanged))
//    {
//        originalClose();
//    }
    
    };    
    
});

function updateTotal()
{
    var total = 0;
    $('.fee').each(function(){
        var cur = parseFloat($(this).val());
        if (!isNaN(cur))
            total += cur;
    })
    $('.fee-all').val(total);
}
</script>
    