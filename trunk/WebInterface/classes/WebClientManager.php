<?php
/********************************************
*
*	Filename:	WebClientManager.php
*	Author:		Ahmet Oguz Mermerkaya
*	E-mail:		ahmetmermerkaya@hotmail.com
*	Begin:		Tuesday, April 21, 2009  12:32
*
*********************************************/
require_once('Base.php');


class WebClientManager extends Base
{
	
	private $actionPrefix = NULL;
	private $tablePrefix = NULL;  // prefix of tables' names in database 	
	private $elementCountInAPage = 10;
	private $elementCountInPhotoPage = 6;
	private $elementCountInLocationsPage = 50;
	private $dataFetchedTime;
	private $imageFetchedTime;
	private $pastPointsFetchedUserId = NULL;
	private $missingImage;
	private $imageDirectory;
	private $imageHandlerURL;
	private $includeImageInUpdatedUserListReq = false;
	private $usermanager = NULL;
	private $fbc = NULL;
	
	const  dataFetchedTimeStoreKey = "wcm_dataFetchedTime";
	const  imageFetchedTimeStoreKey = "wcm_imageFetchedTime";
	
	public function __construct($dbc, $actionPrefix, $tablePrefix, $elementCountInAPage, 
								$elementCountInLocationsPage, $elementCountInPhotoPage) 
	{
		$this->dbc = $dbc;
		$this->actionPrefix = $actionPrefix;
		$this->tablePrefix = $tablePrefix;
		$this->elementCountInAPage = $elementCountInAPage;
		$this->elementCountInLocationsPage = $elementCountInLocationsPage;	
		$this->elementCountInPhotoPage = $elementCountInPhotoPage;
	}
	
	public function setUserManager($usermanager){
		$this->usermanager = $usermanager;
	}
	
	public function setImageRelatedVars($imageDirectory, $missingImage, $imageHandlerURL){
		$this->imageDirectory = $imageDirectory;
		$this->missingImage = $missingImage;
		$this->imageHandlerURL = $imageHandlerURL; 
	}	

	
	public function process($reqArray) 
	{
		$out = NULL;
		switch($reqArray['action']) 
		{
			case $this->actionPrefix . "AuthenticateUser":
				$out = $this->authenticateUser($reqArray);
				break;
			case $this->actionPrefix . "Signout":
				$out = FAILED;
				if ($this->tdo->clearAll() == true){
					$out = SUCCESS;	
				}				
				break;
			case $this->actionPrefix . "SendNewPassword":
				$out = MISSING_PARAMETER;
				if (isset($reqArray['email']) && $reqArray['email'] != "") {
					$out = $this->usermanager->sendNewPassword($reqArray['email']);		
				}		
				break;
			case $this->actionPrefix . "ChangePassword":
				$out = $this->changePassword($reqArray);
				break;	
			case $this->actionPrefix . "GetUserList":
				$out = $this->getUserList($reqArray, $this->elementCountInAPage, "userListReq");
				break;
			case $this->actionPrefix . "GetFriendList":
				$out = $this->getFriendList($reqArray, $this->elementCountInAPage, "userListReq");
				break;	
			case $this->actionPrefix . "SearchUser":
				$out = $this->searchUser($reqArray);
				break;		
			case $this->actionPrefix . "UpdateFriendList":
				$out = $this->getFriendList($reqArray, $this->elementCountInLocationsPage,"userListReq");
				break;		
			case $this->actionPrefix . "GetUpdatedFriendList":
				if ($this->tdo == NULL || 
					($this->dataFetchedTime = $this->tdo->getValue(self::dataFetchedTimeStoreKey)) == NULL)
				{
						$this->dataFetchedTime = time();
						$this->tdo->save(self::dataFetchedTimeStoreKey, $this->dataFetchedTime);
				}
				if ($this->tdo == NULL || 
					($this->imageFetchedTime = $this->tdo->getValue(self::imageFetchedTimeStoreKey)) == NULL)
				{
						$this->imageFetchedTime = time();
						$this->tdo->save(self::imageFetchedTimeStoreKey, $this->imageFetchedTime);
				}	
				$out = $this->getFriendList($reqArray, $this->elementCountInLocationsPage, "updatedUserListReq");
				break;
			case $this->actionPrefix . "GetUserPastPoints":
				$out = $this->getUserPastPoints($reqArray,  $this->elementCountInLocationsPage);
				break;	
			case $this->actionPrefix . "GetImageList":
				$elementCount = $this->elementCountInPhotoPage;
				if (isset($reqArray['list']) && $reqArray['list'] == "long"){
					$elementCount = $this->elementCountInLocationsPage;
				}
				$out = $this->getImageList($reqArray, $elementCount);
				break;	
			case $this->actionPrefix ."GetImage":
				$out = $this->getImage($reqArray);							
				break;							
			case $this->actionPrefix ."SearchImage":
				$out = $this->searchImage($reqArray, $this->elementCountInPhotoPage);
				break;	
			case $this->actionPrefix ."DeleteImage":
				$out = $this->deleteImage($reqArray, UPLOAD_DIRECTORY);
				break;	
			case $this->actionPrefix ."InviteUser":
				$out = $this->inviteUser($reqArray);
				break;
			case $this->actionPrefix ."RegisterUser":
				$out = $this->registerUser($reqArray);
				break;
			case $this->actionPrefix . "ActivateAccount":
				$out = $this->usermanager->activateAccount($reqArray);
				break;
			case $this->actionPrefix . "ActivateAccountRequest":
				//TODO: burada tekrar bir duzenleme yapilsin
				$email = "";
				$key = "";
				if (isset($reqArray["key"]) == true){
					$key = $reqArray["key"];
				}
				if (isset($reqArray["email"]) == true){
					$email = $reqArray["email"];
				}
				$out = DisplayOperator::getActivateAccountPage($_SERVER['PHP_SELF'], LANGUAGE, $key, $email);
				break;	
			case $this->actionPrefix ."RegisterInvitedUser":
				
				if ($this->usermanager->isInvitedUser($reqArray) == true){
					
					$out = DisplayOperator::getRegistrationPage($reqArray["email"], $reqArray["key"], $_SERVER['PHP_SELF']);
				}
				else {
					$out = DisplayOperator::showErrorMessage("There is no valid invitation found");
				}
				break;
			case $this->actionPrefix . "SaveStatusMessage":
				$out = $this->saveStatusMessage($reqArray);
				break;	
			case $this->actionPrefix . "DeleteFriendship":
				$out = $this->deleteFriendship($reqArray);
				break;
			case $this->actionPrefix . "ConfirmFriendship":
				$out = $this->confirmFriendship($reqArray);
				break;	
			case $this->actionPrefix . "AddFriendRequest":
				$out = $this->addFriendRequest($reqArray);
				break;
			case $this->actionPrefix . "GetFriendRequests":
		
				$out = $this->usermanager->getFriendRequests($reqArray['pageNo'], $this->elementCountInAPage);
				break;		
			default:				
				$out = UNSUPPORTED_ACTION;
				if (class_exists("FacebookConnect")) 
				{
					$classname = "FacebookConnect";
					$this->fbc = new $classname($this->dbc, $this->tdo, $this->tablePrefix);
					$providedActions = $this->fbc->getProvidedActions();
					$count = count($providedActions);
					for($i = 0; $i < $count; $i++){
						if ($reqArray['action'] == $this->actionPrefix . $providedActions[$i]){
							$out = $this->fbc->process($reqArray, $providedActions[$i]);
							break;		
						} 
					} 				
				}
				
				break;
		}
		return $out;				
	}
	
