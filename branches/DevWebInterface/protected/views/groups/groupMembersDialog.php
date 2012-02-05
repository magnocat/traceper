<?php 
$this->beginWidget('zii.widgets.jui.CJuiDialog', array(
	    'id'=>'groupMembersWindow',
	    // additional javascript options for the dialog plugin
	    'options'=>array(
	        'title'=>Yii::t('general', 'Group Members'),
	        'autoOpen'=>false,
	        'modal'=>true, 
			'resizable'=>false      
	    ),
	));

$this->renderPartial('groupMembers', array('dataProvider'=>$dataProvider)); 	

$this->endWidget('zii.widgets.jui.CJuiDialog');
?>