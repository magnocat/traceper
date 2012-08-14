<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
	<head>
		<title><?php echo Yii::app()->name; ?></title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
		<meta name="keywords"  content="" />
		<meta name="description" content="open source GPS tracking system" />
		<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/main.css" />
		
	 	<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/js/DataOperations.js"></script>

	<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/js/maps/MapStructs.js"></script>	
	<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/js/maps/GMapOperator.js"></script>
	<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/js/TrackerOperator.js"></script>
	<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/js/LanguageOperator.js"></script>		
	<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/js/bindings.js"></script>	
	
	
	
	</head>
	<body>	

<div id="topbar">
  <div id="pageHead" class="clearfix" role="banner" >
    <h1 id="pageLogo" class="pixelSnapped"><a href="index.php" title="Ana Sayfa"></a></h1>
<div id="headNav" class="clearfix"></div>

    </div>
</div>

<div id="gContainer">
<div id="content">
<div>
<div id="mainContainer">
  <div id="leftContainer">
  
  <?php 
  
  
  /////////////////////////////////////////////////////////////////////////////////////////////////

$this->widget('zii.widgets.jui.CJuiAccordion', array(
        'panels'=>array(
        'System Setup'=>'content for panel 1',
        'Transaction'=>'content for panel 2',
        ),
        // additional javascript options for the accordion plugin
        'options'=>array(
        'animated'=>'bounceslide',
        ),
));
/////////////////////////////////////////////////////////////////////////////////////////////
  
  ?>
  
  </div>
  <div id="contentArea">
    <div id="wallCol"> </div>
    <div id="mediaMapCol">
      <div id="MediaMap"> </div>
      <div id="commentCol"> </div>
    </div>
  </div>
</div>
</div>
  </div>
</div>

	</body>
</html>