	private function authenticateUser($reqArray)
	{
		$out = MISSING_PARAMETER;
		if (isset($reqArray['username']) && $reqArray['username'] != null &&
			isset($reqArray['password']) && $reqArray['password'] != null
		    )
		{
			$out = UNAUTHORIZED_ACCESS;
			$keepUserLoggedIn = false;
			if (isset($reqArray['keepUserLoggedIn']) && $reqArray['keepUserLoggedIn'] == 'true'){
				$keepUserLoggedIn = true;				
			}
			
			if (($this->fbc !== NULL && 
				 $this->fbc->isFacebookUser() === true) 
				 || 
			    ($this->usermanager !== null && 
			    $this->usermanager->authenticateUser($reqArray['username'], md5($reqArray['password']), $keepUserLoggedIn) !== null)) 
			{
				$out = SUCCESS;						
			}	
			
		}		
		return $out;
	}
	
	private function deleteFriendship($reqArray) 
	{
		$out = MISSING_PARAMETER;
		if (isset($reqArray['friendId']) && $reqArray['friendId'] != null ){
			$out = UNAUTHORIZED_ACCESS;
			if ($this->isUserAuthenticated() == true){
				$userId = $this->usermanager->getUserId();
				$friendId = $this->checkVariable($reqArray['friendId']);
				
				$result = $this->usermanager->deleteFriendship($userId, $friendId);
				$out = FAILED;
				if ($result === true) {
					$out = SUCCESS;
				}
			}
		}
		return $out;
	}
	
	private function confirmFriendship($reqArray) 
	{
		$out = MISSING_PARAMETER;
		if (isset($reqArray['friendId']) && $reqArray['friendId'] != null ){
			$out = UNAUTHORIZED_ACCESS;
			if ($this->isUserAuthenticated() == true){
				$userId = $this->usermanager->getUserId();
				$friendId = $this->checkVariable($reqArray['friendId']);
				
				$result = $this->usermanager->confirmFriendship($userId, $friendId);
				$out = FAILED;
				if ($result === true) {
					$out = SUCCESS;
				}
			}
		}
		return $out;
	}
	
	
	private function addFriendRequest($reqArray){
		$out = MISSING_PARAMETER;
		if (isset($reqArray['friendId']) && $reqArray['friendId'] != null )
		{
			$out = UNAUTHORIZED_ACCESS;
			if ($this->isUserAuthenticated() == true)
			{				
				$friendId = $this->checkVariable($reqArray['friendId']);				
				$result = $this->usermanager->addFriendRequest($friendId);
				$out = FAILED;
				if ($result === true) {
					$out = SUCCESS;
				}
			}
		}
		return $out;		
	}
	
