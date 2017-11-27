<?php
    $this->breadcrumbs=array(
                array('label'=>'Home', 'url'=>array(Yii::app()->user->role->HomeUrl)),
                array('label'=>'Routes', 'url'=>array('admin/routes')),
                array('label'=>'Route'),
            );
?>

<style>
.ui-state-highlight{
    height: 1.5em;
}

#tblDetails {
    background-color:#FFE45C !important;
}
</style>

<?php if (isset($model->routeId)) : ?>
<h1>Edit Route</h1>
<?php else : ?>
<h1>Add new Route</h1>
<?php endif; ?>


<div class="standardForm">
<?php $form=$this->beginWidget('CActiveForm', array(
            'id'=>'route-form',
            'errorMessageCssClass'=>'formError',
    )); ?>

    <?php
    echo $form->hiddenField($model, 'routeId');
    echo $form->errorSummary($model, "", "", array('class'=>'errorBox'));
    ?>

    <div>
        <?php echo $form->labelEx($model,'name'); ?>
        <?php echo $form->textField($model,'name', array('size'=>'35')); ?>
    </div>

    <div>
        <?php echo $form->labelEx($model,'showDetailed'); ?>
        <?php echo $form->dropDownList($model, 'showDetailed', $model->getOptionsShowDetailed(), array('empty'=>'select one ->')); ?>
    </div>

    <div>
        <?php echo $form->labelEx($model,'supplierId'); ?>
        <?php echo $form->dropDownList($model, 'supplierId', $model->getOptionsSupplier(), array('empty'=>'select one ->')); ?>
    </div>


    <div class="titleWrap">
        <h2>Route Details</h2>
    </div>

    <table id="tblDetails" class="listing fluid" cellpadding="0" cellspacing="0">
    <tbody>
        <tr>
          <th width="50%">Title</th>
          <th>Delivery Point</th>
        </tr>
        <?php
            $cnt = count($model->routeDetails);
            for ($i = 0; $i < $cnt; $i++):
            ?>
        <tr>
            <td>
                <img src="img/icons/arrow_switch.png">
                <?php echo CHtml::dropDownList('RouteDetails[title'.($i+1).']', $model->routeDetails[$i]['title'], $model->getOptionsTitle(), array('id'=>'ddlRowTitle'.($i+1),'empty'=>'Select a Title ->')); ?>
            </td>
            <td>
                <?php echo CHtml::dropDownList('RouteDetails[delpoint'.($i+1).']', $model->routeDetails[$i]['delpoint'], $model->getOptionsDeliveryPoint(), array('id'=>'ddlRowDelPoint'.($i+1),'empty'=>'Select a Delivery Point ->')); ?>
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
            <td width="50%">
                <img src="img/icons/arrow_switch.png" style="display:none;">
                <?php echo CHtml::dropDownList("ddlDummyTitle", "", $model->getOptionsTitle(), array('empty'=>'Select a Title ->')); ?>
            </td>
            <td width="50%">
                <?php echo CHtml::dropDownList("ddlDummyDelPoint", "", $model->getOptionsDeliveryPoint(), array('empty'=>'Select a Delivery Point ->', 'disabled'=>'true')); ?>
            </td>
        </tr>
        <tr>
            <td align="right">
                <img height="16" width="16" alt="add" src="img/icons/add.png">
                <a href="<?php echo $this->createUrl('admin/title', array('ui'=>'popUp'));?>" title="Add new Title" class="colorBoxIFrame">Add new Title</a>
            </td>
            <td align="right">
                <img height="16" width="16" alt="add" src="img/icons/add.png">
                <a href="<?php echo $this->createUrl('admin/deliverypoint', array('ui'=>'popUp'));?>" title="Add new Delivery Point" class="colorBoxIFrame">Add new Delivery Point</a>
            </td>
        </tr>
    </tbody>
    </table>

    <script>
    $(document).ready(function(){

        $( "#tblDetails" ).sortable({
                placeholder: "ui-state-highlight",
                items: 'tr:not(:first)',
                update: function(evt, ui){
                    repaintTable();
                }
        });
        $( "#tblDetails" ).disableSelection();
        

        $(".colorBoxIFrame").colorbox({width:"500px", height:"600px", iframe:true, onClosed:reloadDropDowns});

        $("select[name^='RouteDetails\\[title']").change(changeRowTitle);
        repaintTable();
        $("#ddlDummyTitle").change(function(){
            if ("" != $(this).val()){
                $("#tblAux tr:first").clone().find("#ddlDummyTitle").each(function(){
                    $(this).attr("id", "ddlRowTitle"+ $("#tblDetails tr").length); // change id
                    $(this).attr("name", "RouteDetails[title" + $("#tblDetails tr").length + "]"); // change name
                    $(this).val($("#ddlDummyTitle").val()); // copy value
                    $(this).change(changeRowTitle); // add event
                    $("#ddlDummyTitle").val(""); // reset dummy
                }).end().find("#ddlDummyDelPoint").each(function(){
                    $(this).attr("id", "ddlRowDelPoint"+ $("#tblDetails tr").length); // change id
                    $(this).attr("name", "RouteDetails[delpoint" + $("#tblDetails tr").length + "]"); // change name
                    $(this).removeAttr("disabled"); // enable new del point
                }).end().appendTo("#tblDetails");
                repaintTable();
            }
        });

        $("#route-form").submit(function(event){
            var result = true;
            $("select[name^='RouteDetails\\[title']").each(function(idx){
                if("" == $(this).val()){
                    $(this).attr("class", "error");
                    result = false;
                }
                else
                    $(this).attr("class", "");
            });
            $("select[name^='RouteDetails\\[delpoint']").each(function(idx){
                if("" == $(this).val()){
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
        // re-sort names
        $("select[name^='RouteDetails\\[title']").each(function(idx){
            $(this).attr("id", "ddlRowTitle"+ (idx+1)); // change id
            $(this).attr("name", "RouteDetails[title" + (idx+1) + "]"); // change name
        });
        $("select[name^='RouteDetails\\[delpoint']").each(function(idx){
            $(this).attr("id", "ddlRowDelPoint"+ (idx+1)); // change id
            $(this).attr("name", "RouteDetails[delpoint" + (idx+1) + "]"); // change name
        });

        $("#tblDetails tr").each(function(idx){
            if(idx > 0)
                $(this).attr("class", "row"+(((idx-1)%2)+1));
        });
        $("#tblDetails img").show();
    }

    function changeRowTitle(){
        if("" == $(this).val()) {
            $(this).parents("tr").remove(); // delete row
            repaintTable();
        }
    }

    function reloadDropDowns() {
        $.getJSON("<?php echo $this->createUrl('admin/route', array('json'=>'dds')) ?>", function(data){
            $("#ddlDummyTitle option:gt(0)").remove();
            $.each(data.titles, function(idx, title){
               $("<option>").attr("value", title.value).text(title.text).appendTo("#ddlDummyTitle");
            });

            $("#ddlDummyDelPoint option:gt(0)").remove();
            $.each(data.delpoints, function(idx, delpoint){
               $("<option>").attr("value", delpoint.value).text(delpoint.text).appendTo("#ddlDummyDelPoint");
            });

            $("select[name^='RouteDetails\\[title']").each(function(idx){
               var prevVal = $(this).val();
               var curList = $(this);
               $(this).find("option:gt(0)").remove();
               $("#ddlDummyTitle option:gt(0)").each(function(idx){
                    $(this).clone().appendTo(curList);
               });
               $(this).val(prevVal);
            });

            $("select[name^='RouteDetails\\[delpoint']").each(function(idx){
               var prevVal = $(this).val();
               var curList = $(this);
               $(this).find("option:gt(0)").remove();
               $("#ddlDummyDelPoint option:gt(0)").each(function(idx){
                    $(this).clone().appendTo(curList);
               });
               $(this).val(prevVal);
            });
        });
    }
    </script>


    <div class="titleWrap">
        <?php echo CHtml::submitButton('Submit', array('class'=>'formButton', 'id'=>'btnSubmit')); ?>
        <ul>
            <li class="seperator">
                <img height="16" width="16" alt="add" src="img/icons/cancel.png">
                <a href="<?php echo $this->createUrl('admin/routes');?>">Cancel</a>
            </li>
        </ul>
    </div>

<?php
$this->endWidget(); ?>
</div>