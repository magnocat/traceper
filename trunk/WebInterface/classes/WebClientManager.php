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
	
	public function __construct($dbc, $actionPrefix, $tablePrefix, $elementCountInAPage, $elementCountInLocationsPage) 
	{
		$this->dbc = $dbc;
		$this->actionPrefix = $actionPrefix;
		$this->tablePrefix = $tablePrefix;
		$this->elementCountInAPage = $elementCountInAPage;
		$this->elementCountInLocationsPage = $elementCountInLocationsPage;	
	}
	
	public function process($reqArray) 
	{
		$out = NULL;
		switch($reqArray['action']) 
		{
			case $this->actionPrefix . "AuthenticateUser":
				
				$out = $this->authenticateUser($reqArray);
					
				break;
			case $this->actionPrefix . "GetUserList":
				
				$out = $this->getUserList($reqArray, $this->elementCountInAPage);
				
				break;
			case $this->actionPrefix . "SearchUser":
				
				$out = $this->searchUser($reqArray);
				
				break;		
			case $this->actionPrefix . "UpdateUserList":
				
				$out = $this->getUserList($reqArray, $this->elementCountInLocationsPage);
				
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
		
	
	//TODO: merging getUserList and getLocations
	private function getUserList($reqArray, $elementCountInAPage) 
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
		
			$sql = 'SELECT
						Id, username, latitude, longitude, altitude, realname, deviceId, dataArrivedTime
					FROM '
						. $this->tablePrefix .'_users
					ORDER BY
						username	
					LIMIT ' . $offset . ',' 
							. $elementCountInAPage;
							
			if (isset($reqArray['trackedUser']) && $reqArray['trackedUser'] != null) 
			{
				$sql = sprintf('(' 
								  . $sql . 
								')
								union
								( SELECT 
									Id, username, latitude, longitude, altitude, realname, deviceId, dataArrivedTime
								  FROM '
									. $this->tablePrefix .'_users
								  WHERE 
						 			Id = %d
						 		  LIMIT 1
						 		 )', $reqArray['trackedUser']);	
			}	
						
			$sqlItemCount = 'SELECT
								ceil(count(Id)/'.$elementCountInAPage.')
							 FROM '
						 		. $this->tablePrefix .'_users';
					 							 		
			$out = $this->prepareXML($sql, $pageNo, $this->dbc->getUniqueField($sqlItemCount));
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
									Id, username, latitude, longitude, altitude, realname, deviceId, dataArrivedTime
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
	
	private function prepareXML($sql, $pageNo, $pageCount)
	{		
		$result = $this->dbc->query($sql);
		$str = NULL;
		
		if ($result)
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
		header("Content-type: application/xml; charset=utf-8");
		$out = '<?xml version="1.0" encoding="UTF-8"?>
				<page pageNo="'.$pageNo.'" pageCount="' . $pageCount . '">'					
					. $str
			   .'</page>';

		return $out;		
	}	
	
}
?>