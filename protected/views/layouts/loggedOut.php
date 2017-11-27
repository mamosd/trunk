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
<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/js/jquery-1.4.2.min.js"></script>
<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/js/jquery-ui-1.8.10.custom.min.js"></script>
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
</head>
<body id="loginBody">
<div id="loginMainWrap">
    <div id="loginLogo">
        <img src="img/logo.png" alt="logo" />
    </div>

    <!--Rounded Corners For The Top - START-->
    <div id="contentWrapTop">
        <div id="contentWrapTopLeft"></div>
        <div id="contentWrapTopRight"></div>
    </div>
    <!--Rounded Corners For The Top - END-->

	<div id="contentWrap">
      <div class="standardForm loginForm">
                <?php echo $content; ?>
      </div>
  </div>

 	<!--Rounded Corners For The Bottom - START-->
 	<div id="contentWrapBottom">
		<div id="contentWrapBottomLeftLogin"></div>
		<div id="contentWrapBottomRightLogin"></div>
	</div>
    <!--Rounded Corners For The Bottom - START-->

    <div id="footerWrapLogin">
        <?php echo date('Y') ?> Aktrion Logistics
    </div>

</div>
</body>
</html>
