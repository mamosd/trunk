<?php
    $this->breadcrumbs=array(
                array('label'=>'Home', 'url'=>array(Yii::app()->user->role->HomeUrl)),
                array('label'=>'Routes'),
            );
?>

<style>
    
    table.listing .row1 td {
	background:#eeeeee;
        padding: 6px;
    }
    table.listing .row2 td {
        background:#eeeeee;
        padding: 6px;
    }
    
    .route-link {
        cursor: pointer;
    }
    
    #mainWrap {
        width: 70% !important;
    }
    .vtop th {
        vertical-align: middle;
    }
    .white {
        background-color: #FFF;
    }
    .red {
        background-color: #f46464;
    }
    .blue {
        background-color: #6af56e;
    }
    .sortable-hand {
        cursor: pointer;
    }
    
    
    .selected-type {
        /*color: red;*/
    }
    
    .unselected-type {
        font-weight: normal !important;
        text-decoration: none !important;
    }
    
    .route-listing tr:hover td { background: #A3FFA3; }
    .highlighted-row td { background: #FFFF99 !important; }
    .ui-state-active, .ui-widget-content .ui-state-active, .ui-widget-header .ui-state-active {
        background: #21E621 !important;
    }
    .ui-state-active a, .ui-state-active a:link, .ui-state-active a:visited {
        color: #fff !important;
    }
</style>

<div class="titleWrap">
    <h1>Add/Edit Routes</h1>
    <ul>
        <li class="seperator"><img src="img/icons/add.png" alt="add" />
            <a href="<?php echo $this->createUrl('admin/route');?>">Add New</a>
        </li>
    </ul>
</div>

<table class="listing fluid vtop sortable route-listing" cellpadding="0" cellspacing="0">
    <thead>
        <tr>
          <th>Round Name</th>
          <th>Supplier</th>
          <th style="width:260px;"></th>
          <th width="10%">Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $count = count($routes);
        for($i = 0; $i < $count; $i++):
        ?>
            <tr class="row<?php echo (($i%2)+1) ?>">
              <td class="route-name"><?php echo $routes[$i]->Name ?></td>
              <td class="route-name"><?php echo $routes[$i]->supplierName ?></td>
              <td class="route-name"></td>
              <td style="white-space: nowrap;">
                  <a href="<?php echo $this->createUrl('admin/route', array('id'=>$routes[$i]->RouteId));?>">
                  <img src="img/icons/page_edit.png" alt="edit" width="16" height="16" />
                  Edit</a>

                  <a class="lnkDelete"
                      href="<?php echo $this->createUrl('admin/routedelete', array('id'=>$routes[$i]->RouteId));?>">
                  <img src="img/icons/cancel.png" alt="delete" width="16" height="16" />
                  Delete</a>
              </td>
            </tr>
        <?php
        endfor;

        if ($count === 0) :
        ?>
            <tr class="row1">
                <td colspan="4">
                    <div class="infoBox">There are no routes setup on the system, <a href="<?php echo $this->createUrl('admin/route');?>">add a new one now</a>.</div>
                </td>
            </tr>
        <?php
        endif;
        ?>
    </tbody>
</table>

<script>
$(function(){
    $(".lnkDelete").click(function(){
        var $row = $(this).parents('tr:first');
        var $nameCell = $(".route-name", $row);
        var routeName = $nameCell.text();
        if (!confirm("Please confirm you wish to delete the route \""+ routeName +"\"."))
            return false;
    });
});

//onclick css
var currentTab = "";
var currentFilter = "";

    
$(function(){

    var selected = $(".ui-tabs-selected");
    var selTab = currentTab;
    if (selected.hasClass("cat-dtr"))
        $("#lnkDtr").click();
    else
        $("#lnkDtc").click();
    $("#tabs").tabs("select", selTab);
    
    $('.route-listing td').click(function(){
        var cssClass = 'highlighted-row';
        var $row = $(this).parents('tr:first');
        if ($row.hasClass(cssClass))
            $row.removeClass(cssClass);
        else
            $row.addClass(cssClass);
    });
});

</script>