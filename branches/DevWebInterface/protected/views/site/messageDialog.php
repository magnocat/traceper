<?php 
$this->beginWidget('zii.widgets.jui.CJuiDialog', array(
	    'id'=>'messageDialogWindow',
	    'options'=>array(
	        'title'=>$title,
	        'autoOpen'=>true,
	        'modal'=>true, 
			'resizable'=>false,
	    	'width'=> '600px',
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