	private function saveStatusMessage($reqArray) {
		$out = MISSING_PARAMETER;
		if (isset($reqArray['statusMessage']) && $reqArray['statusMessage'] != null )
		{
			$out = UNAUTHORIZED_ACCESS;
			if ($this->isUserAuthenticated() == true)
			{
				$statusMessage = $this->checkVariable($reqArray['statusMessage']);
				$userId = $this->usermanager->getUserId();
				$sql = 	sprintf('UPDATE ' . $this->tablePrefix .'_users
						 		 SET status_message = "%s",
						 		 	 status_source = %d,
						 		 	 status_message_time = NOW()
						 		 WHERE  
						 		 	id = %d
						 		 LIMIT 1', $statusMessage, STATUS_MESSAGE_SOURCE_WEB, $userId);
				
				if ($this->dbc->query($sql)) {
					
					$sql = 	sprintf('INSERT INTO ' . $this->tablePrefix .'_status_messages
					             (status_message, status_source, date_time,userId)
					             VALUES ("%s", %d, NOW(),%d)',
					          $statusMessage, STATUS_MESSAGE_SOURCE_WEB, $userId);
					          
					if ($this->dbc->query($sql))
					{	
						$out = SUCCESS;	
					}
				}				
			}			
		}		
		return $out;		
	}
	
	private function inviteUser($reqArray){
		$out = MISSING_PARAMETER;
		if (isset($reqArray['email']) && $reqArray['email'] != null ) 
		 {
		 	$out = UNAUTHORIZED_ACCESS;
			if ($this->isUserAuthenticated() == true)
			{
		 		$email = $this->checkVariable($reqArray['email']);
		 		$message = null;
		 		if (isset($reqArray['message']) && $reqArray['message'] != null ) {
		 			$message = $this->checkVariable($reqArray['message']);		 	
		 		}
		 		$out = $this->usermanager->inviteUser($email, $message);		 	
			}
		 }
		 return $out;
	}
	
	private function registerUser($reqArray)
	{
		$out = MISSING_PARAMETER;
		if (isset($reqArray['email']) && $reqArray['email'] != null &&
			isset($reqArray['name']) && $reqArray['name'] != null &&
			isset($reqArray['password']) && $reqArray['password'] != null)
		 {
			$invitedUser = false;
		 	if (isset($reqArray['key']) && $reqArray['key'] != null)
		 	{
		 		if ($this->usermanager->isInvitedUser($reqArray) == true){
		 			$invitedUser = true;
		 		}
		 	}
		 	$email = $this->checkVariable($reqArray['email']);
		 	$name = $this->checkVariable($reqArray['name']);
		 	$password = $this->checkVariable($reqArray['password']);	
		 	 	
		 	$out = $this->usermanager->registerUser($email, $name, $password, $invitedUser);			
		 }
		 return $out;
		
	}
	private function changePassword($reqArray){
		$out = MISSING_PARAMETER;
		if (isset($reqArray['newPassword']) && $reqArray['newPassword'] != "" &&
			isset($reqArray['currentPassword']) && $reqArray['currentPassword'] != "") 
		{	
			$out = UNAUTHORIZED_ACCESS;
			if ($this->isUserAuthenticated() == true)
			{	
				$newPassword = $reqArray['newPassword'];
				$currentPassword = $reqArray['currentPassword'];					
				$out = $this->usermanager->changePassword($newPassword, $currentPassword);		
			}
		}
		return $out;
	}
	
	private function isUserAuthenticated() {
		$authenticated = false;
		if (($this->fbc !== NULL && 
			 $this->fbc->isFacebookUser() === true) 
			 || 
			($this->usermanager !== null &&
			 $this->usermanager->isUserAuthenticated() == true)) 
		{
			$authenticated = true;
		}
		return $authenticated;
	}
	
	private function getFriendList($reqArray, $elementCountInAPage, $req='updatedUserListReq') 
	{
		$out = UNAUTHORIZED_ACCESS;
		if ($this->isUserAuthenticated() == true)
		{
			$userId = $this->usermanager->getUserId();
			$out = FAILED;
			$pageNo = 1;
			if (isset($reqArray['pageNo']) && $reqArray['pageNo'] > 0) {
					$pageNo = (int) $reqArray['pageNo'];
			}
			$offset = ($pageNo - 1) * $elementCountInAPage;
			
			$sqlItemCount = null;
			if ($req == 'updatedUserListReq')
			{	
				$sqlImageUnion = '';
				$sqlImagePageCountUnion = '';
				if (isset($reqArray["include"]) && $reqArray["include"] == "image")
				{
					$this->includeImageInUpdatedUserListReq = true;
					$sqlImageUnion = 'UNION
									  SELECT 
										u.Id, u.userId, u.latitude, u.longitude, u.altitude, 
										null, null,  date_format(u.uploadTime,"%d %b %Y %T") as dataArrivedTime,
										(unix_timestamp(u.uploadTime) - '. $this->imageFetchedTime .')  as timeDif, "image" as type
									  FROM '. $this->tablePrefix .'_upload u
									  LEFT JOIN '. $this->tablePrefix .'_users usr
									  ON  
 										 usr.Id = u.userId
									  WHERE unix_timestamp(u.uploadTime) >= ' .$this->imageFetchedTime . '
									  		AND ( u.userId IN 
									  			     (SELECT friend1 FROM '. $this->tablePrefix.'_friends 
									  			       WHERE friend2 = '. $userId .' and status = 1
									  			      UNION
									  			      SELECT friend2 FROM '.$this->tablePrefix.'_friends
									  			       WHERE friend1 = '. $userId .' and status = 1)
									  	          OR u.userId = '.$userId .')';
					
					$sqlImagePageCountUnion = 'UNION
											   SELECT
													count(Id)
											   FROM '. $this->tablePrefix .'_upload u
											   WHERE 
											   		unix_timestamp(u.uploadTime) >= ' .$this->imageFetchedTime. '
											  		AND ( u.userId IN 
											  			     (SELECT friend1 FROM '. $this->tablePrefix.'_friends 
											  			       WHERE friend2 = '. $userId .' and status = 1
											  			      UNION
											  			      SELECT friend2 FROM '.$this->tablePrefix.'_friends
											  			       WHERE friend1 = '. $userId .' and status = 1 )
											  	          OR u.userId = '.$userId .')';
				}
				
				$sql = 'SELECT
							u.Id, null as userId, u.status_message, u.latitude, u.longitude, u.altitude, 
							u.realname, u.deviceId, date_format(u.dataArrivedTime,"%d %b %Y %T") as dataArrivedTime, 
							(unix_timestamp(u.dataArrivedTime) - '.$this->dataFetchedTime.') as timeDif,
							"user" as type, 1 as isFriend
						FROM '
							. $this->tablePrefix .'_friends f
						LEFT JOIN '. $this->tablePrefix .'_users u ON (u.Id = f.friend1 OR u.Id = f.friend2) AND u.Id != '. $userId .'
						WHERE
							( ( ( (f.friend1 = '. $userId .') OR (f.friend2 = '. $userId .') 
							     ) 
							     AND f.status = 1
							    ) 
								OR u.Id= '.$userId .') AND
							unix_timestamp(u.dataArrivedTime) >= '. $this->dataFetchedTime .'		
						'. $sqlImageUnion .'						
						ORDER BY
							timeDif 
						DESC	
						LIMIT '. $offset .',' 
							   .$elementCountInAPage;
				
				$sqlPageCount = 'SELECT ceil(sum(itemCount)/'.$elementCountInAPage.')
								 FROM
									(SELECT
										count(u.Id) as itemCount
								 	 FROM ' . $this->tablePrefix .'_friends f
									 LEFT JOIN '. $this->tablePrefix .'_users u ON (u.Id = f.friend1 OR u.Id = f.friend2) AND u.Id != '. $userId .'
									 WHERE ( ( ( (f.friend1 = '. $userId .') OR (f.friend2 = '. $userId .') 
							     				) 
							     				AND f.status = 1
							    		     ) 
											OR u.Id= '.$userId .'
										   ) AND
										unix_timestamp(u.dataArrivedTime) >= '. $this->dataFetchedTime .'		
						  			'. $sqlImagePageCountUnion .'
							 		  ) t';
							
			}
			else //if ($req == 'userListReq') 
			{
				// this is the user list showing in left pane

				$sql = 'SELECT u.Id, u.latitude, u.status_message, u.longitude, u.altitude, "user" as type,
							   u.realname, u.deviceId,  "1" as isFriend, date_format(u.dataArrivedTime,"%d %b %Y %T") as dataArrivedTime
						FROM '. $this->tablePrefix .'_friends f 
						LEFT JOIN '. $this->tablePrefix .'_users u ON (u.Id = f.friend1 OR u.Id = f.friend2) AND u.Id != '. $userId .'
						WHERE   ( ( (f.friend1 = '. $userId .') OR (f.friend2 = '. $userId .') 
							      ) 
							      AND f.status = 1
							     )							 
						ORDER BY		
							u.realname 							
						LIMIT ' . $offset . ',' 
								. $elementCountInAPage;				
		
			//	echo $sql;	 		
				$sqlPageCount = 'SELECT  ceil(count(u.Id)/'.$elementCountInAPage.') 
								FROM '. $this->tablePrefix .'_friends f 
								LEFT JOIN '. $this->tablePrefix .'_users u ON (u.Id = f.friend1 OR u.Id = f.friend2) AND u.Id != '. $userId .'
								WHERE  ( ( (f.friend1 = '. $userId .') OR (f.friend2 = '. $userId .') 
									      ) 
									      AND f.status = 1
									     )					
										 ';
			}	
		
			$pageCount = $this->dbc->getUniqueField($sqlPageCount);
		
			// data fetched time is used only in updated User list req so it is 
			// updated only when $req == 'updatedUserListReq'		 							 								
			if ($req == 'updatedUserListReq' 
				&& $pageNo == $pageCount
				&& $pageCount != 0) 
			{
				$this->dataFetchedTime = time();
				$this->tdo->save(self::dataFetchedTimeStoreKey, $this->dataFetchedTime);
				if (isset($reqArray["include"]) && $reqArray["include"] == "image"){
					$this->imageFetchedTime = $this->dataFetchedTime; 
					$this->tdo->save(self::imageFetchedTimeStoreKey, $this->imageFetchedTime);
				}
			}
			
			$out = $this->prepareXML($sql, $pageNo, $pageCount);
			
		}
		return $out;		
	}
	
	private function searchUser($reqArray) 
	{
		$out = MISSING_PARAMETER;
		if (isset($reqArray['search']) && $reqArray['search'] != NULL && strlen($reqArray['search']) >= 2)
		{
			$out = UNAUTHORIZED_ACCESS;
			if ($this->isUserAuthenticated() == true)
			{
				$out = FAILED;
				$search = $this->checkVariable($reqArray['search']);
				$pageNo = 1;
				if (isset($reqArray['pageNo']) && $reqArray['pageNo'] > 0) {
					$pageNo = (int) $reqArray['pageNo'];
				}
				$offset = ($pageNo - 1) * $this->elementCountInAPage;
				$userId = $this->usermanager->getUserId();
				$sql = //sprintf(
							'  (SELECT 
									Id, 1 as isFriend, status_message, latitude, longitude, altitude, 
									realname, deviceId, date_format(dataArrivedTime,"%d %b %Y %T") as dataArrivedTime
								FROM '
									. $this->tablePrefix .'_users								
								WHERE
									realname like "%'. $search .'%" AND 
									( Id IN (SELECT friend1 FROM '.$this->tablePrefix.'_friends
											 WHERE friend2 = '. $userId .' and status = 1
											 UNION 
											 SELECT friend2 FROM '.$this->tablePrefix.'_friends
											 WHERE friend1 = '. $userId .' and status = 1) 
										 
									 OR Id = '. $userId .' )
								)
								UNION
								(SELECT 
									Id, 0 as isFriend, null, null, null, null, 
									realname, null, null 
								FROM '
									. $this->tablePrefix .'_users								
								WHERE
									realname like "%'. $search .'%" AND 
									( Id NOT IN (SELECT friend1 FROM '.$this->tablePrefix.'_friends
											 WHERE friend2 = '. $userId .'
											 UNION 
											 SELECT friend2 FROM '.$this->tablePrefix.'_friends
											 WHERE friend1 = '. $userId .') 
									 AND Id != '. $userId .'
									)
								)	
								UNION  
								(SELECT 
									Id, 2 as isFriend, null, null, null, null, 
									realname, null, null 
								FROM '
									. $this->tablePrefix .'_users								
								WHERE
									realname like "%'. $search .'%" AND 
									( Id IN (SELECT friend1 FROM '.$this->tablePrefix.'_friends
											 WHERE friend2 = '. $userId .' and status != 1
											 UNION 
											 SELECT friend2 FROM '.$this->tablePrefix.'_friends
											 WHERE friend1 = '. $userId .'  and status != 1) 
									 AND Id != '. $userId .'
									)
								)	
												
							ORDER BY
								realname
							LIMIT '. $offset .' , '. $this->elementCountInAPage ;
						
						//);				
				$sqlItemCount = 'SELECT
			 						ceil(count(Id)/'.$this->elementCountInAPage.')
			 					 FROM '
			 					 	. $this->tablePrefix .'_users
								 WHERE
									realname like "%'. $search .'%" ';
			 					 	
				$out = $this->prepareXML($sql, $pageNo, $this->dbc->getUniqueField($sqlItemCount));
			}
		}
		
		return $out;
	}
	
