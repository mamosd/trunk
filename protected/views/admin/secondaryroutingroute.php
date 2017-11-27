<?php
    $this->breadcrumbs=array(
                array('label'=>'Home', 'url'=>array(Yii::app()->user->role->HomeUrl)),
                array('label'=>'Secondary Routing', 'url'=>array('admin/secondaryrouting')),
                array('label'=>'Route Maintenance', 'url'=>array('admin/secondaryroutingroutes')),
                array('label'=>'Manage Rounds'),
            );
?>
<style>
.ui-state-highlight{
    height: 1.5em;
}

#sortable {
    background-color:#FFE45C !important;
}
</style>

<?php
$form=$this->beginWidget('CActiveForm', array(
            'id'=>'route-form',
            'errorMessageCssClass'=>'formError',
    ));
?>

<div class="titleWrap">
    <h1>Manage Rounds > <?php echo $model->routeId ?></h1>
    <ul>
        <li class="seperator">
            <span id="span-selected">0 selected</span>
        </li>
        <li class="seperator">
            <?php
            $validActions = array();
            if (Yii::app()->user->role->LoginRoleId == LoginRole::SUPER_ADMIN)
                $validActions["delete"] = "delete";
            $validActions["move"] = "move to:";
            echo CHtml::activeDropDownList($model, "axn", $validActions, array('empty'=>'select action -->')); ?>
            <?php echo CHtml::activeDropDownList($model, "selectedRoute", $model->getRoutes(), array('empty'=>'select route -->', 'class' => 'ddl-route', 'style' => 'display:none;')); ?>
        </li>
        <li class="seperator">
            <button id="btnGo">Go</button>
        </li>
    </ul>
</div>


<?php 
    echo $form->errorSummary($model, "", "", array('class'=>'errorBox'));
?>

<table class="listing fluid" id="sortable">
    <tr>
        <th>&nbsp;</th>
        <th>Round Id</th>
        <th>Name</th>
        <th>Address</th>
        <th>Post Code</th>
        <th>&nbsp;</th>
    </tr>
    <?php
    $count = count($model->details);
    for($i = 0; $i < $count; $i++):
        $d = $model->details[$i];
    ?>
    <tr class="row<?php echo ($i%2)+1; ?>" roundid="<?php echo $d->SecondaryRoundId; ?>">
        <td><img src="img/icons/arrow_switch.png" /></td>
        <td><?php echo $d->SecondaryRoundId; ?></td>
        <td><?php echo $d->Name.' '.$d->Surname; ?></td>
        <td><?php echo $d->Address; ?></td>
        <td><?php echo $d->PostCode; ?></td>
        <td>
            <input type="checkbox" class="chk-select" roundid="<?php echo $d->SecondaryRoundId; ?>" />
        </td>
    </tr>
    <?php endfor; ?>
</table>


<div class="titleWrap">
    <?php echo CHtml::submitButton('Submit', array('class'=>'formButton submit-button')); ?>
    <ul>
        <li class="seperator">
            <img height="16" width="16" alt="add" src="img/icons/cancel.png">
            <a href="<?php echo $this->createUrl('admin/secondaryroutingroutes');?>">Cancel</a>
        </li>
    </ul>
</div>
<?php
//echo CHtml::activeHiddenField($model, 'axn');
echo CHtml::activeHiddenField($model, 'selectedRounds');

echo CHtml::activeHiddenField($model, 'routeId');
echo CHtml::activeHiddenField($model, 'sortOrder');

$this->endWidget(); ?>

<script>
$(function() {
        $( "#sortable" ).sortable({
                placeholder: "ui-state-highlight",
                items: 'tr:not(:first)',
                update: function(evt, ui){
                    $("#sortable tr:not(:first)").each(function(idx){
                        //alert(idx);
                        $(this).attr('class', 'row' + ((idx%2)+1) );
                    });
                }
        });
        $( "#sortable" ).disableSelection();

        $(".submit-button").click(function(){
            $("#SecondaryRoutingRouteForm_axn").val('');
            $("#SecondaryRoutingRouteForm_sortOrder").val('');
            $("#sortable tr:not(:first)").each(function(idx){
                $("#SecondaryRoutingRouteForm_sortOrder").val($("#SecondaryRoutingRouteForm_sortOrder").val() + $(this).attr('roundid') + "|");
            });
        });

        $(".chk-select").click(function(){
            $("#span-selected").text($(".chk-select:checked").length + ' selected');
        });

        $("#SecondaryRoutingRouteForm_axn").change(function(){
            $(".ddl-route").hide();
            if ($(this).val() == 'move')
            {
                $(".ddl-route").show();
            }
        });

        $("#btnGo").click(function(){
            if ($(".chk-select:checked").length == 0)
            {
                alert("No Rounds selected.");
                return false;
            }
            var axn = $("#SecondaryRoutingRouteForm_axn").val();

            if (axn == "move")
            {
                if ($("#SecondaryRoutingRouteForm_selectedRoute").val() == "")
                {
                    alert("Select a route to move selected rounds.");
                    return false;
                }
            }

            if (axn == "delete")
            {
                if (!confirm("The selected rounds are about to be deleted, are you sure you wish to proceed?"))
                    return false;
            }

            $("#SecondaryRoutingRouteForm_selectedRounds").val('');
            $(".chk-select:checked").each(function(idx){
                $("#SecondaryRoutingRouteForm_selectedRounds").val($("#SecondaryRoutingRouteForm_selectedRounds").val() + $(this).attr('roundid') + "|");
            });
        });
});
</script>