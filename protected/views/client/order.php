<?php
    $this->breadcrumbs=array(
                array('label'=> Yii::app()->user->name , 'url'=>array(Yii::app()->user->role->HomeUrl)),
                array('label'=>'Titles', 'url'=>array(Yii::app()->user->role->HomeUrl)),
                array('label'=>'Order'),
            );
?>

<h1>Order Entry</h1>

<div class="standardForm">
<?php $form=$this->beginWidget('CActiveForm', array(
            'id'=>'order-form',
            'errorMessageCssClass'=>'formError',
    )); ?>

    <?php
    echo $form->hiddenField($model, 'titleId');
    echo $form->hiddenField($model, 'weightPerPage', array('id'=>'txtWeightPerPage'));
    echo $form->errorSummary($model, "", "", array('class'=>'errorBox'));
    ?>

    <?php echo $form->textField($model,'titleName', array('size'=>'25', 'class'=>'readOnlyField', 'tabindex'=>'-1', 'readonly'=>'readonly')); ?>

    <div>
        <?php echo $form->labelEx($model,'pagination'); ?>
        <?php echo $form->textField($model,'pagination', array('id'=>'txtPagination','size'=>'10')); ?>
    </div>

    <div>
        <?php echo $form->labelEx($model,'bundleSize'); ?>
        <?php echo $form->textField($model,'bundleSize', array('id'=>'txtBundleSize', 'size'=>'10')); ?>
    </div>

    <div>
        <?php echo $form->labelEx($model,'publicationDate'); ?>
        <?php echo $form->textField($model,'publicationDate', array('size'=>'10')); ?>
    </div>

    <div>
        <?php echo $form->labelEx($model,'deliveryDate'); ?>
        <?php echo $form->textField($model,'deliveryDate', array('size'=>'10')); ?>
    </div>


    <h2>Delivery Points Entry</h2>

    <div class="infoRight">
        <div>
            <?php echo $form->labelEx($model,'totalCopies'); ?>
            <?php echo $form->textField($model,'totalCopies', array('id'=>'txtTotalCopies', 'size'=>'10', 'class'=>'readOnlyField', 'tabindex'=>'-1', 'readonly'=>'readonly')); ?>
        </div>
        <div>
            <?php echo $form->labelEx($model,'orderWeight'); ?>
            <?php echo $form->textField($model,'orderWeight', array('id'=>'txtOrderWeight', 'size'=>'10', 'class'=>'readOnlyField', 'tabindex'=>'-1', 'readonly'=>'readonly')); ?>
        </div>
    </div>


    <table id="tblDetails" class="listing fluid" cellpadding="0" cellspacing="0">
    <tbody>
        <tr>
          <th width="30%">Delivery Point</th>
          <th>Copies</th>
          <th>Bundles</th>
          <th>Odds</th>
          <th>Actions</th>
        </tr>
        <?php
            $cnt = count($model->orderDetails);
            for ($i = 0; $i < $cnt; $i++):
            ?>
        <tr>
            <td width="30%">
                <?php echo CHtml::hiddenField("OrderDetails[delpoint".($i+1)."]", $model->orderDetails[$i]["delpoint"], array('id'=>'hidRowDelPoint'.($i+1))); ?>
                <?php echo CHtml::textField("OrderDetails[descdelpoint".($i+1)."]", $model->orderDetails[$i]["descdelpoint"], array('id'=>'txtRowDelPoint'.($i+1), 'size'=>'25', 'class'=>'readOnlyField', 'tabindex'=>'-1', 'readonly'=>'readonly')); ?>
            </td>
            <td>
                <?php echo CHtml::textField("OrderDetails[copies".($i+1)."]", $model->orderDetails[$i]["copies"], array('id'=>'txtRowCopies'.($i+1), 'size'=>'5')); ?>
            </td>
            <td>
                <?php echo CHtml::textField("OrderDetails[bundles".($i+1)."]", "", array('id'=>'txtRowBundles'.($i+1), 'size'=>'5', 'class'=>'readOnlyField', 'tabindex'=>'-1', 'readonly'=>'readonly')); ?>
            </td>
            <td>
                <?php echo CHtml::textField("OrderDetails[odds".($i+1)."]", "", array('id'=>'txtRowOdds'.($i+1), 'size'=>'5', 'class'=>'readOnlyField', 'tabindex'=>'-1', 'readonly'=>'readonly')); ?>
            </td>
            <td>
                <a href="#" id="lnkDelete" tabindex="-1">
                    <img height="16" width="16" alt="add" src="img/icons/delete.png">
                    Delete
                </a>
            </td>
        </tr>
        <?php
            endfor;
        ?>
    </tbody>
    </table>
    <hr />
    <table id="tblAux" class="listing fluid">
    <tbody>
        <tr>
            <td colspan="5">
                <?php echo CHtml::dropDownList("ddlDummyDelPoint", "", $model->getOptionsDeliveryPoint(), array('empty'=>'Select a Delivery Point to Add ->')); ?>
            </td>
        </tr>
        <tr id="trAux" style="display:none;">
            <td width="30%">
                <?php echo CHtml::hiddenField("hidDummyDelPoint"); ?>
                <?php echo Chtml::textField('txtDummyDelPoint', '', array('size'=>'25', 'class'=>'readOnlyField', 'tabindex'=>'-1', 'readonly'=>'readonly')); ?>
            </td>
            <td>
                <?php echo Chtml::textField('txtDummyCopies', '', array('size'=>'5')); ?>
            </td>
            <td>
                <?php echo Chtml::textField('txtDummyBundles', '', array('size'=>'5', 'class'=>'readOnlyField', 'tabindex'=>'-1', 'readonly'=>'readonly')); ?>
            </td>
            <td>
                <?php echo Chtml::textField('txtDummyOdds', '', array('size'=>'5', 'class'=>'readOnlyField', 'tabindex'=>'-1', 'readonly'=>'readonly')); ?>
            </td>
            <td>
                <a href="#" id="lnkDelete" tabindex="-1">
                    <img height="16" width="16" alt="add" src="img/icons/delete.png">
                    Delete
                </a>
            </td>
        </tr>
    </tbody>
    </table>

    <script>
    $(document).ready(function(){

        repaintTable();
        recalculate();
        $("#txtPagination").change(recalculate);
        $("#txtBundleSize").change(recalculate);
        $("#tblDetails tr").each(function(){
            $(this).find("#lnkDelete").click(deleteRow);
            $(this).find("input[id^='txtRowCopies']").change(onChangeCopies);
        });
        
        $("#ddlDummyDelPoint").change(function(){
           if ("" != $(this).val()) {
               var newRow = $("#trAux").clone();
               newRow.removeAttr("style");
               newRow.removeAttr("id");
               
               var delPointFld = newRow.find("#txtDummyDelPoint");
               delPointFld.attr("id", "txtRowDelPoint"+ $("#tblDetails tr").length); // change id
               delPointFld.attr("name", "OrderDetails[descdelpoint" + $("#tblDetails tr").length + "]");
               delPointFld.val($(this).find("option:selected").text());

               var hidPointFld = newRow.find("#hidDummyDelPoint");
               hidPointFld.val($(this).val());
               hidPointFld.attr("id", "hidRowDelPoint"+ $("#tblDetails tr").length); // change id
               hidPointFld.attr("name", "OrderDetails[delpoint" + $("#tblDetails tr").length + "]");

               newRow.find("#lnkDelete").click(deleteRow);
               
               var copiesFld = newRow.find("#txtDummyCopies");
               copiesFld.change(onChangeCopies);
               copiesFld.attr("id", "txtRowCopies"+ $("#tblDetails tr").length); // change id
               copiesFld.attr("name", "OrderDetails[copies" + $("#tblDetails tr").length + "]");

               newRow.find("#txtDummyBundles").each(function(){
                   $(this).attr("id", "txtRowBundles"+ $("#tblDetails tr").length); // change id
                   $(this).attr("name", "OrderDetails[bundles" + $("#tblDetails tr").length + "]");
               });

               newRow.find("#txtDummyOdds").each(function(){
                   $(this).attr("id", "txtRowOdds"+ $("#tblDetails tr").length); // change id
                   $(this).attr("name", "OrderDetails[odds" + $("#tblDetails tr").length + "]");
               });

               newRow.appendTo("#tblDetails");
               repaintTable();

               $(this).val("");
               recalculate();
           }
        });

        $("#order-form").submit(function(event){
            var result = true;
            $("input[name^='OrderDetails\\[copies']").each(function(idx){
                if("" == $(this).val() || isNaN($(this).val())){
                    $(this).attr("class", "error");
                    result = false;
                }
                else
                    $(this).attr("class", "");
            });

            return result;
        });
    });

    function repaintTable(){
/*        $("#tblDetails tr").each(function(idx){
            if(idx > 0)
                $(this).attr("class", "row"+(((idx-1)%2)+1));
        });
*/
    }

    function onChangeCopies() {
        recalculate();
    }

    function recalculate() {
        var pagination = parseInt($("#txtPagination").val(), 10);
        var bundleSize = parseInt($("#txtBundleSize").val(), 10);
        var weightPerPage = parseInt($("#txtWeightPerPage").val(), 10);

        var totalCopies = 0;
        $("#tblDetails tr").each(function(){
            var copies = 0;
            $(this).find("input[id^='txtRowCopies']").each(function(){
                var rowCopies = parseInt($(this).val(), 10);
                if(!isNaN(rowCopies))
                    copies = rowCopies;
            });

            totalCopies += copies;
            var bundles = Math.floor(copies / bundleSize);
            bundles = isNaN(bundles) ? 0 : bundles;
            var odds = copies % bundleSize;
            odds = isNaN(odds) ? 0 : odds;

            $(this).find("input[id^='txtRowBundles']").val(bundles);
            $(this).find("input[id^='txtRowOdds']").val(odds);
        });

        totalCopies = isNaN(totalCopies) ? 0 : totalCopies;
        $("#txtTotalCopies").val(totalCopies);
        var orderWeight = ((totalCopies * pagination * weightPerPage) / 1000).toFixed(2);
        orderWeight = isNaN(orderWeight) ? 0 : orderWeight;
        $("#txtOrderWeight").val(orderWeight);
    }

    function deleteRow(){
        $(this).parents("tr").remove(); // delete row
        // resort names

        $("input[name^='OrderDetails\\[copies']").each(function(idx){
            $(this).attr("id", "txtRowCopies"+ (idx+1)); // change id
            $(this).attr("name", "OrderDetails[copies" + (idx+1) + "]"); // change name
        });
        $("input[name^='OrderDetails\\[descdelpoint']").each(function(idx){
            $(this).attr("id", "txtRowDelPoint"+ (idx+1)); // change id
            $(this).attr("name", "OrderDetails[descdelpoint" + (idx+1) + "]");
        });
        $("input[name^='OrderDetails\\[delpoint']").each(function(idx){
            $(this).attr("id", "hidRowDelPoint"+ (idx+1)); // change id
            $(this).attr("name", "OrderDetails[delpoint" + (idx+1) + "]"); // change name
        });
        $("input[name^='OrderDetails\\[bundles']").each(function(idx){
            $(this).attr("id", "txtRowBundles"+ (idx+1)); // change id
            $(this).attr("name", "OrderDetails[bundles" + (idx+1) + "]");
        });
        $("input[name^='OrderDetails\\[odds']").each(function(idx){
            $(this).attr("id", "txtRowOdds"+ (idx+1)); // change id
            $(this).attr("name", "OrderDetails[odds" + (idx+1) + "]");
        });

        repaintTable();
        recalculate();

        return false;
    }
    </script>

    <div class="titleWrap">
        <?php echo CHtml::submitButton('Submit', array('class'=>'formButton')); ?>
        <ul>
            <li class="seperator">
                <img height="16" width="16" alt="add" src="img/icons/cancel.png">
                <a href="<?php echo $this->createUrl('client/titles');?>">Cancel</a>
            </li>
        </ul>

    </div>

<?php
$this->endWidget(); ?>
</div>