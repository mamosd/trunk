<?php
    $this->breadcrumbs=array(
                array('label'=>'Home', 'url'=>array(Yii::app()->user->role->HomeUrl)),
                array('label'=>'Print Centres'),
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
    
    
    .couponcode:hover .coupontooltip {
        display: block;
    }


    .coupontooltip {
        display: none;
        background: #FFFFFF;
        box-shadow: 0px 0px 3px #000000;
        filter: progid:DXImageTransform.Microsoft.DropShadow(OffX=3, OffY=3, Color=#CCCCCC);
        border-radius: 5px;
        margin-left: 28px;
        padding: 20px;
        position: absolute;
        z-index: 1000;
        width:200px;
        height:50px;
    }

    .couponcode {
        margin:0px;
    }    
</style>

<div class="titleWrap">
    <h1>Add/Edit Print Centres</h1>
    <ul>
        <li>
            Filter: 
            <select id="ddlFilter">
                <option value="*">All</option>
                <option selected="selected" value="ON">Live</option>
                <option value="OFF">Not Live</option>
            </select>
        </li>        
        <li class="seperator"><img src="img/icons/add.png" alt="add" />
            <a href="<?php echo $this->createUrl('admin/printcentre');?>">Add New</a>
        </li>
    </ul>
</div>

<table class="listing fluid vtop sortable route-listing" cellpadding="0" cellspacing="0" id="tblListing">
    <thead>
    <tr>
      <th>Name</th>
      <th>Address</th>
      <th>Postal Code</th>
      <th>County</th>
      <th>Creation Date</th>
      <th>Update Date</th>
      <th>Updated By</th>
      <th class="live-flag">Live</th>
      <th width="10%">Actions</th>
    </tr>
    </thead>
    <tbody>
        <?php
        $count = count($pcs);
        for($i = 0; $i < $count; $i++):
        ?>
            <tr class="row<?php echo (($i%2)+1) ?>">
              <td><?php echo $pcs[$i]->Name ?></td>
              <td>
              <div class="couponcode">Show address
                <span class="coupontooltip"><?php echo str_replace("\n", '<br />', $pcs[$i]->Address)  ?></span>
              </div>
              </td>
              <td><?php echo $pcs[$i]->postalCode ?></td>
              <td><?php echo $pcs[$i]->county ?></td>
              <td><?php echo $pcs[$i]->DateCreated ?></td>
              <td><?php echo $pcs[$i]->DateUpdated ?></td>
              <td><?php echo $pcs[$i]->UpdatedBy ?></td>
              <td class="live-flag" sorttable_customkey="<?php echo $pcs[$i]->Enabled; ?>">
                  <?php
                  echo $pcs[$i]->Enabled==0? $a='<img src="img/icons/cancel.png" alt="edit" width="16" height="16" />' : $a='<img src="img/icons/accept.png" alt="edit" width="16" height="16" />';
                  ?>
              </td>
              <td>
                  <a href="<?php echo $this->createUrl('admin/printcentre', array('id'=>$pcs[$i]->PrintCentreId));?>">
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
                    <div class="infoBox">There are no print centres setup on the system, <a href="<?php echo $this->createUrl('admin/printcentre');?>">add a new one now</a>.</div>
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
                        
            //sorttable.innerSortFunction.apply(liveTH, []);
            
            switch(filter)
            {
                case "*":
                    $("tr", $tbl).show();
                    break;
                case "ON":
                    $("tr", $tbl).hide();
                    $("tr", $tbl).find(".live-flag[sorttable_customkey='1']").each(function(){
                        $(this).parents('tr:first').show();
                    });
                    break;
                case "OFF":
                    $("tr", $tbl).hide();
                    $("tr", $tbl).find(".live-flag[sorttable_customkey='0']").each(function(){
                        $(this).parents('tr:first').show();
                    });
                    break;
            }
        });
        
        $("#ddlFilter").change();
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