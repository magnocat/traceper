<?php 
$this->beginWidget('zii.widgets.jui.CJuiDialog', array(
	    'id'=>'uploadSearchResults',
	    // additional javascript options for the dialog plugin
	    'options'=>array(
	        'title'=>Yii::t('common', 'Search Results'),
	        'autoOpen'=>false,
	        'modal'=>true, 
			'resizable'=>false,
			'width'=>isset($dataProvider)?'20em':'auto',
            'height'=>'auto',			      
	    ),
	));

	if (isset($dataProvider)) {
		$this->renderPartial('getList', array('dataProvider'=>$dataProvider, 'fileType'=>$fileType,  'searchResult'=>true), false, true);
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