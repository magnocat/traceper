<?php

class DisplayOperator
{	
	private static function getMetaNLinkSection(){
		
		$str = <<<EOT
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
		<meta name="keywords"  content="" />
		<meta name="description" content="open source GPS tracking system" />
		<link rel="stylesheet" type="text/css" href="style.css" />
		<link rel="shortcut icon" href="images/icon.png" type="image/x-icon"/>
EOT;
		
		return $str;		
	}
	
	public static function getActivateAccountPage($callbackURL, $language, $key, $email){
	    $head = self::getMetaNLinkSection();
		$str = <<<EOT
		<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
			<html>
			<head>
				<title></title>
				$head	
				<script type="text/javascript" src="js/jquery/jquery.min.js"></script>
				<script type="text/javascript" src="js/TrackerOperator.js"></script>
				
				<script type="text/javascript" src="js/LanguageOperator.js"></script>
				<script>	
				var langOp = new LanguageOperator();
				langOp.load("$language"); 	
				$(document).ready( function(){
					var trackerOp = new TrackerOperator("$callbackURL");	
					trackerOp.langOperator = langOp;
					trackerOp.activateAccount("$key", "$email");			
				});						
				</script>
			</head>
			<body>
				<div id='loginLogo' ></div>
				<div id="activateAccountInfo">									
				</div>
				
			</body>
			</html>				
EOT;
		
		return $str;		
		
	
	}
	
