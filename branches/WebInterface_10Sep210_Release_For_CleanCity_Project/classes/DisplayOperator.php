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
	
	public static function getLoginPage($page, $callbackURL, $language){
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
					$('#usernameLabel').text(langOp.emailLabel+":");	
					$('#passwordLabel').text(langOp.passwordLabel+":");
					$('#rememberMeLabel').text(langOp.rememberMeLabel).click(function(){
						$('#rememberMe').attr('checked', !($('#rememberMe').attr('checked')));
							
					});
					$('#forgotPassword').text(langOp.forgotPassword);
					$('#submitLoginFormButton').attr('value', langOp.submitFormButtonLabel);	
					$('#emailLabel').text(langOp.emailLabel + ":");					
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
				
				});	
				function sendNewPassword(){
					TRACKER.sendNewPassword($('#email').val());
				}
				function authenticateUser(){
					TRACKER.authenticateUser($('#emailLogin').val(), $('#password').val(), $('#rememberMe').attr('checked'));
				}
				
				
				</script>
			</head>
			<body>
				<div id='loginLogo' ></div>
				<div id="userLoginForm">									
					<div id="usernameLabel"></div>
					<div><input type="text" name="email" id="emailLogin" /></div>
					<div id="passwordLabel"></div>
					<div><input type="password" name="password" id="password" /></div>
					<div><input type="checkbox" name="rememberMe" id="rememberMe"/>
						 <div style="display:inline" id="rememberMeLabel"></div>
					</div>
					<div id="forgotPassword"></div>	
					<div id="register">Register</div>				
					<div><input type="button" id="submitLoginFormButton" value="" /></div>				
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
	
	
	
	public static function getMainPage($callbackURL, $fetchPhotosInInitialization, $updateUserListInterval, $queryIntervalForChangedUsers, $apiKey, $language) {

		$head = self::getMetaNLinkSection();
		$realname = self::$realname;
		
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
      	  
	<link type="text/css" href="js/jquery/plugins/colorbox/colorbox.css" rel="stylesheet" media="screen"/>
    <link type="text/css" href="js/jquery/plugins/superfish/css/superfish.css" rel="stylesheet" media="screen"/>
	
	<script type="text/javascript" src="js/jquery/jquery.min.js"></script>
	<script type="text/javascript" src="js/jquery/plugins/jquery.cookie.js"></script>
	<script type="text/javascript" src="js/jquery/plugins/colorbox/jquery.colorbox-min.js"></script>
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
	   	
   					var trackerOp = new TrackerOperator('$callbackURL', map, $fetchPhotosInInitialization, $updateUserListInterval, $queryIntervalForChangedUsers, langOp);			
   					trackerOp.getUserList(1); 	
   				}
			}
   			catch (e) {
				
			}    			
			bindElements(langOp, trackerOp);
			$('#user_title').click();
		});	
	</script>
	
	</head>
	<body  onunload="GUnload();" >	
	<div id='wrap'>	
				<div class='logo_inFullMap'></div>										
				<div id='bar'></div>
				<div id='sideBar'>						
					<div id='content'>						
	 						<div id='logo'></div>
	 						<ul id='userarea'><li id="username">$realname
	 										   <ul>
	 										   <li id="changePassword"></li>
	 										   <li id="signout"></li>
	 										
	 										<!--
	 										   <div id="inviteUserDiv">Invite User</div>
	 										-->
	 											</ul>
	 										</li>
	 						</ul>
							<div id='lists'>	
								<div class='titles'>									
									<div class='title active_title' id='user_title'><div class='arrowImage'></div></div>
									<div class='title' id='photo_title'><div class='arrowImage'></div></div>								
								</div>
								<div id='usersList'>											
									<div class='search'>						
										<input type='text' id='searchBox' value='' /><img src='images/search.png' id='searchButton'  />
									</div>
									<div id="users"></div>
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
	<div style="display:none;">	
	<div id='aboutus'></div>
	<div id='InviteUserForm'>
		<div id="inviteUserEmailLabel"></div> <input type='text' name='useremail' id='useremail' /><br/>		
		<input type='button' name='inviteUserButton' id='inviteUserButton'/>
	</div>	
	<div id='changePasswordForm'>
		<div id="currentPasswordLabel"></div>
		<div><input type='password' name='currentPassword' id='currentPassword' /></div>
		<div id="newPasswordLabel"></div>
		<div><input type='password' name='newPassword' id='newPassword' /></div>
		<div id="newPasswordAgainLabel"></div>
		<div><input type='password' name='newPasswordAgain' id='newPasswordAgain' /></div>
		<div></div>
		<div><input type='button' name='changePassword' id='changePasswordButton' /> &nbsp; <input type='button' name='cancel' id='changePasswordCancel' /></div>
	</div>
	</div>
	</body>
</html>
MAIN_PAGE;

		  return $str;
}

}