	private function getUserPastPoints($reqArray, $elementCountInAPage)
	{
		$out = MISSING_PARAMETER;
		if (isset($reqArray['userId']) && !empty($reqArray['userId']))	{
					
			$out = UNAUTHORIZED_ACCESS;
			$userIdOnMap = (int) $reqArray['userId'];
			$userId = $this->usermanager->getUserId();
			if ($this->isUserAuthenticated() == true && 
				($this->usermanager->isFriend($userId, $userIdOnMap) == true ||
				 $userId == $userIdOnMap))
			{
				
				$this->pastPointsFetchedUserId = $userIdOnMap;
				$pageNo = 1;
				if (isset($reqArray['pageNo']) && $reqArray['pageNo'] > 0) {
					$pageNo = (int) $reqArray['pageNo'];
				}
				$offset = ($pageNo - 1) * $elementCountInAPage;
				$offset++;  // to not get the last location
				$sql = 'SELECT 
							longitude, latitude, deviceId, 
							date_format(dataArrivedTime,"%d %b %Y %T") as dataArrivedTime
						 FROM ' . $this->tablePrefix .'_user_was_here
						 WHERE 
						 	userId = '. $userIdOnMap . '
						 ORDER BY 
						 	Id DESC
						 LIMIT '. $offset . ','
								. $elementCountInAPage;
				//echo $sql;				
				// subtract 1 to not get the last location into consideration								
				$sqlItemCount = 'SELECT 
									ceil((count(Id)-1)/ '.$elementCountInAPage .')
								 FROM '. $this->tablePrefix .'_user_was_here
								 wHERE 
								 	userId = '. $userIdOnMap ;				
				
				$out = $this->prepareXML($sql, $pageNo, $this->dbc->getUniqueField($sqlItemCount), "userPastLocations");
			}
		}
		return $out;
	}
	
