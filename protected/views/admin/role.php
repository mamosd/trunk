<?php
    $this->breadcrumbs=array(
                array('label'=>'Home', 'url'=>array(Yii::app()->user->role->HomeUrl)),
                array('label'=>'Roles', 'url'=>array('admin/roles')),
                array('label'=>'Role'),
            );
    $baseUrl = Yii::app()->request->baseUrl;
    $cs=Yii::app()->getClientScript();
    $cs->registerCoreScript ( 'jquery.ui' );
    $cs->registerCSSFile($baseUrl.'/css/multi-select.css');
    $cs->registerScriptFile($baseUrl.'/js/jquery.multi-select.js',CClientScript::POS_END);
    $cs->registerCSSFile($baseUrl.'/css/ui.dynatree.css');
    $cs->registerScriptFile($baseUrl.'/js/jquery.dynatree.js',CClientScript::POS_END);
?>

<?php if (isset($model->roleId)) : ?>
<h1>Edit Role</h1>
<?php else : ?>
<h1>Add new Role</h1>
<?php endif; ?>

<div class="standardForm">
<?php $form=$this->beginWidget('CActiveForm', array(
            'id'=>'role-form',
            'errorMessageCssClass'=>'formError',
    )); ?>

    <?php
    echo $form->hiddenField($model, 'roleId');
    echo $form->errorSummary($model, "", "", array('class'=>'errorBox'));
    ?>

<div id="tabs">

    <ul>
        <li><a href="#tabs-g">General</a></li>
        <li><a href="#tabs-perms">Permissions</a></li>
        <!--li><a href="#tabs-users">Users</a></li-->
    </ul>

    <div id="tabs-g">

        <div>
            <?php echo $form->labelEx($model,'name'); ?>
            <?php echo $form->textField($model,'name', array('size'=>'50')); ?>
        </div>

        <div>
            <?php echo $form->labelEx($model,'description'); ?>
            <?php echo $form->textField($model,'description', array('size'=>'50')); ?>
        </div>

        <div>
            <?php echo $form->labelEx($model,'defaultForLoginRoleId') ?>
            <?php echo $form->dropDownList($model, 'defaultForLoginRoleId', LoginRole::getRoleOptions(), array( 'prompt' => '- None -'))?>
        </div>

        <div>
            <?php echo $form->labelEx($model,'parentRoles'); ?>
            <?php echo $form->listBox(
                $model,
                "parentRoles",
                Role::getAllAsOptionList($model->roleId),
                array("multiple" => "multiple","id" => "parentRoles"));
            ?>
        </div>
    </div>
    <div id="tabs-perms">
        <div>

<?php function permTree($list,$model) { ?>
<ul>
<?php foreach ($list as $l): ?>
    <li data="<?php echo (isset($l["Id"])?"key: '".$l["Id"]."',":""); ?>select: <?php echo (isset($l["Id"]) && in_array($l['Id'], $model->perms))? 'true' : 'false'  ?>,">
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


            <?php echo $form->labelEx($model,'perms'); ?>
            <div id="perms">
                <?php permTree(Permission::getAllAsTree(),$model); ?>
            </div>
            <div id="hidden_perms" style="display:none">
                <?php echo $form->checkBoxList($model,
                    'perms',
                    Permission::getOptions()) ?>
            </div>
        </div>
    </div>

    <!--div id="tabs-users">

        <h4>Add user to this role:</h4>
        <?php echo CHtml::dropDownList("userAddedToRole",null, $model->listExcludedUsers(), array( "prompt" => "- None -", "id" => "userAddedToRole" )); ?>
        <button type="button" id="addUserToRole">Add User To Role</button>
        <h3>Users using this role</h3>
        <ul>
            <?php foreach ($model->listUsers() as $user): ?>
                <li>
                    <a href="<?php echo $this->createUrl('admin/login', array("id"=>$user->LoginId, "type" => $user->LoginRoleId));?>" target="_blank">
                        <?php echo "{$user->FriendlyName} ({$user->UserName})"?>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>
    </div-->

</div>

    <div class="titleWrap">
        <?php echo CHtml::submitButton('Submit', array('class'=>'formButton')); ?>
        <ul>
            <li class="seperator">
                <img height="16" width="16" alt="add" src="<?php echo $baseUrl; ?>/img/icons/cancel.png">
                <a href="<?php echo $this->createUrl('admin/roles');?>">Cancel</a>
            </li>
        </ul>

    </div>

<?php
$this->endWidget(); ?>
</div>


<script>
$(function() {
    $( "#tabs" ).tabs();

    $("#parentRoles").multiSelect();

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
        }
    });

    $("#addUserToRole").click(function(){
        $.ajax({
            type: 'POST',
            url: '<?php echo Yii::app()->createUrl('admin/add_role_to_user'); ?>',
            data: {
                'RoleId' : <?php echo $model->roleId; ?>,
                'loginId': $('#userAddedToRole').val()
            },
            success: function() {
                location.reload();
            }
        });
    });

});
</script>
