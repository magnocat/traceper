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
		$this->renderPartial('getList', array('dataProvider'=>$dataProvider, 'fileType'=>$fileType, 'viewId'=>'searchUploadListView', 'searchResult'=>true), false, true);
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

		?>	
		<div id="uploadSearchResultsOK" style="padding-top:2em;text-align:center">
		<?php
			$app = Yii::app();
			
			echo CHtml::ajaxLink('<button class="btn btn-sliding-green btn-sliding-green-a icon-checkmark" style="'.(($app->language == 'en')?'padding-left:25px;padding-right:25px;':'padding-left:28px;padding-right:28px;').'">'.'<span style="font-family:Helvetica">'.Yii::t('common', 'OK').'</span>'.'</button>', '#',
					array(),
					array('onclick'=>'$("#uploadSearchResults").dialog("close"); return false;'));
		?>
		</div>					
		
		<?php	
	}

	$this->endWidget('zii.widgets.jui.CJuiDialog');
?>