	private function getImageList($reqArray, $elementCountInAPage)
	{
		$out = UNAUTHORIZED_ACCESS;
		if ($this->isUserAuthenticated() == true)
		{
			$out = FAILED;
			$pageNo = 1;
			if (isset($reqArray['pageNo']) && $reqArray['pageNo'] > 0) {
					$pageNo = (int) $reqArray['pageNo'];
			}
			$offset = ($pageNo - 1) * $elementCountInAPage;
			$userId = $this->usermanager->getUserId();
			$sql = 'SELECT 
							u.Id, u.userId, usr.realname, u.latitude, 
							u.altitude, u.longitude, date_format(u.uploadTime,"%d %b %Y %H:%i") uploadTime
					FROM '. $this->tablePrefix . '_upload u
					LEFT JOIN '. $this->tablePrefix .'_users usr
						ON  usr.Id = u.userId
					WHERE u.userId in 
							(SELECT friend1 FROM '.$this->tablePrefix.'_friends
							 WHERE friend2 = '. $userId .' and status = 1
							 UNION 
							 SELECT friend2 FROM '.$this->tablePrefix.'_friends
							 WHERE friend1 = '. $userId .' and status = 1)
						  OR u.userId = '. $userId .'
					ORDER BY 
						u.Id 
					DESC
					LIMIT 
							' . $offset . ',' . $elementCountInAPage;
		
			$sqlItemCount = 'SELECT
			 						ceil(count(Id)/'.$elementCountInAPage.')
			 				 FROM '
			 					 	. $this->tablePrefix .'_upload u
			 				WHERE u.userId in 
									 	(SELECT friend1 FROM '.$this->tablePrefix.'_friends
									 		WHERE friend2 = '. $userId .' and status = 1
									 	UNION 
									 	SELECT friend2 FROM '.$this->tablePrefix.'_friends
									 		WHERE friend1 = '. $userId .' and status = 1)
						  		  OR u.userId = '. $userId;
			 					 	
			$pageCount = $this->dbc->getUniqueField($sqlItemCount);			 	
			if ($pageNo == $pageCount
				&& $pageCount != 0) 
			{
				$this->imageFetchedTime = time();
				$this->tdo->save(self::imageFetchedTimeStoreKey, $this->imageFetchedTime);
			}
			
			$out = $this->prepareXML($sql, $pageNo, $pageCount, "imageList");
			 					 	
		}
		return $out;
	}
	
