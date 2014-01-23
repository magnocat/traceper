<?php 
$this->beginWidget('zii.widgets.jui.CJuiDialog', array(
	    'id'=>'userSearchResults',
	    // additional javascript options for the dialog plugin
	    'options'=>array(
	        'title'=>Yii::t('common', 'Search Results'),
	        'autoOpen'=>false,
	        'modal'=>true, 
			'resizable'=>false,    
            'width'=>isset($dataProvider)?'22em':'auto',
	    	'height'=>'auto' //'height'=>200,	    			
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

		?>		
		<div id="userSearchResultsOK" style="padding-top:2em;text-align:center">
		<?php
			$app = Yii::app();
			
			echo CHtml::ajaxLink('<button class="btn btn-sliding-green btn-sliding-green-a icon-checkmark" style="'.(($app->language == 'en')?'padding-left:25px;padding-right:25px;':'padding-left:28px;padding-right:28px;').'">'.'<span style="font-family:Helvetica">'.Yii::t('common', 'OK').'</span>'.'</button>', '#',
					array(),
					array('onclick'=>'$("#userSearchResults").dialog("close"); return false;'));
		?>
		</div>					
		
		<?php	
	}

	$this->endWidget('zii.widgets.jui.CJuiDialog');
?>