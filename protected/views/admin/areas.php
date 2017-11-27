<?php
    $this->breadcrumbs=array(
                array('label'=>'Home', 'url'=>array(Yii::app()->user->role->HomeUrl)),
                array('label'=>'Area Management'),
            );
?>

<div class="titleWrap">
    <h1>Area Management</h1>
    <ul>
        <li class="seperator"><img src="img/icons/add.png" alt="add" />
            <a href="<?php echo $this->createUrl('admin/area');?>">Add New</a>
        </li>
    </ul>
</div>

<table class="listing fluid" cellpadding="0" cellspacing="0">
<tbody>
    <tr>
      <th width="5%">Id</th>
      <th>Name</th>
      <th width="20%">Date Last Updated</th>
      <th width="10%">Actions</th>
    </tr>

    <?php
    $count = count($areas);
    $idx = 0;
    for($i = 0; $i < $count; $i++):
    ?>
        <tr class="row<?php echo (($idx%2)+1) ?>">
          <td><?php echo $areas[$i]->Id ?></td>
          <td><?php echo $areas[$i]->Name; ?></td>
          <td><?php echo $areas[$i]->DateUpdated; ?></td>
          <td>
              <a href="<?php echo $this->createUrl('admin/area', array('id'=>$areas[$i]->Id));?>">
              <img src="img/icons/page_edit.png" alt="edit" width="16" height="16" />
              Edit</a>
          </td>
        </tr>
    <?php
            $idx++;
    endfor;

    if ($idx === 0) :
    ?>
        <tr class="row1">
            <td colspan="4">
                <div class="infoBox">There are no areas on the system, <a href="<?php echo $this->createUrl('admin/area');?>">add a new one now</a>.</div>
            </td>
        </tr>
    <?php
    endif;
    ?>
</tbody>
</table>