	private function searchImage($reqArray, $elementCountInAPage)
	{
		$searchedUserId = null;
		$realname = null;
		$userId = $this->usermanager->getUserId();
		$pageNo = 1;
		if (isset($reqArray['pageNo']) && $reqArray['pageNo'] > 0) {
					$pageNo = (int) $reqArray['pageNo'];
		}
		$offset = ($pageNo - 1) * $elementCountInAPage;
		$out = MISSING_PARAMETER;	
		if (isset($reqArray['userId']) && !empty($reqArray['userId']))
		{
			$searchedUserId = (int) $reqArray['userId'];
			$sql = 'SELECT 
						u.Id, u.userId, usr.realname, u.latitude, 
						u.altitude, u.longitude, date_format(u.uploadTime,"%d %b %Y %H:%i") uploadTime
					FROM '. $this->tablePrefix . '_upload u
					LEFT JOIN '. $this->tablePrefix .'_users usr
					ON  usr.Id = u.userId
					WHERE u.userId = '. $searchedUserId .' AND 
						  u.userId in 
							(SELECT friend1 FROM '.$this->tablePrefix.'_friends
							 WHERE friend2 = '. $userId .' and status = 1
							 UNION 
							 SELECT friend2 FROM '.$this->tablePrefix.'_friends
							 WHERE friend1 = '. $userId .' and status = 1) 
					LIMIT ' . $offset . ',' . $elementCountInAPage;
			
			$sqlItemCount = 'SELECT
			 						ceil(count(Id)/'.$elementCountInAPage.')
			 				  FROM '. $this->tablePrefix .'_upload
			 				  WHERE userId = '. $searchedUserId .' AND 
			 				  		 u.userId in 
									(SELECT friend1 FROM '.$this->tablePrefix.'_friends
									 WHERE friend2 = '. $userId .' and status = 1
									 UNION 
									 SELECT friend2 FROM '.$this->tablePrefix.'_friends
									 WHERE friend1 = '. $userId .' and status = 1) ';
		}
		else if (isset($reqArray['realname']) && !empty($reqArray['realname'])){
			$realname = $this->checkVariable($reqArray['realname']);
			
			$sql = 'SELECT 
						u.Id, u.userId, usr.realname, u.latitude, 
						u.altitude, u.longitude, date_format(u.uploadTime,"%d %b %Y %H:%i") uploadTime
					FROM '. $this->tablePrefix . '_upload u
					LEFT JOIN '. $this->tablePrefix .'_users usr
					ON  usr.Id = u.userId
					WHERE usr.realname like "%'. $realname .'%" AND
						  usr.Id in 
							(SELECT '. $userId .' 
							 UNION
							 SELECT friend1 FROM '.$this->tablePrefix.'_friends
							 WHERE friend2 = '. $userId .' and status = 1
							 UNION 
							 SELECT friend2 FROM '.$this->tablePrefix.'_friends
							 WHERE friend1 = '. $userId .' and status = 1)
					ORDER BY u.Id
					DESC
					LIMIT ' . $offset . ',' . $elementCountInAPage;
			
			$sqlItemCount = 'SELECT
			 						ceil(count(u.Id)/'.$elementCountInAPage.')
			 				  FROM '. $this->tablePrefix .'_upload u
			 				  LEFT JOIN '. $this->tablePrefix .'_users usr
							  ON  usr.Id = u.userId
							  WHERE usr.realname like "%'. $realname .'%" AND
							  		 usr.Id in 
									(SELECT '. $userId .' 
							 		 UNION
							 		 SELECT friend1 FROM '.$this->tablePrefix.'_friends
									 WHERE friend2 = '. $userId .' and status = 1
									 UNION 
									 SELECT friend2 FROM '.$this->tablePrefix.'_friends
									 WHERE friend1 = '. $userId .' and status = 1)';
			
		}
		if ($realname != null || $userId != null){
			$out = UNAUTHORIZED_ACCESS;
			if ($this->isUserAuthenticated() == true){
//				$out = FAILED;
				$out = $this->prepareXML($sql, $pageNo, $this->dbc->getUniqueField($sqlItemCount), "imageList");
			}
		}
		
		return $out;		
	}
	/**
	 * 
	 *
	 */
	private function getImage($reqArray)
	{
		$out = MISSING_PARAMETER;
		if (isset($reqArray['imageId']) && !empty($reqArray['imageId']))
		{
			$out = UNAUTHORIZED_ACCESS;
			if ($this->isUserAuthenticated() == true)	
			{
				$imageId = (int) $reqArray['imageId'];
				$thumb = false;
				if (isset($reqArray['thumb']) && $reqArray['thumb']=='ok')
				{ $thumb = true;
				}					
				$thumbCreator = new ImageOperator($this->imageDirectory, $this->missingImage);
				$out = $thumbCreator->getImage($imageId, $thumb);					
			}
		}		
		return $out;	
	}
	
	
	private function deleteImage( $reqArray )
	{
		$out = MISSING_PARAMETER;
		if (isset($reqArray['imageId']) && !empty($reqArray['imageId'])) 
		{
			
			$out = UNAUTHORIZED_ACCESS;		
			$imageId = $reqArray['imageId']; 	
			if ($this->isUserAuthenticated() == true)
			{
				$thumbCreator = new ImageOperator($this->imageDirectory, $this->missingImage);
				
				$userId = $this->usermanager->getUserId();
				
				$sql = sprintf ('DELETE FROM '.$this->tablePrefix.'_upload
				                 WHERE id = %d and userId = %d
				                 LIMIT 1', $imageId, $userId );
				
			 	$out = FAILED;
			    if	($this->dbc->query($sql) != false && 
			    	 $this->dbc->getAffectedRows() == 1) 
			    {
			    	$out = $thumbCreator->deleteImage($imageId);    				    	   	
			    }
			  }
		}
		return $out;       
}
	    
	
	
	
	
	/**
	 * this function generates xml that is used when getting user list or user past locations
	 * params: $type may be "userList" or "userPastLocations" or "imageList"
	 */
	private function prepareXML($sql, $pageNo, $pageCount, $type="userList")
	{		
		$result = NULL;
		// if page count equal to 0 then there is no need to run query
//		echo $sql;
		if ($pageCount >= $pageNo && $pageCount != 0) {
			$result = $this->dbc->query($sql);		
		}
				
		$str = NULL;
		$userId = NULL;
		if ($result != NULL )
		{		
			if ($type == "userList") 
			{
				while ( $row = $this->dbc->fetchObject($result) )
				{
					if (isset($row->type) && $row->type == "image")
					{
						$str .= $this->getImageXMLItem($row);
					}
					else
					{
						$str .= $this->getUserXMLItem($row);
					}
					

				}
			}
			else if ($type == "userPastLocations") 
			{				
				while ( $row = $this->dbc->fetchObject($result) )
				{
					$row->latitude = isset($row->latitude) ? $row->latitude : null;
					$row->longitude = isset($row->longitude) ? $row->longitude : null;
					$row->altitude = isset($row->altitude) ? $row->altitude : null;
					$row->dataArrivedTime = isset($row->dataArrivedTime) ? $row->dataArrivedTime : null;
					$row->deviceId = isset($row->deviceId) ? $row->deviceId : null;
					
					$str .= '<location latitude="'.$row->latitude.'"  longitude="'. $row->longitude .'" altitude="'.$row->altitude.'" >'
								.'<time>'. $row->dataArrivedTime .'</time>'
								.'<deviceId>'. $row->deviceId .'</deviceId>'
							.'</location>';
				}						
			}	
			else if($type == "imageList")
			{
				while ( $row = $this->dbc->fetchObject($result) )
				{
					$str .= $this->getImageXMLItem($row);
				}				
			}	
		}
		
		header("Content-type: application/xml; charset=utf-8");
		
		$pageNo = $pageCount == 0 ? 0 : $pageNo;
		
		$pageStr = 'pageNo="'.$pageNo.'" pageCount="' . $pageCount .'"' ;
		
		if ($this->pastPointsFetchedUserId != NULL) {
			$pageStr .= ' userId="'.$this->pastPointsFetchedUserId.'"';
		}
		if ( $type == "imageList" || $this->includeImageInUpdatedUserListReq == true)
		{
			$pageStr.= ' thumbSuffix="&amp;thumb=ok" origSuffix="" ';
		}
		
		$out = '<?xml version="1.0" encoding="UTF-8"?>'
				.'<page '. $pageStr . ' >'					
					. $str
			   .'</page>';

		return $out;		
	}	
	
	private function getImageXMLItem($row)
	{
		$row->latitude = isset($row->latitude) ? $row->latitude : null;
		$row->longitude = isset($row->longitude) ? $row->longitude : null;
		$row->altitude = isset($row->altitude) ? $row->altitude : null;
		$row->uploadTime = isset($row->uploadTime) ? $row->uploadTime : null;
		$row->Id = isset($row->Id) ? $row->Id : null;
		$row->userId = isset($row->userId) ? $row->userId : null;
		$row->realname = isset($row->realname) ? $row->realname : null;


		$str = '<image url="'. $this->imageHandlerURL .'/'. urlencode('?action='. $this->actionPrefix .'GetImage&imageId='. $row->Id) .'"   id="'. $row->Id  .'" byUserId="'. $row->userId .'" byRealName="'. $row->realname .'" altitude="'.$row->altitude.'" latitude="'. $row->latitude.'"	longitude="'. $row->longitude .'"  time="'.$row->uploadTime.'"/>';

		return $str;
	}
	
	private function getUserXMLItem($row)
	{
		$row->Id = isset($row->Id) ? $row->Id : null;
//		$row->username = isset($row->username) ? $row->username : null;
		$row->isFriend = isset($row->isFriend) ? $row->isFriend : 0; 
		$row->realname = isset($row->realname) ? $row->realname : null;
		$row->latitude = isset($row->latitude) ? $row->latitude : null;
		$row->longitude = isset($row->longitude) ? $row->longitude : null;
		$row->altitude = isset($row->altitude) ? $row->altitude : null;
		$row->dataArrivedTime = isset($row->dataArrivedTime) ? $row->dataArrivedTime : null;
		$row->message = isset($row->message) ? $row->message : null;
		$row->deviceId = isset($row->deviceId) ? $row->deviceId : null;
		$row->status_message = isset($row->status_message) ? $row->status_message : null;
			
		$str = '<user>'
		. '<Id isFriend="'.$row->isFriend.'">'. $row->Id .'</Id>'
//		. '<username>' . $row->username . '</username>'
		. '<realname>' . $row->realname . '</realname>'
		. '<location latitude="' . $row->latitude . '"  longitude="' . $row->longitude . '" altitude="' . $row->altitude . '" />'
		. '<time>' . $row->dataArrivedTime . '</time>'
		. '<message>' . $row->message . '</message>'
		. '<status_message>' . $row->status_message . '</status_message>'
		. '<deviceId>' . $row->deviceId . '</deviceId>'
		.'</user>';
		
		return $str;
	}
	
}
?>