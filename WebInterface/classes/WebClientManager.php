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
	
	private $authenticator = NULL; // authenticator object
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
	
	public function setAuthenticator($authenticator){
		$this->authenticator = $authenticator;
	}
	public function setImageRelatedVars($imageDirectory, $missingImage, $imageHandlerURL){
		$this->imageDirectory = $imageDirectory;
		$this->missingImage = $missingImage;
		$this->imageHandlerURL = $imageHandlerURL; 
	}
	public function process($reqArray, $dataFetchedTime="", $imageFetchedTime="") 
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
					$out = $this->authenticator->sendNewPassword($reqArray['email']);		
				}		
				break;
			case $this->actionPrefix . "ChangePassword":
				$out = $this->changePassword($reqArray);
				break;	
			case $this->actionPrefix . "GetUserList":
				$out = $this->getUserList($reqArray, $this->elementCountInAPage, "userListReq");
				break;
			case $this->actionPrefix . "SearchUser":
				$out = $this->searchUser($reqArray);
				break;		
			case $this->actionPrefix . "UpdateUserList":
				$out = $this->getUserList($reqArray, $this->elementCountInLocationsPage,"userListReq");
				break;		
			case $this->actionPrefix . "GetUpdatedUserList":
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
				$out = $this->getUserList($reqArray, $this->elementCountInLocationsPage, "updatedUserListReq");
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
			default:
				
				$out = UNSUPPORTED_ACTION;
				
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
			
			if ($this->authenticator !== null && 
			    $this->authenticator->authenticateUser($reqArray['username'], md5($reqArray['password']), $keepUserLoggedIn) !== null) {
				$out = SUCCESS;						
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
		 		$out = $this->usermanager->inviteUser($email);		 	
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
		 			
		 	$email = $this->checkVariable($reqArray['email']);
		 	$name = $this->checkVariable($reqArray['name']);
		 	$password = $this->checkVariable($reqArray['password']);		 	
		 	$out = $this->usermanager->registerUser($email, $name, $password);			
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
				$out = $this->authenticator->changePassword($newPassword, $currentPassword);		
			}
		}
		return $out;
	}
	
	private function isUserAuthenticated() {
		$authenticated = false;
		if ($this->authenticator !== null &&
			$this->authenticator->isUserAuthenticated() == true) 
		{
			$authenticated = true;
		}
		return $authenticated;
	}
	
	private function getUserList($reqArray, $elementCountInAPage, $req='updatedUserListReq') 
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
									  WHERE unix_timestamp(u.uploadTime) >= ' .$this->imageFetchedTime;
					
					$sqlImagePageCountUnion = 'UNION
											   SELECT
													count(Id)
											   FROM '. $this->tablePrefix .'_upload u
											   WHERE 
											   		unix_timestamp(uploadTime) >= ' .$this->imageFetchedTime;
				}
				
				$sql = 'SELECT
							Id, null as userId, latitude, longitude, altitude, 
							realname, deviceId, date_format(dataArrivedTime,"%d %b %Y %T") as dataArrivedTime, 
							(unix_timestamp(dataArrivedTime) - '.$this->dataFetchedTime.') as timeDif,
							"user" as type
						FROM '
							. $this->tablePrefix .'_users
						WHERE
							unix_timestamp(dataArrivedTime) >= '. $this->dataFetchedTime .'		
						'. $sqlImageUnion .'						
						ORDER BY
							timeDif 
							DESC	
						LIMIT '. $offset .',' 
							   .$elementCountInAPage;
				
				$sqlPageCount = 'SELECT ceil(sum(itemCount)/'.$elementCountInAPage.')
								 FROM
									(SELECT
										count(Id) as itemCount
								 	 FROM '
							 			. $this->tablePrefix .'_users
							 	 	 WHERE 
							 			unix_timestamp(dataArrivedTime) >= ' . $this->dataFetchedTime .'
							 		  '. $sqlImagePageCountUnion .'
							 		  ) t';
							
			}
			else //if ($req == 'userListReq') 
			{
				// this is the user list showing in left pane
				
				$sql = 'SELECT
							Id, latitude, longitude, altitude, 
							realname, deviceId, date_format(dataArrivedTime,"%d %b %Y %T") as dataArrivedTime
						FROM '
							. $this->tablePrefix .'_users
						ORDER BY
							realname 							
						LIMIT ' . $offset . ',' 
								. $elementCountInAPage;
							
				$sqlPageCount = 'SELECT
									ceil(count(Id)/'.$elementCountInAPage.')
								 FROM '
							 		. $this->tablePrefix .'_users';
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
			
				$sql = //sprintf(
							'SELECT 
									Id, latitude, longitude, altitude, 
									realname, deviceId, date_format(dataArrivedTime,"%d %b %Y %T") as dataArrivedTime
								FROM '
									. $this->tablePrefix .'_users								
								WHERE
									realname like "%'. $search .'%"
								ORDER BY
									realname
								LIMIT '. $offset .' , '. $this->elementCountInAPage ;
							
						//);
						
				$sqlItemCount = 'SELECT
			 						ceil(count(Id)/'.$this->elementCountInAPage.')
			 					 FROM '
			 					 	. $this->tablePrefix .'_users
								WHERE
									realname like "%'. $search .'%"';
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
			if ($this->isUserAuthenticated() == true){
				$userId = (int) $reqArray['userId'];
				$this->pastPointsFetchedUserId = $userId;
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
						 	userId = '. $userId . '
						 ORDER BY 
						 	Id DESC
						 LIMIT '. $offset . ','
								. $elementCountInAPage;
								
				// subtract 1 to not get the last location into consideration								
				$sqlItemCount = 'SELECT 
									ceil((count(Id)-1)/ '.$elementCountInAPage .')
								 FROM '. $this->tablePrefix .'_user_was_here
								 wHERE 
								 	userId = '. $userId . '' ;				
				
				$out = $this->prepareXML($sql, $pageNo, $this->dbc->getUniqueField($sqlItemCount), "userPastLocations");
			}
		}
		return $out;
	}
	
	private function getImageList($reqArray, $elementCountInAPage){
		$out = UNAUTHORIZED_ACCESS;
		if ($this->isUserAuthenticated() == true)
		{
			$out = FAILED;
			$pageNo = 1;
			if (isset($reqArray['pageNo']) && $reqArray['pageNo'] > 0) {
					$pageNo = (int) $reqArray['pageNo'];
			}
			$offset = ($pageNo - 1) * $elementCountInAPage;
			
			$sql = 'SELECT 
								u.Id, u.userId, usr.realname, u.latitude, 
								u.altitude, u.longitude, date_format(u.uploadTime,"%d %b %Y %H:%i") uploadTime
							FROM '. $this->tablePrefix . '_upload u
							LEFT JOIN '. $this->tablePrefix .'_users usr
							ON  
								usr.Id = u.userId
							ORDER BY 
								u.Id 
							DESC
							LIMIT 
							' . $offset . ',' . $elementCountInAPage;
			
			$sqlItemCount = 'SELECT
			 						ceil(count(Id)/'.$elementCountInAPage.')
			 					 FROM '
			 					 	. $this->tablePrefix .'_upload';
			 					 	
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
		$userId = null;
		$realname = null;
		$pageNo = 1;
		if (isset($reqArray['pageNo']) && $reqArray['pageNo'] > 0) {
					$pageNo = (int) $reqArray['pageNo'];
		}
		$offset = ($pageNo - 1) * $elementCountInAPage;
		$out = MISSING_PARAMETER;	
		if (isset($reqArray['userId']) && !empty($reqArray['userId'])){
			$userId = (int) $reqArray['userId'];
			$sql = 'SELECT 
						u.Id, u.userId, usr.realname, u.latitude, 
						u.altitude, u.longitude, date_format(u.uploadTime,"%d %b %Y %H:%i") uploadTime
					FROM '. $this->tablePrefix . '_upload u
					LEFT JOIN '. $this->tablePrefix .'_users usr
					ON  usr.Id = u.userId
					WHERE u.userId = '. $userId .' 
					LIMIT ' . $offset . ',' . $elementCountInAPage;
			
			$sqlItemCount = 'SELECT
			 						ceil(count(Id)/'.$elementCountInAPage.')
			 				  FROM '. $this->tablePrefix .'_upload
			 				  WHERE userId = '. $userId .'';
		}
		else if (isset($reqArray['realname']) && !empty($reqArray['realname'])){
			$realname = $this->checkVariable($reqArray['realname']);
			
			$sql = 'SELECT 
						u.Id, u.userId, usr.realname, u.latitude, 
						u.altitude, u.longitude, date_format(u.uploadTime,"%d %b %Y %H:%i") uploadTime
					FROM '. $this->tablePrefix . '_upload u
					LEFT JOIN '. $this->tablePrefix .'_users usr
					ON  usr.Id = u.userId
					WHERE usr.realname like "%'. $realname .'%"
					ORDER BY u.Id
					DESC
					LIMIT ' . $offset . ',' . $elementCountInAPage;
			
			$sqlItemCount = 'SELECT
			 						ceil(count(u.Id)/'.$elementCountInAPage.')
			 				  FROM '. $this->tablePrefix .'_upload u
			 				  LEFT JOIN '. $this->tablePrefix .'_users usr
							  ON  usr.Id = u.userId
							  WHERE usr.realname like "%'. $realname .'%"';
			
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
			if ($this->isUserAuthenticated() == true)
			{
				$thumbCreator = new ImageOperator($this->imageDirectory, $this->missingImage);
				$out = $thumbCreator->deleteImage($imageId);
				
				$sql = sprintf ('DELETE FROM traceper_upload
				                 WHERE id = %d 
				                 LIMIT 1', $imageId );
				
			 	$out = FAILED;
			    if	($this->dbc->query($sql) != false ) 
			    {
			    	$out = SUCCESS;    	
			    	if(file_exists($orimg_path) == true)
			    	{
			    		if (unlink($orimg_path) != true){
			    			$out = FAILED;			    			
			    		}
			    	}
			    	if (file_exists($thumbimg_path) == true)
			    	{
			    		if (unlink($thumbimg_path) != true){
			    			$out = FAILED;
			    		}
			    	}   	
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
		$row->realname = isset($row->realname) ? $row->realname : null;
		$row->latitude = isset($row->latitude) ? $row->latitude : null;
		$row->longitude = isset($row->longitude) ? $row->longitude : null;
		$row->altitude = isset($row->altitude) ? $row->altitude : null;
		$row->dataArrivedTime = isset($row->dataArrivedTime) ? $row->dataArrivedTime : null;
		$row->message = isset($row->message) ? $row->message : null;
		$row->deviceId = isset($row->deviceId) ? $row->deviceId : null;
			
		$str = '<user>'
		. '<Id>'. $row->Id .'</Id>'
//		. '<username>' . $row->username . '</username>'
		. '<realname>' . $row->realname . '</realname>'
		. '<location latitude="' . $row->latitude . '"  longitude="' . $row->longitude . '" altitude="' . $row->altitude . '" />'
		. '<time>' . $row->dataArrivedTime . '</time>'
		. '<message>' . $row->message . '</message>'
		. '<deviceId>' . $row->deviceId . '</deviceId>'
		.'</user>';
		
		return $str;
	}
	
}
?>