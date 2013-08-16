
<!-- 
Diðer dialoglardan farklý olarak bu dialog gösterildiðinde ana sayfa yüklenmediðinden ve dolayýsýyla style sheet'ler de
yüklenmemiþ olduðundan burada yükle
-->

<html>
<head>
<title><?php echo Yii::app()->name; ?></title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="keywords" content="" />
<meta name="description" content="open source GPS tracking system" />
<link rel="stylesheet" type="text/css"
	href="<?php echo Yii::app()->request->baseUrl; ?>/css/style.css"
	media="screen, projection" />
<link rel="stylesheet" type="text/css"
	href="<?php echo Yii::app()->request->baseUrl; ?>/css/main.css" />
<link rel="stylesheet" type="text/css"
	href="<?php echo Yii::app()->request->baseUrl; ?>/css/form.css" />
</head>
<body>

<?php 
$this->beginWidget('zii.widgets.jui.CJuiDialog', array(
	    'id'=>'messageDialogWindow',
	    'options'=>array(
	        'title'=>$title,
	        'autoOpen'=>true,
	        'modal'=>true, 
			'resizable'=>false,
	    	'width'=> '600px',
	    	'height'=>'auto',
			'buttons' =>array (
					Yii::t('common', 'OK')=>"js:function(){
								$(this).dialog('close');
								location.href = '".Yii::app()->homeUrl."';
							}",
						)      
	    ),
	));
	echo '</br>';
	echo '<div align="center" id="messageDialogText">'.$result.'</div>';
	$this->endWidget('zii.widgets.jui.CJuiDialog');
?>

</body>
</html>