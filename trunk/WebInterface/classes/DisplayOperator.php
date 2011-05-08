<?php

class DisplayOperator
{
	private static $realname;
	private static $userId;
	
	public static function setUsernameAndId($name, $Id){
		self::$realname = $name;
		self::$userId = $Id;
	}
	
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
					$('#forgotPassword').text(langOp.forgotPassword);
					$('#submitLoginFormButton').attr('value', langOp.login);	
					$('#emailLabel').text(langOp.emailLabel + ":");	
					$("#aboutus").html(langOp.aboutus);
									
					$('#showLoginFormButton').attr('value', langOp.showLoginForm);
					$('#sendNewPassword').attr('value', langOp.sendNewPassword).click(function(){
						sendNewPassword();
					});
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
					$('#forgotPassword, #showLoginFormButton').click(function(){
						$('#userLoginForm').toggle();
						$('#forgotPasswordForm').toggle();
					});
					$('#register, #cancelRegistration').click(function(){
						$('#userLoginForm').toggle();
						$('#registerForm').toggle();
					});		
					$('#registerButton').click(function(){
						TRACKER.registerUser($('#registerEmail').val(), $('#registerName').val(), $('#registerPassword').val(), $('#registerConfirmPassword').val());
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
				function sendNewPassword(){
					TRACKER.sendNewPassword($('#email').val());
				}
				function authenticateUser(){
					TRACKER.authenticateUser($('#emailLogin').val(), $('#password').val(), $('#rememberMe').attr('checked'), function(){ $('#password').val(""); });
				}
				</script>
			</head>
			<body>
				$pluginScript
				<div style="padding:20px"><img src="images/logo.png" style="display:block; float:left; width:300px; margin-right:200px"/>	
				<div id="userLoginForm">	
					<div>								
						<font id="usernameLabel"></font><br/>
						<input type="text" name="email" id="emailLogin" /><br/>
						<input type="checkbox" name="rememberMe" id="rememberMe"/>
						<div style="display:inline" id="rememberMeLabel"></div>
					</div>
					<div>
						<font id="passwordLabel"></font><br/>
						<input type="password" name="password" id="password" /><br/>
						<font id="forgotPassword"></font>	
					</div>
					<div><br/><input type="button" id="submitLoginFormButton" value=""/> 
					     <br/><font id="register" style="display:block">Register</font>
					</div>
				</div>					
				</div>
				
				<div id='aboutus' class="loginPageBlock">  
				</div>
							
				<div id='message_warning' class="containerPlus draggable {buttons:'c', skin:'default', icon:'alert.png',width:'600', closed:'true' }">
				</div>
				<div id='message_info' class="containerPlus draggable {buttons:'c', skin:'default', icon:'tick_ok.png',width:'600', closed:'true' }">
				</div>
				
				<div id="forgotPasswordForm" style="display:none">
					<div id="emailLabel"></div>
					<div><input type="text" name="email" id="email" /><input type="button" id="sendNewPassword"/></div>
					<div><input type="button" name="showLoginFormButton" id="showLoginFormButton" /></div>
				</div>
				<div id="registerForm" style="display:none">		
					<div id="registerEmailLabel">E-mail:</div><input type="text" id="registerEmail" /><br />
					<div id="registerNameLabel">Name:</div><input type="text" id="registerName" /><br />
					<div id="registerPasswordLabel">Password:</div><input type="password" id="registerPassword" /><br />
					<div id="registerConfirmPasswordLabel">Password Again:</div><input type="password" id="registerConfirmPassword" /><br />
					<input type="button" id="registerButton" value="Register" />
					<input type="button" id="cancelRegistration" value="Cancel" />
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
				
				<script type="text/javascript" src="js/jquery/jquery.min.js"></script>
				<script type="text/javascript" src="js/TrackerOperator.js"></script>
				
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
				
				});	
				</script>
			</head>
			<body>
			<div id="registerForm">		
					<div id="registerEmailLabel">E-mail:</div><input type="text" id="registerEmail" /><br />
					<div id="registerNameLabel">Name:</div><input type="text" id="registerName" /><br />
					<div id="registerPasswordLabel">Password:</div><input type="password" id="registerPassword" /><br />
					<div id="registerConfirmPasswordLabel">Password Again:</div><input type="password" id="registerConfirmPassword" /><br />
					<input type="button" id="registerButton" value="Register" />
					<input type="button" id="cancelRegistration" value="Cancel" />
				</div>
			</body>
			</html>
EOT;
		return $out;
		
	}
	
	public static function getMainPage($callbackURL, $userInfo, $fetchPhotosInInitialization, $updateUserListInterval, $queryIntervalForChangedUsers, $apiKey, $language, $pluginScript) {

		$head = self::getMetaNLinkSection();
		$realname = self::$realname;
		$userId = self::$userId;	
		$latitude = $userInfo->latitude;
		$longitude = $userInfo->longitude;
		$time = $userInfo->time;
		$deviceId = $userInfo->deviceId;	
		
		$str = <<<MAIN_PAGE
		<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
	<head>
		<title></title>
		  $head		
     <script type="text/javascript" src="http://www.google.com/jsapi?key=$apiKey">
 	 </script>
    
      <script type="text/javascript" charset="utf-8">
   
        google.load("maps", "2.x",{"other_params":"sensor=true"});
   
      </script>
      	  
   <link type="text/css" href="js/jquery/plugins/superfish/css/superfish.css" rel="stylesheet" media="screen"/>
	 <link rel="stylesheet" type="text/css" href="js/jquery/plugins/mb.containerPlus/css/mbContainer.css" title="style"  media="screen"/>
  
<!--	
	<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1/jquery.js"></script>
-->
<!--	<script type="text/javascript" src="js/jquery/jquery.min.js"></script> -->
	<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1/jquery.js"></script>
	<script type="text/javascript" src="js/jquery/plugins/jquery.cookie.js"></script>
	<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jqueryui/1/jquery-ui.min.js"></script>
    <script type="text/javascript" src="js/jquery/plugins/mb.containerPlus/inc/jquery.metadata.js"></script> 
  	<script type="text/javascript" src="js/jquery/plugins/mb.containerPlus/inc/mbContainer.js"></script> 
	
  	<script type="text/javascript" src="js/jquery/plugins/superfish/js/superfish.js"></script>
	<script type="text/javascript" src="js/DataOperations.js"></script>
			
	<script type="text/javascript" src="js/TrackerOperator.js"></script>
	<script type="text/javascript" src="js/LanguageOperator.js"></script>		
	<script type="text/javascript" src="js/bindings.js"></script>	
	<script type="text/javascript">		
		var langOp = new LanguageOperator();
		var fetchPhotosDefaultValue =  $fetchPhotosInInitialization;
		langOp.load("$language"); 	
				
		$(document).ready( function(){			
			setLanguage(langOp);	
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
			
			var map;
			try 
			{
				if (GBrowserIsCompatible()) 
				{
   					map = new GMap2(document.getElementById("map"));
   					map.setCenter(new GLatLng(39.504041,35.024414), 3);
					map.setUIToDefault();					
					map.setMapType(G_HYBRID_MAP);	
					map.enableRotation();
	   	
   					var trackerOp = new TrackerOperator('$callbackURL', map, $fetchPhotosInInitialization, $updateUserListInterval, $queryIntervalForChangedUsers, langOp, $userId);			
   					
   					var personIcon = new GIcon(G_DEFAULT_ICON);
					personIcon.image = "images/person.png";
					personIcon.iconSize = new GSize(24,24);
					personIcon.shadow = null;
					markerOptions = { icon:personIcon };
	   				
					var point = new GLatLng($latitude, $longitude);
   					TRACKER.users[$userId] = new TRACKER.User( {//username:username,
										   realname:'$realname',
										   latitude:$latitude,
										   longitude:$longitude,
										   time:'$time',
										   message:'',
										   deviceId:'$deviceId',
										   gmarker:new GMarker(point, markerOptions),														   
										});
					GEvent.addListener(TRACKER.users[$userId].gmarker, "click", function() {
  						TRACKER.openMarkerInfoWindow($userId);	
  					});
  				
					GEvent.addListener(TRACKER.users[$userId].gmarker,"infowindowopen",function(){
						TRACKER.users[$userId].infoWindowIsOpened = true;
	  				});
					
	  				GEvent.addListener(TRACKER.users[$userId].gmarker,"infowindowclose",function(){
	  					TRACKER.users[$userId].infoWindowIsOpened = false;
	  				});
	  				if (typeof TRACKER.users[$userId].pastPointsGMarker == "undefined") {
	  					TRACKER.users[$userId].pastPointsGMarker = new Array(TRACKER.users[$userId].gmarker);
	  				}
					map.addOverlay(TRACKER.users[$userId].gmarker);
   					trackerOp.getFriendList(1); 	
   				}
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
      			
      			bindElements(langOp, trackerOp);
			    $('#user_title').click();
		});	
	</script>
	
	</head>
	<body  onunload="GUnload();" >	
	$pluginScript
	<div id='wrap'>
				<div class='logo_inFullMap'></div>										
				<div id='bar'></div>
				<div id='sideBar'>						
					<div id='content'>						
	 						<div id='logo'></div>
	 						<ul id='userarea'><li id="username">$realname
	 											<!-- asagidaki iki satir dil dosyasından alınmalı -->
	 											<!--<input type="text" style='width:230px;height:25px' id="statusMessage" value="Status message"/>-->
	 											<!--<input type="button" value="Send" id="sendStatusMessageButton"/>-->
	 										   <!--
	 											<ul>
	 										   <li id="changePassword"></li>
	 										   <li id="signout"></li>
	 										   <li id="inviteUserDiv">Invite User</li>
	 										
	 											</ul>
	 										-->	
	 										</li>
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
									<div id="photos"></div>
									<div class='searchResults'>
										<a href='#returnToPhotoList' id="returnToPhotoList"></a>	
										<div id='results'></div>								
									</div>
								</div>	
								
							<!-- <div id='footer'>							
									<a href='#auLink'></a>								
								</div>
							-->					
							</div>							
					</div>
																																									
				</div>
				
				<div id='map'>MAP</div>	
				<div id='infoBottomBar'></div>
				<div id='loading'></div>											
	</div>
  	
	<div id='aboutus' class="containerPlus draggable {buttons:'c',icon:'browser.png', skin:'default', width:'600', closed:'true'}">  
	<div class="logo"></div></div>
	<div id='changePasswordForm' class="containerPlus draggable {buttons:'c', icon:'changePass.png' ,skin:'default', width:'300', closed:'true' }">  
		<div id="currentPasswordLabel"></div>
		<div><input type='password' name='currentPassword' id='currentPassword' /></div>
		<div id="newPasswordLabel"></div>
		<div><input type='password' name='newPassword' id='newPassword' /></div>  
		<div id="newPasswordAgainLabel"></div>
		<div><input type='password' name='newPasswordAgain' id='newPasswordAgain' /></div>
		<div></div>
		<div><input type='button' name='changePassword' id='changePasswordButton' /> &nbsp; <input type='button' name='cancel' id='changePasswordCancel'/></div>
	</div>
	
	<div id='friendRequestsList' class="containerPlus draggable {buttons:'c', icon:'friends.png' ,skin:'default', width:'300', closed:'true' }">  
		
	</div>
	
		<div id='InviteUserForm' class="containerPlus draggable {buttons:'c', skin:'default', width:'300',  closed:'true' }">  
		<div id="inviteUserEmailLabel"></div> 
		<textarea name='useremail' id='useremail' ></textarea><br/>		
		<div id="inviteUserInvitationMessage"></div>
		
		<textarea name='invitationMessage' id='invitationMessage'></textarea><br/>
		
		<input type='button' name='inviteUserButton' id='inviteUserButton'/>&nbsp; <input type='button' name='cancel' id='inviteUserCancel'/></div>
	</div>	
	
	<div id='message_warning' class="containerPlus draggable {buttons:'c', skin:'default', icon:'alert.png',width:'600', closed:'true' }">
	</div>
	<div id='message_info' class="containerPlus draggable {buttons:'c', skin:'default', icon:'tick_ok.png',width:'600', closed:'true' }">
	</div>
				
	<div style="display:none;">	
>


	
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