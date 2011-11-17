<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
	<head>
		<title></title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
		<meta name="keywords"  content="" />
		<meta name="description" content="open source GPS tracking system" />
		<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/style.css" media="screen, projection" />
		<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/main.css" />
		<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/form.css" />
		<link rel="shortcut icon" href="<?php echo Yii::app()->request->baseUrl; ?>/images/icon.png" type="image/x-icon"/>
   	 <link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/js/jquery/plugins/mb.containerPlus/css/mbContainer.css" title="style"  media="screen"/>
<!-- 
	<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1/jquery.js"></script>
-->  
	<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/js/jquery/plugins/jquery.cookie.js"></script>
<!-- 
	<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jqueryui/1/jquery-ui.min.js"></script>
-->
     <script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/js/jquery/plugins/mb.containerPlus/inc/jquery.metadata.js"></script> 
  	<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/js/jquery/plugins/mb.containerPlus/inc/mbContainer.js"></script>
  	<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/js/jquery/plugins/rating/jquery.raty.min.js"></script> 
	
  	<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/js/jquery/plugins/superfish/js/superfish.js"></script>
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
				//TODO: setUserId should be a real id
//				trackerOp.setUserId(0);
		  		trackerOp.getFriendList(1);	
		  		trackerOp.getImageList();   				
			}
   			catch (e) {
				
			}
