<?php
    $this->breadcrumbs=array(
                array('label'=>'Home', 'url'=>array(Yii::app()->user->role->HomeUrl)),
                array('label'=>'Secondary Routing'),
            );
    $areas = $model->getOptionsArea();
    $baseUrl = Yii::app()->request->baseUrl;    
?>

<h1>Secondary Routing - Process Rounds</h1>

<?php if ($axn == 'init'): ?>
<div class="infoBox">
    This will RE-INITIALIZE the secondary routes/rounds/mappings with the information on the spreadsheet. <br />
    NOTES: <br />
    - Only ONE sheet with route/sort order information (cols A-C) must exist on the spreadsheet.<br />
    - Do not include bundle size/total copies lines<br />
<!--    - This will delete and recreate the information for this on the system, so ONLY one spreadsheet must contain ALL ROUTES information.<br/>-->
    - Verify that the # of processed rows on the success message matches the number of rows in the document.
</div>
<?php endif;?>


<?php if ((isset($result['rows']))&&($result['rows'] != 0)): ?>
<div class="successBox">
    <?php echo $result['rows']; ?> rows processed
</div>
<?php endif;?>
<?php if ((isset($result['routes']))&&(count($result['routes']) != 0)): ?>
<div class="successBox">
    Routes updated: <?php echo implode(', ', $result['routes']) ; ?>.
</div>
<?php endif;?>
<?php if ((isset($result['newRounds']))&&($result['newRounds'] != false)): ?>
<div class="warningBox">
    New Rounds have been found on the provided spreadsheet, they have been appended to the bottom of the list (maximum sort order assigned).
</div>
<?php endif;?>



<div class="standardForm">
    <?php echo CHtml::form('','post',array('enctype'=>'multipart/form-data'));

    echo CHtml::errorSummary($model, "", "", array('class'=>'errorBox'));
    ?>

    <div>
        <?php echo CHtml::activeLabelEx($model,'spreadSheet'); ?>
        <?php echo CHtml::activeFileField($model,'spreadSheet'); ?>
    </div>
    
    <div>
        <?php echo CHtml::activeLabelEx($model,'area'); ?>
        <?php echo CHtml::activeDropDownList($model,'area', $areas, array('empty' => '-- select one')); ?>
    </div>

    <div class="titleWrap">
        <?php echo CHtml::submitButton('Submit', array('class'=>'formButton')); ?>
    </div>

    <?php echo CHtml::endForm(); ?>
</div>

<div class="titleWrap">
    <h1>Existing Routes</h1>
    <ul>
        <li>
            <a href="#" id="lnkReferences" title="Reference">colour reference</a>
        </li>
        <li class="seperator">
            <button id="btnDeleteAll" style="padding:5px;">
                <span class="span-action-text">Delete Selected</span> 
                (<span class="span-selected">0</span>)
                <img src="<?php echo $baseUrl; ?>/img/icons/cancel.png" />
            </button>
        </li>
    </ul>
</div>

<?php
// sort routes by AreaId
$routes = $model->getRoutes();
$sorted = array();
foreach ($routes as $r) {
    $r->AreaId = (empty($r->AreaId)) ? 0 : $r->AreaId;
    if (!isset($sorted[$r->AreaId]))
            $sorted[$r->AreaId] = array();
    $sorted[$r->AreaId][] = $r;
}

$areasFound = array_keys($sorted);
?>

