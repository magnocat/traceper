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
	private $userId;
	
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
				case $this->actionPrefix ."AuthenticateCar":
					$out = $this->authenticateCar($reqArray);
					break;	
				case $this->actionPrefix . "GetCarOptions":
					//TODO: needs refactoring...
					$out = $this->getCarOptions($reqArray);
					return '<options>'. $out .'</options>';
				case $this->actionPrefix . "SetCarServices":
					$out = $this->setCarServices($reqArray);
					break;
				case $this->actionPrefix . "RegisterMe":
					$out = $this->registerUser($reqArray);
					break;
				case $this->actionPrefix ."SetUserStatus":
					$out = $this->setUserStatus($reqArray);
					break;
				case $this->actionPrefix ."SetUserWithInDistance":
					$out = $this->setUserWithInDistance($reqArray);
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
			
			$sql = sprintf('SELECT Id, userIsValid, expiredate < NOW() as accountExpired
							FROM '. $this->tablePrefix .'_users
							WHERE email = "%s" AND
								  password = "%s"
							LIMIT 1', $email, $password);
			
			$result = $this->dbc->query($sql);
			$out = UNAUTHORIZED_ACCESS;
			if ($result != false) 
			{
				$row = $this->dbc->fetchObject($result);
				if ($row) 
				{		
					if ($row->userIsValid == "0") {
						$out = USER_NOT_VALIDATED;
					}	
					else if ($row->accountExpired == "1"){
						$out = USER_ACCOUNT_EXPIRED;
					}	
					else {
						$this->userId = $row->Id;
						$sql = sprintf('UPDATE '. $this->tablePrefix .'_users
										SET onlineStatus = 0
										WHERE email = "%s" AND
								  			password = "%s"
										LIMIT 1', $email, $password);
						$this->dbc->query($sql);
						$out = SUCCESS;
					}			
				}
			}
				
		}
		return $out;
	}
	
	private function authenticateCar($reqArray)
	{
		$out = MISSING_PARAMETER;
		if (isset($reqArray['car_login']) && $reqArray['car_login'] != NULL
			&& isset($reqArray['car_password']) && $reqArray['car_password'] != NULL
			&& isset($reqArray['email']) && $reqArray['email'] != NULL
			&& isset($reqArray['password']) && $reqArray['password'] != NULL)
		{
			$car_login = $this->checkVariable($reqArray['car_login']);
			$password = $this->checkVariable($reqArray['car_password']);
			
			$sql = sprintf('SELECT car_id, caroptions, caroptions1, caroptions2, car_phone
							FROM '. $this->tablePrefix .'_car
							WHERE car_login = "%s" AND
								  car_pw = "%s"
							LIMIT 1', $car_login, $password);
	
			$result = $this->dbc->query($sql);
			$out = UNAUTHORIZED_ACCESS;
			// below line makes this->userId ready
			$authResult = $this->authenticateUser($reqArray);
	
			if ($authResult == SUCCESS && ($row = $this->dbc->fetchObject($result)) != false)
			{
				$carId = $row->car_id;
				$userId = $this->userId;
				$carOptions = $row->caroptions;
				$carOptions1 = $row->caroptions1;
				$carOptions2 = $row->caroptions2;
				$carPhone = $row->car_phone;
				
				
				if ($carId != NULL) {

					// check if car is in use
					$sql = sprintf('SELECT Id
								FROM '.  $this->tablePrefix .'_users
								WHERE car_id = %d AND userIsValid = 1 AND Id != %d', $carId, $userId);

					$result = $this->dbc->query($sql);
					$numRows = $this->dbc->numRows($result);
					
					if ($numRows >= 1) {
						$out = CAR_IN_USE;
					}
					else {
						$email = $this->checkVariable($reqArray['email']);
						$password = $this->checkVariable($reqArray['password']);

						$userCarOptions = $carOptions;
						if ($userCarOptions == ""){
							$userCarOptions = $carOptions1;
						}
						else if ($carOptions1 != "") {
							$userCarOptions .= ',' . $carOptions1 ;	
						}
						
						if ($userCarOptions == ""){
							$userCarOptions = $carOptions2;
						}
						else if($carOptions2 != ""){
							$userCarOptions .= ',' . $carOptions2 ;	
						}
						//TODO: comma problem in all_options
						//update car_id, options field in user table
						$carPhoneSql = "";
						if ($carPhone != "") {
							$carPhoneSql = ' call_phone = "'.$carPhone.'" ';
						}
						else {
							$carPhoneSql = ' call_phone = user_phone ';
						}
						$sql = sprintf('UPDATE '. $this->tablePrefix .'_users
									SET car_id = %d, caroptions="%s" ,
										all_options = concat(user_options1, if(user_options1!="" && user_options2!="", ",", ""), user_options2, if(user_options1!="" || user_options2!="", ",", ""), "%s"),
										'. $carPhoneSql .'
									WHERE email = "%s" AND
								  		password = "%s"
									LIMIT 1', $carId, $userCarOptions, $userCarOptions, $email, $password);

						$this->dbc->query($sql);
						
						$allOptions = $this->dbc->query(sprintf('SELECT all_options 
														 FROM '. $this->tablePrefix.'_users
														 WHERE email = "%s" AND
								  							   password = "%s"
														  LIMIT 1', $email, $password));
						
						$sqlCarLog = sprintf('INSERT INTO ' 
									. $this->tablePrefix .'_car_log(all_options, userId, car_id, logdate)
								  VALUES("%s", %d, %d, NOW())', $allOptions, $userId, $carId);
						
						$this->dbc->query($sqlCarLog);
						$out = SUCCESS;
					}
				}
			}
				
		}
		return $out;
	}
	
	private function setUserWithInDistance($reqArray) 
	{
		$out = $this->authenticateUser($reqArray);
		if ($out == SUCCESS) {
			$out = MISSING_PARAMETER;
			if (isset($reqArray['withInDistance']) && $reqArray['withInDistance'] != NULL) 
			{
				$withInDistance = $this->checkVariable($reqArray['withInDistance']);
				$sql = sprintf('UPDATE ' . $this->tablePrefix . '_users
								SET user_withInDistance = "%s" 
								WHERE Id = %d 
								LIMIT 1', $withInDistance, $this->userId);
				$out = FAILED;
				if ($this->dbc->query($sql)) {
					$out = SUCCESS;
				}
			}		
		}
		return $out;
	}
	
	private function getCarOptions($reqArray)
	{
		$sql = sprintf('SELECT id, Title
							FROM '. $this->tablePrefix .'_caroptions
							ORDER BY sort');
	
		$carOptions = "";
		if (($result = $this->dbc->query($sql)) != false) 
		{
			$carOptions = "";
			while (($row = $this->dbc->fetchObject($result)) != false) {
				$carOptions .= '<caroption id="'.$row->id.'" title="'.$row->Title.'" />';			
			}
		}
		return $carOptions;
	}
	
	private function setUserStatus($reqArray)
	{
		$out = $this->authenticateUser($reqArray);
		if ($out == SUCCESS) {
			$out = MISSING_PARAMETER;
			if (isset($reqArray['statusOnline']) && $reqArray['statusOnline'] != NULL){
				$statusOnline = (int)$this->checkVariable($reqArray['statusOnline']);
				
				$sql = sprintf('UPDATE ' . $this->tablePrefix .'_users
								SET onlineStatus = %d
								WHERE Id = %d
								LIMIT 1', $statusOnline, $this->userId);
				
				$this->dbc->query($sql);
				$out = SUCCESS;
			}						
		}
		return $out;
	}
	
	private function setCarServices($reqArray)
	{
		$out = $this->authenticateUser($reqArray);
		if ($out == SUCCESS) {
			$email = $this->checkVariable($reqArray['email']);
			$password = $this->checkVariable($reqArray['password']);
			$out = MISSING_PARAMETER;
			if (isset($reqArray['serviceId']) && $reqArray['serviceId'] != NULL)
			{
								
				$carService = $this->checkVariable($reqArray['serviceId']);
				//TODO: if user_options1 and user_options2 is empty,
				// done: test it carefully
				$sql = sprintf('UPDATE ' . $this->tablePrefix .'_users
							SET caroptions = %d,  all_options = concat(user_options1, if(user_options1!="" && user_options2!="", ",", ""), user_options2, if(user_options1!="" || user_options2!="", ",", ""), %d),
								call_phone = user_phone
							WHERE email = "%s" AND
								  			password = "%s"
							LIMIT 1', $carService, $carService, $email, $password);
			
				$this->dbc->query($sql);
		
				$sql = sprintf('SELECT all_options, car_id FROM ' . $this->tablePrefix .'_users
							WHERE email = "%s" AND
						  		  password = "%s"
							LIMIT 1', $email, $password);

				$result = $this->dbc->query($sql);
				$row = $this->dbc->fetchObject($result);
				$allOptions = $row->all_options;
				$carId = $row->car_id;
			
				$sqlCarLog = sprintf('INSERT INTO ' 
									. $this->tablePrefix .'_car_log(all_options, userId, car_id, logdate)
								  VALUES("%s", %d, %d, NOW())', $allOptions, $this->userId, $carId);
			
				$this->dbc->query($sqlCarLog);
				$out = SUCCESS;
			}
		}
		return $out;
	}
	
	
	private function getImage($reqArray, $uploadedFile)
	{
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

			$sql = sprintf('SELECT Id, user_withInDistance, user_options1, user_options2, caroptions, car_id, all_options
								FROM '. $this->tablePrefix.'_users 
							WHERE email = "%s" 
						  		  AND 
						  		  password = "%s"
							LIMIT 1', $email, $password);
			
			$result = $this->dbc->query($sql);
			$out = UNAUTHORIZED_ACCESS;
			
			if ($row = $this->dbc->fetchObject($result)){
				//$userId = $this->dbc->getUniqueField($sql);
				$userId = $row->Id;
				$user_withInDistance = $row->user_withInDistance;
				$user_options1 = $row->user_options1;
				$user_options2 = $row->user_options2;
				$caroptions = $row->caroptions;
				$car_id = $row->car_id;
				$all_options = $row->all_options;
					
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
											(userId, latitude, longitude, altitude, dataArrivedTime, deviceId, dataCalculatedTime, 
												user_withInDistance, user_options1, user_options2, caroptions, car_id, all_options)
		    							 VALUES(%d,	%f, %f, %f, NOW(), "%s", "%s", "%s", "%s", "%s", "%s", "%s", "%s") 
										',
										$userId, $latitude, $longitude, $altitude, $deviceId, $calculatedTime,
										$user_withInDistance, $user_options1, $user_options2, $caroptions, $car_id, $all_options);			   
				
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