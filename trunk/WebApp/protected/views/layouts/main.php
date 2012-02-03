<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
	<head>
		<title><?php echo Yii::app()->name; ?></title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
		<meta name="keywords"  content="" />
		<meta name="description" content="open source GPS tracking system" />
		<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/style.css" media="screen, projection" />
		<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/main.css" />
		<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/form.css" />
		<link rel="shortcut icon" href="<?php echo Yii::app()->request->baseUrl; ?>/images/icon.png" type="image/x-icon"/>
 	<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/js/DataOperations.js"></script>

	<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/js/maps/MapStructs.js"></script>	
	<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/js/maps/GMapOperator.js"></script>
	<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/js/TrackerOperator.js"></script>
	<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/js/LanguageOperator.js"></script>		
	<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/js/bindings.js"></script>	

	<?php 
		Yii::app()->clientScript->registerScript('appStart',"var checked = false;
			try 
			{
				var mapStruct = new MapStruct();
			    var initialLoc = new MapStruct.Location({latitude:39.504041,
			    								  longitude:35.024414}); 
				mapOperator.initialize(initialLoc);
				//TODO: ../index.php should be changed 
				//TODO: updateUserListInterval 
				//TODO: queryIntervalForChangedUsers 
   				var trackerOp = new TrackerOperator('index.php', mapOperator, fetchPhotosDefaultValue, 5000, 30000)	   	
				trackerOp.setLangOperator(langOp);	  				
			}
   			catch (e) {
				
			}

  		",
		CClientScript::POS_READY);
		
		 if (Yii::app()->user->isGuest == false){
		 		Yii::app()->clientScript->registerScript('getDataInBackground',
														'trackerOp.getFriendList(1);	
		  												 trackerOp.getImageList(); ',
		 												CClientScript::POS_READY); 				
		 }
	
	?>
 
	<script type="text/javascript">		
		var langOp = new LanguageOperator();
		var fetchPhotosDefaultValue =  1;  //TODO: $fetchPhotosInInitialization;
		langOp.load("en");  //TODO: itshould be parametric
		
		var mapOperator = new MapOperator();
	</script>
	   
	</head>
	<body>	
	
	<?php

///////////////////////////// About traceper Window ///////////////////////////	
	$this->beginWidget('zii.widgets.jui.CJuiDialog', array(
	    'id'=>'Logo',
	    // additional javascript options for the dialog plugin
	    'options'=>array(
	        'title'=>Yii::t('general', 'About'),
	        'autoOpen'=>false,
	        'modal'=>true, 
			'resizable'=>false,
			'width'=> '600px'     
	    ),
	));

	echo '<div id="logo"></div>';	
	echo 'traceper is a GPS tracking system for mobile users, it is free, it is open source, it is simple. You can track and see your friends\' positions online.<br/><br/><div class=\"title\">Support</div>If you need support to modify and use this software, We can share all information we have, so feel free to contact us.<br/><br/><div class=\"title\">License</div>This software is free. It can be modified and distributed without notification.<br/><br/><div class=\"title\">Disclaimer</div>This software guarantees nothing, use it with your own risk. No responsilibity is taken for any situation.<br/><br/><div class=\"title\">Contact</div><a href=\"mailto:contact@mekya.com\">contact@mekya.com</a><br/><br/><div class=\"title\">Project Team</div><div id=\"projectteam\">Adnan Kalay - adnankalay@gmail.com <br/> Ahmet Oguz Mermerkaya - ahmetmermerkaya@gmail.com <br/> Murat Salman - salman.murat@gmail.com </div>';
			
	$this->endWidget('zii.widgets.jui.CJuiDialog');	

///////////////////////////// User Login Window ///////////////////////////	
	echo '<div id="userLoginWindow"></div>';
///////////////////////////// Register Window ///////////////////////////
	echo '<div id="registerWindow"></div>';
///////////////////////////// Register GPS Tracker Window ///////////////////////////
	echo '<div id="registerGPSTrackerWindow"></div>';
///////////////////////////// GeoFence Window ///////////////////////////
	echo '<div id="geoFenceWindow"></div>';	
///////////////////////////// Change Password Window ///////////////////////////
	echo '<div id="changePasswordWindow"></div>';
///////////////////////////// Invite User Window ///////////////////////////
	echo '<div id="inviteUsersWindow"></div>';
///////////////////////////// Friend Request Window ///////////////////////////	
	echo '<div id="friendRequestsWindow"></div>';
///////////////////////////// Create Group Window ///////////////////////////	
	echo '<div id="createGroupWindow"></div>';	
