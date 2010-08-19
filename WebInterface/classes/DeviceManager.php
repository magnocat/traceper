<?php
/********************************************
*
*	Filename:	DeviceManager.php
*	Author:		Ahmet Oguz Mermerkaya
*	E-mail:		ahmetmermerkaya@hotmail.com
*	Begin:		Tuesday, April 21, 2009  12:31
*
*********************************************/
require_once('Base.php');

class DeviceManager extends Base
{
	private $actionPrefix;
	private $tablePrefix = NULL;	
	private $locationResolution = 6;
	private $gpsMinDataSentInterval = 60000;
	private $gpsMinDistanceInterval = 100;
	private $uploadPath;
	private $usermanager = NULL;
	
	public function __construct($dbc, $actionPrefix, $tablePrefix, $gpsMinDataSentInterval,
	 							$gpsMinDistanceInterval) 
	{
		$this->dbc = $dbc;
		$this->actionPrefix = $actionPrefix;
		$this->tablePrefix = $tablePrefix;
		$this->gpsMinDataSentInterval = $gpsMinDataSentInterval;
		$this->gpsMinDistanceInterval = $gpsMinDistanceInterval;
		
	}
	
	public function setUserManager($usermanager){
		$this->usermanager = $usermanager;		
	}
	
	public function setUploadPath($uploadPath){
		$this->uploadPath = $uploadPath;
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
				case $this->actionPrefix . "GetImage":
					
					$out = $this->getImage($reqArray, $_FILES);	
//					move_uploaded_file($_FILES["image"]["tmp_name"], 'image.jpg');
					
					break;
				case $this->actionPrefix . "UnregisterMe":
				    //this action is not supported, may
				    //$out = $this->unregisterUser($reqArray);
			//		break;
				default:
					$out = UNSUPPORTED_ACTION;
					break;
			}
		}
		else
		{
			$out = MISSING_PARAMETER;
		}
		
		return $this->prepareXML($out);		
	}
	
	private function getImage($reqArray, $uploadedFile){
		$out = MISSING_PARAMETER;
		if (isset($uploadedFile["image"])
			&& isset($reqArray['latitude']) && $reqArray['latitude'] != NULL
			&& isset($reqArray['longitude']) && $reqArray['longitude'] != NULL
			&& isset($reqArray['altitude']) && $reqArray['altitude'] != NULL
			&& isset($reqArray['email']) && $reqArray['email'] != NULL
			&& isset($reqArray['password']) && $reqArray['password'] != NULL)
		{
			$out = FAILED;
			if ($uploadedFile["image"]["error"] == UPLOAD_ERR_OK )
			{
				$latitude = (float) $reqArray['latitude'];
				$longitude = (float) $reqArray['longitude'];
				$altitude = (float) $reqArray['altitude'];
				$email = $this->checkVariable($reqArray['email']);
				$password = $this->checkVariable($reqArray['password']);

				$sql = sprintf('INSERT INTO '
									. $this->tablePrefix .'_upload
									(userId, latitude, longitude, altitude, uploadtime)
								SELECT Id, %s, %s, %s, NOW()
									FROM '. $this->tablePrefix .'_users
								WHERE email = "%s" AND
									  password = "%s"
								LIMIT 1', $latitude, $longitude, $altitude,
									$email, $password);
				if ($this->dbc->query($sql)){
					if (move_uploaded_file($uploadedFile["image"]["tmp_name"], $this->uploadPath .'/'.$this->dbc->lastInsertId() . '.jpg'))
					{
						$out = SUCCESS; 						
					}
				}
			}
			
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
			&& isset($reqArray['email']) && $reqArray['email'] != NULL
			&& isset($reqArray['password']) && $reqArray['password'] != NULL
			&& isset($reqArray['deviceId']) && $reqArray['deviceId'] != NULL
		)
		{
			//rounding takes place in database			
			$latitude = (float) $reqArray['latitude'];
			$longitude = (float) $reqArray['longitude'];
			$altitude = (float) $reqArray['altitude'];
			$email = $this->checkVariable($reqArray['email']);
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
								email = '%s' 
								AND 
								password = '%s'
							LIMIT 1;", 
						   $latitude, $longitude, $altitude, $deviceId, $email, $password);
			
			$sqlWasHere = sprintf('INSERT INTO '
									. $this->tablePrefix . '_user_was_here
										(userId, latitude, longitude, altitude, dataArrivedTime, deviceId)
	    							SELECT Id, %f, %f, %f, NOW(), "%s" 
									FROM '. $this->tablePrefix.'_users 
									WHERE email = "%s" 
										  AND 
										  password = "%s"
									LIMIT 1',
									$latitude, $longitude, $altitude, $deviceId, $email, $password);			   
			
			$out = FAILED;
			if ($this->dbc->query($sql)) {	
				$out = SUCCESS;				
				if ($this->dbc->getAffectedRows() === 1) {
					$this->dbc->query($sqlWasHere);					
				}
				else {
					$out = UNAUTHORIZED_ACCESS;
				}
					
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
		$out = MISSING_PARAMETER;	
		if (isset($reqArray['realname']) && $reqArray['realname'] != NULL 
			&& isset($reqArray['email']) && $reqArray['email'] != NULL     
			&& isset($reqArray['password']) && $reqArray['password'] != NULL 
			)
		{
			
			$realname = $this->checkVariable($reqArray['realname']);
			$email = $this->checkVariable($reqArray['email']);					
			$password = $this->checkVariable($reqArray['password']);
			$out = FAILED;
			if ($this->usermanager != NULL) {
				$out = $this->usermanager->registerUser($email, $realname, $password);
			}							 							 
		}
		
		return $out;		
	}
	
	/**
	 * 
	 * @param $reqArray
	 * @return unknown_type
	 */
/*	
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
*/	
	private function prepareXML($result)
	{
		$out = '<?xml version="1.0" encoding="UTF-8"?>'
				.'<response>'
					.'<actionResult  value="'.$result.'"/>'
					.'<settings>'
						.'<minDataSentInterval value="'. $this->gpsMinDataSentInterval .'"/>'
						.'<minDistanceInterval value="'. $this->gpsMinDistanceInterval . '"/>'
					.'</settings>'
				.'</response>';
		return $out;
		
	}
}
?>