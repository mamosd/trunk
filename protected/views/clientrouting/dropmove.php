<?php
    $this->breadcrumbs=array(
                array('label'=>'Home', 'url'=>array(Yii::app()->user->role->HomeUrl)),
                array('label'=>'Client Routing', 'url' => '#'),
                array('label'=>'Drop Move')
            );
    
    $baseUrl = Yii::app()->request->baseUrl;

    $routeInfo = $model->info;
    $deliveryDate = CTimestamp::formatDate("d/m/Y",CDateTimeParser::parse($routeInfo->DeliveryDate, "yyyy-MM-dd"))
?>
<h1>Move Drop</h1>
<h1><?php echo $routeInfo->ClientName.' ('.$routeInfo->PrintCentreName.') - '.$deliveryDate.' - '.$routeInfo->RouteId.'<br/>Wholesaler: '.$routeInfo->WholesalerAlias;  ?></h1>

<div class="standardForm">
<?php $form=$this->beginWidget('CActiveForm', array(
            'id'=>'move-form',
            'errorMessageCssClass'=>'formError',
    )); ?>

    <?php
    echo $form->hiddenField($model, 'instanceDetailsId');
    echo $form->errorSummary($model, "", "", array('class'=>'errorBox'));
    ?>

    <div>
        <?php echo CHtml::activeLabelEx($model,'newRoute'); ?>
        <?php echo CHtml::activeDropDownList($model,'newRoute', $model->getNewRouteOptions(), array('empty' => '-- select one')); ?>
    </div>
    
    <div class="titleWrap">
        <?php echo CHtml::submitButton('Move to Selected Route', array('class'=>'formButton')); ?>
    </div>
    
    <?php
$this->endWidget(); ?>
</
