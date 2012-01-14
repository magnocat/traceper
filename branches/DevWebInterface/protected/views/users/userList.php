<?php

if ($dataProvider != null) {
	$isFriendRequestList = isset($friendRequestList) ? true : false;
	$isSearchResult = isset($searchResult) ? true : false;
	$isFriendList = isset($friendList) ? true : false;

	$viewId = isset($viewId) ? $viewId : 'userListView';

	
	if ($isFriendList == true) {
		
		//TODO: Refactor make common confirmation dialog 	
		/** This is the friend ship id holder, when user clicks delete, its content is filled***/
		echo "<div id='friendShipId' style='display:none'></div>";
		echo "<div id='friendId' style='display:none'></div>";
		echo "<div id='gridViewId' style='display:none'></div>";
		$this->beginWidget('zii.widgets.jui.CJuiDialog', array(
		    'id'=>'confirmation',
			// additional javascript options for the dialog plugin
		    'options'=>array(
		        'title'=>Yii::t('general', 'Delete friendship'),
		        'autoOpen'=>false,
		        'modal'=>true, 
				'resizable'=>false,
				'buttons' =>array (
					"OK"=>"js:function(){
								". CHtml::ajax(
										array(
												'url'=>Yii::app()->createUrl('users/deleteFriendShip'),
												'data'=> array('friendShipId'=>"js:$('#friendShipId').html()"),
												'success'=> 'function(result) { 	
															 	try {
															 		$("#confirmation").dialog("close");
																	var obj = jQuery.parseJSON(result);
																	if (obj.result && obj.result == "1") 
																	{
																		$.fn.yiiGridView.update($("#gridViewId").text());
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
															}',
											)) .
							"}",
				"Cancel"=>"js:function() {
					$( this ).dialog( \"close\" );
				}" 
				)),
			));
		echo "Do you want to delete this user from your friend list?";
		$this->endWidget('zii.widgets.jui.CJuiDialog');
		
		$this->beginWidget('zii.widgets.jui.CJuiDialog', array(
		    'id'=>'addAsFriendConfirmation',
			// additional javascript options for the dialog plugin
		    'options'=>array(
		        'title'=>Yii::t('general', 'Add as friend'),
		        'autoOpen'=>false,
		        'modal'=>true, 
				'resizable'=>false,
				'buttons' =>array (
					"OK"=>"js:function(){
								". CHtml::ajax(
					  						array("url"=>Yii::app()->createUrl("users/addAsFriend"),
					  							  'data'=> array('friendId'=>"js:$('#friendId').html()"),
					  							  "success"=>'function(result) {
					  							  		$("#addAsFriendConfirmation").dialog("close"); 
					  							  		try {
														var obj = jQuery.parseJSON(result);
														if (obj.result && obj.result == "1") 
														{
															$.fn.yiiGridView.update($("#gridViewId").text());
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
					  							  		
													}'
					  						))
									.
							"}",
				"Cancel"=>"js:function() {
					$( this ).dialog( \"close\" );
				}" 
				)),
			));
		echo "Do you want to add this user as a friend?";
		$this->endWidget('zii.widgets.jui.CJuiDialog');
	}
	

	$this->widget('zii.widgets.grid.CGridView', array(
		    'dataProvider'=>$dataProvider,
	 		'id'=>$viewId,
			'summaryText'=>'',
			'pager'=>array( 
				 'header'=>'',
		         'firstPageLabel'=>'',
		         'lastPageLabel'=>'',
			       ),
		    'columns'=>array(
		array(            // display 'create_time' using an expression
	//    'name'=>'realname',
					'name'=>'Group Settings',
					'type' => 'raw',
  
					//'value'=>'CHtml::dropDownList("listname", "M", array("M" => "Male", "F" => "Female"))',		
		            //'value'=>'CHtml::link("<img src=\"images/addGroup.png\"  />", "#")',

					'value'=>'CHtml::ajaxLink("<div class=\"userOperations\" id=\"groupSettings\">
 										<img src=\"images/GroupSettings.png\"/><div></div>
 									 </div>", Yii::app()->createUrl("groups/updateGroup"), 
	 						array(
	    						"complete"=> "function() { $(\"#groupSettingsWindow\").dialog(\"open\"); return false;}",
	 							"update"=> "#groupSettingsWindow",
							),
							array(
								"id"=>"showGroupSettingsWindowWindow","class"=>"vtip", "title"=>"Edit Settings"))',		
		
					'htmlOptions'=>array('width'=>'80px'),
					'visible'=>$isFriendList
		),
		       

		array(            // display 'create_time' using an expression
				    'name'=>'Name',
					'type' => 'raw',
		            'value'=>'CHtml::link($data["realname"], "#", array(
    										"onclick"=>"TRACKER.trackUser(".$data["id"].");",
										))',	
		),
		array(            // display 'create_time' using an expression
	//    'name'=>'realname',
					'type' => 'raw',
		            'value'=>'CHtml::link("<img src=\"images/delete.png\"  />", "#",
										array("onclick"=>"$(\"#friendShipId\").text(".$data[\'friendShipId\'].");
														 $(\"#gridViewId\").text(\"'.$viewId.'\"); 
														 $(\"#confirmation\").dialog(\"open\");", 
												"class"=>"vtip", 
												"title"=>'.($isFriendRequestList?'"Reject"':'"Delete Friend"').
											')
					  				  )',
					'htmlOptions'=>array('width'=>'16px'),
					'visible'=>$isFriendList || $isFriendRequestList,
		),
		array(            // display 'create_time' using an expression
	//    'name'=>'realname',
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
					  								 $(\"#addAsFriendConfirmation\").dialog(\"open\");", "class"=>"vtip", "title"=>"Add as Friend"
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