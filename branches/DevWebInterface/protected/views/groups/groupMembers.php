<?php

function getMemberPhoto($data, $row){
	$value = null;

	switch($data['profilePhotoStatus'])
	{
		case Users::NO_TRACEPER_PROFILE_PHOTO_EXISTS:
			{
				if($data['fb_id'] == 0)
				{
					$value = '<div class="hi-icon-in-list icon-user" style="color:#FFDB58; cursor:default;"></div>';
				}
				else
				{
					$value = CHtml::image('https://graph.facebook.com/'.$data['fb_id'].'/picture?type=square', '#', array('width'=>'33px', 'height'=>'36px'));
				}
			}
			break;

		case Users::TRACEPER_PROFILE_PHOTO_EXISTS:
		case Users::BOTH_PROFILE_PHOTOS_EXISTS_USE_TRACEPER:
			{
				$value = CHtml::image('profilePhotos/'.$data['id'].'.png', '#', array('width'=>'33px', 'height'=>'36px'));
			}
			break;

		case Users::BOTH_PROFILE_PHOTOS_EXISTS_USE_FACEBOOK:
			{
				$value = CHtml::image('https://graph.facebook.com/'.$data['fb_id'].'/picture?type=square', '#', array('width'=>'33px', 'height'=>'36px'));
			}
			break;

		default:
			Fb::warn($profilePhotoSource, "default - profilePhotoSource");
	}

	return $value;
}

if ($dataProvider != null) 
{
	//TODO: Refactor make common confirmation dialog 	
	/** This is the group member id holder, when user clicks delete, its content is filled***/
	
	$deleteGroupMemberQuestion = Yii::t('groups', 'Do you really want to remove this group member?');
	
	echo "<div id='groupMemberId' style='display:none'></div>";
	echo "<div id='groupId' style='display:none'></div>";
	
	$deleteGroupMemberJSFunction = "function deleteGroupMember() { "
										.CHtml::ajax(
										array(
												'url'=>Yii::app()->createUrl('groups/deleteGroupMember'),
												'data'=> array('userId'=>"js:$('#groupMemberId').html()", 'groupId'=>"js:$('#groupId').html()"),
												'success'=> 'function(result) { 	
															 	try {
															 		TRACKER.closeConfirmationDialog();
																	var obj = jQuery.parseJSON(result);
																	if (obj.result && obj.result == "1") 
																	{
																		$.fn.yiiGridView.update("groupMembersListView");
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

	Yii::app()->clientScript->registerScript('groupMembersFunctions',
												$deleteGroupMemberJSFunction,
												CClientScript::POS_READY);	
	
	?>
	<div id="groupMembersGridView" style="overflow:hidden;">
	<?php
	$this->widget('zii.widgets.grid.CGridView', array(
		    'dataProvider'=>$dataProvider,
	 		'id'=>'groupMembersListView',
			'summaryText'=>'',
			'emptyText'=>Yii::t('groups','You have not added any of your friends into this group.'),
			'htmlOptions'=>array('style'=>'font-size:14px;'),
			'pager'=>array( 
				 'header'=>'',
		         'firstPageLabel'=>'',
		         'lastPageLabel'=>'',
			       ),
		    'columns'=>array(
		    		array(            // display 'create_time' using an expression
		    				'name'=>Yii::t('users', ''),
		    				'type' => 'raw',
							//'value'=>'"<div class=\"hi-icon-in-list icon-user\" style=\"color:#FFDB58; cursor:default;\"></div>"',
		    				
		    				'value'=>'getMemberPhoto($data, $row)',
		    				
		    				'htmlOptions'=>array('width'=>'40px', 'style'=>'text-align: center;'),
		    		),		    		
		    		
					array(            // display 'create_time' using an expression
							    'name'=>Yii::t('common', 'Name'),
								'type' => 'raw',		
								'sortable'=>true,	
								'value'=>'$data["Name"]',	
					),
					array(            // display 'create_time' using an expression
				//    'name'=>'realname',
								'type' => 'raw',
			            'value'=>'CHtml::link("<div class=\"hi-icon-in-list icon-user\" style=\"color:#FFDB58\"></div>
						    				   <div class=\"userActionDeleteIcon icon-close\"></div>", "#",
						    				   
											array("onclick"=>"$(\"#groupMemberId\").text(".$data[\'userId\'].");
															  $(\"#groupId\").text(".$data[\'groupId\'].");
															  TRACKER.showConfirmationDialog(\"'.$deleteGroupMemberQuestion.'\", deleteGroupMember);",
															   
													"class"=>"vtip", 
													"title"=>'.("Yii::t('groups', 'Remove Group Member')").',
													"style"=>"position: relative;")
													)',
			
								'htmlOptions'=>array('width'=>'44px')
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