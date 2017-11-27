<?php
    $baseUrl = Yii::app()->request->baseUrl;
    $readOnlyAttrs = array('class' => 'readOnlyField', 'readOnly' => 'readOnly');
?>

<h1>
    Upload Route Plan
</h1>

<?php 
    if (isset($uploadResult)):
        switch ($uploadResult['status']):
            case 'success': 
                $nextDay = DateTime::createFromFormat('d/m/Y', $model->planningDate);
                $nextDay = $nextDay->modify('+1 day');
                $nextDate = $nextDay->format('d/m/Y');
                if (in_array($model->planningDate, $uploadResult['dates']) || in_array($nextDate, $uploadResult['dates'])): ?>
                    <div class="infoBox">There are jobs imported that will be visible for currently selected planning date. These jobs will show upon closing this popup.</div>
<?php           endif;
    
                $currentDates = array ($model->planningDate, $nextDate);
                foreach ($uploadResult['dates'] as $date):
                    if (!in_array($date, $currentDates)):
                        $linkDay = DateTime::createFromFormat('d/m/Y', $date);
                        $linkDay = $linkDay->modify('-1 day');
                        $linkDate = $linkDay->format('d/m/Y');
                        $url = $this->createUrl('polestar/routeview', array('PolestarRouteViewForm[planningDate]' => $linkDate, 'PolestarRouteViewForm[printCentreId]' => $model->printCentreId)); ?>
                    <div class="warningBox">
                        There are jobs imported for <?php echo $date ?>. Click <a href="<?php echo $url ?>" target="_clank">here</a> to see the route screen showing them.
                     </div>
<?php               endif;
                endforeach;
?>
                
<?php                break;
            default: ?>
                <div class="warningBox">An error has occurred while importing, please notify the system administrator.</div>
<?php                
        endswitch;
    endif;
?>

<?php echo CHtml::form('','post',array('enctype'=>'multipart/form-data'));

    echo CHtml::errorSummary($model, "", "", array('class'=>'errorBox'));
    
    echo CHtml::activeHiddenField($model,'printCentreId');
    echo CHtml::activeHiddenField($model,'planningDate');
    
    ?>

<fieldset>
<br/>
<div class="stackedForm">
    <div class="field">
        <?php echo CHtml::label('Print Centre', FALSE); 
        echo CHtml::textField('pc', PolestarPrintCentre::model()->findByPk($model->printCentreId)->Name, $readOnlyAttrs);
        ?>
    </div>
    <div class="field">
        <?php echo CHtml::activeLabelEx($model,'spreadsheet');
              echo CHtml::activeFileField($model,'spreadSheet');
            ?>
    </div>
    
    <div class="field">
        <button>Upload Sheet</button>
    </div>
</div>
</fieldset>

<?php echo CHtml::endForm(); ?>
