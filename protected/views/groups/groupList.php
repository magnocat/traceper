<?php

if ($dataProvider != null) {
	$emptyText = Yii::t('groups', 'No groups found');
	$ajaxUrl = null;
	$isSearchResult = isset($searchResult) ? true : false;
	$deleteGroupQuestion = Yii::t('groups', 'Do you really want to delete this group?');
	
	if ($isSearchResult == true){
		$ajaxUrl = Yii::app()->createUrl($this->route, array( CHtml::encode('SearchForm[keyword]')=>$model->attributes['keyword']) ) ;
	}
	
	//TODO: Refactor make common confirmation dialog 	
	/** This is the friend ship id holder, when user clicks delete, its content is filled***/
	echo "<div id='groupId' style='display:none'></div>";
	echo "<div id='gridViewId' style='display:none'></div>";
	
	$deleteGroupJSFunction = "function deleteGroup() { "
									.CHtml::ajax(
											array(
													'url'=>Yii::app()->createUrl('groups/deleteGroup'),
													'data'=> array('groupId'=>"js:$('#groupId').html()"),
													'success'=> 'function(result) { 	
																 	try {
																 		TRACKER.closeConfirmationDialog();
																		var obj = jQuery.parseJSON(result);
																		if (obj.result && obj.result == "1") 
																		{																			
																			$.fn.yiiGridView.update($("#gridViewId").text());
																		}
																		else 
																		{
																			TRACKER.showMessageDialog("'.Yii::t('groups', 'Sorry,an error occured in operation - 1').'");
																		}
		
																	}
																	catch(ex) {
																		TRACKER.showMessageDialog("'.Yii::t('groups', 'Sorry,an error occured in operation - 2').'");
																	}
																}',
												)).
									"}";	
	Yii::app()->clientScript->registerScript('groupFunctions',
			$deleteGroupJSFunction,
			CClientScript::POS_READY);
		
	$this->widget('zii.widgets.grid.CGridView', array(
		    'dataProvider'=>$dataProvider,
	 		'id'=>$viewId,
			'ajaxUrl'=>$ajaxUrl,
			'summaryText'=>'',
			'emptyText'=>$emptyText,
			'pager'=>array( 
				 'id'=>'GroupsPager',
				 'header'=>'',
		         'firstPageLabel'=>'',
		         'lastPageLabel'=>'',
			       ),
		    'columns'=>array(
		array(            // display 'create_time' using an expression
					'name'=>Yii::t('groups', 'Privacy Settings'),
					'type' => 'raw',

		            'value'=>'CHtml::link(\'<img src="images/PrivacySettings.png"  />\', \'#\',
										array(\'onclick\'=>CHtml::ajax(
											array(
												\'url\'=>Yii::app()->createUrl(\'groups/setPrivacyRights\', array(\'groupId\'=>$data[\'id\'])),
												
					    						\'complete\'=> \'function() { $("#groupPrivacySettingsWindow").dialog("open"); return false;}\',
					 							\'update\'=> \'#groupPrivacySettingsWindow\',	
					 							
											)),\'class\'=>\'vtip\', \'title\'=>\''.Yii::t('common', 'Edit Settings').'\')
					  				 )',		
		
					'htmlOptions'=>array('width'=>'50px', 'style'=>'padding-left:30px;')
		),
		       

		array(            // display 'create_time' using an expression
				    'name'=>Yii::t('common', 'Name'),
					'type' => 'raw',
		            'value'=>'CHtml::link($data["name"], "#", array())',

		            'value'=>'CHtml::link($data["name"], "#",
										array(\'onclick\'=>CHtml::ajax(
											array(
												\'url\'=>Yii::app()->createUrl(\'groups/getGroupMembers\', array(\'groupId\'=>$data[\'id\'])),
												
					    						\'complete\'=> \'function() { $("#groupMembersWindow").dialog("open"); return false;}\',
					 							\'update\'=> \'#groupMembersWindow\',	
					 							
											)),\'class\'=>\'vtip\', \'title\'=>\''.Yii::t('groups', 'View Group Members').'\')
					  				 )',		
		
		
		),
		array(            // display 'create_time' using an expression
	//    'name'=>'realname',
					'type' => 'raw',				
					'value'=>'CHtml::link("<img src=\"images/delete.png\"  />", "#",
										array("onclick"=>"
												$(\"#groupId\").text(".$data[\'id\'].");
												$(\"#gridViewId\").text(\"'.$viewId.'\");
												TRACKER.showConfirmationDialog(\"'.$deleteGroupQuestion.'\", deleteGroup);
												",
												"class"=>"vtip",
												"title"=>'.("Yii::t('groups', 'Delete Group')").
												')
										)',				

					'htmlOptions'=>array('width'=>'16px')
		),
	),
	));
	




}
/*
 */
?>