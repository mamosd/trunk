<?php
    $this->breadcrumbs=array(
                array('label'=>'Home', 'url'=>array(Yii::app()->user->role->HomeUrl)),
                array('label'=>'Roles'),
            );
    $baseUrl = Yii::app()->request->baseUrl;

    $cancelImg = CHtml::image("$baseUrl/img/icons/cancel.png");
    $acceptImg = CHtml::image("$baseUrl/img/icons/accept.png");


    $cs=Yii::app()->getClientScript();

    $cs->registerCSS("","
.row_daily {
    background-color: rgba(255,255,0,0.3);
}
#mainWrap {
    width: 95% !important;
}
");
?>

<div class="titleWrap">
    <h1>Add/Edit Roles</h1>
    <div style="float: left; padding-top: 20px; padding-left: 10px;">
        <form action="" id="change-live-form">
            <?php echo CHtml::listBox('isLive', $isLive, array(1=>'Live roles', 0=>'All roles'), array('size'=>1, 'onchange'=>"$('#change-live-form').submit();"));?>
        </form>
    </div>
    <ul>
        <li class="seperator"><img src="<?php echo $baseUrl; ?>/img/icons/add.png" alt="add" />
            <a href="<?php echo $this->createUrl('admin/role');?>">Add New</a>
        </li>
    </ul>
</div>

<table class="listing fluid" cellpadding="0" cellspacing="0">
<tbody>
    <tr>
      <th>Name</th>
      <th>Description</th>
      <th>Live</th>
      <th>Default for Login Role</th>
      <th>Last updated</th>
      <th>Last updated by</th>
    </tr>
    <?php foreach ($roles as $role): ?>
        <tr class="<?php echo cycle("row1","row2") ?>">
          <td><?php echo $role->Name ?></td>
          <td><?php echo $role->Description ?></td>
          <td><?php echo $role->IsLive ? $acceptImg : $cancelImg; ?></td>
          <td><?php echo isset($role->DefaultForLoginRole)? $role->DefaultForLoginRole->Description : ""?></td>
          <td><?php echo $role->DateUpdated ?></td>
          <td><?php echo @$role->UpdatedByUser->FriendlyName ?></td>
          <td>
              <a href="<?php echo $this->createUrl('admin/role', array('id'=>$role->Id));?>">
                <img src="<?php echo $baseUrl; ?>/img/icons/page_edit.png" alt="edit" width="16" height="16" />
                Edit
              </a>
          </td>
        </tr>
    <?php
    endforeach;
    if (empty($roles)) :
    ?>
        <tr class="row1">
            <td colspan="4">
                <div class="infoBox">There are no roles setup on the system, <a href="<?php echo $this->createUrl('admin/role');?>">add a new one now</a>.</div>
            </td>
        </tr>
    <?php
    endif;
    ?>
</tbody>
</table>