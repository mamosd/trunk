<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><?php echo CHtml::encode($this->pageTitle); ?></title>
<link rel="stylesheet" type="text/css" media="all"  href="<?php echo Yii::app()->request->baseUrl; ?>/css/all.css" />
<link rel="stylesheet" type="text/css" media="all"  href="<?php echo Yii::app()->request->baseUrl; ?>/css/custom-theme/jquery-ui-1.8.10.custom.css" />
<link rel="stylesheet" type="text/css" media="all"  href="<?php echo Yii::app()->request->baseUrl; ?>/css/jquery.wysiwyg.css" />
<link rel="stylesheet" type="text/css" media="all"  href="<?php echo Yii::app()->request->baseUrl; ?>/css/visualize.css" />
<link rel="stylesheet" type="text/css" media="all"  href="<?php echo Yii::app()->request->baseUrl; ?>/css/colorbox.css" />
<!--[if IE 6]>
    <link rel="stylesheet" type="text/css" media="all"  href="<?php echo Yii::app()->request->baseUrl; ?>/css/ie6.css" />
    <link rel="stylesheet" type="text/css" media="all"  href="<?php echo Yii::app()->request->baseUrl; ?>/css/colorbox-ie.css" />
<![endif]-->
<!--script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/js/jquery-1.4.2.min.js"></script-->
<?php 
    $cs=Yii::app()->getClientScript();
    $cs->registerCoreScript ( 'jquery.ui' );
?>
<!--script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/js/jquery-ui-1.8.10.custom.min.js"></script-->
<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/js/jquery.wysiwyg.js"></script>
<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/js/excanvas.js"></script>
<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/js/visualize.jQuery.js"></script>
<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/js/jquery.colorbox-min.js"></script>
<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/js/custom.js"></script>
<!--[if lt IE 9]>
    <script src="http://ie7-js.googlecode.com/svn/version/2.1(beta3)/IE9.js">
    IE7_PNG_SUFFIX=".png";
    </script>
<![endif]-->
<style>
#mainWrap {
    width: 95% !important;
}
</style>
</head>
<body id="loggedInBody">
	<div id="mainWrap">
		<div id="headerWrap">
			<div id="systemMenu">
                            <?php $this->widget('zii.widgets.CMenu',array(
                                    'items'=>array(
                                            //array('label'=>'Welcome '.Yii::app()->user->name, 'visible'=>!Yii::app()->user->isGuest),
                                            array('label'=>'Home', 'url'=> (!Yii::app()->user->isGuest ? array(Yii::app()->user->role->HomeUrl) : '#'), 'visible' => !Yii::app()->user->isGuest),
                                            array('label'=>'Login', 'url'=>array('/account/login'), 'visible'=>Yii::app()->user->isGuest, 'itemOptions'=>array('class'=>'seperator')),
                                            array('label'=>'Logout ('.Yii::app()->user->name.')', 'url'=>array('/account/logout'), 'visible'=>!Yii::app()->user->isGuest, 'itemOptions'=>array('class'=>'seperator'))
                                    ),
                            )); ?>
			</div>
			<div id="logo">
				<img src="img/logo.png" alt="logo" />
			</div>
			<div id="topNavigationWrap">
                        <?php $this->widget('zii.widgets.CMenu',array(
                                'items'=> $this->menu,
                                'htmlOptions'=>array('class'=>'dropdown'),
                                'activeCssClass'=>'current',
                            )); ?>
			</div>
		</div>

        <!--Rounded Corners For The Top - START-->
		<div id="contentWrapTop">
			<div id="contentWrapTopLeft"></div>
			<div id="contentWrapTopRight"></div>
		</div>
        <!--Rounded Corners For The Top - END-->

        <!--contentWrap START-->
		<div id="contentWrap">

            <!--contentWrapSidebar START
			<div id="contentWrapSidebar">
				<p>[This is the sidebar content]</p>
			</div>
            contentWrapSidebar END-->

            <!--contentWrapMain START-->
			<div id="contentWrapMain">
                <div id="crumbsWrap">
                    <?php $this->widget('zii.widgets.CMenu',array(
                        'id' => 'crumbs',
                        'items'=> $this->breadcrumbs,
                        'lastItemCssClass' => 'bold',
                    )); ?>
                </div>

                <?php echo $content; ?>

                <div id="contentWrapMainBottomSpacer"></div>
            </div>
            <!--contentWrapMain END-->
        </div>
        <!--contentWrap END-->

        <div id="contentWrapBottom">
            <div id="contentWrapBottomLeft"></div>
            <div id="contentWrapBottomRight"></div>
        </div>

        <div id="footerWrap">
            <?php echo date('Y') ?> Aktrion Logistics
        </div>

    </div>
    <!--mainWrap END-->

</body>
</html>