/*			    			
			    $('.containerPlus').buildContainers({
			        containment:'document',
			        elementsPath:'js/jquery/plugins/mb.containerPlus/elements/',
			        onClose:function(o){},
			        onIconize:function(o){},
			        effectDuration:10,
			        zIndexContext:'auto' 
      			});
 */
  //    			setLanguage(langOp);
  //    			bindElements(langOp, trackerOp);			    
  //    			$('#user_title').click();",
		CClientScript::POS_READY);
		
		 if (Yii::app()->user->isGuest == false){
		 		Yii::app()->clientScript->registerScript('getFirstFriendListPage',
		 												CHtml::ajax(array(
																			'url'=>$this->createUrl('users/getFriendList'),
																			'update'=>'#friends',
																	)),
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
///////////////////////////// Change Password Window ///////////////////////////
	echo '<div id="changePasswordWindow"></div>';
///////////////////////////// Invite User Window ///////////////////////////
	echo '<div id="inviteUsersWindow"></div>';
///////////////////////////// Friend Request Window ///////////////////////////	
	echo '<div id="friendRequestsWindow"></div>';
/*	
	$this->beginWidget('zii.widgets.jui.CJuiDialog', array(
	    'id'=>'friendRequestsWindow',
	    // additional javascript options for the dialog plugin
	    'options'=>array(
	        'title'=>Yii::t('general', 'Friend Requests'),
	        'autoOpen'=>false,
	        'modal'=>true, 
			'resizable'=>false,
			'width'=> '400px',
			'height'=> '550'    
	    ),
	));
	
	echo '<div id="friendRequestsList" class="">  
	</div>';	
			
	$this->endWidget('zii.widgets.jui.CJuiDialog');	
*/
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

?>
	
	
	<div id='wrap'>
				<div class='logo_inFullMap'></div>										
				<div id='bar'></div>
				<div id='sideBar'>						
					<div id='content'>	
 							<?php 
 										echo CHtml::link('<div id="logo"></div>', '#', array(
    										'onclick'=>'$("#Logo").dialog("open"); return false;',
										));									
							?>		
							<?php if (Yii::app()->user->isGuest == true) { ?>									 						
		 						<div id="loginBlock">
		 								
		 								<?php 				
												echo CHtml::ajaxLink(Yii::t('general', 'Login'), $this->createUrl('site/login'), 
	 												array(
	    												'complete'=> 'function() { $("#userLoginWindow").dialog("open"); return false;}',
	 													'update'=> '#userLoginWindow',
													),
													array(
														'id'=>'showLoginWindow')); 
												
												echo ' '; //To separate Login and Register
												
												echo CHtml::ajaxLink(Yii::t('general', 'Register'), $this->createUrl('site/register'), 
	 												array(
	    												'complete'=> 'function() { $("#registerWindow").dialog("open"); return false;}',
	 													'update'=> '#registerWindow',
													),
													array(
														'id'=>'showRegisterWindow'));																							
									?>		
		 						</div>
	 						<?php }?>
	 						
	 						<div id="userBlock" <?php if (Yii::app()->user->isGuest == true) {	echo "style='display:none'"; }  ?>>
	 						
								<ul id='userarea'><li id="username"><?php if (Yii::app()->user->isGuest == false){ 
																				echo Yii::app()->user->name; 
																			}?>
												 </li>
	 							</ul>							
	 							
	 							<?php 
 									echo CHtml::ajaxLink('<div style="clear:both" id="changePassword" class="userOperations">	
	 													<img src="images/changePassword.png"  /><div></div>
	 												  </div>', $this->createUrl('site/changePassword'), 
 										array(
    										'complete'=> 'function() { $("#changePasswordWindow").dialog("open"); return false;}',
 											'update'=> '#changePasswordWindow',
										),
										array(
											'id'=>'showChangePasswordWindow')); 

 									echo CHtml::ajaxLink('<div class="userOperations" id="inviteUser">
	 													<img src="images/invite.png"  /><div></div>
	 												 </div>', $this->createUrl('site/inviteUsers'), 
 										array(
    										'complete'=> 'function() { $("#inviteUsersWindow").dialog("open"); return false;}',
 											'update'=> '#inviteUsersWindow',
										),
										array(
											'id'=>'showInviteUsersWindow'));										
																			

// 									echo CHtml::link('<div class="userOperations" id="inviteUser">
//	 													<img src="images/invite.png"  /><div></div>
//	 												 </div>', '#', array(
//    												'onclick'=>'$("#inviteUserWindow").dialog("open"); return false;',
//									));	
									
									echo CHtml::ajaxLink('<div class="userOperations" id="friendRequests">
	 													<img src="images/friends.png"  /><div></div>
	 												 </div>', $this->createUrl('users/GetFriendRequestList'), 
 										array(
    										'complete'=> 'function() { $("#friendRequestsWindow").dialog("open"); return false;}',
 											'update'=> '#friendRequestsWindow',
										),
										array(
											'id'=>'showFriendRequestsWindow'));
									
									
// 									echo CHtml::link('<div class="userOperations" id="friendRequests">	
//	 													<img src="images/friends.png"  /><div></div>
//	 												 </div>', '#', array(
//   												'onclick'=>'$("#friendRequestsWindow").dialog("open"); return false;',
//									));	

 									echo CHtml::link('<div  class="userOperations" id="signout">	 			
	 													<img src="images/signout.png"  /><div></div>		
	 												 </div>', $this->createUrl('site/logout'), array()); 																			
	 							?>
	 						</div>
	 						
	 						<div id='lists'> 	
								<div class='titles'>											
									<?php
						 			
//										echo CHtml::ajaxLink('<div>Users</div>', $this->createUrl('users/getFriendList'), 
// 														array(
//				 											'update'=> '#friends',
//				 											'complete'=>'function(result){ $("#friendsList").show();$("#photosList").hide(); return false;}',
//														), 
//														array(
//															'id'=>'kk',
//														)); 
									?>								
				
						<!-- 			
									<div class="title" id="photo_title"><div class="arrowImage"></div></div>
						 -->
						 
						 			<?php
//									echo CHtml::ajaxLink('<div  id="image_title">Photos</div>', $this->createUrl('image/getList'), 
// 														array(
//				 											'update'=> '#photos',
//				 											'complete'=>'function(result){ $("#friendsList").hide();$("#photosList").show(); return false;}',
//														), 
//														array(
//															'id'=>'imageTitle',
//														)); 
									?>	
									
									<?php										
								        
										$this->widget('zii.widgets.jui.CJuiTabs', array(
										    'tabs' => array(
												'Users' => array('ajax' => $this->createUrl('users/getFriendList')),
										        'Photos' => array('ajax' => $this->createUrl('image/getList')),
										    ),
										    // additional javascript options for the tabs plugin
										    'options' => array(
										        'collapsible' => true,
										    ),
										));								        
							        
								    ?>
               																																		
								</div>  
								<div id='friendsList'>
									<div id="userSearch">
											<?php $this->renderPartial('//users/searchUser', array('model'=>new SearchForm())); ?>
									</div>
							<!-- 												
									<div class='search'>						
										<input type='text' id='searchBox' value='' /><img src='images/search.png' id='searchButton'  />
									</div>
							 -->
									<div id="friends"></div>
							<!--  
									<div class='searchResults'>
										<a href='#returnToUserList'></a>	
										<div id='results'></div>								
									</div>
							-->				
								</div> 
								<div id="photosList">	
									<div id="imageSearch">
											<?php $this->renderPartial('//image/search', array('model'=>new SearchForm())); ?>
									</div>		
							<!-- 															
									<div class='search'>
										<input type='text' id='searchBox' value='' /><img src='images/search.png' id='searchButton'  />
									</div>
							
									<input type='checkbox' id='showPhotosOnMap'> Show photos on map
							 -->		<div id="photos">
										</div>
							<!--  			
									<div class='searchResults'>
										<a href='#returnToPhotoList' id="returnToPhotoList"></a>	
										<div id='results'></div>								
									</div>
							-->
								</div>		
							</div> 													
					</div>
																																									
				</div>
	
				<div id="map"></div>					
				<div id='infoBottomBar'></div>
				<div id='loading'></div>											
	</div>

	<div id="forgotPasswordForm" class="containerPlus draggable {buttons:'c', skin:'default', icon:'tick_ok.png',width:'300', height:'200', closed:'true' }">
		<div id="emailLabel"></div>
		<div><input type="text" name="email" id="email" /><input type="button" id="sendNewPassword"/></div>
	</div>
		
	<div id='message_warning' class="containerPlus draggable {buttons:'c', skin:'default', icon:'alert.png',width:'400', closed:'true' }">
	</div>
	<div id='message_info' class="containerPlus draggable {buttons:'c', skin:'default', icon:'tick_ok.png',width:'400', closed:'true' }">
	</div>
	
	</body>
</html>



