<?php
    $this->breadcrumbs=array(
                array('label'=>'Home', 'url'=>array(Yii::app()->user->role->HomeUrl)),
                array('label'=>'Pallets', 'url'=>'#'),
                array('label'=>'Print Centre', 'url'=>'#'),
                array('label'=>'Upload Pallet Sheet'),
            );
?>
    
<h1>Upload Pallet Sheet</h1>

<?php 
if (!empty($result)):
    
    if ($result['error'] === false): ?>
    <div class="successBox">
        Pallet sheet for <?php echo $result['printcentre']; ?>, week ending <?php echo $result['weekending']; ?> uploaded successfully.
    </div>
<?php else: ?>
    <div class="errorBox">
        Error uploading the pallet sheet, please review the sheet and try again.<br/>
        Details: <?php echo $result['errordesc']; ?>
    </div>
<?php 
    endif;
endif;?>

<div class="standardForm">

    <?php if (isset($result['error']) && $result['error'] !== false && $result['errortype'] == 'existing'): ?>
        <h3>Are you sure to upload the file again?</h3>
        <form action="" method="post">
            <input type="hidden" name="filename" value="<?=$result['errorfile']?>" />
            <input type="submit" name="uploadFileAgain" value="Yes" />
            <input type="submit" name="" value="No" />
        </form>
        <br />
    <?php endif; ?>


    <?php echo CHtml::form('','post',array('enctype'=>'multipart/form-data'));

    echo CHtml::errorSummary($model, "", "", array('class'=>'errorBox'));
    ?>

    <div>
        <?php echo CHtml::activeLabelEx($model,'spreadSheet'); ?>
        <?php echo CHtml::activeFileField($model,'spreadSheet'); ?>
    </div>

    <div class="titleWrap">
        <?php echo CHtml::submitButton('Submit', array('class'=>'formButton')); ?>
    </div>

    <?php echo CHtml::endForm(); ?>
</div>
