<?php 
$this->beginWidget('zii.widgets.jui.CJuiDialog', array(
	    'id'=>'messageDialogWindow',
	    'options'=>array(
	        'title'=>$title,
	        'autoOpen'=>true,
	        'modal'=>true, 
			'resizable'=>false,
	    	'width'=>'600px',
	    	'height'=>'auto',
			'buttons' =>array (
					Yii::t('common', 'OK')=>"js:function(){
								$(this).dialog('close');
								location.href = '".Yii::app()->homeUrl."';
							}",
						),
	    	'close' => 'js:function(){ location.href = "'.Yii::app()->homeUrl.'"; }'
	    ),
	));
	echo '</br>';
	echo '<div id="messageDialogText" style="font-family:Helvetica;">'.$result.'</div>';
	$this->endWidget('zii.widgets.jui.CJuiDialog');
?>