///////////////////////////// Group Settings Window ///////////////////////////	
	echo '<div id="groupSettingsWindow"></div>';
///////////////////////////// Group Privacy Settings Window ///////////////////////////	
	echo '<div id="groupPrivacySettingsWindow"></div>';	
///////////////////////////// Photo Comment Window ///////////////////////////	
	$this->beginWidget('zii.widgets.jui.CJuiDialog', array(
	    'id'=>'photoCommentWindow',
	    // additional javascript options for the dialog plugin
	    'options'=>array(
	        'title'=>Yii::t('general', 'Comment Window'),
	        'autoOpen'=>false,
	        'modal'=>true, 
			'resizable'=>false,
			'width'=> '400px',
			'height'=> '300'     
	    ),
	));
	
	echo '	<div id="photoCommentForm" class="">
		<div id="photoCommentLabel">Comment:</div>
		<textarea id="photoCommentTextBox" cols="40" rows="7" style="resize:none">Enter your comments here...	</textarea><br/>
		<input type="button" id="sendCommentButton" value="Upload Comment" /><br/>
		<input type="button" id="deleteCommentButton" value="Delete Comment" />	 
	</div>';
			
	$this->endWidget('zii.widgets.jui.CJuiDialog');		
/////////////////////////////////////////////////////////////////////////////////////////////////

	// this is a generic message dialog
	$this->beginWidget('zii.widgets.jui.CJuiDialog', array(
	    'id'=>'messageDialog',
	    // additional javascript options for the dialog plugin
	    'options'=>array(
	        'title'=>Yii::t('general', 'Message'),
	        'autoOpen'=>false,
	        'modal'=>true, 
			'resizable'=>false,
            'width'=>'auto',
            'height'=>'auto',
			'buttons'=>array(
					"OK"=>"js:function(){
						$(this).dialog('close');
					}"
				),	
	      
	    ),
	));
	echo '</br>';
	echo '<div align="center" id="messageDialogText"></div>';	
	$this->endWidget('zii.widgets.jui.CJuiDialog');	
/////////////////////////////////////////////////////////////////////////////////////////////
/*
 * this is a generic confirmation dialog
 */	
	$this->beginWidget('zii.widgets.jui.CJuiDialog', array(
	    'id'=>'confirmationDialog',
		// additional javascript options for the dialog plugin
	    'options'=>array(
	        'title'=>Yii::t('general', 'Confirmation'),
	        'autoOpen'=>false,
	        'modal'=>true, 
			'resizable'=>false,
			'buttons' =>array (
				"OK"=>'js:function(){}',
				"Cancel"=>"js:function() {
					$(this).dialog( 'close' );
				}" 
			)),
	));	
	echo '<div id="question"></div>';
	$this->endWidget('zii.widgets.jui.CJuiDialog');
	
