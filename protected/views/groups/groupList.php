<?php

if ($dataProvider != null) {
	//TODO: Refactor make common confirmation dialog 	
	/** This is the friend ship id holder, when user clicks delete, its content is filled***/
	echo "<div id='groupId' style='display:none'></div>";
	$this->beginWidget('zii.widgets.jui.CJuiDialog', array(
	    'id'=>'groupDeleteConfirmation',
		// additional javascript options for the dialog plugin
	    'options'=>array(
	        'title'=>Yii::t('groups', 'Delete Group'),
	        'autoOpen'=>false,
	        'modal'=>true, 
			'resizable'=>false,
			'buttons' =>array (
				"OK"=>"js:function(){
							". CHtml::ajax(
									array(
											'url'=>Yii::app()->createUrl('groups/deleteGroup'),
											'data'=> array('groupId'=>"js:$('#groupId').html()"),
											'success'=> 'function(result) { 	
														 	try {
														 		$("#groupDeleteConfirmation").dialog("close");
																var obj = jQuery.parseJSON(result);
																if (obj.result && obj.result == "1") 
																{
																	$.fn.yiiGridView.update($("groupListView").text());
																}
																else 
																{
																	$("#messageDialogText").html("'.Yii::t('groups', 'Sorry,an error occured in operation - 1').'");
																	$("#messageDialog").dialog("open");
																}

															}
															catch(ex) {
																$("#messageDialogText").html("'.Yii::t('groups', 'Sorry,an error occured in operation - 2').'");
																$("#messageDialog").dialog("open");
															}
														}',
										)) .
						"}",
			"Cancel"=>"js:function() {
				$( this ).dialog( \"close\" );
			}" 
			)),
		));	
	echo Yii::t('groups', 'Do you want to delete this group?').'<br/> <br/>';
	$this->endWidget('zii.widgets.jui.CJuiDialog');	
	

	$this->widget('zii.widgets.grid.CGridView', array(
		    'dataProvider'=>$dataProvider,
	 		'id'=>'groupListView',
			'summaryText'=>'',
			'pager'=>array( 
				 'header'=>'',
		         'firstPageLabel'=>'',
		         'lastPageLabel'=>'',
			       ),
		    'columns'=>array(
		array(            // display 'create_time' using an expression
					'name'=>'Privacy Settings',
					'type' => 'raw',

		            'value'=>'CHtml::link(\'<img src="images/PrivacySettings.png"  />\', \'#\',
										array(\'onclick\'=>CHtml::ajax(
											array(
												\'url\'=>Yii::app()->createUrl(\'groups/setPrivacyRights\', array(\'groupId\'=>$data[\'id\'])),
												
					    						\'complete\'=> \'function() { $("#groupPrivacySettingsWindow").dialog("open"); return false;}\',
					 							\'update\'=> \'#groupPrivacySettingsWindow\',	
					 							
											)),\'class\'=>\'vtip\', \'title\'=>\'Edit Settings\')
					  				 )',		
		
					'htmlOptions'=>array('width'=>'50px', 'style'=>'padding-left:30px;')
		),
		       

		array(            // display 'create_time' using an expression
				    'name'=>'Name',
					'type' => 'raw',
		            'value'=>'CHtml::link($data["name"], "#", array())',

		            'value'=>'CHtml::link($data["name"], "#",
										array(\'onclick\'=>CHtml::ajax(
											array(
												\'url\'=>Yii::app()->createUrl(\'groups/getGroupMembers\', array(\'groupId\'=>$data[\'id\'])),
												
					    						\'complete\'=> \'function() { $("#groupMembersWindow").dialog("open"); return false;}\',
					 							\'update\'=> \'#groupMembersWindow\',	
					 							
											)),\'class\'=>\'vtip\', \'title\'=>\'Group Members\')
					  				 )',		
		
		
		),
		array(            // display 'create_time' using an expression
	//    'name'=>'realname',
					'type' => 'raw',
		            'value'=>'CHtml::link("<img src=\"images/delete.png\"  />", "#",
										array("onclick"=>"$(\"#groupId\").text(".$data[\'id\'].");
														 $(\"#groupDeleteConfirmation\").dialog(\"open\");", 
												"class"=>"vtip", 
												"title"=>"Delete Group"))',

					'htmlOptions'=>array('width'=>'16px')
		),
	),
	));
	




}
/*
 */
?>