<?php
    $this->pageTitle = Yii::app()->name.' - '.Yii::app()->user->role->Description;
    $this->breadcrumbs=array(
                array('label'=>'Home'),
            );
?>

<h1>Welcome to <i><?php echo CHtml::encode(Yii::app()->name); ?></i></h1>

<p>This is a default content for SUPPLIERs.</p>