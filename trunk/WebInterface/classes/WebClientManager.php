<?php
/********************************************
*
*	Filename:	WebClientManager.php
*	Author:		Ahmet Oguz Mermerkaya
*	E-mail:		ahmetmermerkaya@hotmail.com
*	Begin:		Tuesday, April 21, 2009  12:32
*
*********************************************/
require_once('Manager.php');


class WebClientManager extends Manager
{
	private $dbc = NULL;
	private $actionPrefix = NULL;
	private $tablePrefix = NULL;	
	private $elementCountInAPage = 10;
	private $elementCountInLocationsPage = 50;
	private $dataFetchedTime;
	private $pastPointsFetchedUserId = NULL;
	
	public function __construct($dbc, $actionPrefix, $tablePrefix, $elementCountInAPage, $elementCountInLocationsPage) 
	{
		$this->dbc = $dbc;
		$this->actionPrefix = $actionPrefix;
		$this->tablePrefix = $tablePrefix;
		$this->elementCountInAPage = $elementCountInAPage;
		$this->elementCountInLocationsPage = $elementCountInLocationsPage;	
	}
	
	public function process($reqArray, $dataFetchedTime="") 
	{
		$out = NULL;
		switch($reqArray['action']) 
		{
			case $this->actionPrefix . "AuthenticateUser":
				
				$out = $this->authenticateUser($reqArray);
					
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
				$this->dataFetchedTime = &$dataFetchedTime;				
				$out = $this->getUserList($reqArray, $this->elementCountInLocationsPage,"updatedUserListReq");
				
				break;
			case $this->actionPrefix . "GetUserPastPoints":
				
				$out = $this->getUserPastPoints($reqArray,  $this->elementCountInLocationsPage);
				
				break;				
			default:
				
				$out = UNSUPPORTED_ACTION;
				
				break;
		}
		return $out;				
	}
	
	private function authenticateUser($reqArray)
	{
		//TODO: add related code when authenticate user is activated
		$out = MISSING_PARAMETER;
		if (isset($reqArray['username']) && $reqArray['username'] != null &&
			isset($reqArray['password']) && $reqArray['password'] != null
		    )
		{
			$out = SUCCESS;
		}
		
		return $out;
	}
	
	private function isUserAuthenticated() {
		//TODO: add related code when authenticate user is activated
		return true;
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
				$sql = 'SELECT
							Id, username, latitude, longitude, altitude, 
							realname, deviceId, date_format(dataArrivedTime,"%d %b %Y %T") as dataArrivedTime, (unix_timestamp(dataArrivedTime) - '.$this->dataFetchedTime.') as timeDif
						FROM '
							. $this->tablePrefix .'_users
						WHERE
							unix_timestamp(dataArrivedTime) >= '. $this->dataFetchedTime .'
						ORDER BY
							timeDif 
							DESC	
						LIMIT '. $offset .',' 
							   .$elementCountInAPage;
				
				$sqlPageCount = 'SELECT
									ceil(count(Id)/'.$elementCountInAPage.')
								 FROM '
							 		. $this->tablePrefix .'_users
							 	WHERE 
							 		unix_timestamp(dataArrivedTime) >= ' . $this->dataFetchedTime;
							
			}
			else //if ($req == 'userListReq') 
			{
				// this is the user list showing in left pane
				
				$sql = 'SELECT
							Id, username, latitude, longitude, altitude, 
							realname, deviceId, date_format(dataArrivedTime,"%d %b %Y %T") as dataArrivedTime, null
						FROM '
							. $this->tablePrefix .'_users
						ORDER BY
							username 
						LIMIT ' . $offset . ',' 
								. $elementCountInAPage;
							
				$sqlPageCount = 'SELECT
									ceil(count(Id)/'.$elementCountInAPage.')
								 FROM '
							 		. $this->tablePrefix .'_users';
			}	
						
//			if (isset($reqArray['trackedUser']) && $reqArray['trackedUser'] != null) 
//			{
//				$trackedUser = (int) $reqArray['trackedUser'];				
//				
//				$sql =			'(' 
//								  . $sql . 
//								')
//								union
//								( SELECT 
//									Id, username, latitude, longitude, altitude, 
//									realname, deviceId, date_format(dataArrivedTime,"%d %b %Y %T") as dataArrivedTime, null
//								  FROM '
//									. $this->tablePrefix .'_users
//								  WHERE 
//						 			Id = '. $trackedUser .'						 		  
//						 			LIMIT 1
//						 		 )' ;	
//			}	
			
			$pageCount = $this->dbc->getUniqueField($sqlPageCount);
			// data fetched time is used only in updated User list req so it is 
			// updated only when $req == 'updatedUserListReq'		 							 								
			if ($req == 'updatedUserListReq' 
				&& $pageNo == $pageCount
				&& $pageCount != 0) 
			{
				$this->dataFetchedTime = time();
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
									Id, username, latitude, longitude, altitude, 
									realname, deviceId, date_format(dataArrivedTime,"%d %b %Y %T") as dataArrivedTime
								FROM '
									. $this->tablePrefix .'_users								
								WHERE
									username like "%'. $search .'%"
									OR
									realname like "%'. $search .'%"
								ORDER BY
									username
								LIMIT '. $offset .' , '. $this->elementCountInAPage ;
							
						//);
						
				$sqlItemCount = 'SELECT
			 						ceil(count(Id)/'.$this->elementCountInAPage.')
			 					 FROM '
			 					 	. $this->tablePrefix .'_users
								WHERE
									username like "%'. $search .'%"
									OR
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
	
	/**
	 * this function generates xml that is used when getting user list or user past locations
	 * params: $type may be "userList" or "userPastLocations"
	 */
	private function prepareXML($sql, $pageNo, $pageCount, $type="userList")
	{		
		$result = NULL;
		// if page count equal to 0 then there is no need to run query
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
					$row->Id = isset($row->Id) ? $row->Id : null;
					$row->username = isset($row->username) ? $row->username : null;
					$row->realname = isset($row->realname) ? $row->realname : null;
					$row->latitude = isset($row->latitude) ? $row->latitude : null;
					$row->longitude = isset($row->longitude) ? $row->longitude : null;
					$row->altitude = isset($row->altitude) ? $row->altitude : null;
					$row->dataArrivedTime = isset($row->dataArrivedTime) ? $row->dataArrivedTime : null;
					$row->message = isset($row->message) ? $row->message : null;
					$row->deviceId = isset($row->deviceId) ? $row->deviceId : null;

					$str .= '<user>'
					. '<Id>'. $row->Id .'</Id>'
					. '<username>' . $row->username . '</username>'
					. '<realname>' . $row->realname . '</realname>'
					. '<location latitude="' . $row->latitude . '"  longitude="' . $row->longitude . '" altitude="' . $row->altitude . '" />'
					. '<time>' . $row->dataArrivedTime . '</time>'
					. '<message>' . $row->message . '</message>'
					. '<deviceId>' . $row->deviceId . '</deviceId>'
					.'</user>';

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
		}
		header("Content-type: application/xml; charset=utf-8");
		
		$pageNo = $pageCount == 0 ? 0 : $pageNo;
		
		$pageStr = 'pageNo="'.$pageNo.'" pageCount="' . $pageCount .'"' ;
		
		if ($this->pastPointsFetchedUserId != NULL) {
			$pageStr .= ' userId="'.$this->pastPointsFetchedUserId.'"';
		}
		
		
		$out = '<?xml version="1.0" encoding="UTF-8"?>'
				.'<page '. $pageStr . ' >'					
					. $str
			   .'</page>';

		return $out;		
	}	
	
}
?>