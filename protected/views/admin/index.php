<?php
    $this->pageTitle = Yii::app()->name.' - '.Yii::app()->user->role->Description;

    $this->breadcrumbs=array(
            array('label'=>'Home Page'),
        );
?>

<div class="infoBox">
    Please select a menu option to continue.
</div>

<?php if ( Login::checkPermission('#^navigation/NQ/primary/#',true)) : ?>
<div id="crumbsWrap" style="clear:both;">
    <?php $this->widget('zii.widgets.CMenu',array(
        'id' => 'crumbs',
        'items'=> array(
                array('label'=>'Primary Routing'),
            ),
        'lastItemCssClass' => 'bold',
    )); ?>
</div>

<div class="home-icon-container">
    <div class="home-icon">
        <a href="<?php echo $this->createUrl('admin/reportingcontrol'); ?>">
        <img src="<?php echo Yii::app()->request->baseUrl; ?>/img/icons-large/control-icon.png" >
        <div class="home-icon-title">
            Control
        </div>
        </a>
    </div>

    <div class="home-icon">
        <a href="<?php echo $this->createUrl('admin/orders'); ?>">
        <img src="<?php echo Yii::app()->request->baseUrl; ?>/img/icons-large/orders-icon.png" >
        <div class="home-icon-title">
            Orders
        </div>
        </a>
    </div>

    <div class="home-icon">
        <a href="<?php echo $this->createUrl('admin/routes'); ?>">
        <img src="<?php echo Yii::app()->request->baseUrl; ?>/img/icons-large/routes-icon.png" >
        <div class="home-icon-title">
            Routes
        </div>
        </a>
    </div>

    <div class="home-icon">
        <a href="<?php echo $this->createUrl('admin/deliverypoints'); ?>">
        <img src="<?php echo Yii::app()->request->baseUrl; ?>/img/icons-large/delivery-points-icon.png" >
        <div class="home-icon-title">
            Delivery<br/>Points
        </div>
        </a>
    </div>

    <div class="home-icon">
        <a href="<?php echo $this->createUrl('admin/titles'); ?>">
        <img src="<?php echo Yii::app()->request->baseUrl; ?>/img/icons-large/titles-icon.png" >
        <div class="home-icon-title">
            Titles
        </div>
        </a>
    </div>

    <div class="home-icon">
        <a href="<?php echo $this->createUrl('admin/printcentres'); ?>">
        <img src="<?php echo Yii::app()->request->baseUrl; ?>/img/icons-large/print-centres-icon.png" >
        <div class="home-icon-title">
            Print<br/>Centres
        </div>
        </a>
    </div>

    <div class="home-icon">
        <a href="<?php echo $this->createUrl('admin/suppliers'); ?>">
        <img src="<?php echo Yii::app()->request->baseUrl; ?>/img/icons-large/suppliers-icon.png" >
        <div class="home-icon-title">
            Suppliers
        </div>
        </a>
    </div>

    <div class="home-icon">
        <a href="<?php echo $this->createUrl('admin/logins'); ?>">
        <img src="<?php echo Yii::app()->request->baseUrl; ?>/img/icons-large/admin-icon.png" >
        <div class="home-icon-title">
            Admin
        </div>
        </a>
    </div>
</div>
<?php endif; ?>

<?php if ( Login::checkPermission('#^navigation/NQ/secondary/#',true)) : ?>
<div id="crumbsWrap" style="clear:both;">
    <?php $this->widget('zii.widgets.CMenu',array(
        'id' => 'crumbs',
        'items'=> array(
                array('label'=>'Secondary Routing'),
            ),
        'lastItemCssClass' => 'bold',
    )); ?>
</div>

<div class="home-icon-container">
    <div class="home-icon">
        <a href="<?php echo $this->createUrl('admin/secondaryrouting'); ?>">
        <img src="<?php echo Yii::app()->request->baseUrl; ?>/img/icons-large/orders-icon.png" >
        <div class="home-icon-title">
            Process<br/>Rounds
        </div>
        </a>
    </div>
</div>
<?php endif; ?>