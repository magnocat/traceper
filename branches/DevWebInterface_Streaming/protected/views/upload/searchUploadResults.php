<?php 
$this->beginWidget('zii.widgets.jui.CJuiDialog', array(
	    'id'=>'uploadSearchResults',
	    // additional javascript options for the dialog plugin
	    'options'=>array(
	        'title'=>Yii::t('general', 'Search Results'),
	        'autoOpen'=>false,
	        'modal'=>true, 
			'resizable'=>false      
	    ),
	));

	if (isset($dataProvider)) {
		$this->renderPartial('getList', array('dataProvider'=>$dataProvider, 'fileType'=>$fileType), false, true);
	}

	$this->endWidget('zii.widgets.jui.CJuiDialog');
?>