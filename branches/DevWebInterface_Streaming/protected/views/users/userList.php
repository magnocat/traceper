<?php

if ($dataProvider != null) {
	$isFriendRequestList = isset($friendRequestList) ? true : false;
	$isSearchResult = isset($searchResult) ? true : false;
	$isFriendList = isset($friendList) ? true : false;

	$viewId = isset($viewId) ? $viewId : 'userListView';

	$emptyText = "No users found";
	// if $ajaxUrl is null in cgridview, it sends its data the route but in search we need to add
	// keyword parameter
	$ajaxUrl = null;
	$deleteFrienshipQuestion = "Do you want to delete this user from your friend list?";
	$addAsFriendQuestion = "Do you want to add this user as a friend?";
	if ($isFriendRequestList == true) {
		$deleteFrienshipQuestion = "Do you want to reject this user's friend request?";
		$emptyText = "There is no friendship requests found";
	}
	else if ($isSearchResult == true){
		$ajaxUrl = Yii::app()->createUrl($this->route, array( CHtml::encode('SearchForm[keyword]')=>$model->attributes['keyword']) ) ;
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
												'data'=> array('friendShipId'=>"js:$('#friendShipId').html()"),
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
																		TRACKER.showMessageDialog("Sorry,an error occured in operation");
																	}
																}
																catch(ex) {
																	TRACKER.showMessageDialog("Sorry,an error occured in operation");
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
																$.fn.yiiGridView.update($("#gridViewId").text());
															}
															else 
															{
																TRACKER.showMessageDialog("Sorry,an error occured in operation");
															}
														}
														catch(ex) {
															TRACKER.showMessageDialog("Sorry,an error occured in operation");
														}
					  							  		
													}'
					  						))
									.
									"}"; 

		Yii::app()->clientScript->registerScript('frienshipFunctions',
														$deleteFriendshipJSFunction
														.$addAsFriendJSFunction,
		 												CClientScript::POS_READY);									
	}
	
	$createGeofenceJSFunction = "function createGeofence(){
		 								". CHtml::ajax(
											array(
												'url'=>Yii::app()->createUrl('geofence/CreateGeofence'),
												'data'=> array('name'=>'1',
																'point1Latitude'=>1,
																'point1Longitude'=>1,
																'point2Latitude'=>2,
																'point2Longitude'=>2,
																'point3Latitude'=>3,
																'point3Longitude'=>3),
												'success'=> 'function(result) { 	
															 	try {
															 		TRACKER.closeConfirmationDialog();
																	var obj = jQuery.parseJSON(result);
																	if (obj.result && obj.result == "1") 
																	{
																	}
																	else 
																	{
																		TRACKER.showMessageDialog("Sorry,an error occured in operation1");
																	}
																}
																catch(ex) {
																	TRACKER.showMessageDialog("Sorry,an error occured in operation2");
																}
															}',
											)).
										"}";

	Yii::app()->clientScript->registerScript('getGeofenceInBackground',
														$createGeofenceJSFunction,
		 												CClientScript::POS_READY);
	

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
					'name'=>'Group Settings',
					'type' => 'raw',
					
		            'value'=>'CHtml::link("<img src=\"images/GroupSettings.png\"  />", "#",
										array(\'onclick\'=>CHtml::ajax(
											array(
												\'url\'=>Yii::app()->createUrl(\'groups/updateGroup\', array(\'friendId\'=>$data[\'id\'])),
												
					    						\'complete\'=> \'function() { $("#groupSettingsWindow").dialog("open"); return false;}\',
					 							\'update\'=> \'#groupSettingsWindow\',	
					 							
											)),\'class\'=>\'vtip\', \'title\'=>\'Edit Settings\')
					  				 )',		
		
					'htmlOptions'=>array('width'=>'50px', 'style'=>'padding-left:30px;'),
					'visible'=>$isFriendList
		),
		       

		array(            // display 'create_time' using an expression
				    'name'=>'Name',
					'type' => 'raw',
					'sortable'=>$isFriendList ? true : false,
		            'value'=>'CHtml::link($data["Name"], "#", array(
    										"onclick"=>"TRACKER.trackUser(".$data["id"].");",
										))',	
		),
		array(            // display 'create_time' using an expression
					'type' => 'raw',
		            'value'=>'CHtml::link("<img src=\"images/delete.png\"  />", "#",
										array("onclick"=>"$(\"#friendShipId\").text(".$data[\'friendShipId\'].");
														 $(\"#gridViewId\").text(\"'.$viewId.'\");
														 TRACKER.showConfirmationDialog(\"'.$deleteFrienshipQuestion.'\", deleteFriendship);
														 ", 									
												"class"=>"vtip", 
												"title"=>'.($isFriendRequestList?'"Reject"':'"Delete Friend"').
											')
					  				  )',
					'htmlOptions'=>array('width'=>'16px'),
					'visible'=>$isFriendList || $isFriendRequestList,
		),
		array(            // display 'create_time' using an expression
					'type' => 'raw',
		            'value'=>'(isset($data[\'status\']) && $data[\'status\'] == 0 
								&& isset($data[\'requester\']) && $data[\'requester\'] == false) ?
									CHtml::link(\'<img src="images/approve.png"  />\', \'#\',
										array(\'onclick\'=>CHtml::ajax(
											array(
												\'url\'=>Yii::app()->createUrl(\'users/approveFriendShip\', array(\'friendShipId\'=>$data[\'friendShipId\'])),
												\'success\'=> \'function(result) { 
													try {
														$("#confirmation").dialog("close");
														var obj = jQuery.parseJSON(result);
														if (obj.result && obj.result == "1") 
														{
															$.fn.yiiGridView.update("'.$viewId.'");
														}
														else 
														{
															$("#messageDialogText").html("Sorry,an error occured in operation");
															$("#messageDialog").dialog("open");
														}
													}
													catch(ex) {
														$("#messageDialogText").html("Sorry,an error occured in operation");
														$("#messageDialog").dialog("open");
													}
													
												}\',
											)),\'class\'=>\'vtip\', \'title\'=>\'Approve\')
					  				 )
					  			: ""',
					'htmlOptions'=>array('width'=>'16px'),
					'visible'=>$isFriendRequestList,
		),
		array(            // display 'create_time' using an expression
	/*  This field can only be seen in search results
	* if status == -1 it means there is no relation between these users*/
					'type' => 'raw',
		            'value'=>' (isset($data[\'status\']) && $data[\'status\'] == -1 && $data["id"] != Yii::app()->user->id) ?  
		            				 CHtml::link("<img src=\"images/user_add_friend.png\"  />", "#",
					  				array("onclick"=>"$(\"#friendId\").text(".$data[\'id\'].")
					  								 $(\"#gridViewId\").text(\"'.$viewId.'\"); 
													 TRACKER.showConfirmationDialog(\"'.$addAsFriendQuestion.'\", addasFriend); 
													 ",
										  "class"=>"vtip", 
										  "title"=>"Add as Friend"													 									
					  					)
					 				)
					 			: "";',
					'htmlOptions'=>array('width'=>'16px'),
					'visible'=>$isSearchResult,
		),
	),
	));
	




}
/*
 */
?>