<?php
	///////////////////////////// About traceper Window ///////////////////////////
	$this->beginWidget('zii.widgets.jui.CJuiDialog', array(
			'id'=>'termsWindow',
			// additional javascript options for the dialog plugin
			'options'=>array(
					'title'=>Yii::t('site', 'Terms of Use'),
					'autoOpen'=>false,
					'modal'=>true,
					'resizable'=>false,
					'width'=> '900px',
					'close' => 'js:function(){ showFormErrorsIfExist(); }'					
			),
	));	

	//echo '<div id="logo"></div>';
	//echo 'traceper is a GPS tracking system for mobile users, it is free, it is open source, it is simple. You can track and see your friends\' positions online.<br/><br/><div class=\"title\">Support</div>If you need support to modify and use this software, We can share all information we have, so feel free to contact us.<br/><br/><div class=\"title\">License</div>This software is free. It can be modified and distributed without notification.<br/><br/><div class=\"title\">Disclaimer</div>This software guarantees nothing, use it with your own risk. No responsilibity is taken for any situation.<br/><br/><div class=\"title\">Contact</div><a href=\"mailto:contact@mekya.com\">contact@mekya.com</a><br/><br/><div class=\"title\">Project Team</div><div id=\"projectteam\">Adnan Kalay - adnankalay@gmail.com <br/> Ahmet Oguz Mermerkaya - ahmetmermerkaya@gmail.com <br/> Murat Salman - salman.murat@gmail.com </div>';
	echo '<div style="font-size:13px; max-height:540px; overflow:auto;">'.Yii::t('layout', 'Traceper Terms').'</div>';

	$this->endWidget('zii.widgets.jui.CJuiDialog');
	
?>	