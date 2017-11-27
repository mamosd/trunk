<?php
    $this->breadcrumbs=array(
                array('label'=>'Home', 'url'=>array(Yii::app()->user->role->HomeUrl)),
                array('label'=>'Secondary Routing', 'url'=>array('admin/secondaryrouting')),
                array('label'=>'Route Maintenance'),
            );
    $areas = $model->getOptionsArea();
?>


<h1>Route Maintenance</h1>


<?php echo CHtml::form('','post',array('enctype'=>'multipart/form-data'));

echo CHtml::errorSummary($model, "", "", array('class'=>'errorBox'));
?>

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
        <table class="listing" cellpadding="0" cellspacing="0">
        <tbody>
            <tr>
              <th>Code</th>
              <th>Bundle Size</th>
              <th>Bundle Weight</th>
              <th>Date Updated</th>
              <td>&nbsp;</td>
              <?php if (Yii::app()->user->role->LoginRoleId == LoginRole::SUPER_ADMIN): ?>
              <td>&nbsp;</td>
              <?php endif; ?>
            </tr>

            <?php
            //$routes = $model->getRoutes();
            $count = count($routes);
            for($i = 0; $i < $count; $i++):
            ?>

            <tr class="row<?php echo ($i%2)+1; ?>">
                <td><?php echo $routes[$i]->SecondaryRouteId; ?></td>
                <td><?php echo CHtml::textField("RoutesForm[{$routes[$i]->SecondaryRouteId}][BundleSize]", $routes[$i]->BundleSize, array('size'=>10)); ?></td>
                <td><?php echo CHtml::textField("RoutesForm[{$routes[$i]->SecondaryRouteId}][BundleWeight]", $routes[$i]->BundleWeight, array('size'=>10)); ?></td>
                <td><?php echo $routes[$i]->DateUpdated; ?></td>
                <td>
                    <?php
                        echo CHtml::link("Manage Rounds", $this->createUrl('admin/secondaryroutingroute', array('id'=>$routes[$i]->SecondaryRouteId)));
                    ?>
                </td>
                <?php //if (Yii::app()->user->role->LoginRoleId == LoginRole::SUPER_ADMIN): ?>
                <td>
                    <?php
                        echo CHtml::link("Delete Route", $this->createUrl('admin/secondaryroutingroutedelete', array('id'=>$routes[$i]->SecondaryRouteId)), array('class' => 'lnk-delete'));
                    ?>
                </td>
                <?php //endif; ?>
            </tr>

            <?php
            endfor;
            ?>
        </tbody>
        </table>
        
    </div>
    <?php
        endforeach;
        ?>
</div>        

<div class="titleWrap">
    <?php echo CHtml::submitButton('Submit', array('class'=>'formButton')); ?>
    <ul>
        <li class="seperator">
            <img height="16" width="16" alt="add" src="img/icons/cancel.png">
            <a href="<?php echo $this->createUrl(Yii::app()->user->role->HomeUrl);?>">Cancel</a>
        </li>
    </ul>
</div>
<?php echo CHtml::endForm(); ?>

<script>
$(document).ready(function(){
    $(".lnk-delete").click(function(){
        return confirm("The route is about to be deleted. Do you wish to proceed?");
    });
});
</script>