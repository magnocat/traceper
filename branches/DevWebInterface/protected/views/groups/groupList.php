<?php

if ($dataProvider != null) {
	$emptyText = Yii::t('groups', 'You do not have any groups at the moment. In order to group your friends, you could create new group(s) by the link {createGroupIcon} at the top menu or by {createGroupByHere}.', array('{createGroupIcon}'=>'<div class="lo-icon-in-tooltip icon-users"></div>', 
			'{createGroupByHere}'=>	CHtml::ajaxLink(Yii::t('common', 'here'), $this->createUrl('groups/createGroup'),
														array(
																'complete'=> 'function() { $("#createGroupWindow").dialog("open"); return false;}',
																'update'=> '#createGroupWindow',
														),
														array(
																'id'=>'showCreateGroupWindowAtGroupList-'.uniqid(), //Unique ID oluşturmayınca her ajaxta bir önceki sorgular da tekrarlanıyor
																))
			));

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
													'id'=>'deleteGroupAjaxLink-'.uniqid(), //Unique ID oluşturmayınca her ajaxta bir önceki sorgular da tekrarlanıyor														
													'url'=>Yii::app()->createUrl('groups/deleteGroup'),
													'data'=> array('groupId'=>"js:$('#groupId').html()"),
													'success'=> 'function(result) { 	
																 	try {
																 		TRACKER.closeConfirmationDialog();
																		var obj = jQuery.parseJSON(result);
																		
																		if(obj.result)
																		{
																			if(obj.result == "1") 
																			{																			
																				$.fn.yiiGridView.update($("#gridViewId").text());
																			}
																			else if(obj.result == "0")  
																			{
																				TRACKER.showMessageDialog("'.Yii::t('groups', 'Group deletion failed!').'");
																			}
																			else if(obj.result == "-1")
																			{
																				TRACKER.showMessageDialog("'.Yii::t('groups', 'Group does not exist!').'");
																			}
																			else
																			{
																				TRACKER.showMessageDialog("'.Yii::t('groups', 'Undefined result happened!').'");
																			}
																		}
																	}
																	catch(ex) {
																		TRACKER.showMessageDialog("'.Yii::t('groups', 'jQuery.parseJSON(result) got exception!').'");
																	}
																}',
												)).
									"}";	
	Yii::app()->clientScript->registerScript('groupFunctions',
			$deleteGroupJSFunction,
			CClientScript::POS_READY);
	?>
	<div id="groupsGridView" style="overflow:auto;">
	<?php		
	$this->widget('zii.widgets.grid.CGridView', array(
		    'dataProvider'=>$dataProvider,
	 		'id'=>$viewId,
			'ajaxUrl'=>$ajaxUrl,
			'summaryText'=>'',
			'emptyText'=>$emptyText,
			'htmlOptions'=>array('style'=>'font-size:14px;'),
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

		            'value'=>'CHtml::link("Privacy Settings", \'#\',
										array(\'onclick\'=>CHtml::ajax(
											array(
												\'url\'=>Yii::app()->createUrl(\'groups/setPrivacyRights\', array(\'groupId\'=>$data[\'id\'])),
												
					    						\'complete\'=> \'function() { $("#groupPrivacySettingsWindow").dialog("open"); return false;}\',
					 							\'update\'=> \'#groupPrivacySettingsWindow\',	
					 							
											)),\'class\'=>\'vtip\', \'title\'=>\''.Yii::t('groups', 'Edit the privacy settings of this group').'\', \'class\'=>\'lo-icon icon-lock\')
					  				 )',		
		
					'htmlOptions'=>array('width'=>'50px', 'style'=>'text-align: center;', 'class'=>'lo-icon-effect-3 lo-icon-effect-3a')
		),
		    		
    		array(            // display 'create_time' using an expression
    				'name'=>Yii::t('users', 'Group Settings'),
    				'type' => 'raw',
    				'value'=>'CHtml::link("Group Settings", "#",
					    				array(\'onclick\'=>CHtml::ajax(
						    				array(
							    				\'url\'=>Yii::app()->createUrl(\'groups/updateGroup\', array(\'groupId\'=>$data[\'id\'])),
							    		
							    				\'complete\'=> \'function() { $("#groupSettingsWindow").dialog("open"); return false;}\',
							    				\'update\'=> \'#groupSettingsWindow\',   					
								    		)),\'class\'=>\'vtip\', \'title\'=>\''.Yii::t('groups', 'Edit the members of this group').'\', \'class\'=>\'lo-icon icon-cog\')
							    		)',
    		
    				'htmlOptions'=>array('width'=>'50px', 'style'=>'text-align: center;', 'class'=>'lo-icon-effect-3 lo-icon-effect-3a')
    		),		    		
		    		
//     		array(            // display 'create_time' using an expression
//     				'name'=>Yii::t('users', 'Group Settings'),
//     				'type' => 'raw',   					
//     				'value'=>'CHtml::link("<img src=\"images/GroupSettings.png\"  />", "#",
//     				array(\'onclick\'=>CHtml::ajax(
//     				array(
//     				\'url\'=>Yii::app()->createUrl(\'groups/updateGroup\', array(\'groupId\'=>$data[\'id\'])),
    		
//     				\'complete\'=> \'function() { $("#groupSettingsWindow").dialog("open"); return false;}\',
//     				\'update\'=> \'#groupSettingsWindow\',
    					
//     		)),\'class\'=>\'vtip\', \'title\'=>\''.Yii::t('groups', 'Edit the members of this group').'\')
//     		)',
    		
//     				'htmlOptions'=>array('width'=>'50px', 'style'=>'text-align: center;')
//     		),		    		
		       

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
					 							
											)),\'class\'=>\'vtip\', \'title\'=>\''.Yii::t('groups', 'View group members').'\')
					  				 )',		
		
		
		),
// 		array(            // display 'create_time' using an expression
// 	//    'name'=>'realname',
// 					'type' => 'raw',				
// 					'value'=>'CHtml::link("<img src=\"images/delete.png\"  />", "#",
// 										array("onclick"=>"
// 												$(\"#groupId\").text(".$data[\'id\'].");
// 												$(\"#gridViewId\").text(\"'.$viewId.'\");
// 												TRACKER.showConfirmationDialog(\"'.$deleteGroupQuestion.'\", deleteGroup);
// 												",
// 												"class"=>"vtip",
// 												"title"=>'.("Yii::t('groups', 'Delete Group')").
// 												')
// 										)',				

// 					'htmlOptions'=>array('width'=>'16px')
// 		),
		    		
		    		array(            // display 'create_time' using an expression
		    				//    'name'=>'realname',
		    				'type' => 'raw',
		    				'value'=>'CHtml::link("Delete Group", "#",
		    				array("onclick"=>"
		    				$(\"#groupId\").text(".$data[\'id\'].");
		    				$(\"#gridViewId\").text(\"'.$viewId.'\");
		    				TRACKER.showConfirmationDialog(\"'.$deleteGroupQuestion.'\", deleteGroup);
		    				",
		    				"class"=>"vtip",
		    				"title"=>'.("Yii::t('groups', 'Delete Group')").',
		    				"class"=>"lo-icon icon-close")
		    		)',
		    				//'htmlOptions'=>array('width'=>'16px')
		    				'htmlOptions'=>array('width'=>'28px', 'style'=>'text-align: center;', 'class'=>'lo-icon-effect-3 lo-icon-effect-red'),
		    		),		    		
	),
	));
	?>
	</div>
	<?php
}
/*
 */
?>