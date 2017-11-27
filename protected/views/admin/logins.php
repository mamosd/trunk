<?php
    $this->breadcrumbs=array(
                array('label'=>'Home', 'url'=>array(Yii::app()->user->role->HomeUrl)),
                array('label'=>'Login Management'),
            );
?>

<div class="titleWrap">
    <h1>Login Management</h1>
    <ul>
        <li class="seperator"><img src="img/icons/add.png" alt="add" />
            <a href="<?php echo $this->createUrl('admin/login');?>">Add New</a>
        </li>
    </ul>
</div>

<?php if (Yii::app()->user->role->LoginRoleId == LoginRole::SUPER_ADMIN): ?>
<h2>Super Administrator Logins</h2>
<table class="listing fluid" cellpadding="0" cellspacing="0">
<tbody>
    <tr>
      <th>User Name</th>
      <th>Is Active</th>
      <th>Date Last Updated</th>
      <th width="10%">Actions</th>
    </tr>

    <?php
    $count = count($logins);
    $idx = 0;
    for($i = 0; $i < $count; $i++):
        if(!strcmp($logins[$i]->LoginRoleId, LoginRole::SUPER_ADMIN)):
    ?>
        <tr class="row<?php echo (($idx%2)+1) ?>">
          <td><?php echo $logins[$i]->UserName ?></td>
          <td><?php echo ($logins[$i]->IsActive === '1') ? 'Yes' : 'No'; ?></td>
          <td><?php echo $logins[$i]->DateUpdated; ?></td>
          <td>
              <a href="<?php echo $this->createUrl('admin/login', array('id'=>$logins[$i]->LoginId));?>">
              <img src="img/icons/page_edit.png" alt="edit" width="16" height="16" />
              Edit</a>
          </td>
        </tr>
    <?php
            $idx++;
        endif;
    endfor;

    if ($idx === 0) :
    ?>
        <tr class="row1">
            <td colspan="4">
                <div class="infoBox">There are no super administrator logins setup on the system, <a href="<?php echo $this->createUrl('admin/login');?>">add a new one now</a>.</div>
            </td>
        </tr>
    <?php
    endif;
    ?>
</tbody>
</table>
<?php endif; ?>

<h2>Administrator Logins</h2>
<table class="listing fluid" cellpadding="0" cellspacing="0">
<tbody>
    <tr>
      <th>User Name</th>
      <th>Is Active</th>
      <th>Date Last Updated</th>
      <th width="10%">Actions</th>
    </tr>

    <?php
    $count = count($logins);
    $idx = 0;
    for($i = 0; $i < $count; $i++):
        if(!strcmp($logins[$i]->LoginRoleId, LoginRole::ADMINISTRATOR)):
    ?>
        <tr class="row<?php echo (($idx%2)+1) ?>">
          <td><?php echo $logins[$i]->UserName ?></td>
          <td><?php echo ($logins[$i]->IsActive === '1') ? 'Yes' : 'No'; ?></td>
          <td><?php echo $logins[$i]->DateUpdated; ?></td>
          <td>
              <a href="<?php echo $this->createUrl('admin/login', array('id'=>$logins[$i]->LoginId));?>">
              <img src="img/icons/page_edit.png" alt="edit" width="16" height="16" />
              Edit</a>
          </td>
        </tr>
    <?php
            $idx++;
        endif;
    endfor;

    if ($idx === 0) :
    ?>
        <tr class="row1">
            <td colspan="4">
                <div class="infoBox">There are no administrator logins setup on the system, <a href="<?php echo $this->createUrl('admin/login');?>">add a new one now</a>.</div>
            </td>
        </tr>
    <?php
    endif;
    ?>
</tbody>
</table>

<h2>Supplier Logins</h2>
<table class="listing fluid" cellpadding="0" cellspacing="0">
<tbody>
    <tr>
      <th>User Name</th>
      <th>Is Active</th>
      <th>Date Last Updated</th>
      <th width="10%">Actions</th>
    </tr>

    <?php
    $count = count($logins);
    $idx = 0;
    for($i = 0; $i < $count; $i++):
        if(!strcmp($logins[$i]->LoginRoleId, LoginRole::SUPPLIER)):
    ?>
        <tr class="row<?php echo (($idx%2)+1) ?>">
          <td><?php echo $logins[$i]->UserName ?></td>
          <td><?php echo ($logins[$i]->IsActive === '1') ? 'Yes' : 'No'; ?></td>
          <td><?php echo $logins[$i]->DateUpdated; ?></td>
          <td>
              <a href="<?php echo $this->createUrl('admin/login', array('id'=>$logins[$i]->LoginId));?>">
              <img src="img/icons/page_edit.png" alt="edit" width="16" height="16" />
              Edit</a>
          </td>
        </tr>
    <?php
            $idx++;
        endif;
    endfor;

    if ($idx === 0) :
    ?>
        <tr class="row1">
            <td colspan="4">
                <div class="infoBox">There are no supplier logins setup on the system, <a href="<?php echo $this->createUrl('admin/login');?>">add a new one now</a>.</div>
            </td>
        </tr>
    <?php
    endif;
    ?>
</tbody>
</table>

<h2>Client Logins</h2>
<table class="listing fluid" cellpadding="0" cellspacing="0">
<tbody>
    <tr>
      <th>User Name</th>
      <th>Is Active</th>
      <th>Date Last Updated</th>
      <th width="10%">Actions</th>
    </tr>

    <?php
    $count = count($logins);
    $idx = 0;
    for($i = 0; $i < $count; $i++):
        if(!strcmp($logins[$i]->LoginRoleId, LoginRole::CLIENT)):
    ?>
        <tr class="row<?php echo (($idx%2)+1) ?>">
          <td><?php echo $logins[$i]->UserName ?></td>
          <td><?php echo ($logins[$i]->IsActive === '1') ? 'Yes' : 'No'; ?></td>
          <td><?php echo $logins[$i]->DateUpdated; ?></td>
          <td>
              <a href="<?php echo $this->createUrl('admin/login', array('id'=>$logins[$i]->LoginId));?>">
              <img src="img/icons/page_edit.png" alt="edit" width="16" height="16" />
              Edit</a>
          </td>
        </tr>
    <?php
            $idx++;
        endif;
    endfor;

    if ($idx === 0) :
    ?>
        <tr class="row1">
            <td colspan="4">
                <div class="infoBox">There are no client logins setup on the system, <a href="<?php echo $this->createUrl('admin/login');?>">add a new one now</a>.</div>
            </td>
        </tr>
    <?php
    endif;
    ?>
</tbody>
</table>