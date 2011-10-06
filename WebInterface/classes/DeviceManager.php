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
				case $this->actionPrefix . "AuthenticateMe":
					$out = $this->authenticateUser($reqArray);
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
	
	private function authenticateUser($reqArray){
		$out = MISSING_PARAMETER;
		if (isset($reqArray['email']) && $reqArray['email'] != NULL
			&& isset($reqArray['password']) && $reqArray['password'] != NULL)
		{
			$email = $this->checkVariable($reqArray['email']);
			$password = $this->checkVariable($reqArray['password']);
			
			$sql = sprintf('SELECT Id
							FROM '. $this->tablePrefix .'_users
							WHERE email = "%s" AND
								  password = "%s"
							LIMIT 1', $email, $password);
			
			$userId = $this->dbc->getUniqueField($sql);
			$out = UNAUTHORIZED_ACCESS;
			if ($userId != NULL) {
				$out = SUCCESS;
			}			
				
		}
		return $out;
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
				
				$publicData = 0;
				if (isset($reqArray['publicData']) && $reqArray['publicData'] != NULL) {
					$tmp = (int) $this->checkVariable($reqArray['publicData']);
					if ($tmp == 1) {
						$publicData = 1;
					}
				}
			
				$sql = sprintf('INSERT INTO '
									. $this->tablePrefix .'_upload
									(userId, latitude, longitude, altitude, uploadtime, publicData)
								SELECT Id, %s, %s, %s, NOW(), %d
									FROM '. $this->tablePrefix .'_users
								WHERE email = "%s" AND
									  password = "%s"
								LIMIT 1', $latitude, $longitude, $altitude, $publicData,
									$email, $password);
				if ($this->dbc->query($sql))
				{
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
		$out = MISSING_PARAMETER;
		if (isset($reqArray['latitude']) && $reqArray['latitude'] != NULL
			&& isset($reqArray['longitude']) && $reqArray['longitude'] != NULL
			&& isset($reqArray['altitude']) && $reqArray['altitude'] != NULL
			&& isset($reqArray['email']) && $reqArray['email'] != NULL
			&& isset($reqArray['password']) && $reqArray['password'] != NULL
			&& isset($reqArray['deviceId']) && $reqArray['deviceId'] != NULL
			&& isset($reqArray['time']) && $reqArray['time'] != NULL
		)
		{
			$status_message = NULL;
			$status_message_query = '';
			
			if (isset($reqArray['status_message']) && $reqArray['status_message'] != NULL){
				$status_message = $this->checkVariable($reqArray['status_message']);
				$status_message_query = sprintf(', status_message = "%s",
						 		 	             status_source = %d,
						 		 	             status_message_time = NOW()',$status_message, STATUS_MESSAGE_SOURCE_MOBILE);
			}
			//rounding takes place in database			
			$latitude = (float) $reqArray['latitude'];
			$longitude = (float) $reqArray['longitude'];
			$altitude = (float) $reqArray['altitude'];
			$email = $this->checkVariable($reqArray['email']);
			$password = $this->checkVariable($reqArray['password']);
			$deviceId = $this->checkVariable($reqArray['deviceId']);
			$calculatedTime = $this->checkVariable($reqArray['time']);
			$calculatedTime = date('Y-m-d H:i:s', $calculatedTime);

			$sql = sprintf('SELECT Id
								FROM '. $this->tablePrefix.'_users 
							WHERE email = "%s" 
						  		  AND 
						  		  password = "%s"
							LIMIT 1', $email, $password);
			
			$userId = $this->dbc->getUniqueField($sql);
			
			$out = UNAUTHORIZED_ACCESS;
			if ($userId != null) 
			{
			
				//only update the location of the users whose location changed
				$sql = sprintf('UPDATE '
									. $this->tablePrefix .'_users 
								SET
								  	latitude = %f , '
								  .'	longitude = %f , '
								  .'	altitude = %f ,	'						 	
								  .'	dataArrivedTime = NOW(), '
								  .'	deviceId = "%s"	,'
								  .'    dataCalculatedTime = "%s" '
								  .	$status_message_query 						 	
							   .' WHERE '
								  .' Id = %d '
							   .' LIMIT 1;', 
							   $latitude, $longitude, $altitude, $deviceId, $calculatedTime, $userId);
				
							   
				$sqlWasHere = sprintf('INSERT INTO '
										. $this->tablePrefix . '_user_was_here
											(userId, latitude, longitude, altitude, dataArrivedTime, deviceId, dataCalculatedTime)
		    							 VALUES(%d,	%f, %f, %f, NOW(), "%s", "%s") 
										',
										$userId, $latitude, $longitude, $altitude, $deviceId, $calculatedTime);			   
				
				$out = FAILED;
			
				if ($this->dbc->query($sql)) {						
						
					if ($this->dbc->getAffectedRows() === 1) 
					{
						if ($this->dbc->query($sqlWasHere)) 
						{							
							$out = SUCCESS;					
							if ($status_message != NULL) {
								$locationId = $this->dbc->lastInsertId();
								
								$sql = 	sprintf('INSERT INTO ' . $this->tablePrefix .'_status_messages
							    	         (status_message, status_source, date_time, userId, locationId)
							    	         VALUES ("%s", %d, NOW(),%d)',
							    		      $status_message, STATUS_MESSAGE_SOURCE_MOBILE, $userId, $locationId);
							    
							    $out = FAILED;
							    if ($this->dbc->query($sql)){
							    	$out = SUCCESS;
							    }								    
							}
						}
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