	public static function getLoginPage($page, $callbackURL, $language, $pluginScript){
		$head = self::getMetaNLinkSection();
		
//		$facebookPluginLoginExt = ROOT_DIRECTORY."/plugins/FacebookConnect/login.php";
//		if (file_exists($facebookPluginLoginExt)) {
//			$extension = require($facebookPluginLoginExt);
//		}
		$str = <<<EOT
		<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
			<html xmlns="http://www.w3.org/1999/xhtml"
      			  xmlns:fb="http://www.facebook.com/2008/fbml">
			<head>
				<title></title>
				$head	
 			    <link rel="stylesheet" type="text/css" href="js/jquery/plugins/mb.containerPlus/css/mbContainer.css" title="style"  media="screen"/>
 
				<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1/jquery.js"></script>
				<script type="text/javascript" src="js/jquery/plugins/jquery.cookie.js"></script>
				<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jqueryui/1/jquery-ui.min.js"></script>
    			<script type="text/javascript" src="js/jquery/plugins/mb.containerPlus/inc/jquery.metadata.js"></script> 
  				<script type="text/javascript" src="js/jquery/plugins/mb.containerPlus/inc/mbContainer.js"></script> 
				
				<script type="text/javascript" src="js/TrackerOperator.js"></script>
				
				<script type="text/javascript" src="js/LanguageOperator.js"></script>
				<script>	
				var langOp = new LanguageOperator();
				langOp.load("$language"); 	
				$(document).ready( function(){				    
					var trackerOp = new TrackerOperator("$callbackURL");	
					trackerOp.langOperator = langOp;
					$('#usernameLabel').text(langOp.emailLabel+":");	
					$('#passwordLabel').text(langOp.passwordLabel+":");
					$('#rememberMeLabel').text(langOp.rememberMeLabel).click(function(){
						$('#rememberMe').attr('checked', !($('#rememberMe').attr('checked')));
							
					});
					$('#forgotPasswordLink').text(langOp.forgotPassword);
					$('#sendNewPassword').attr('value', langOp.sendNewPassword);	
					$('#registerLink').text(langOp.registerLabel);	
					$('#emailLabel').text(langOp.emailLabel + ":");	
					$("#aboutus").append(langOp.aboutus);
					$("#submitLoginFormButton").val(langOp.submitFormButtonLabel);	
					$('#aboutusLink').text(langOp.aboutTitle);				

					$('#email').keypress(function(event){
						if (event.keyCode == '13'){
							sendNewPassword();	
						}
					});
					$('#username , #password').keypress(function(event){
						if (event.keyCode == '13'){
							authenticateUser();
						}						
					});		
					$('#submitLoginFormButton').click(function(){
						authenticateUser();
					});
					$('#forgotPasswordLink').click(function(){
						$('#forgotPasswordForm').mb_open();
						$('#forgotPasswordForm').mb_centerOnWindow(true);

						$('#sendNewPassword').click(function(){
						    TRACKER.sendNewPassword($('#email').val(),
						   		function(result){
	                                $('#forgotPasswordForm input[type!=button]').attr('value', '');
									$('#forgotPasswordForm').mb_close();
								});
						});
					});
					$('#registerLink').click(function(){
						$('#registerForm').mb_open();
						$('#registerForm').mb_centerOnWindow(true);
						
						$('#registerButton').click(function(){
							TRACKER.registerUser($('#registerEmail').val(), $('#registerName').val(), $('#registerPassword').val(), $('#registerConfirmPassword').val(),null, 
								function(result){
	                                $('#registerForm input[type!=button]').attr('value', '');
									$('#registerForm').mb_close();
								});						
						});	
					});	

					$('#aboutusLink').click(function(){
						$('#aboutus').mb_open();
						$('#aboutus').mb_centerOnWindow(true);
					});	

					$(".containerPlus").buildContainers({
			        	containment:"document",
			        	elementsPath:"js/jquery/plugins/mb.containerPlus/elements/",
			        	onClose:function(o){},
			        	onIconize:function(o){},
			        	effectDuration:10,
			        	zIndexContext:"auto" 
      				});		
				
				});	
				function authenticateUser(){
					TRACKER.authenticateUser($('#emailLogin').val(), $('#password').val(), $('#rememberMe').attr('checked'), function(){ $('#password').val(""); });
				}
				</script>
			</head>
			<body>
				$pluginScript
				<div align="center" style="margin-top:60px"><img src="images/logo.png" style="display:block"/>	
						
				</div>
				<br/>
				
				<br/> 
				<div align="center" class="link" id='aboutusLink'></div>					
					
				<div id='aboutus' class="containerPlus draggable {buttons:'c', skin:'default', icon:'browser.png', width:'600', closed:'true' }">  
				<div class="logo"></div></div>
							
				<div id='message_warning' class="containerPlus draggable {buttons:'c', skin:'default', icon:'alert.png',width:'600', closed:'true' }">
				</div>
				<div id='message_info' class="containerPlus draggable {buttons:'c', skin:'default', icon:'tick_ok.png',width:'600', closed:'true' }">
				</div>
				
				
				
			</body>
			</html>				
EOT;
		
		return $str;		
	}
	
	
	public static function getRegistrationPage($email, $invitationKey, $callbackURL)
	{
		$out = <<<EOT
		<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
			<html xmlns="http://www.w3.org/1999/xhtml"
      			  xmlns:fb="http://www.facebook.com/2008/fbml">
			<head>
				<title></title>
				  <link rel="stylesheet" type="text/css" href="js/jquery/plugins/mb.containerPlus/css/mbContainer.css" title="style"  media="screen"/>
 
				<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1/jquery.js"></script>
				<script type="text/javascript" src="js/jquery/plugins/jquery.cookie.js"></script>
				<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jqueryui/1/jquery-ui.min.js"></script>
    			<script type="text/javascript" src="js/jquery/plugins/mb.containerPlus/inc/jquery.metadata.js"></script> 
  				<script type="text/javascript" src="js/jquery/plugins/mb.containerPlus/inc/mbContainer.js"></script> 
	
				
<!--				<script type="text/javascript" src="js/jquery/jquery.min.js"></script>
-->				<script type="text/javascript" src="js/TrackerOperator.js"></script>
				
				<script type="text/javascript" src="js/LanguageOperator.js"></script>
				<script>	
				var langOp = new LanguageOperator();
				langOp.load("en"); 	
				$(document).ready( function(){				    
					var trackerOp = new TrackerOperator("$callbackURL");	
					trackerOp.langOperator = langOp;
					$('#registerButton').click(function(){
						TRACKER.registerUser($('#registerEmail').val(), $('#registerName').val(), $('#registerPassword').val(), $('#registerConfirmPassword').val(), "$invitationKey");
					});			
					
					$(".containerPlus").buildContainers({
			        	containment:"document",
			        	elementsPath:"js/jquery/plugins/mb.containerPlus/elements/",
			        	onClose:function(o){},
			        	onIconize:function(o){},
			        	effectDuration:10,
			        	zIndexContext:"auto" 
      				});					
				});	
				</script>
			</head>
			<body>
			
				<div id='message_warning' class="containerPlus draggable {buttons:'c', skin:'default', icon:'alert.png',width:'600', closed:'true' }">
				</div>
				<div id='message_info' class="containerPlus draggable {buttons:'c', skin:'default', icon:'tick_ok.png',width:'600', closed:'true' }">
				</div>
				
			</body>
			</html>
EOT;
		return $out;
		
	}
	
