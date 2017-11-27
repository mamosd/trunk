<?php
    $this->breadcrumbs=array(
                array('label'=>'Home', 'url'=>array(Yii::app()->user->role->HomeUrl)),
                array('label'=>'Delivery Points'),
            );
    
    $baseUrl = Yii::app()->request->baseUrl;
    $cs = Yii::app()->getClientScript();
    $cs->registerScriptFile($baseUrl.'/js/sorttable.js');
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
    <h1>Add/Edit Delivery Points</h1>
    <ul>
        <?php
        if ( Login::checkPermission('#^navigation/NQ/secondary/#',true) && Login::checkPermission('#^navigation/NQ/secondary/#',true) )        
        {
        ?>
        <li>
            Filter: 
            <select id="ddlFilter">
                <option value="*">Show All</option>
                <option value="Primary">Show Primary</option>
                <option value="Secondary">Show Secondary</option>
            </select>
        </li>
        <?php
        }
        ?>
        <li class="seperator"><img src="img/icons/add.png" alt="add" />
            <a href="<?php echo $this->createUrl('admin/deliverypoint');?>">Add New</a>
        </li>
    </ul>
</div>

<table class="listing fluid vtop sortable route-listing" cellpadding="0" cellspacing="0" id="tblListing">
    <thead>
        <tr>
            <th>Name</th>
            <th>Address</th>
            <th class="live-flag">NQ</th>
            <th width="10%">Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $count = count($dps);
        for($i = 0; $i < $count; $i++):
        ?>
            <tr class="row<?php echo (($i%2)+1) ?>">
              <td><?php echo $dps[$i]->Name ?></td>
              <td><?php echo str_replace("\n", '<br />', $dps[$i]->Address)  ?></td>
              <td class="live-flag" sorttable_customkey="<?php echo $dps[$i]->NQ ?>">
                  <?php echo $dps[$i]->NQ ?>
              </td>
              <td>
                  <a href="<?php echo $this->createUrl('admin/deliverypoint', array('id'=>$dps[$i]->DeliveryPointId));?>">
                  <img src="img/icons/page_edit.png" alt="edit" width="16" height="16" />
                  Edit</a>
              </td>
            </tr>
        <?php
        endfor;

        if ($count === 0) :
        ?>
            <tr class="row1">
                <td colspan="4">
                    <div class="infoBox">There are no delivery points setup on the system, <a href="<?php echo $this->createUrl('admin/deliverypoint');?>">add a new one now</a>.</div>
                </td>
            </tr>
        <?php
        endif;
        ?>
    </tbody>
</table>

<script>
    $(function(){
        
        var liveTH = null;
        var headers = document.getElementsByTagName("th");
        for (var i = 0; i < headers.length; i++)
            if (headers[i].className == 'live-flag')
                liveTH = headers[i];
        
        $("#ddlFilter").change(function(){
            var filter = $(this).val();
            var $tbl = $("#tblListing tbody");
                        
            sorttable.innerSortFunction.apply(liveTH, []);
            
            switch(filter)
            {
                case "*":
                    $("tr", $tbl).show();
                    break;
                case "Primary":
                    $("tr", $tbl).hide();
                    $("tr", $tbl).find(".live-flag[sorttable_customkey='Primary']").each(function(){
                        $(this).parents('tr:first').show();
                    });
                    $("tr", $tbl).find(".live-flag[sorttable_customkey='All']").each(function(){
                        $(this).parents('tr:first').show();
                    });
                    break;
                case "Secondary":
                    $("tr", $tbl).hide();
                    $("tr", $tbl).find(".live-flag[sorttable_customkey='Secondary']").each(function(){
                        $(this).parents('tr:first').show();
                    });
                    $("tr", $tbl).find(".live-flag[sorttable_customkey='All']").each(function(){
                        $(this).parents('tr:first').show();
                    });
                    break;
            }
        });
    });
</script>

<script>
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