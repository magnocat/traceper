<?php

//In order not to receive "TypeError: settings is undefined" exception while the grid view is being updated at testDeleteFriendShip() test
//Yii::app()->clientScript->scriptMap['jquery.min.js'] = false;
//Yii::app()->clientScript->scriptMap['jquery.ba-bbq.js'] = false;

if ($dataProvider != null) {
	$isFriendRequestList = isset($friendRequestList) ? true : false;
	$isSearchResult = isset($searchResult) ? true : false;
	$isFriendList = isset($friendList) ? true : false;
	
	
	if (isset($groupType) == false) {
		$groupType = ""; // TODO: grouptype is defined in usersInfo.php. Needs refactoring 
	}
	
	$emptyText = Yii::t('users', 'You do not have any friend at the moment. In order to add new friends, you could search them by name and choose from the list. If you could not find your friends by search, you could invite them to Traceper first by the link {inviteIcon}(Invite Friends) at the top menu or by {inviteByHere}.', array('{inviteIcon}'=>'<div class="lo-icon-in-tooltip icon-inviteUsers"></div>', 
			'{inviteByHere}'=>CHtml::ajaxLink(Yii::t('common', 'here'), $this->createUrl('site/inviteUsers'),
								array(
										'complete'=> 'function() { $("#inviteUsersWindow").dialog("open"); return false;}',
										'update'=> '#inviteUsersWindow',
								),
								array(
										'id'=>'inviteUsersByHereAjaxLink-'.uniqid() //Unique ID oluşturmayınca her ajaxta bir önceki sorgular da tekrarlanıyor
										))			
			));
			
	$userSearchEmptyText = Yii::t('users', 'No users found registered by this name unfortunately. You should invite your friend to Traceper first by the link {inviteIcon}(Invite Friends) at the top menu or by {inviteByHere}. After he/she joins Traceper, you could be friends.', array('{inviteIcon}'=>'<div class="lo-icon-in-tooltip icon-inviteUsers"></div>', 
			'{inviteByHere}'=>CHtml::ajaxLink(Yii::t('common', 'here'), $this->createUrl('site/inviteUsers'),
								array(
										'complete'=> 'function() { $("#inviteUsersWindow").dialog("open"); return false;}',
										'update'=> '#inviteUsersWindow',
								),
								array(
										'id'=>'inviteUsersByHereAtUserSearchAjaxLink'.uniqid() //Unique ID oluşturmayınca her ajaxta bir önceki sorgular da tekrarlanıyor
										))			
			));
	
	
	
	$friendshipRequestsEmptyText = Yii::t('users', 'You have no pending friendship requests at the moment.');
	
	// if $ajaxUrl is null in cgridview, it sends its data the route but in search we need to add
	// keyword parameter
	$ajaxUrl = null;
	$deleteFrienshipQuestion = Yii::t('users', 'Do you really want to delete this user from your friend list?');
	$deleteStaffQuestion = Yii::t('users', 'Do you really want to delete the account of this staff?');
	$addAsFriendQuestion = Yii::t('users', 'Do you want to add this user as a friend?');

	if ($isFriendRequestList == true) {
		$deleteFrienshipQuestion = Yii::t('users', 'Do you want to reject this user\'s friend request?');
		$emptyText = Yii::t('users', 'No friendship requests found');
	}
	else 
	{	
		if ($isSearchResult == true)
		{
			$ajaxUrl = Yii::app()->createUrl($this->route, array( CHtml::encode('SearchForm[keyword]')=>$model->attributes['keyword']) ) ;
		}	
	}

	if ($isFriendList == true) {
		
		//TODO: Refactor make common confirmation dialog 	
		/** This is the friend ship id holder, when user clicks delete, its content is filled***/
		echo "<div id='friendShipId' style='display:none'></div>";
		echo "<div id='friendId' style='display:none'></div>";
		echo "<div id='gridViewId' style='display:none'></div>";
				
		$deleteFriendshipJSFunction = "function deleteFriendship() { "
										.CHtml::ajax(
											array(
												'url'=>Yii::app()->createUrl('users/deleteFriendShip'),
												//'data'=> array('friendShipId'=>"js:$('#friendShipId').html()"),
												'data'=> array('friendId'=>"js:$('#friendId').html()"),
												'success'=> 'function(result) { 	
															 	try {
															 		TRACKER.closeConfirmationDialog();
																	var obj = jQuery.parseJSON(result);
																	if (obj.result && obj.result == "1") 
																	{
																		$.fn.yiiGridView.update($("#gridViewId").text());
													
																		if(obj.friendShipStatus == "0")
																		{
																			TRACKER.showMessageDialog("'.Yii::t('users', 'You have rejected the friendship request').'");
																		}
																		else
																		{
																			TRACKER.getFriendList(1, 0/*UserType::RealUser*/, null/*New friend id*/, obj.deletedFriendId);
																			TRACKER.showMessageDialog("'.Yii::t('users', 'The person is removed from your friend list').'");
																		}
																		
																	}
																	else if (obj.result && obj.result == "0")
																	{
																		$.fn.yiiGridView.update($("#gridViewId").text());
																		TRACKER.showMessageDialog("'.Yii::t('common', 'Sorry, an error occured in operation').'");
																	}													
																	else 
																	{
																		TRACKER.showMessageDialog("'.Yii::t('common', 'Sorry, an error occured in operation').'");
																	}
																}
																catch(e) {
																	TRACKER.showMessageDialog("The following error occurred: " + e.name + " - " + e.message);
																}
															}',
											)).
										"}";	

		$deleteUserJSFunction = "function deleteUser() { "
									.CHtml::ajax(
											array(
													'url'=>Yii::app()->createUrl('users/deleteUser'),
													'data'=> array('userId'=>"js:$('#friendId').html()"),
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
																			TRACKER.showMessageDialog("'.Yii::t('common', 'Sorry, an error occured in operation').'");
																		}
																	}
																	catch(ex) {
																		TRACKER.showMessageDialog("'.Yii::t('common', 'Sorry, an error occured in operation').'");
																	}
												}',
											)).
											"}";		
		 												
		$addAsFriendJSFunction = "function addasFriend(){
									". CHtml::ajax(
					  						array("url"=>Yii::app()->createUrl("users/addAsFriend"),
					  							  'data'=> array('friendId'=>"js:$('#friendId').html()"),
					  							  "success"=>'function(result) {
					  							  		TRACKER.closeConfirmationDialog();
														try {
															var obj = jQuery.parseJSON(result);
															if (obj.result && obj.result == "1") 
															{
																TRACKER.showMessageDialog("'.Yii::t('users', 'Friend request is sent...').'");
																$.fn.yiiGridView.update($("#gridViewId").text());
															}
															else 
															{
																TRACKER.showMessageDialog("'.Yii::t('common', 'Sorry, an error occured in operation').'");
															}
														}
														catch(ex) {
															TRACKER.showMessageDialog("'.Yii::t('common', 'Sorry, an error occured in operation').'");
														}
					  							  		
													}'
					  						))
									.
									"}"; 

		Yii::app()->clientScript->registerScript('frienshipFunctions',
														$deleteFriendshipJSFunction
														.$deleteUserJSFunction
														.$addAsFriendJSFunction,
		 												CClientScript::POS_READY);									
	}
	?>
	<div id="usersGridView" style="overflow:<?php echo ($isFriendList?"auto":"hidden"); ?>;"> 
	<?php		
	$this->widget('zii.widgets.grid.CGridView', array(
		    'dataProvider'=>$dataProvider,
	 		'id'=>$viewId,
			'ajaxUrl'=>$ajaxUrl,
			'summaryText'=>'',
			'emptyText'=>$isFriendList?$emptyText:($isFriendRequestList?$friendshipRequestsEmptyText:$userSearchEmptyText),
			'htmlOptions'=>array('style'=>'font-size:14px;'),				
			'pager'=>array(
				 'id'=>'UsersPager-'.uniqid(), //Unique ID oluşturmayınca her ajaxta bir önceki sorgular da tekrarlanıyor
				 'header'=>'',
		         'firstPageLabel'=>'',
		         'lastPageLabel'=>'',
			       ),
		    'columns'=>array(
				    		array(            // display 'create_time' using an expression
				    				'name'=>Yii::t('users', ''),
				    				'type' => 'raw',				    					
				    				//'value'=>'CHtml::image("images/Friend.png")',
				    				'value'=> $isFriendList ? '($data["isVisible"] == 1)?CHtml::link("<div class=\"hi-icon-effect-user hi-icon-effect-usera\"><div class=\"hi-icon-in-list icon-user\"></div></div>", "#", array("onclick"=>"TRACKER.trackUser(".$data["id"].");", "title"=>Yii::t("users", "See your friend\'s position on the map"))):CHtml::label("<div class=\"hi-icon-in-list icon-user\" style=\"color:#FFDB58\"></div>", "#", array("title"=>Yii::t("users", "This user does not share his/her location info at the moment")))' :
							    			  '"<div class=\"hi-icon-in-list icon-user\" style=\"color:#FFDB58; cursor:default;\"></div>"',					    		
				    				'htmlOptions'=>array('width'=>'40px', 'style'=>'text-align: center;')
				    				//'visible'=>$isFriendList || $isFriendRequestList
				    		),		    		
		    		
// 		array(            // display 'create_time' using an expression
// 					'name'=>Yii::t('users', 'Group Settings'),
// 					'type' => 'raw',
					
// 		            'value'=>'CHtml::link("<img src=\"images/GroupSettings.png\"  />", "#",
// 										array(\'onclick\'=>CHtml::ajax(
// 											array(
// 												\'url\'=>Yii::app()->createUrl(\'groups/updateGroup\', array(\'friendId\'=>$data[\'id\'], \'groupType\'=>'.$groupType.')),
												
// 					    						\'complete\'=> \'function() { $("#groupSettingsWindow").dialog("open"); return false;}\',
// 					 							\'update\'=> \'#groupSettingsWindow\',	
					 							
// 											)),\'class\'=>\'vtip\', \'title\'=>\''.Yii::t('common', 'Edit Settings').'\')
// 					  				 )',	
		
// 					'htmlOptions'=>array('width'=>'50px', 'style'=>'text-align: center;'),
// 					'visible'=>$isFriendList
// 		),
		
		/*
		array(            // display 'create_time' using an expression
					'name'=>Yii::t('users', 'Geofence Settings'),
					'type' => 'raw',
					
		            'value'=>'CHtml::link("<img src=\"images/GeofenceSettings.png\"  />", "#",
										array(\'onclick\'=>CHtml::ajax(
											array(
												\'url\'=>Yii::app()->createUrl(\'geofence/UpdateGeofencePrivacy\', array(\'friendId\'=>$data[\'id\'])),
												
					    						\'complete\'=> \'function() { $("#geofenceSettingsWindow").dialog("open"); return false;}\',
					 							\'update\'=> \'#geofenceSettingsWindow\',	
					 							
											)),\'class\'=>\'vtip\', \'title\'=>\''.Yii::t('common', 'Edit Settings').'\')
					  				 )',		
		
					'htmlOptions'=>array('width'=>'50px', 'style'=>'padding-left:30px;'),
					'visible'=>$isFriendList
		),
		*/       

		array(            // display 'create_time' using an expression
				    'name'=>Yii::t('common', 'Name'),
					'type' => 'raw',
					'sortable'=>$isFriendList ? true : false,
		            'value'=> $isFriendList ? '($data["isVisible"] == 1)?CHtml::link($data["Name"], "#", array("onclick"=>"TRACKER.trackUser(".$data["id"].");", "title"=>Yii::t("users", "See your friend\'s position on the map"))):CHtml::label($data["Name"], "#", array("title"=>Yii::t("users", "This user does not share his/her location info at the moment")))' : '$data["Name"]',
					'htmlOptions'=>array('width'=>'200px'),

// 					'value'=> $isFriendList ? 'CHtml::link($data["Name"], "#", array("onclick"=>"TRACKER.trackUser(".$data["id"].");", "title"=>Yii::t("users", "See your friend\'s position on the map")))' :
// 					'(isset($data[\'status\']) && $data[\'status\'] == 1) ?
// 					CHtml::link($data["Name"], "#", array("onclick"=>"TRACKER.trackUser(".$data["id"].");", "title"=>Yii::t("users", "See your friend\'s position on the map"))):
// 					$data["Name"]',				
		),
		//below line is the first line of onClick...
		//it is deleted due to refactoring on model and controller side
		// $(\"#friendShipId\").text(".$data[\'friendShipId\'].");
	
    	array(            // display 'create_time' using an expression
    				'type' => 'raw',
    				'value'=> 'CHtml::link("<div class=\"hi-icon-in-list icon-user\" style=\"color:#FFDB58\"></div>
						    				<div class=\"userActionDeleteIcon icon-close\"></div>", "#",
						    				array("onclick"=>"
							    				$(\"#friendId\").text(".$data[\'id\'].");
							    				$(\"#gridViewId\").text(\"'.$viewId.'\");
							    				 
							    				if((".$data[\'userType\']." == \"'.UserType::RealUser.'\") || (".$data[\'userType\']." == \"'.UserType::GPSDevice.'\"))
							    				{
							    					TRACKER.showConfirmationDialog(\"'.$deleteFrienshipQuestion.'\", deleteFriendship);
							    				}
							    				else
							    				{
							    					TRACKER.showConfirmationDialog(\"'.$deleteStaffQuestion.'\", deleteUser);
							    				}
							    				",
						    				"class"=>"vtip",
						    				"title"=>'.("Yii::t('users', 'Delete from your friend list')").',
						    				"style"=>"position: relative;")
						    				)'
    				,
    				'htmlOptions'=>array('width'=>'44px'),
    				//'visible'=>($isFriendList || $isFriendRequestList) || '(isset($data[\'status\']) && $data[\'status\'] == 0 && isset($data[\'requester\']) && $data[\'requester\'] == false)',
    				'visible'=>$isFriendList
    		),		    		
		    		
		array(            // display 'create_time' using an expression
					'type' => 'raw',
		            'value'=> 'CHtml::link("Reject User", "#",
										array("onclick"=>"
														 $(\"#friendId\").text(".$data[\'id\'].");
														 $(\"#gridViewId\").text(\"'.$viewId.'\");
				
														 if((".$data[\'userType\']." == \"'.UserType::RealUser.'\") || (".$data[\'userType\']." == \"'.UserType::GPSDevice.'\"))
														 {
															 TRACKER.showConfirmationDialog(\"'.$deleteFrienshipQuestion.'\", deleteFriendship);
														 }
														 else
														 {
															 TRACKER.showConfirmationDialog(\"'.$deleteStaffQuestion.'\", deleteUser);
														 }				
														 ", 									
												"class"=>"vtip", 
												"title"=>'.("Yii::t('users', 'Reject')").',
												"class"=>"lo-icon icon-close")
					  				  )'				
				, 
					'htmlOptions'=>array('width'=>'28px', 'style'=>'text-align:center;', 'class'=>'lo-icon-effect-3 lo-icon-effect-red'),
					//'visible'=>($isFriendList || $isFriendRequestList) || '(isset($data[\'status\']) && $data[\'status\'] == 0 && isset($data[\'requester\']) && $data[\'requester\'] == false)',
					'visible'=>$isFriendRequestList
		),
		    		
		    		
		    		
		   //below line is a parameter of Yii::app()->createUrl(\'users/approveFriendShip...
		   //it is deleted due to refactoring on model and controller side				
		    		/*, array(\'friendShipId\'=>$data[\'friendShipId\'])*/
		array(            // display 'create_time' using an expression
					'type' => 'raw',
		            'value'=> '(isset($data["status"]) && $data["status"] == 0 
								&& isset($data["requester"]) && $data["requester"] == false) ?
									CHtml::link("Approve User", "#",
										array("onclick"=>CHtml::ajax(
											array(
												"url"=>Yii::app()->createUrl("users/approveFriendShip", array("friendId"=>$data["id"])),
												"success"=> "function(result) { 
													try {
														$(\"#confirmation\").dialog(\"close\");
														var obj = jQuery.parseJSON(result);
														if (obj.result && obj.result == \"1\") 
														{
															$.fn.yiiGridView.update(\"'.$viewId.'\");
															$.fn.yiiGridView.update(\"userListView\");
															TRACKER.getFriendList(1, 0/*UserType::RealUser*/, obj.friendId/*New friend id*/);
															TRACKER.showMessageDialog(\"'.Yii::t('users', 'The friendship request is approved, you are now friends...').'\");
														}
														else 
														{
															TRACKER.showMessageDialog(\"'.Yii::t('common', 'Sorry, an error occured in operation').'\");
														}
													}
													catch(e) {
														TRACKER.showMessageDialog(\"The following error occurred: \" + e.name + \" - \" + e.message);
													}
													
												}",
											)),"class"=>"vtip", "title"=>"'.Yii::t('users', 'Approve').'", "class"=>"lo-icon icon-checkmark")
					  				 )
					  			: ""',
				'htmlOptions'=>array('width'=>'28px', 'style'=>'text-align: center;', 'class'=>'lo-icon-effect-3 lo-icon-effect-green'),
					'visible'=>$isFriendRequestList
		),
		array(            // display 'create_time' using an expression
	/*  This field can only be seen in search results
	* if status == -1 it means there is no relation between these users*/
					'type' => 'raw',
		            'value'=>' (isset($data[\'status\']) && $data[\'status\'] == -1) ?  
		            				 CHtml::link("<div class=\"hi-icon-in-list icon-user\" style=\"color:#FFDB58\"></div>
						    					  <div class=\"userActionAddIcon icon-plus\"></div>", "#",
								  				  array("onclick"=>"$(\"#friendId\").text(".$data[\'id\'].")
								  								 $(\"#gridViewId\").text(\"'.$viewId.'\"); 
																 TRACKER.showConfirmationDialog(\"'.$addAsFriendQuestion.'\", addasFriend); 
																 ",
													  "class"=>"vtip", 
													  "title"=>'."Yii::t('users', 'Add as Friend')".',
													  "style"=>"position: relative;"													 									
								  					)
					 				)
					 			: (
										(isset($data[\'status\']) && $data[\'status\'] == 1) ?
				
										CHtml::label("<div class=\"hi-icon-in-list icon-user\" style=\"color:#FFDB58\"></div>
						    						<div class=\"userStatusIcon icon-checkmark\" style=\"color:#43C6DB;\"></div>", "#",
								    				array("class"=>"vtip",
								    					  "title"=>'."Yii::t('users', 'Already your friend')".',
								    				      "style"=>"position: relative;")
						 				):										
										
										(
											(isset($data[\'status\']) && $data[\'status\'] == 0 && isset($data[\'requester\']) && $data[\'requester\'] == true)?
				
											CHtml::label("<div class=\"hi-icon-in-list icon-user\" style=\"color:#FFDB58\"></div>
							    						<div class=\"userStatusIcon icon-question\" style=\"color:#6698FF;\"></div>", "#",
									    				array("class"=>"vtip",
									    					  "title"=>'."Yii::t('users', 'Waiting reply for your friendship request')".',
									    				      "style"=>"position: relative;")				
							 				):
				
											CHtml::label("<div class=\"hi-icon-in-list icon-user\" style=\"color:#FFDB58\"></div>
							    						<div class=\"userStatusIcon icon-envelope\" style=\"color:#D462FF;\"></div>", "#",
									    				array("class"=>"vtip",
									    					  "title"=>'."Yii::t('users', 'This user wants to be friend with you. You can approve or reject the request via <Friendship Requests> menu at the top')".',
									    				      "style"=>"position: relative;")				
											)
				
										)
								   )
								;',
					'htmlOptions'=>array('width'=>'44px'),
					'visible'=>$isSearchResult,
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