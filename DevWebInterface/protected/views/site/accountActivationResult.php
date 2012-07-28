<?php 
$this->beginWidget('zii.widgets.jui.CJuiDialog', array(
	    'id'=>'accountActivationWindow',
	    'options'=>array(
	        'title'=>Yii::t('general', 'Account Activation'),
	        'autoOpen'=>true,
	        'modal'=>true, 
			'resizable'=>false,
			'buttons' =>array (
					"OK"=>"js:function(){
								$(this).dialog('close');
								location.href = '".Yii::app()->homeUrl."';
							}",
						)      
	    ),
	));
	
	echo $result;

	$this->endWidget('zii.widgets.jui.CJuiDialog');
?>