<div id="tabs">    

    <ul>
        <?php
        foreach ($areasFound as $a): ?>
            <li><a href="#tabs-<?php echo $a; ?>"><?php echo isset($areas[$a])? $areas[$a] : 'No Area'; ?></a></li>
        <?php
        endforeach;
        ?>
    </ul>
    
    <?php
        foreach ($areasFound as $a): 
            
            $routes = $sorted[$a];
            
            ?>
    <div id="tabs-<?php echo $a; ?>">
        
        <table class="listing fluid" cellpadding="0" cellspacing="0">
        <tbody>
            <tr>
              <th width="1">Actions</th>
              <th width="1"></th>
              <th>Code</th>
              <th width="20%">Date Updated</th>
            </tr>

            <?php
            $count = count($routes);
            for($i = 0; $i < $count; $i++):

                $adu = explode('-', $routes[$i]->DateUpdated);
                $adud = explode(' ',$adu[2]);
                $dateUpdated = mktime(0, 0, 0, $adu[1], $adud[0], $adu[0]);
                $now = time();
                $dateDiff = $now - $dateUpdated;
                $daysDiff = floor($dateDiff/(60*60*24));

                $cssclass = 'redbg';
                if ($daysDiff < 1)
                    $cssclass = 'greenbg';
                if ($daysDiff == 1)
                    $cssclass = 'amberbg';
            ?>
                <tr class="<?php echo $cssclass; ?>">
                    <td style="white-space: nowrap; text-align: right;">
                      <a href="<?php echo $this->createUrl('admin/secondaryroutingexport', array('id'=>$routes[$i]->SecondaryRouteId, 'format'=>'xls'));?>"
                         title="Export">
                      <img src="img/icons/page_excel.png" alt="edit" width="16" height="16" /></a>
                      <!--<a href="<?php echo $this->createUrl('admin/secondaryroutingexport', array('id'=>$routes[$i]->SecondaryRouteId, 'format'=>'pdf'));?>">
                      <img src="img/icons/page_white_acrobat.png" alt="edit" width="16" height="16" /></a>-->
                      <a class="lnkDelete"
                         rel="<?php echo $routes[$i]->SecondaryRouteId ?>"
                        href="<?php echo $this->createUrl('admin/secondaryroutedelete', array('secondaryrouteid'=>$routes[$i]->SecondaryRouteId));?>"
                        title="Delete">
                        <img src="img/icons/cancel.png" alt="delete" width="16" height="16" />
                      </a>
                  </td>
                  <td>
                      <input 
                          type="checkbox"
                          class="picked"
                          rel="<?php echo $routes[$i]->SecondaryRouteId; ?>"
                          />
                  </td>
                  <td><?php echo $routes[$i]->SecondaryRouteId ?></td>
                  <td><?php echo $routes[$i]->DateUpdated ?></td>
                </tr>
            <?php
            endfor;

            if ($count === 0) :
            ?>
                <tr class="row1">
                    <td colspan="4">
                        <div class="infoBox">There are no routes (secondary) setup on the system.</div>
                    </td>
                </tr>
            <?php
            endif;
            ?>
        </tbody>
        </table>
        
        
    </div>
    <?php
        endforeach;
        ?>
</div>






<div style="display:none;">
<div id="divReferences">
    <div class="greenbg padded-5">Route updated today.</div>
    <br/>
    <div class="amberbg padded-5">Route updated yesterday.</div>
    <br/>
    <div class="redbg padded-5">Route not updated within the last 48 hours.</div>
</div>
</div>

<script type="text/javascript">
    $(document).ready(function() {
        $("#lnkReferences").colorbox({inline:true, href:"#divReferences"}
        );
    });
    
    $(function(){
    $(".lnkDelete").click(function(){
        var routeName = $(this).attr('rel');
        if (!confirm("Please confirm you wish to delete the route \""+ routeName +"\"."))
            return false;
    });
    
    $(".picked").click(function(){
        $(".span-selected").text($(".picked:checked").length);
    });
    
    $("#btnDeleteAll").click(function(){
        if (!routesSelected())
            return false;
        
        if (!confirm('Please confirm you wish to delete all of the selected routes.'))
            return false;
        
        var routes = $(".picked:checked").map(function(){
            return $(this).attr('rel');
        }).get()
        .join('|');

        var url = "<?php echo $this->createUrl('admin/secondaryroutedelete'); ?>";
        url += (url.indexOf("?") == -1) ? "?" : "&";
        url += "secondaryrouteid=" + routes;
        
        location.href = url;
    }); 
});

function routesSelected() {
    var sel = $(".picked:checked").length;
    if (sel < 1) {
        alert('You must select at least one route to continue.');
        return false;
    }
    return true;
}

</script>