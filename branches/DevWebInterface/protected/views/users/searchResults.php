<?php 
$this->beginWidget('zii.widgets.jui.CJuiDialog', array(
	    'id'=>'userSearchResults',
	    // additional javascript options for the dialog plugin
	    'options'=>array(
	        'title'=>Yii::t('general', 'Search Results'),
	        'autoOpen'=>false,
	        'modal'=>true, 
			'resizable'=>false,    
            'width'=>isset($dataProvider)?'20em':'auto',
            'height'=>'auto',
	    ),
	));

	if (isset($dataProvider)) {
		$this->renderPartial('userList', array('dataProvider'=>$dataProvider, 'model'=>$model, 'viewId'=>'searchResultList', 'searchResult'=>true), false, true);
	}
	else
	{
	?>
		<div class="row" style="padding-top:2em">
	<?php 
			echo Yii::t('layout', 'Please enter a keyword to search...'); 
	?>
		</div>
	<?php		
	}

	$this->endWidget('zii.widgets.jui.CJuiDialog');
?>