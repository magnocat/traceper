<?php
/********************************************
*
*	Filename:	DeviceManager.php
*	Author:		Ahmet Oguz Mermerkaya
*	E-mail:		ahmetmermerkaya@hotmail.com
*	Begin:		Tuesday, April 21, 2009  12:31
*
*********************************************/
require_once('Manager.php');

class DeviceManager extends Manager
{
	private $dbc = NULL;
	private $actionPrefix;
	private $tablePrefix = NULL;	
	private $locationResolution = 6;
	
	public function __construct($dbc, $actionPrefix, $tablePrefix) {
		$this->dbc = $dbc;
		$this->actionPrefix = $actionPrefix;
		$this->tablePrefix = $tablePrefix;
		
	}
	/**
	 * 
	 * @param $reqArray
	 * @return unknown_type
	 */
	public function process($reqArray) {
		
		$out = NULL;
		if (isset($reqArray['action']))
		{			
			switch($reqArray['action']) 
			{
				case $this->actionPrefix . "TakeMyLocation":
					
					$out = $this->updateUserLocation($reqArray);										
				
					break;
				case $this->actionPrefix . "RegisterMe":
				
					$out = $this->registerUser($reqArray);
				
					break;
				case $this->actionPrefix . "UnregisterMe":
				    //this action is not supported, may
				    //$out = $this->unregisterUser($reqArray);
					break;
				default:
					$out = UNSUPPORTED_ACTION;
					break;
			}
		}
		else
		{
			$out = MISSING_PARAMETER;
		}
		
		return $out;		
	}
	/**
	 * 
	 * @param $reqArray
	 * @return unknown_type
	 */	
	private function updateUserLocation($reqArray)
	{
		if (isset($reqArray['latitude']) && $reqArray['latitude'] != NULL
			&& isset($reqArray['longitude']) && $reqArray['longitude'] != NULL
			&& isset($reqArray['altitude']) && $reqArray['altitude'] != NULL
			&& isset($reqArray['username']) && $reqArray['username'] != NULL
			&& isset($reqArray['password']) && $reqArray['password'] != NULL
			&& isset($reqArray['deviceId']) && $reqArray['deviceId'] != NULL
		)
		{
			//rounding takes place in database			
			$latitude = (float) $reqArray['latitude'];
			$longitude = (float) $reqArray['longitude'];
			$altitude = (float) $reqArray['altitude'];
			$username = $this->checkVariable($reqArray['username']);
			$password = $this->checkVariable($reqArray['password']);
			$deviceId = $this->checkVariable($reqArray['deviceId']);

			//only update the location of the users whose location changed
			$sql = sprintf("UPDATE "
								. $this->tablePrefix ."_users 
							SET
							 	latitude = %f , 
							 	longitude = %f ,
							 	altitude = %f ,							 	
							 	dataArrivedTime = NOW(),
							 	deviceId = '%s'							 	
							WHERE 
								username = '%s' 
								AND 
								password = '%s'
							LIMIT 1;", 
						   $latitude, $longitude, $altitude, $deviceId, $username, $password,
						   $latitude, $longitude, $altitude );


			$out = FAILED;
			if ($this->dbc->query($sql)) {
				$out = SUCCESS;
			}
		}
		else {
			$out = MISSING_PARAMETER;
		}
		return $out;		
	}
	/**
	 * 
	 * @param $reqArray
	 * @return unknown_type
	 */
	private function registerUser($reqArray)
	{		
		if (isset($reqArray['realname']) && $reqArray['realname'] != NULL 
			&& isset($reqArray['email']) && $reqArray['email'] != NULL     
			&& isset($reqArray['username']) && $reqArray['username'] != NULL 
			&& isset($reqArray['password']) && $reqArray['password'] != NULL 
			&& isset($reqArray['im']) && $reqArray['im'] != NULL 				
			)
		{
			
			$realname = $this->checkVariable($reqArray['realname']);
			$email = $this->checkVariable($reqArray['email']);					
			$username = $this->checkVariable($reqArray['username']);
			$password = $this->checkVariable($reqArray['password']);
			$im = $this->checkVariable($reqArray['im']);	
			
			$sql = sprintf("INSERT INTO "
								. $this->tablePrefix ."_users
								  (username, password, realname, email, im ) 
							VALUES
							('%s',      '%s',      '%s',      '%s',   '%s');",
							 $username,	$password, $realname, $email, $im);
			$out = FAILED;
			
			if ($this->dbc->query($sql)) {
				$out = SUCCESS;
			}	
			else if ($this->dbc->getErrorNo() == DB_ERROR_CODES::DB_KEY_DUPLICATE) {				
				$out = USER_NAME_ALREADY_EXIST;
			}			 							 
		}
		else {
			$out = MISSING_PARAMETER;
		}
		return $out;		
	}
	
	/**
	 * 
	 * @param $reqArray
	 * @return unknown_type
	 */
	private function unregisterUser($reqArray){
		
		if (isset($reqArray['username']) && $reqArray['username'] != NULL 
			&& isset($reqArray['password']) && $reqArray['password'] != NULL
		   )
		{
			$username = $this->checkVariable($reqArray['username']);
			$password = $this->checkVariable($reqArray['password']);
			
			$sql = sprintf("DELETE FROM ".
								$this->tablePrefix . "_users
							WHERE 
								username = '%s'
								AND
								password = '%s'
							LIMIT 1;", 
							$username, $password);
			
			$out = FAILED;
			if ($this->dbc->query($sql)) {
				$out = SUCCESS;
			}						
		}
		else {
			$out = MISSING_PARAMETER;
		}
		return $out;				
		
	}
}
?>