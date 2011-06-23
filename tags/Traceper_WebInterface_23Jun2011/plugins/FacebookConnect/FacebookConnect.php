<?php

class FacebookConnect extends UserManager 
{
	private $actionSigninFacebookUser = "SigninFacebookUser";
	private $providedActions;
	const facebook_id = "facebookConnect_facebook_id";
	private static $applicationId = "158859544141926";
	private $userId = NULL;
	
	public function __construct($dbc, $tdo, $tablePrefix){	
		parent::__construct($dbc, $tdo, $tablePrefix);	
	}
	
	public function getLoginScript()
	{
		$appId = self::$applicationId;
		$out = <<<EOT
          <div id="fb-root"></div>
		  <script src="http://connect.facebook.net/en_US/all.js"></script>
		  <script>	
		  		
				$(document).ready( function(){
					var actionSigninFacebookUser = "WebClientSigninFacebookUser";
					
					FB.init({
                		appId  : '$appId',
                		status : true, // check login status
                		cookie : true, // enable cookies to allow the server to access the session
                		xfbml  : true  // parse XFBML
         			});	
         		
         			FB.getLoginStatus(function(response) {
			            if (response.session) {			                 
			                 signinFacebookUser();
			            } else {				            		    
			               $('#userLoginForm').append('<div style="padding:10px;"><fb:login-button autologoutlink="true">Connect with Facebook</fb:login-button></div>');  
			               FB.XFBML.parse($('#userLoginForm').get(0));
						   FB.Event.subscribe('auth.login', function(response){
		          				if (response.session){
		          					signinFacebookUser();
		          				}
		          		   });
					               
			            }
                       
          			});         		
          			
          			function  signinFacebookUser(){
          				FB.api('/me?fields=name,id', function(response){
          					var params = "action=" + actionSigninFacebookUser + "&name=" + response.name + "&facebook_id=" + response.id;
				
							TRACKER.ajaxReq(params, function(result){			
								
								if (result == "1"){
									location.href = "index.php";
								}
								else {
								    TRACKER.showMessage("....An unknow error...", "info");
								}
							});
          				
          				});
          				
          			}
          			
				
				});	
		</script>
EOT;

		return $out;		
	}
	
	public function getProvidedActions(){
		$this->providedActions = array($this->actionSigninFacebookUser);
		return $this->providedActions;		
	}
	
	public function isFacebookUser(){
		$out = false;
		if ($this->tdo->getValue(self::facebook_id) != NULL) {
			$out = true;
		}
		return $out;		
	}
	
	public function getFacebookId(){
		return $this->tdo->getValue(self::facebook_id);
	}
	
	public function getUserId()
	{
		if ($this->userId == NULL)
		{
			$facebookId = $this->tdo->getValue(self::facebook_id);
			if ($facebookId != NULL){
				$sql = sprintf('SELECT Id FROM '
								. $this->tablePrefix . '_users 
								WHERE facebook_id = %d
								LIMIT 1', $facebookId);
								
				$this->userId = $this->dbc->getUniqueField($sql);			
			}
		}
		return $this->userId;
		
	}
	
	public function process($reqArray, $action){
		$out = UNSUPPORTED_ACTION;
		if ($action == $this->actionSigninFacebookUser)
		{			
			if (isset($reqArray['name']) && $reqArray['name'] != null &&
				isset($reqArray['facebook_id']) && $reqArray['facebook_id'] != null)
			{
				// checket whether the user is registered before
				$name = $this->checkVariable($reqArray['name']);
				$facebook_id = (int)$reqArray['facebook_id'];
				
				$sql = sprintf('SELECT Id FROM ' . 
									$this->tablePrefix . '_users
								WHERE facebook_id = %d 
								LIMIT 1', $facebook_id);
				$userId = $this->dbc->getUniqueField($sql);									
				$out = SUCCESS;
				if ($userId == null){				
					$sql = sprintf('INSERT INTO '.$this->tablePrefix.'_users (email, realname, facebook_id )
					    VALUE("%s","%s","%d")', $facebook_id, $name, $facebook_id);			
					$out = FAILED;
					if ($this->dbc->query($sql) != false){
						$out = SUCCESS;
						$userId = $this->dbc->lastInsertId();
					}				
				}
				if ($userId != NULL){
					$this->tdo->save(self::userId, $userId);
					$this->tdo->save(self::realname, $name);
					$this->tdo->save(self::facebook_id, $facebook_id);					
				}				
			}
		}
		return $out;
		
	}
	
	public function logout() {
		$out = <<<EOT
          <script>	
		 	FB.logout(function(response) {
  				// user is now logged out
			});			
		 </script>
EOT;
		return $out;
		
	}
	
	public function getMainScript()
	{
		$appId = self::$applicationId;
		$userId = $this->getUserId();
		$facebookId = $this->getFacebookId();

		$out = <<<EOT
          <div id="fb-root"></div>
		  <script src="http://connect.facebook.net/en_US/all.js"></script>
		  <script>	
		  		
				$(document).ready( function(){
					TRACKER.setFacebookId($facebookId);	
					if (TRACKER.facebookId != null)
					{	
						FB.init({
	                		appId  : '$appId',
	                		status : true, // check login status
	                		cookie : true, // enable cookies to allow the server to access the session
	                		xfbml  : true  // parse XFBML
	         			});	
	         			alert
	         			FB.getLoginStatus(function(response) {			
				            if (response.session) {	
				            	FB.api('/me?fields=name,id', function(response){
				            		if (response.id != TRACKER.facebookId){
				            			TRACKER.showMessage(TRACKER.langOperator.unauthorizedAccess, "warning");
				            			TRACKER.signout();
									}	          				     				
	          					});	 				                
				            }   
				            else {
				            	TRACKER.signout();
							}                    
	          			}); 
	          		}         			         			
				
				});	
		</script>
EOT;
		return $out;
	}
	

}
?>