<?php 
$this->beginWidget('zii.widgets.jui.CJuiDialog', array(
	    'id'=>'userSearchResults',
	    // additional javascript options for the dialog plugin
	    'options'=>array(
	        'title'=>Yii::t('general', 'Search Results'),
	        'autoOpen'=>false,
	        'modal'=>true, 
			'resizable'=>false      
	    ),
	));

	if (isset($dataProvider)) {
		$this->renderPartial('userList', array('dataProvider'=>$dataProvider, 'model'=>$model, 'viewId'=>'searchResultList', 'searchResult'=>true), false, true);
	}

	$this->endWidget('zii.widgets.jui.CJuiDialog');
?>