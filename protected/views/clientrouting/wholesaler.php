<?php
    $this->breadcrumbs=array(
                array('label'=>'Home', 'url'=>array(Yii::app()->user->role->HomeUrl)),
                array('label'=>'Client Routing', 'url' => '#'),
                array('label'=>'Wholesaler')
            );
    
    $baseUrl = Yii::app()->request->baseUrl;
?>

<h1>Edit Wholesaler</h1>

<?php echo CHtml::form('','post');
    echo CHtml::errorSummary($model, "", "", array('class'=>'errorBox'));
    
    echo CHtml::activeHiddenField($model, 'wholesalerId');
    echo CHtml::activeHiddenField($model, 'clientId');
    
    ?>

<div class="standardForm stackedForm">

    <div class="field">
        <?php echo CHtml::activeLabelEx($model,'code'); ?>
        <?php echo CHtml::activeTextField($model,'code', array('size' => 5)); ?>
    </div>
    
    <div class="field">
        <?php echo CHtml::activeLabelEx($model,'name'); ?>
        <?php echo CHtml::activeTextField($model,'name', array('size' => 50)); ?>
    </div>
    
    <div class="field">
        <?php echo CHtml::activeLabelEx($model,'alias'); ?>
        <?php echo CHtml::activeTextField($model,'alias', array('size' => 50)); ?>
    </div>
    
    <?php if(!empty($model->linkedWholesalers)): 
        echo CHtml::activeLabelEx($model,'linkedWholesalers');
        
        foreach ($model->linkedWholesalers as $item): ?>
            | <a href="<?php echo $this->createUrl('clientrouting/wholesaler', array('ui'=>'popUp', 'wsid' => $item->ClientWholesalerId));?>">
                <?php echo $item->FriendlyName; ?>
            </a>
    <?php 
        endforeach;
    else: ?>
    <div class="field">
        <?php echo CHtml::activeLabelEx($model,'groupUnder'); ?>
        <?php echo CHtml::activeDropDownList($model,'groupUnder', $model->getWholesalerOptions(), array('empty' => '-- none')); ?>
    </div>
    <?php endif; ?>
            
    <br style="clear:both"/>
            
    <div class="field">
        <?php echo CHtml::activeLabelEx($model,'address1'); ?>
        <?php echo CHtml::activeTextField($model,'address1', array('size' => 75)); ?>
    </div>
    
    <div class="field">
        <?php echo CHtml::activeLabelEx($model,'address2'); ?>
        <?php echo CHtml::activeTextField($model,'address2', array('size' => 75)); ?>
    </div>
    
    <div class="field">
        <?php echo CHtml::activeLabelEx($model,'address3'); ?>
        <?php echo CHtml::activeTextField($model,'address3', array('size' => 75)); ?>
    </div>
    
    <div class="field">
        <?php echo CHtml::activeLabelEx($model,'address3'); ?>
        <?php echo CHtml::activeTextField($model,'address3', array('size' => 75)); ?>
    </div>
    
    <div class="field">
        <?php echo CHtml::activeLabelEx($model,'address4'); ?>
        <?php echo CHtml::activeTextField($model,'address4', array('size' => 75)); ?>
    </div>
    
    <div class="field">
        <?php echo CHtml::activeLabelEx($model,'address5'); ?>
        <?php echo CHtml::activeTextField($model,'address5', array('size' => 75)); ?>
    </div>

</div>


<div class="titleWrap">
    <?php echo CHtml::submitButton('Save', array('class'=>'formButton btn-submit')); ?>
    <ul>
        <li class="seperator">
            <img height="16" width="16" alt="add" src="img/icons/cancel.png"/>
            <a href="#" onclick="parent.$.colorbox.close(); return false;">Cancel</a>
        </li>
    </ul>

</div>

<?php echo CHtml::endForm(); ?>
