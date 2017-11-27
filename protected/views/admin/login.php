<?php
    $this->breadcrumbs=array(
                array('label'=>'Home', 'url'=>array(Yii::app()->user->role->HomeUrl)),
                array('label'=>'Login Management', 'url'=>array('admin/logins')),
                array('label'=>'Login'),
            );
    $baseUrl = Yii::app()->request->baseUrl;
    $cs=Yii::app()->getClientScript();
    $cs->registerCoreScript ( 'jquery.ui' );
    $cs->registerCSSFile($baseUrl.'/css/multi-select.css');
    $cs->registerScriptFile($baseUrl.'/js/jquery.multi-select.js',CClientScript::POS_END);
    $cs->registerCSSFile($baseUrl.'/css/ui.dynatree.css');
    $cs->registerScriptFile($baseUrl.'/js/jquery.dynatree.js',CClientScript::POS_END);
?>

<?php if (isset($model->loginId)) : ?>
<h1>Edit Login</h1>
<?php else : ?>
<h1>Add new Login</h1>
<?php endif; ?>

<?php $form=$this->beginWidget('CActiveForm', array(
            'id'=>'login-edit-form',
            'errorMessageCssClass'=>'formError',
    )); ?>

    <?php
    echo $form->hiddenField($model, 'loginId');
    echo $form->errorSummary($model, "", "", array('class'=>'errorBox'));
    ?>

<div id="tabs">

    <ul>
        <li><a href="#tabs-g">General</a></li>
        <li><a href="#tabs-polestar">Polestar</a></li>
        <li><a href="#tabs-perms">Permissions</a></li>
    </ul>

<div class="standardForm" id="tabs-g">

    <div>
        <?php echo $form->labelEx($model,'username'); ?>
        <?php echo $form->textField($model,'username', array('size'=>'15')); ?>
    </div>

    <div>
        <?php echo $form->labelEx($model,'friendlyName'); ?>
        <?php echo $form->textField($model,'friendlyName', array('size'=>'25')); ?>
    </div>
    
    <div>
        <?php echo $form->labelEx($model,'email'); ?>
        <?php echo $form->textField($model,'email', array('size'=>'25')); ?>
    </div>

    <div>
        <?php echo $form->labelEx($model,'role'); ?>
        <?php echo $form->dropDownList($model, 'role', $model->getRoleOptions(), array('id'=>'ddlLoginRole', 'empty'=>'select one ->')); ?>
    </div>

    <div id="divRole<?php echo LoginRole::SUPPLIER ?>" style="display:none;">
        <?php echo $form->labelEx($model,'supplierId'); ?>
        <?php echo $form->dropDownList($model, 'supplierId', $model->getSupplierOptions()); ?>
    </div>

    <div id="divRole<?php echo LoginRole::CLIENT ?>" style="display:none;">
        <?php echo $form->labelEx($model,'clientId'); ?>
        <?php echo $form->dropDownList($model, 'clientId', $model->getClientOptions()); ?>
    </div>

    <div>
        <?php echo $form->labelEx($model,'isActive'); ?>
        <?php echo $form->checkBox($model,'isActive'); ?>
    </div>

    <div>
        <?php echo $form->labelEx($model,'newPassword'); ?>
        <?php echo $form->passwordField($model,'newPassword', array('size'=>'15')); ?>
    </div>

    <div>
        <?php echo $form->labelEx($model,'newPassword2'); ?>
        <?php echo $form->passwordField($model,'newPassword2', array('size'=>'15')); ?>
    </div>

</div>

    <div id="tabs-polestar">
        
        <fieldset style="width:50%;">
            <legend>Print Centres</legend>
            <em>Select the print centres on which this login will be able to operate</em> <br/><br/>
            
            <?php $PrintCentres = PolestarPrintCentre::getAllAsOptions(); ?>
            <?php foreach ($PrintCentres as $id => $name): ?>
                <div>
                    <?php echo CHtml::checkBox("LoginEditForm[polestarPrintCentres][]",in_array($id,$model->polestarPrintCentres), array('value' => $id)); ?> <?php echo $name ?>
                </div>
            <?php endforeach; ?>
            
        </fieldset>
        
    </div>    
    
<div id="tabs-perms">

    <div>
        <?php echo $form->labelEx($model,'roles'); ?>
        <?php echo $form->listBox(
            $model,
            "roles",
            Role::getAllAsOptionList(),
            array("multiple" => "multiple","class" => "multiSelect"));
        ?>
    </div>

<?php function permTree($list,$model) { ?>
<ul>
<?php foreach ($list as $l): ?>
    <li data="<?php echo (isset($l["Id"])?"key: '".$l["Id"]."',":""); ?>select: <?php echo (isset($l["Id"]) && !(empty($model->permissions)) && in_array($l['Id'], $model->permissions))? 'true' : 'false'  ?>,">
        <?php if (!empty($l['Description'])): ?>
            <?php echo $l['Description']; ?>
        <?php else:?>
            <div style="font-weight: bold;"><?php echo $l['Name']; ?></div>
        <?php endif?>
        <?php if (!empty($l['Children'])) permTree($l['Children'],$model); ?>
    </li>
<?php endforeach; ?>
</ul>
<?php } ?>

    <?php if (Login::checkPermission(Permission::PERM__FUN__LOGIN__PERMISSIONS)) : ?>
    <div id="tabs-permissions">
        <?php echo $form->labelEx($model,'permissions'); ?>
        <div id="perms">
            <?php permTree(Permission::getAllAsTree(),$model); ?>
        </div>
                    <div id="hidden_perms" style="display:none">
                <?php echo $form->checkBoxList($model,
                    'permissions',
                    Permission::getOptions()) ?>
            </div>
    </div>
    <?php endif; ?>
    
</div>

</div>

<div class="titleWrap">
    <?php echo CHtml::submitButton('Submit', array('class'=>'formButton')); ?>
    <ul>
        <li class="seperator">
            <img height="16" width="16" alt="add" src="img/icons/cancel.png">
            <a href="<?php echo $this->createUrl('admin/logins');?>">Cancel</a>
        </li>
    </ul>

</div>

<?php
$this->endWidget(); ?>

<script>
    $(document).ready(function() {

    $(".multiSelect").multiSelect();

    $("#ddlLoginRole").change(function() {
        $("#ddlLoginRole option").each(function() {
            $("#divRole"+$(this).val()).slideUp();
        });
        $("#divRole"+$(this).val()).slideDown();
    });

    $("#ddlLoginRole").change();

    $("#perms").dynatree({
        checkbox: true,
        selectMode: 3,
        debugLevel: 0,
        onSelect: function(select, node) {
            var selNodes = node.tree.getSelectedNodes();
            $('#hidden_perms').find(":checkbox").attr("checked",false);
            $.map(selNodes, function(node){
                $('#hidden_perms [value="' + node.data.key + '"]').attr("checked",true);
            });
            $(node.li).find("input").each(function(){
                var input = $(this);
                if (select) {
                    input.val(input.attr("data-perm"));
                } else {
                    input.val("");
                }
            });
        },
    });
});
</script>