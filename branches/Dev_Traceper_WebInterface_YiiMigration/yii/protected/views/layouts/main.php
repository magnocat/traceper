<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
	<head>
		<title></title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
		<meta name="keywords"  content="" />
		<meta name="description" content="open source GPS tracking system" />
		<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/style.css" media="screen, projection" />

		<link rel="shortcut icon" href="<?php echo Yii::app()->request->baseUrl; ?>/images/icon.png" type="image/x-icon"/>
   	 <link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/js/jquery/plugins/mb.containerPlus/css/mbContainer.css" title="style"  media="screen"/>
  
	<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1/jquery.js"></script>
	<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/js/jquery/plugins/jquery.cookie.js"></script>
	<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jqueryui/1/jquery-ui.min.js"></script>
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

	<script type="text/javascript">		
		var langOp = new LanguageOperator();
		var fetchPhotosDefaultValue =  1;  //TODO: $fetchPhotosInInitialization;
		langOp.load("en");  //TODO: itshould be parametric
		
		var mapOperator = new MapOperator();
		
		$(document).ready( function(){
							
			var checked = false;
			// showPhotosOnMapCookieId defined in bindings.js
			if ($.cookie && $.cookie(showPhotosOnMapCookieId) != null){
				if ($.cookie(showPhotosOnMapCookieId) == "true"){
					checked = true;
				}				
			}
			else if (fetchPhotosDefaultValue == 1){
				checked = true;
			}
			$('#showPhotosOnMap').attr('checked', checked);
			
			
			try 
			{
				
				var mapStruct = new MapStruct();
			    var initialLoc = new MapStruct.Location({latitude:39.504041,
			    								  longitude:35.024414}); 
				mapOperator.initialize(initialLoc);
				//TODO: ../index.php should be changed 
				//TODO: $updateUserListInterval 
				//TODO: $queryIntervalForChangedUsers 
   				var trackerOp = new TrackerOperator('../index.php', mapOperator, fetchPhotosDefaultValue, 5000, 30000)	   	
				trackerOp.setLangOperator(langOp),
				//TODO: setUserId should be a real id
				trackerOp.setUserId(0);
		  		trackerOp.getFriendList(1);	   				
			}
   			catch (e) {
				
			}    			
			    $(".containerPlus").buildContainers({
			        containment:"document",
			        elementsPath:"js/jquery/plugins/mb.containerPlus/elements/",
			        onClose:function(o){},
			        onIconize:function(o){},
			        effectDuration:10,
			        zIndexContext:"auto" 
      			});
      			setLanguage(langOp);
      			bindElements(langOp, trackerOp);			    
      			$('#user_title').click();
      			     		
		});	
	</script>
   
	</head>
	<body>	
	
	<div id='wrap'>
				<div class='logo_inFullMap'></div>										
				<div id='bar'></div>
				<div id='sideBar'>						
					<div id='content'>						
	 						<div id='logo'></div>
	 						<div id="loginBlock">
	 								<div style="clear:both" id="loginLink" class="userOperations"></div>
	 								<div class="userOperations" id="registerLink"></div>
	 						</div>
	 						<div id="userBlock" style="display:none">
								<ul id='userarea'><li id="username"><!--  $realname --></li>
	 							</ul>
	 							<div style="clear:both" id="changePassword" class="userOperations">	
	 								<img src='images/changePassword.png'  /><div></div>
	 							</div>
	 							<div class="userOperations" id="inviteUser">
	 								<img src='images/invite.png'  /><div></div>
	 							</div>
	 							<div class="userOperations" id="friendRequests">	
	 								<img src='images/friends.png'  /><div></div>
	 							</div>
	 							<div  class="userOperations" id="signout">	 			
	 								<img src='images/signout.png'  /><div></div>		
	 							</div>
	 						</div>
	 						<div id='lists'>	
								<div class='titles'>									
									<div class='title active_title' id='user_title'><div class='arrowImage'></div></div>
									<div class='title' id='photo_title'><div class='arrowImage'></div></div>	
									<!-- <div class='title' id='friendRequest_title'><div class='arrowImage'>Friend Requests</div></div>-->							
								</div>
								<div id='friendsList'>											
									<div class='search'>						
										<input type='text' id='searchBox' value='' /><img src='images/search.png' id='searchButton'  />
									</div>
									<div id="friends"></div>
									<div class='searchResults'>
										<a href='#returnToUserList'></a>	
										<div id='results'></div>								
									</div>		
								</div>
								<div id="photosList">									
									<div class='search'>
										<input type='text' id='searchBox' value='' /><img src='images/search.png' id='searchButton'  />
									</div>
									<input type='checkbox' id='showPhotosOnMap'> Show photos on map
									<div id="photos">
										<a href='#displayComments', id="commentsWindow"></a>
										</div>
									<div class='searchResults'>
										<a href='#returnToPhotoList' id="returnToPhotoList"></a>	
										<div id='results'></div>								
									</div>
								</div>				
							</div> 													
					</div>
																																									
				</div>
				
				<div id="map"></div>					
				<div id='infoBottomBar'></div>
				<div id='loading'></div>											
	</div>
  	
	<div id='aboutus' class="containerPlus draggable {buttons:'c',icon:'browser.png', skin:'default', width:'600', closed:'true'}">  
	<div class="logo"></div></div>
	<div id='changePasswordForm' class="containerPlus draggable {buttons:'c', icon:'changePass.png' ,skin:'default', width:'250', height:'225', title:'<div id=\'changePasswordFormTitle\'></div>', closed:'true' }">  
					<br/>
		<div id="currentPasswordLabel"></div>
		<div><input type='password' name='currentPassword' id='currentPassword' /></div>
		<div id="newPasswordLabel"></div>
		<div><input type='password' name='newPassword' id='newPassword' /></div>  
		<div id="newPasswordAgainLabel"></div>
		<div><input type='password' name='newPasswordAgain' id='newPasswordAgain' /></div>
		<div></div>
		<div><input type='button' name='changePassword' id='changePasswordButton' /> &nbsp; <input type='button' name='cancel' id='changePasswordCancel'/></div>
	</div>
	
	<div id='friendRequestsList' class="containerPlus draggable {buttons:'c', icon:'friends.png' ,skin:'default', width:'400', height:'550', title:'<div id=\'friendRequestsListTitle\'></div>', closed:'true' }">  
	</div>
	
	<div id='InviteUserForm' class="containerPlus draggable {buttons:'c', skin:'default', width:'350', height:'350', title:'<div id=\'inviteUserFormTitle\'></div>',  closed:'true'}">  
		<div id="inviteUserEmailLabel"></div> 
		<textarea name='useremail' id='useremail' style="width:300px; height:100px"></textarea><br/>		
		<div id="inviteUserInvitationMessage"></div>
		
		<textarea name='invitationMessage' id='invitationMessage' style="width:300px; height:100px"></textarea><br/>
		
		<input type='button' name='inviteUserButton' id='inviteUserButton'/>&nbsp; <input type='button' name='cancel' id='inviteUserCancel'/></div>
	</div>	
	<div id="userLoginForm" class="containerPlus draggable {buttons:'c', skin:'default', icon:'tick_ok.png',width:'250', height:'250', closed:'true' }">	
			<div id="usernameLabel"></div>
			<input type="text" name="email" id="emailLogin" />
			<div id="passwordLabel"></div>
			<input type="password" name="password" id="password"/>
			<div class="link" id="forgotPasswordLink"></div>
			<input type="checkbox" name="rememberMe" id="rememberMe"/>
			<div style="display:inline" class="link" id="rememberMeLabel"></div><br/>
		    <input type="button" id="submitLoginFormButton" value=""/> <br/>
	</div>
	<div id="forgotPasswordForm" class="containerPlus draggable {buttons:'c', skin:'default', icon:'tick_ok.png',width:'300', height:'200', closed:'true' }">
		<div id="emailLabel"></div>
		<div><input type="text" name="email" id="email" /><input type="button" id="sendNewPassword"/></div>
	</div>
	
	<div id="registerForm" class="containerPlus draggable {buttons:'c', skin:'default', icon:'tick_ok.png',width:'250', closed:'true' }">		
		<div id="registerEmailLabel">E-mail:</div><input class="registerFormText" type="text" id="registerEmail" /><br />
		<div id="registerNameLabel">Name:</div><input class="registerFormText" type="text" id="registerName" /><br />
		<div id="registerPasswordLabel">Password:</div><input class="registerFormText" type="password" id="registerPassword" /><br />
		<div id="registerConfirmPasswordLabel">Password Again:</div><input class="registerFormText" type="password" id="registerConfirmPassword" /><br />
		<input type="button" id="registerButton" value="Register" />
	</div>
	
	<div id='message_warning' class="containerPlus draggable {buttons:'c', skin:'default', icon:'alert.png',width:'400', closed:'true' }">
	</div>
	<div id='message_info' class="containerPlus draggable {buttons:'c', skin:'default', icon:'tick_ok.png',width:'400', closed:'true' }">
	</div>

	<div id="photoCommentForm" class="containerPlus draggable {buttons:'c', skin:'default', icon:'tick_ok.png', width:'400', height:'400', title:'Comment Window', closed:'true' }">
		<div id="photoCommentLabel">Comment:</div>
		<textarea id="photoCommentTextBox" cols="40" rows="7">Enter your comments here...	</textarea><br/>
		<input type="button" id="sendCommentButton" value="Upload Comment" /><br/>
		<input type="button" id="deleteCommentButton" value="Delete Comment" />	 
	</div>
	
	</body>
</html>