	public static function getMainPage($callbackURL, $userInfo, $fetchPhotosInInitialization, $updateUserListInterval, $queryIntervalForChangedUsers, $apiKey, $language, $pluginScript) {

		$head = self::getMetaNLinkSection();
		$realname = "";
		$userId = "";	
		$latitude = "";
		$longitude = "";
		$time = "";
		$deviceId = "";	
		$userArea = "";
		$forms = "";
		$hideUserArea = "";
		if ($userInfo != null) 
		{
			$realname = $userInfo->realname;
			$userId = $userInfo->Id;	
			$latitude = $userInfo->latitude;
			$longitude = $userInfo->longitude;
			$time = $userInfo->time;
			$deviceId = $userInfo->deviceId;	
		}
		else {
			// langOp is initialized before document.ready function in str variable 
			$userArea = <<<USER_AREA
							<div id="loginBlock">
	 						<div style="clear:both" id="loginLink" class="userOperations"></div>
	 						<div class="userOperations" id="registerLink"></div>
	 						</div>
USER_AREA;
			$hideUserArea .= "display:none";
		}
		
		$userArea .= <<<USER_AREA
						<div id="userBlock" style="$hideUserArea">
						<ul id='userarea'><li id="username">$realname</li>
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
USER_AREA;
		
		
		
		
		$str = <<<MAIN_PAGE
		<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
	<head>
		<title></title>
		  $head		
   	 <link rel="stylesheet" type="text/css" href="js/jquery/plugins/mb.containerPlus/css/mbContainer.css" title="style"  media="screen"/>
  
	<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1/jquery.js"></script>
	<script type="text/javascript" src="js/jquery/plugins/jquery.cookie.js"></script>
	<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jqueryui/1/jquery-ui.min.js"></script>
    <script type="text/javascript" src="js/jquery/plugins/mb.containerPlus/inc/jquery.metadata.js"></script> 
  	<script type="text/javascript" src="js/jquery/plugins/mb.containerPlus/inc/mbContainer.js"></script>
  	<script type="text/javascript" src="js/jquery/plugins/rating/jquery.raty.min.js"></script> 
	
  	<script type="text/javascript" src="js/jquery/plugins/superfish/js/superfish.js"></script>
	<script type="text/javascript" src="js/DataOperations.js"></script>

	<script type="text/javascript" src="js/maps/MapStructs.js"></script>	
	<script type="text/javascript" src="js/maps/GMapOperator.js"></script>
	<script type="text/javascript" src="js/TrackerOperator.js"></script>
	<script type="text/javascript" src="js/LanguageOperator.js"></script>		
	<script type="text/javascript" src="js/bindings.js"></script>	

	<script type="text/javascript">		
		var langOp = new LanguageOperator();
		var fetchPhotosDefaultValue =  $fetchPhotosInInitialization;
		langOp.load("$language");
		
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
   				var trackerOp = new TrackerOperator('$callbackURL', mapOperator, $fetchPhotosInInitialization, $updateUserListInterval, $queryIntervalForChangedUsers)	   	
				trackerOp.setLangOperator(langOp),
				trackerOp.setUserId($userId);
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
	$pluginScript
	<div id='wrap'>
				<div class='logo_inFullMap'></div>										
				<div id='bar'></div>
				<div id='sideBar'>						
					<div id='content'>						
	 						<div id='logo'></div>
	 						$userArea
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

	<div id="photoCommentForm" class="containerPlus draggable {buttons:'c', skin:'default', icon:'photo_container.png', width:'400', height:'600',dock:'right', title:'Comment Window', closed:'true' }" style="top:1px; right:1px">
		<div id="photoComments"></div>
		<div id="photoCommentForm">	
			<textarea id="photoCommentTextBox" cols="40" rows="7">Enter your comments here...</textarea><br/>
			<input type="button" id="sendCommentButton" value="Send Comment"/><br/>
		</div> 
	</div>
	
	</body>
</html>
MAIN_PAGE;

		  return $str;
}	
	public static function showErrorMessage($message) {
		return $message;
	}

}