<?php

if ($dataProvider != null) {
	$isFriendRequestList = isset($friendRequestList) ? true : false;
	$isSearchResult = isset($searchResult) ? true : false;
	$isFriendList = isset($friendList) ? true : false;

	$viewId = isset($viewId) ? $viewId : 'userListView';

	
	if ($isFriendList == true) {
		/** This is the friend ship id holder, when user clicks delete, its content is filled***/
		echo "<div id='friendShipId' style='display:none'></div>";
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
	}
	
	
//$this->widget('application.extensions.menu.SMenu',
//	array(
//		  "menu"=>array(
//					    array("url"=>array("route"=>"/product/create"),
//					          "label"=>"Sspiner",
//					          array("url"=>array("route"=>"/product/create"),
//					                "label"=>"Create product",
//					                ),
//					          array("url"=>array("route"=>"/product/list"),
//					                "label"=>"Product List",
//					                ),
//					          array("url"=>"",
//					                "label"=>"View Products",
//					                array("url"=>array("route"=>"/product/show",
//					                       			   "params"=>array("id"=>3),
//					                                   "htmlOptions"=>array("title"=>"title")),
//					                      "label"=>"Product 3"
//					          		      ),
//					                array("url"=>array("route"=>"/product/show",
//					                                   "params"=>array("id"=>4)),
//					                      "label"=>"Product 4",
//							              array("url"=>array("route"=>"/product/show",
//							                                 "params"=>array("id"=>5)),
//							                    "label"=>"Product 5"
//							                    )
//					                      )
//					                )
//					          ),
//					        
//				        array("url"=>array("route"=>"/event/create"),
//				              "label"=>"Scalendar"
//				              ),
//				              
//				        array("url"=>array(),
//				              "label"=>"Admin",
//				              array("url"=>array("route"=>"/event/admin"),
//				                    "label"=>"Scalendar Admin"
//				                    ),
//				              array("url"=>array("route"=>"/product/admin"),
//				                    "label"=>"Sspinner Admin"
//				                    ),
//				              array("url"=>array("route"=>"/product/admin"),
//				                    "label"=>"Disabled Link",
//				                    "disabled"=>true
//				                    )
//				              ),
//				              
//				        array("url"=>array(),
//				              "label"=>"Documentation",
//				              array("url"=>array("link"=>"http://www.yiiframework.com",
//				                                 "htmlOptions"=>array("target"=>"_BLANK")
//				                                 ),
//				                    "label"=>"Yii Framework"
//				                    ),
//				              array("url"=>array("route"=>"site/spinnerDoc"),
//				                    "label"=>"Sspinner"
//				                    ),
//				              array("url"=>array("route"=>"site/calendarDoc"),
//				                    "label"=>"Scalendar"
//				                    ),
//				              array("url"=>array("route"=>"site/menuDoc"),
//				                    "label"=>"Smenu"
//				                    ),
//				              )
//	         			),
//		  "stylesheet"=>"menu_white.css",
//		  "menuID"=>"myMenu",
//		  "delay"=>3
//		  )
//);



//$this->widget('application.extensions.menu.SMenu',
//	array(
//		  'menu'=>array(				              
//				        array('url'=>array(),
//				              'label'=>'Admin',
//				        	  'icon'=>'images/addGroup.png',
//				              array('url'=>array('route'=>'/event/admin'),
//				                    'label'=>'Scalendar Admin',
//				                    ),
//				              array('url'=>array('route'=>'/product/admin'),
//				                    'label'=>'Sspinner Admin'
//				                    ),
//				              array('url'=>array('route'=>'/product/admin'),
//				                    'label'=>'Disabled Link',
//				                    'disabled'=>true
//				                    )
//				              ),
//	         			),
//		  'stylesheet'=>'menu_white.css',
//		  'menuID'=>'myMenu',
//		  'delay'=>3
//		  )
//);

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
					'name'=>'Add Group',
					'type' => 'raw',
//					'value'=>
//					'$this->widget(\'application.extensions.menu.SMenu\',
//						array(
//							  "menu"=>array(				              
//									        array("url"=>array(),
//									              "label"=>"Admin",  
//									              array("url"=>array("route"=>"/event/admin"),
//									                    "label"=>"Scalendar Admin"
//									                    ),
//									              array("url"=>array("route"=>"/product/admin"),
//									                    "label"=>"Sspinner Admin"
//									                    ),
//									              array("url"=>array("route"=>"/product/admin"),
//									                    "label"=>"Disabled Link",
//									                    "disabled"=>true
//									                    )
//									              ),
//						         			),
//							  "stylesheet"=>"menu_white.css",
//							  "menuID"=>"myMenu",
//							  "delay"=>3
//							  )
//					)',
					//'value'=>'CHtml::dropDownList("listname", "M", array("M" => "Male", "F" => "Female"))',		
		            'value'=>'CHtml::link("<img src=\"images/addGroup.png\"  />", "#")',
					'htmlOptions'=>array('width'=>'10px'),
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
														 $(\"#confirmation\").dialog(\"open\");"
											)
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
															$.fn.yiiGridView.update("'.$viewId.'", {
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
											)))
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
		            				 CHtml::link(\'<img src="images/user_add_friend.png"  />\', \'#\',
					  				array(\'onclick\'=>CHtml::ajax(
					  						array(\'url\'=>Yii::app()->createUrl(\'users/addAsFriend\', array(\'friendId\'=>$data[\'id\'])),
					  							  \'success\'=>\'function(result) { alert(result); }\',
												 )
					  						)
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