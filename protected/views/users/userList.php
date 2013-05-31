<?php

//testDeleteFriendShip() testinde grid view güncellenirken "TypeError: settings is undefined" exception'ý almamak için
//Yii::app()->clientScript->scriptMap['jquery.min.js'] = false;
//Yii::app()->clientScript->scriptMap['jquery.ba-bbq.js'] = false;

if ($dataProvider != null) {
	$isFriendRequestList = isset($friendRequestList) ? true : false;
	$isSearchResult = isset($searchResult) ? true : false;
	$isFriendList = isset($friendList) ? true : false;
	
	
	if (isset($groupType) == false) {
		$groupType = ""; // TODO: grouptype is defined in usersInfo.php. Needs refactoring 
	}
	$emptyText = Yii::t('users', 'No users found');
	// if $ajaxUrl is null in cgridview, it sends its data the route but in search we need to add
	// keyword parameter
	$ajaxUrl = null;
	$deleteFrienshipQuestion = Yii::t('users', 'Do you want to delete this user from your friend list?');
	$deleteStaffQuestion = Yii::t('users', 'Do you want to delete the account of this staff?');
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
																		TRACKER.showMessageDialog("'.Yii::t('users', 'The person is removed from your friend list').'");
																	}
																	else if (obj.result && obj.result == "0")
																	{
																		$.fn.yiiGridView.update($("#gridViewId").text());
																		TRACKER.showMessageDialog("'.Yii::t('users', 'The friendship request is rejected').'");
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
																TRACKER.showMessageDialog("'.Yii::t('users', 'Friend request is sent').'");
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
	
		

	$this->widget('zii.widgets.grid.CGridView', array(
		    'dataProvider'=>$dataProvider,
	 		'id'=>$viewId,
			'ajaxUrl'=>$ajaxUrl,
			'summaryText'=>'',
			'emptyText'=>$emptyText,
			'pager'=>array( 
				 'header'=>'',
		         'firstPageLabel'=>'',
		         'lastPageLabel'=>'',
			       ),
		    'columns'=>array(
		array(            // display 'create_time' using an expression
					'name'=>Yii::t('users', 'Group Settings'),
					'type' => 'raw',
					
		            'value'=>'CHtml::link("<img src=\"images/GroupSettings.png\"  />", "#",
										array(\'onclick\'=>CHtml::ajax(
											array(
												\'url\'=>Yii::app()->createUrl(\'groups/updateGroup\', array(\'friendId\'=>$data[\'id\'], \'groupType\'=>'.$groupType.')),
												
					    						\'complete\'=> \'function() { $("#groupSettingsWindow").dialog("open"); return false;}\',
					 							\'update\'=> \'#groupSettingsWindow\',	
					 							
											)),\'class\'=>\'vtip\', \'title\'=>\''.Yii::t('common', 'Edit Settings').'\')
					  				 )',	
		
					'htmlOptions'=>array('width'=>'50px', 'style'=>'padding-left:30px;'),
					'visible'=>$isFriendList
		),
		
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
		            'value'=>'CHtml::link($data["Name"], "#", array(
    										"onclick"=>"TRACKER.trackUser(".$data["id"].");",
										))',	
		),
		//below line is the first line of onClick...
		//it is deleted due to refactoring on model and controller side
		// $(\"#friendShipId\").text(".$data[\'friendShipId\'].");
		array(            // display 'create_time' using an expression
					'type' => 'raw',
		            'value'=>'CHtml::link("<img src=\"images/delete.png\"  />", "#",
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
												"title"=>'.($isFriendRequestList?"Yii::t('users', 'Reject')":"Yii::t('users', 'Delete from your friend list')").
											')
					  				  )', 
					'htmlOptions'=>array('width'=>'16px'),
					'visible'=>$isFriendList || $isFriendRequestList,
		),
		    		
		   //below line is a parameter of Yii::app()->createUrl(\'users/approveFriendShip...
		   //it is deleted due to refactoring on model and controller side				
		    		/*, array(\'friendShipId\'=>$data[\'friendShipId\'])*/
		array(            // display 'create_time' using an expression
					'type' => 'raw',
		            'value'=>'(isset($data[\'status\']) && $data[\'status\'] == 0 
								&& isset($data[\'requester\']) && $data[\'requester\'] == false) ?
									CHtml::link(\'<img src="images/approve.png"  />\', \'#\',
										array(\'onclick\'=>CHtml::ajax(
											array(
												\'url\'=>Yii::app()->createUrl(\'users/approveFriendShip\', array(\'friendId\'=>$data[\'id\'])),
												\'success\'=> \'function(result) { 
													try {
														$("#confirmation").dialog("close");
														var obj = jQuery.parseJSON(result);
														if (obj.result && obj.result == "1") 
														{
															$.fn.yiiGridView.update("'.$viewId.'");
															$.fn.yiiGridView.update("userListView");
															TRACKER.showMessageDialog("'.Yii::t('users', 'The friendship request is approved, you are now friends...').'");
														}
														else 
														{
															TRACKER.showMessageDialog("'.Yii::t('common', 'Sorry, an error occured in operation').'");
														}
													}
													catch(e) {
														TRACKER.showMessageDialog("The following error occurred: " + e.name + " - " + e.message);
													}
													
												}\',
											)),\'class\'=>\'vtip\', \'title\'=>\''.Yii::t('users', 'Approve').'\')
					  				 )
					  			: ""',
					'htmlOptions'=>array('width'=>'16px'),
					'visible'=>$isFriendRequestList,
		),
		array(            // display 'create_time' using an expression
	/*  This field can only be seen in search results
	* if status == -1 it means there is no relation between these users*/
					'type' => 'raw',
		            'value'=>' (isset($data[\'status\']) && $data[\'status\'] == -1) ?  
		            				 CHtml::link("<img src=\"images/user_add_friend.png\"  />", "#",
					  				array("onclick"=>"$(\"#friendId\").text(".$data[\'id\'].")
					  								 $(\"#gridViewId\").text(\"'.$viewId.'\"); 
													 TRACKER.showConfirmationDialog(\"'.$addAsFriendQuestion.'\", addasFriend); 
													 ",
										  "class"=>"vtip", 
										  "title"=>'."Yii::t('users', 'Add as Friend')".'													 									
					  					)
					 				)
					 			: (
										(isset($data[\'status\']) && $data[\'status\'] == 0) ?
										CHtml::image("images/friend_request_waiting.png", "#",
						  				array("class"=>"vtip", 
											  "title"=>'."Yii::t('users', 'Friend request is waiting')".'										  													 									
						  					)
						 				)
						 				: CHtml::image("images/alreadyFriend.png", "#",
						  					array("class"=>"vtip", 
											  	  "title"=>'."Yii::t('users', 'Already your friend')".'										  													 									
						  					)
						 				)				
								   )
								;',
					'htmlOptions'=>array('width'=>'16px'),
					'visible'=>$isSearchResult,
		),
	),
	));
	




}
/*
 */
?>