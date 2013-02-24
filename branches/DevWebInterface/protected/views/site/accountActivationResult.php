<?php 
$this->beginWidget('zii.widgets.jui.CJuiDialog', array(
	    'id'=>'accountActivationWindow',
	    'options'=>array(
	        'title'=>Yii::t('site', 'Account Activation'),
	        'autoOpen'=>true,
	        'modal'=>true, 
			'resizable'=>false,
			'buttons' =>array (
					Yii::t('common', 'OK')=>"js:function(){
								$(this).dialog('close');
								location.href = '".Yii::app()->homeUrl."';
							}",
						)      
	    ),
	));
	
	echo $result;

	$this->endWidget('zii.widgets.jui.CJuiDialog');
?>