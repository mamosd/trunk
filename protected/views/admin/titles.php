<?php
    $this->breadcrumbs=array(
                array('label'=>'Home', 'url'=>array(Yii::app()->user->role->HomeUrl)),
                array('label'=>'Titles'),
            );
    
    $weekdays = Yii::app()->locale->getWeekDayNames();
    $baseUrl = Yii::app()->request->baseUrl;
    
    $cs = Yii::app()->getClientScript();
    $cs->registerScriptFile($baseUrl.'/js/sorttable.js');
?>

<style>

    table.listing .row1 td 
    {
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
    
    #mainWrap {
        width: 95% !important;
    }
 
</style>

<div class="titleWrap">
    <h1>Add/Edit Titles</h1>
    <ul>
        <li>
            Show
            <?php 
                $filter = isset($_GET['f']) ? $_GET['f'] : '1';
                echo CHtml::dropDownList('ddlFilter', $filter, array('*' => 'All', '1' => 'Live', '0' => 'Not Live'));
                ?>
        </li>

        <li>
            Print Centre
            <?php
            $filterPrintCentres = isset($_GET['fp']) ? $_GET['fp'] : '*';
            $printCentres=Title::model()->findAll(array(
            'select'=>'t.PrintCentreId,b.Name',
            'join'=>'INNER JOIN printcentre as b'
                . ' on b.PrintCentreId=t.PrintCentreId',
            'group'=>'t.PrintCentreId',
            'distinct'=>true,
            ));
            $list=CHtml::listData($printCentres,'Name','Name');

            $list=array('*' => 'All')+$list;

            echo CHtml::dropDownList('printCentres', $filterPrintCentres, $list);
            ?>
        </li>        
        
        <li class="seperator"><img src="img/icons/add.png" alt="add" />
            <a href="<?php echo $this->createUrl('admin/title');?>">Add New</a>
        </li>
    </ul>
</div>

<table id="tblListing" class="listing fluid vtop sortable route-listing" cellpadding="0" cellspacing="0">

<thead>
    <tr>
      <th>Name</th>
      <th>Print Centre</th>
      <th>Login / Region</th>
      <th>Print Day</th>
      <th>Off Press Time</th>
      <th>Weight per Page</th>
      <th>Live</th>
      <th width="5%">Actions</th>
    </tr>
</thead>

<tbody>
    <?php
    $count = count($titles);
    for($i = 0; $i < $count; $i++):
    ?>
        <tr class="row<?php echo (($i%2)+1) ?>">
          <td><?php echo $titles[$i]->Name ?></td>
          <td><?php echo $titles[$i]->PrintCentreName ?></td>
          <td><?php echo $titles[$i]->LoginFriendlyName ?></td>
          <td><?php echo $weekdays[$titles[$i]->PrintDay] ?></td>
          <td><?php echo ($titles[$i]->OffPressTime === '0' || empty($titles[$i]->OffPressTime)) ? '-' : $titles[$i]->OffPressTime ?></td>
          <td><?php echo $titles[$i]->WeightPerPage == 0 ? '-' : $titles[$i]->WeightPerPage ?></td>
          <td>
              <img src="<?php echo $baseUrl ?>/img/icons/<?php echo $titles[$i]->IsLive == 1 ? 'accept' : 'cancel' ?>.png" />
              
          </td>
          <td>
              <a href="<?php echo $this->createUrl('admin/title', array('id'=>$titles[$i]->TitleId));?>">
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
                <div class="infoBox">There are no titles setup on the system, <a href="<?php echo $this->createUrl('admin/title');?>">add a new one now</a>.</div>
            </td>
        </tr>
    <?php
    endif;
    ?>
</tbody>
</table>

<?php
$urlParams=$_GET;
unset($urlParams['r']);
$urlParams['f']='999';
?>

<script>
$(function(){
    $("#ddlFilter").change(function(){
        var url = "<?php echo $this->createUrl('admin/titles', array_merge( $_GET,array('f' => '999') ) ) ?>";
        var filter = $(this).val();
        url = url.replace('999', filter);
        location.href = url;
    });

    $("#printCentres").change(function(){
        var url = "<?php echo $this->createUrl('admin/titles',  array_merge( $_GET,array('fp' => '999') ) ) ?>";
        var filter = $(this).val();
        url = url.replace('999', filter);
        location.href = url;
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