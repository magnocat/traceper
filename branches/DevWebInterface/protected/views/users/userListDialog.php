<?php 
$this->beginWidget('zii.widgets.jui.CJuiDialog', array(
	    'id'=>'friendRequestsWindow',
	    // additional javascript options for the dialog plugin
	    'options'=>array(
	        'title'=>Yii::t('general', 'Friend Requests'),
	        'autoOpen'=>false,
	        'modal'=>true, 
			'resizable'=>false      
	    ),
	));

$this->renderPartial('userList', array('dataProvider'=>$dataProvider, 'viewId'=>'userListDialog')); 	

$this->endWidget('zii.widgets.jui.CJuiDialog');
?>