?>
	<div id='wrap'>
				<div class='logo_inFullMap'></div>										
				<div id='bar'></div>
				<div id='sideBar'>						
					<div id='content'>	
 							<?php 
 										echo CHtml::link('<div id="logo"></div>', '#', array(
    										'onclick'=>'$("#Logo").dialog("open"); return false;', 'class'=>'vtip', 'title'=>'Click here to learn about traceper',
										));									
							?>		
							<?php if (Yii::app()->user->isGuest == true) { ?>									 						
		 						<div id="loginBlock">
		 								<?php 	
											$this->widget('zii.widgets.jui.CJuiButton', array(
															'name'=>'login',
															'caption'=>'Login',
															'id'=>'showLoginWindow',											
														    'onclick'=>'function(){ '.
																			CHtml::ajax(
																						array(
																						'url'=>$this->createUrl('site/login'),
	    																				'complete'=> 'function() { $("#userLoginWindow").dialog("open"); return false;}',
	 																					'update'=> '#userLoginWindow',
																				)).
																		' }',
											));
		 								
											$this->widget('zii.widgets.jui.CJuiButton', array(
															'name'=>'register',
															'caption'=>'Register',
															'id'=>'showRegisterWindow',											
														    'onclick'=>'function(){ '.
																			CHtml::ajax(
																						array(
																						'url'=>$this->createUrl('site/register'),
																						'complete'=> 'function() { $("#registerWindow").dialog("open"); return false;}',
		 																				'update'=> '#registerWindow',
																			)).
																		' }',
													));
									?>		
		 						</div>
	 						<?php }?>
	 						
	 						<div id="userBlock" <?php
	 											$userId = "$('#userId').html()"; 
	 											if (Yii::app()->user->isGuest == true) {	
	 													echo "style='display:none'"; 
	 													}
	 											else {
	 												$userId = Yii::app()->user->id;
	 											}  ?>>
	 							
	 						
								<ul id='userarea'><li id="username" onclick="TRACKER.trackUser(<?php echo $userId; ?>)"><?php if (Yii::app()->user->isGuest == false){ 
																				echo Yii::app()->user->name; 
																			}?>
												 </li>
	 							</ul>							
	 							<div id="userId" style="display:none;"></div>
	 							
	 							<?php 
 									echo CHtml::ajaxLink('<div style="clear:both" id="changePassword" class="userOperations">	
	 													<img src="images/changePassword.png"  /><div></div>
	 												  </div>', $this->createUrl('site/changePassword'), 
 										array(
    										'complete'=> 'function() { $("#changePasswordWindow").dialog("open"); return false;}',
 											'update'=> '#changePasswordWindow',
										),
										array(
											'id'=>'showChangePasswordWindow','class'=>'vtip', 'title'=>'Change Password')); 

 									echo CHtml::ajaxLink('<div class="userOperations" id="inviteUser">
	 													<img src="images/invite.png"  /><div></div>
	 												 </div>', $this->createUrl('site/inviteUsers'), 
 										array(
    										'complete'=> 'function() { $("#inviteUsersWindow").dialog("open"); return false;}',
 											'update'=> '#inviteUsersWindow',
										),
										array(
											'id'=>'showInviteUsersWindow','class'=>'vtip', 'title'=>'Invite Friends'));										
									
									echo CHtml::ajaxLink('<div class="userOperations" id="friendRequests">
	 													<img src="images/friends.png"  /><div></div>
	 												 </div>', $this->createUrl('users/GetFriendRequestList'), 
 										array(
    										'complete'=> 'function() { $("#friendRequestsWindow").dialog("open"); return false;}',
 											'update'=> '#friendRequestsWindow',
										),
										array(
											'id'=>'showFriendRequestsWindow','class'=>'vtip', 'title'=>'Friend Requests'));
									/*	
									echo CHtml::ajaxLink('<div class="userOperations" id="createGroup">
	 													<img src="images/createGroup.png"  /><div></div>
	 												 </div>', $this->createUrl('groups/createGroup'), 
 										array(
    										'complete'=> 'function() { $("#createGroupWindow").dialog("open"); return false;}',
 											'update'=> '#createGroupWindow',
										),
										array(
											'id'=>'showCreateGroupWindow','class'=>'vtip', 'title'=>'Create New Group'));										
									*/
										
									echo CHtml::ajaxLink('<div class="userOperations" id="createGroup">
	 													<img src="images/registerGPSTracker.png"  /><div></div>
	 												 </div>', $this->createUrl('site/registerGPSTracker'), 
 										array(
    										'complete'=> 'function() { $("#registerGPSTrackerWindow").dialog("open"); return false;}',
 											'update'=> '#registerGPSTrackerWindow',
										),
										array(
											'id'=>'showRegisterGPSTrackerWindow','class'=>'vtip', 'title'=>'Register GPS Tracker'));
											
 												
									echo CHtml::link('<div  class="userOperations" id="signout">	 			
	 													<img src="images/signout.png"  /><div></div>		
	 												 </div>', $this->createUrl('site/logout'), array('class'=>'vtip', 'title'=>'Sign Out')); 																			
	 							?>
	 						</div>
	 						
	 						<div id='lists'> 	
								<div class='titles'>											
									<?php										
								    	//if (Yii::app()->user->isGuest == false)
								    	{
											$this->widget('zii.widgets.jui.CJuiTabs', array(
											    'tabs' => array(
													'Users' => array('ajax' => $this->createUrl('users/getFriendList'), 
																	 'id'=>'users_tab'),
											        'Photos' => array('ajax' => $this->createUrl('image/getList'), 
											        				  'id'=>'photos_tab'),
											    ),
											    'id'=>"tab_view",
											    // additional javascript options for the tabs plugin
											    'options' => array(
											        'collapsible' => false,
											    	'cache'=>true,							   
											    ),
											));									    										    		
								    	}    													        
								    ?>
								</div>
							</div>		
						</div> 													
					</div>
																																									
				</div>
				<div id="map" ></div>					
				<div id='infoBottomBar'></div>
				<div id='loading'></div>											
	</div>

	<div id="forgotPasswordForm" class="containerPlus draggable {buttons:'c', skin:'default', icon:'tick_ok.png',width:'300', height:'200', closed:'true' }">
		<div id="emailLabel"></div>
		<div><input type="text" name="email" id="email" /><input type="button" id="sendNewPassword"/></div>
	</div>	
	
	</body>
</html>



