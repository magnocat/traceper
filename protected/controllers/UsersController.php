<?php

class UsersController extends Controller
{	
	private $dataFetchedTimeKey = "UsersController.dataFetchedTime";
	/**
	 * This is the default 'index' action that is invoked
	 * when an action is not explicitly requested by users.
	 */
	public function actionIndex()
	{
		// renders the view file 'protected/views/site/index.php'
		// using the default layout 'protected/views/layouts/main.php'
		$this->render('index');
	}

	/**
	 * This is the action to handle external exceptions.
	 */
	public function actionError()
	{
		if($error=Yii::app()->errorHandler->error)
		{
			if(Yii::app()->request->isAjaxRequest)
				echo $error['message'];
			else
				$this->render('error', $error);
		}
	}

	public function filters()
	{
		return array(
				'accessControl',
		);
	}

	public function accessRules()
	{
		return array(
				array('deny',
						//'deniedCallback' => array($this, 'deniedAction'),
						'actions'=>array('addAsFriend', 'deleteFriendShip','getFriendRequestList', 'getFriendList',
								'getUserPastPointsJSON', 'search', 'searchJSON',
								'takeMyLocation', 'getUserInfoJSON', 'getFriendRequestListJson',
								'getUserListJson', 'upload', 'updateLocationByGeolocation', 'useTraceperProfilePhoto', 
								'useFacebookProfilePhoto', 'viewProfilePhoto'),
						'users'=>array('?'),
				)
		);
	}
	
// 	public function deniedAction($rule){
// 		if (isset($_REQUEST['latitude']))
// 		{
// 			echo "Deneme";
// 		}
// 		else
// 		{
// 			echo "not mobile";
// 		}		
// 	}	
	
	private function calculateDistance($lat1, $lon1, $lat2, $lon2, $unit = "K") {
		$theta = $lon1 - $lon2;
		$dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) +  cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
		$dist = acos($dist);
		$dist = rad2deg($dist);
		$miles = $dist * 60 * 1.1515;
		$unit = strtoupper($unit);
		
		$distance = 0;
	
		if($unit == "K")
		{
			$distance = ($miles * 1.609344);
		}
		elseif($unit == "N") {
			$distance = ($miles * 0.8684);
		}
		else
		{
			$distance = $miles;
		}
		
		return $distance;
	}

	private function string2upper($str) {
		$convertedString = null;
		
		if(Yii::app()->language == 'tr')
		{
			$str = str_replace(array('i', 'ı', 'ü', 'ğ', 'ş', 'ö', 'ç'), array('İ', 'I', 'Ü', 'Ğ', 'Ş', 'Ö', 'Ç'), $str);			
		}
		else
		{
			//Nothing to do special	
		}
		
		$convertedString = strtoupper($str);
		
		return $convertedString;
	}
	
	
	
	/*
	 * check whether user guest or not (actually for checking whether session timed out or not)
	 * Bu fonksiyonu mobil session'in kaybolup kaybolmadigini anlamak icin kullanacak
	*/
	public function actionIsUserGuest()
	{
		$out = null;
		
		if (Yii::app()->user->isGuest == true)
		{
			$out = "1";
		}
		else
		{
			$out = "0";
		}
		
		echo $out;		
	}
	
	/*
	 * this action is used by mobile clients
	*/
	public function actionTakeMyLocation()
	{
		$result = null;
		$message = null;
		
		//Bu degiskenleri fonksiyonun en sonunda result:1 ise gondereceginden burada tanimla
		$minDistanceInterval = 0;
		$minDataSentInterval = 0;
		$address = null;
		$country = null;
		$bLogAsPastLocation = false;
		
		try
		{
			//if(Yii::app()->request->isPostRequest)
			{
				if (isset($_REQUEST['latitude']) && ($_REQUEST['latitude'] != NULL)
						&& isset($_REQUEST['longitude']) && ($_REQUEST['longitude'] != NULL)
						&& isset($_REQUEST['altitude']) && ($_REQUEST['altitude'] != NULL)
						&& isset($_REQUEST['deviceId']) && ($_REQUEST['deviceId'] != NULL)
						&& isset($_REQUEST['time']) && ($_REQUEST['time'] != NULL)
				)
				{
					$latitude = round((float) $_REQUEST['latitude'], 6);
					$longitude = round((float) $_REQUEST['longitude'], 6);
					$altitude = round((float) $_REQUEST['altitude'], 6);
					$deviceId = $_REQUEST['deviceId'];
					$calculatedTime = date('Y-m-d H:i:s', $_REQUEST['time']);
					$arrivedTime = date('Y-m-d H:i:s');
					
					//Fb::warn("lat:$latitude, lon:$longitude, al:$altitude, ct:$calculatedTime, at:$arrivedTime", "actionTakeMyLocation()");
						
					if (Yii::app()->user->id != false)
					{
						$lastLatitude = 0;
						$lastLongitude = 0;
						$lastAltitude = 0;
						$lastAddress = null;
							
						Users::model()->getMinimumIntervalValues(Yii::app()->user->id, $minDistanceInterval, $minDataSentInterval);
						UserWasHere::model()->getMostRecentLocation(Yii::app()->user->id, $lastLatitude, $lastLongitude, $lastAltitude, $lastAddress);
							
						$distanceInKms = $this->calculateDistance($lastLatitude, $lastLongitude, $latitude, $longitude);
						$distanceInMs = $distanceInKms * 1000;

						//If the distance difference is greater than minDistanceInterval, add a new record to UserWasHere table
						if($distanceInMs > $minDistanceInterval)
						{								
							$this->getaddress($_REQUEST['latitude'], $_REQUEST['longitude'], $address, $country);
							//Fb::warn($address.' '.Yii::t('countries', $country), "actionTakeMyLocation()");
							
							//Madem adres alindi, mevcut konum (Users tablosu) adres karsilastirmasi yapilmadan guncellensin
							
							//$updatedRowCount = Users::model()->updateLocationWithAddress($latitude, $longitude, $altitude, $address, $country, $arrivedTime, $calculatedTime, LocationSource::Mobile,  Yii::app()->user->id);
							$updateLocationResult = Users::model()->updateLocationWithAddress($latitude, $longitude, $altitude, $address, $country, $arrivedTime, $calculatedTime, LocationSource::Mobile,  Yii::app()->user->id);
							
							//Address info is also updated with location info if the distance difference is high enough
							//if ($updatedRowCount > 0)
							if(true == $updateLocationResult)
							{
								$result = "1"; //Location updated successfully
							}
							else
							{
								//$updatedRowCount = Users::model()->updateLocationWithAddress($latitude, $longitude, $altitude, $address, $country, $arrivedTime, $calculatedTime, LocationSource::Mobile,  Yii::app()->user->id);
								$updateLocationResult = Users::model()->updateLocationWithAddress($latitude, $longitude, $altitude, $address, $country, $arrivedTime, $calculatedTime, LocationSource::Mobile,  Yii::app()->user->id);
								$message = '';
									
								//if ($updatedRowCount > 0)
								if(true == $updateLocationResult)
								{
									$result = "1"; //Location updated successfully
									$message = "Error occured at 1. time while location(WITH address) save operation, but update is successful at 2. time!";
								}
								else
								{
									$result = "0"; //Error occured in save operation
									$message = "Error occured during location(WITH address) save operation to database (2 times)!";
								}
									
								$message .= '<br/><br/>';
								$message .= 'Updated row count:'.$updatedRowCount.'<br/>';
								$message .= 'latitude:'.$latitude.'<br/>';
								$message .= 'longitude:'.$longitude.'<br/>';
								$message .= 'altitude:'.$altitude.'<br/>';
								$message .= 'adress:'.$address.'<br/>';
								$message .= 'country:'.$country.'<br/>';
								$message .= 'calculatedTime:'.$calculatedTime.'<br/>';
								$this->sendErrorMail('takeMyLocationNotUpdatedWithAddress', 'Error (Users-updateLocationWithAddress) in actionTakeMyLocation()', $message);
							}							
							
							//Son gecmis izin adres bilgisi ile anlik adres bilgisi farkli ise yeni bir gecmis iz olarak kaydet
							if(strncmp($lastAddress, $address, 300) != 0)
							{
								$bLogAsPastLocation = true;
								
								//Fb::warn("Log past loc", "actionTakeMyLocation()");
							}
							else
							{
								$bLogAsPastLocation = false;
								
								//Fb::warn("NOT Log past loc", "actionTakeMyLocation()");
							}
						}
						else
						{
							$bLogAsPastLocation = false;
							
							//$updatedRowCount = Users::model()->updateLocation($latitude, $longitude, $altitude, $arrivedTime, $calculatedTime, LocationSource::Mobile, Yii::app()->user->id);
							$updateLocationResult = Users::model()->updateLocation($latitude, $longitude, $altitude, $arrivedTime, $calculatedTime, LocationSource::Mobile, Yii::app()->user->id);
							
							//Only location info (without address info) is updated if the distance difference is smaller than the threshold
							//if ($updatedRowCount > 0)
							if(true == $updateLocationResult)
							{
								$result = "1"; //Location updated successfully
									
								//Fb::warn('Location updated successfully', "actionTakeMyLocation()");
							}
							else
							{
								//Fb::warn('Location CANNOT be updated!', "actionTakeMyLocation()");
									
								//Veritabanı ilk seferde guncellenemezse ikinci kez dene
								//$updatedRowCount = Users::model()->updateLocation($latitude, $longitude, $altitude, $arrivedTime, $calculatedTime, LocationSource::Mobile, Yii::app()->user->id);
								$updateLocationResult = Users::model()->updateLocation($latitude, $longitude, $altitude, $arrivedTime, $calculatedTime, LocationSource::Mobile, Yii::app()->user->id);
								$message = '';
									
								//if ($updatedRowCount > 0)
								if(true == $updateLocationResult)
								{
									$result = "1"; //Location updated successfully
									$message = "Error occured at 1. time while location(without address) save operation, but update is successful at 2. time!";
								}
								else
								{
									$result = "0"; //Error occured in save operation
									$message = "Error occured during location(without address) save operation to database (2 times)!";
								}
									
								$message .= '<br/><br/>';
								$message .= 'Updated row count:'.$updatedRowCount.'<br/>';
								$message .= 'latitude:'.$latitude.'<br/>';
								$message .= 'longitude:'.$longitude.'<br/>';
								$message .= 'altitude:'.$altitude.'<br/>';
								$message .= 'calculatedTime:'.$calculatedTime.'<br/>';
								$this->sendErrorMail('takeMyLocationNotUpdatedWithoutAddress', 'Error (Users-updateLocation) in actionTakeMyLocation()', $message);
							}							
						}
						
						if(true == $bLogAsPastLocation)
						{
							//Fb::warn('if($distanceInMs > $minDistanceInterval)', "UsersController");
							
							if(UserWasHere::model()->logLocation(Yii::app()->user->id, $latitude, $longitude, $altitude, $deviceId, $calculatedTime, $address, $country))
							{
								//Fb::warn('UserWasHere::model()->logLocation() successful', "UsersController");
							
								//$result = "1"; //Values updated successfully
							}
							else
							{
								//Fb::warn('UserWasHere::model()->logLocation() ERROR', "UsersController");
							
								//$result = "0"; //Error occured in save operation
							
								$message = "Error occured during location save operation to UserWasHere table!";
							
								$message .= '<br/><br/>';
								$message .= 'latitude:'.$latitude.'<br/>';
								$message .= 'longitude:'.$longitude.'<br/>';
								$message .= 'altitude:'.$altitude.'<br/>';
								$message .= 'deviceId:'.$deviceId.'<br/>';
								$message .= 'calculatedTime:'.$calculatedTime.'<br/>';
								$message .= 'address:'.$address.'<br/>';
								$message .= 'country:'.$country.'<br/>';
								$this->sendErrorMail('userWasHereLogLocationError', 'Error (UserWasHere-logLocation) in actionTakeMyLocation()', $message);
							}							
						}
						else
						{
							//Ayni adres bilgisiyle yeni bir kayit olusturma	
						}
					}
					else
					{
						$result = "-1"; //No valid user Id
							
						$message = "User ID is not valid (Guest User)";
						$this->sendErrorMail('takeMyLocationInvalidUserID', 'Error in actionTakeMyLocation()', $message);
					}
				}
				else
				{
					$result = "-2"; //Missing Parameter
			
					$message = '"Missing Parameter" error occured:'.'<br/><br/>';
					
					if(isset($_REQUEST['latitude']) == false)
					{
						$message .= '"latitude" is missing!'.'<br/>';						
					}
					else if($_REQUEST['latitude'] == NULL)
					{
						$message .= '"latitude" is  NULL!'.'<br/>';						
					}

					if(isset($_REQUEST['longitude']) == false)
					{
						$message .= '"longitude" is missing!'.'<br/>';
					}
					else if($_REQUEST['longitude'] == NULL)
					{
						$message .= '"longitude" is  NULL!'.'<br/>';
					}

					if(isset($_REQUEST['altitude']) == false)
					{
						$message .= '"altitude" is missing!'.'<br/>';
					}
					else if($_REQUEST['altitude'] == NULL)
					{
						$message .= '"altitude" is  NULL!'.'<br/>';
					}					
					
					if(isset($_REQUEST['deviceId']) == false)
					{
						$message .= '"deviceId" is missing!'.'<br/>';
					}
					else if($_REQUEST['deviceId'] == NULL)
					{
						$message .= '"altitude" is  NULL!'.'<br/>';
					}					
					
					if(isset($_REQUEST['time']) == false)
					{
						$message .= '"time" is missing!'.'<br/>';
					}
					else if($_REQUEST['time'] == NULL)
					{
						$message .= '"time" is  NULL!'.'<br/>';
					}					

					$this->sendErrorMail('takeMyLocationMissingParameter', 'Error in actionTakeMyLocation()', $message);
				}
			}
			// 		else
				// 		{
				// 			$result = "-2"; //Missing Parameter
				
				// 			$message = "Not a POST request! Only post reequests are accepted";
				// 			$this->sendErrorMail('Error in actionTakeMyLocation()', $message);
				// 		}					
		}
		catch(Exception $e)
		{
			$result = "-3"; //Exception occured
			$message = $e->getMessage();
				
			$this->sendErrorMail('takeMyLocationExceptionOccured', 'Exception occured in actionTakeMyLocation()', $message);
		}
		
 		$formattedAddress = null;
		
// 		if($address != null)
// 		{
// 			Users::model()->getUserAddressInfo(Yii::app()->user->id, );
// 		}

		if(true == $bLogAsPastLocation) //Adres bilgisi degismis ise
		{
			$formattedAddress = $address.' / '.$this->string2upper(Yii::t('countries', $country));
		}
		else
		{
			$formattedAddress = null;
		}

		$resultArray = array("result"=>$result);
			
		if($result == "1") {
			$resultArray = array_merge($resultArray, array(
					"address"=>$formattedAddress
			));
		}

		echo CJSON::encode(
				$resultArray
		);		

		//$this->redirect(array('geofence/checkGeofenceBoundaries', 'friendId' => Yii::app()->user->id, 'friendLatitude' => $latitude, 'friendLongitude' => $longitude));
		Yii::app()->end();
	}
	
	public function actionUpdateLocationByGeolocation()
	{
		//Fb::warn("actionUpdateLocationByGeolocation() called", "UsersController");
		
		//$_POST['altitude'] genelde sifir donuyor, o nedenle NULL kontrolu yapma
		if (isset($_POST['latitude']) && ($_POST['latitude'] != NULL) && 
			isset($_POST['longitude']) && ($_POST['longitude'] != NULL))
		{
			$address = null;
			$country = null;
			
			$latitude = round((float) $_POST['latitude'], 6);
			$longitude = round((float) $_POST['longitude'], 6);
			$altitude = 0;
			
			if(isset($_POST['altitude']) && ($_POST['altitude'] != NULL))
			{
				$altitude = round((float) $_POST['altitude'], 6);
			}
			else
			{
				$altitude = 0;
			}
				
			$date = date('Y-m-d H:i:s');
			$this->getaddress($latitude, $longitude, $address, $country);			
			Users::model()->updateLocationWithAddress($latitude, $longitude, $altitude, $address, $country, $date, $date, LocationSource::WebGeolocation,  Yii::app()->user->id);			
		}		
	}

	public function actionGetLocationByWebIP()
	{
		//Fb::warn("actionGetLocationByWebIP() called", "UsersController");
	
		if (isset($_POST['countryName']) && ($_POST['countryName'] != NULL) &&
			isset($_POST['latitude']) && ($_POST['latitude'] != NULL) &&
			isset($_POST['longitude']) && ($_POST['longitude'] != NULL))
		{
			Yii::app()->session['countryName'] = $_POST['countryName'];
			Yii::app()->session['latitude'] = round((float) $_POST['latitude'], 6);
			Yii::app()->session['longitude'] = round((float) $_POST['longitude'], 6);	
		}
	}
		
	/*
	 * this action is used by mobile clients
	*/
	public function actionUpdateProfile()
	{
		$result = null;
		$message = null;
		
		$realname =  null;
		$password = null;
		$gender =  null;		
		$minDataSentInterval = null;
		$minDistanceInterval = null;
		$autoSend = null;

		$atLeastOneItemExists = false;
		
		if (isset($_REQUEST['realname']) && $_REQUEST['realname'] != NULL)
		{
			$realname = $_REQUEST['realname'];
			$atLeastOneItemExists = true;
		}
		
		if (isset($_REQUEST['password']) && $_REQUEST['password'] != NULL)
		{
			$password = $_REQUEST['password'];
			$atLeastOneItemExists = true;
		}

		if (isset($_REQUEST['gender']) && $_REQUEST['gender'] != NULL)
		{
			$gender = $_REQUEST['gender'];
			$atLeastOneItemExists = true;
		}		
				
		if (isset($_REQUEST['minDataSentInterval']) && $_REQUEST['minDataSentInterval'] != NULL)
		{
			$minDataSentInterval = $_REQUEST['minDataSentInterval'];
			$atLeastOneItemExists = true;
		}

		if (isset($_REQUEST['minDistanceInterval']) && $_REQUEST['minDistanceInterval'] != NULL)
		{
			$minDistanceInterval = $_REQUEST['minDistanceInterval'];
			$atLeastOneItemExists = true;
		}	

		if (isset($_REQUEST['autoSend']) && $_REQUEST['autoSend'] != NULL)
		{
			$autoSend = $_REQUEST['autoSend'];
			$atLeastOneItemExists = true;
		}	

		if(true == $atLeastOneItemExists)
		{			
			if (Yii::app()->user->id != false)
			{
				if(Users::model()->updateProfileItemsNotNull(Yii::app()->user->id, $realname, $password, $gender, $minDataSentInterval, $minDistanceInterval, $autoSend))
				{
					$result = "1"; //Not null values saved successfully
					
					//Fb::warn("Not null values saved successfully", "UsersController");
				}
				else
				{
					$result = "0"; //Error occured in save operation
				}					
			}
			else
			{
				$result = "-1"; //No valid user Id
				
				$message = "User ID is not valid (Guest User)";
				$this->sendErrorMail('updateProfileInvalidUser', 'Error in actionUpdateProfile()', $message);				
			}
		}
		else
		{
			$result = "-2"; //There is not any parameter which is not null
			
			$message = "There is not any parameter which is not null";
			$this->sendErrorMail('updateProfileAllParametersNull', 'Error in actionUpdateProfile()', $message);			
		}

		echo CJSON::encode(array(
				"result"=>$result,
		));		
		
		Yii::app()->end();
	}	
		
	public function actionGetFriendList()
	{
		$userType = array();

		if (isset($_GET['userType']) && $_GET['userType'] != NULL)
		{
			$userType = $_GET['userType'];
		}
		
// 		if (isset($_GET['ajax']) && $_GET['ajax'] != NULL)
// 		{
// 			Fb::warn($_GET['ajax'], "actionGetFriendList - ajax");
// 		}				
		//Fb::warn($userType, "actionGetFriendList - userType");
		//Fb::warn($this->getFriendIdList(false/*$par_onlyVisible*/, $userType), "actionGetFriendList - getFriendIdList");

		if(Yii::app()->user->id != null)
		{
			$dataProvider = Users::model()->getListDataProvider($this->getFriendIdList(false/*$par_onlyVisible*/, $userType), $userType);
		}
		else
		{
			$dataProvider = null;
		}
		
		if (Yii::app()->request->isAjaxRequest)
		{
			if (YII_DEBUG)
			{
				Yii::app()->clientscript->scriptMap['jquery.js'] = false;
			}
			else
			{
				Yii::app()->clientscript->scriptMap['jquery.min.js'] = false;
			}
			
			if(($userType == UserType::RealStaff) || ($userType == UserType::GPSStaff))
			{
				Yii::app()->clientScript->scriptMap['jquery.yiigridview.js'] = false;
			}			
		}		

		//Yii::app()->clientScript->scriptMap['jquery.js'] = false;
				
		$this->renderPartial('usersInfo',array('dataProvider'=>$dataProvider,'model'=>new SearchForm(), 'userType'=>$userType), false, false/*true olduğunda sayfa değiştirirken 2 kere ajax sorgusu yapıyor*/);
	}

	private function unsetFriendIdList() {
		unset(Yii::app()->session['friendList']);
	}

	private function getFriendCount($par_onlyVisible = false, $par_friendUserType = null) {
		if (true/*isset(Yii::app()->session['friendList'])== false*/) {
			$this->getFriendIdList($par_onlyVisible, $par_friendUserType);			
		}

		return Yii::app()->session['friendCount'];
	}

	private function getFriendArray($par_onlyVisible = false, $par_friendUserType = null) {
		if (true/*isset(Yii::app()->session['friendList'])== false*/) {
			$this->getFriendIdList($par_onlyVisible, $par_friendUserType);
		}

		return Yii::app()->session['friendArray'];
	}


	private function getFriendIdList($par_onlyVisible = false, $par_friendUserType = null) {
		//if (isset(Yii::app()->session['friendList']) == false) {
		if (true) {
			if($par_onlyVisible === true)	
			{
				$friendsResult = Users::model()->getVisibleFriendList(Yii::app()->user->id, $par_friendUserType);
			}
			else
			{
				$friendsResult = Users::model()->getFriendList(Yii::app()->user->id, $par_friendUserType);
			}
						
			$length = count($friendsResult);
			Yii::app()->session['friendCount'] = $length;
			$friends = array();
			for ($i = 0; $i < $length; $i++) {
				array_push($friends, $friendsResult[$i]['friend']);
			}
			$result = -1;
			if (count($friends) > 0) {
				$result = implode(',', $friends);
			}
			Yii::app()->session['friendArray'] = $friends;
			Yii::app()->session['friendList'] = $result;
		}		
		 
		//return Yii::app()->session['friendList'];
		return $result;
	}

	public function actionGetUserInfoJSON()
	{
		$out = "Missing parameter";
		
		if (isset($_REQUEST['userId']) && $_REQUEST['userId'] > 0) {
			
// 			//Adres bilgilerinin kullanici diline gore getirilmesi icin
// 			if(isset($_REQUEST['language']))
// 			{
// 				if($_REQUEST['language'] == 'tr')
// 				{
// 					Yii::app()->language = 'tr';
// 				}
// 				else
// 				{
// 					Yii::app()->language = 'en';
// 				}
// 			}			

			$userId = (int) $_REQUEST['userId'];
			$friendArray = $this->getFriendArray(true/*$par_onlyVisible*/, null/*userTypes*/); //Burası direk true degil mobilde gelen istekle guncellenmeli
			$out = "No permission to get this user location";
			
			if ($userId == Yii::app()->user->id || array_search($userId,$friendArray) !== false)
			{
				$dataProvider = Users::model()->getListDataProviderForJson($userId, null, null, null, 0, 1, 1);
				$out = $this->prepareJson($dataProvider);
			}
			else
			{
				$out = "No permission to get this user location";
				
				$message = "No permission to get this user($userId) location (not the requester or one of his/her friends) !";
				$this->sendErrorMail('getUserInfoJSON()', 'Error in actionGetUserInfoJSON()', $message);			
			}			
		}
		else
		{
			$message = "Missing Parameter: 'userId' is missing!";
			$this->sendErrorMail('getUserInfoJSON()', 'Error in actionGetUserInfoJSON()', $message);			
		}

		echo $out;
		Yii::app()->end();
	}
	/**
	 * this is intented to be used by mobile app
	 * Enter description here ...
	 */
	public function actionGetUserListJson()
	{	
		//Fb::warn("actionGetUserListJson() called", "UsersController");
		
		try
		{
			$pageNo = 1;
			$userTypes = array();
			
// 			//Adres bilgilerinin kullanici diline gore getirilmesi icin
// 			if(isset($_REQUEST['language']))
// 			{
// 				if($_REQUEST['language'] == 'tr')
// 				{
// 					Yii::app()->language = 'tr';
// 				}
// 				else
// 				{
// 					Yii::app()->language = 'en';
// 				}
// 			}			
			
			if(isset(Yii::app()->session['visibleFriendCount']) == false)
			{
				Yii::app()->session['visibleFriendCount'] = 0;
			}
			
			if (isset($_REQUEST['pageNo']) && $_REQUEST['pageNo'] > 0) {
				$pageNo = (int)$_REQUEST['pageNo'];
			}
			
			if (isset($_REQUEST['userType'])) {
				$userTypes[] = (int)$_REQUEST['userType'];
					
				//Fb::warn("userType is SET", "actionGetUserListJson()");
			}
			
			$offset = ($pageNo - 1) * Yii::app()->params->itemCountInDataListPage;
			
			//Webde kullanicinin kendi ismine tikladiginda kendini konumunu gorebilmesi icin
			
			//Burası direk true degil mobilde gelen istekle guncellenmeli
			$allFriendCount = $this->getFriendCount(false/*$par_onlyVisible*/, $userTypes) + 1; // +1 is for herself
			$visibleFriendCount = $this->getFriendCount(true/*$par_onlyVisible*/, $userTypes) + 1; // +1 is for herself
			//$friendCount = $this->getFriendCount();
				
			//$friendIdList = $this->getFriendIdList(true/*$par_onlyVisible*/, $userTypes);
			$friendIdList = $this->getFriendIdList(false/*$par_onlyVisible*/, $userTypes);
			
			//Fb::warn($userTypes, "userTypes");
			
			if (isset($_REQUEST['client']) && $_REQUEST['client']=='mobile')
			{
				//Do not add user himself for mobile
			}
			else //For web
			{
				if ($friendIdList != -1) {
					$friendIdList .= ',' . Yii::app()->user->id;
				}
				else {
					$friendIdList = Yii::app()->user->id;
				}
			
				//Fb::warn($friendIdList, "friendIdList");
			}
			
			$time = null;
			$updateType = null;
			
			if(isset(Yii::app()->session[$this->dataFetchedTimeKey]) == false)
			{
				Yii::app()->session[$this->dataFetchedTimeKey] = time();
			}
			
			//Sadece zamansal olarak update olmuslar istendiginde ve visible arkadas sayisi degismediyse onlyUpdated yoksa ALL
			if (isset($_REQUEST['list']) && ($_REQUEST['list'] == "onlyUpdated") && ($visibleFriendCount == Yii::app()->session['visibleFriendCount'])) {
				$time = Yii::app()->session[$this->dataFetchedTimeKey];
				$updateType = 'onlyUpdated';
			
				//Fb::warn("onlyUpdated", "actionGetUserListJson()");
			}
			else
			{
				$updateType = 'all';
					
				//Fb::warn("ALL, friendCount:".$friendCount." - sessionCount:".Yii::app()->session['visibleFriendCount'], "actionGetUserListJson()");
			}
			
			$newFriendId = null;
			
			if (isset($_REQUEST['newFriendId'])) {
				$newFriendId = (int)$_REQUEST['newFriendId'];
			}
			
			//Fb::warn("actionGetUserListJson() called", "UsersController");
			
			$dataProvider = Users::model()->getListDataProviderForJson($friendIdList, $userTypes, $newFriendId,  $time, $offset, Yii::app()->params->itemCountInDataListPage, $allFriendCount);

			//$dataProvider = Users::model()->getListDataProviderForJson($friendIdList, $userTypes, $newFriendId,  $time, $offset, Yii::app()->params->itemCountInDataListPage, null);
			
			$out = $this->prepareJson($dataProvider, $updateType);
			
			Yii::app()->session[$this->dataFetchedTimeKey] = time();
			Yii::app()->session['visibleFriendCount'] = $visibleFriendCount;			
			
			//Fb::warn($out, "Json()");
			
			//header('HTTP/1.1 200 OK');
			echo $out;
		}
		catch(Exception $e)
		{
			$message = $e->getMessage();
			
			$this->sendErrorMail('getUserInfoJSONExceptionOccured', 'PHP Exception in actionGetUserListJson()', $message);
		}
		
		Yii::app()->end();
	}

	public function actionGetUserPastPointsJSON(){

		$out = null;
		
		if (isset($_REQUEST['userId']))
		{
			$userId = (int) $_REQUEST['userId'];
			$pageNo = 1;
			if (isset($_REQUEST['pageNo']) && $_REQUEST['pageNo'] > 0) {
				$pageNo = (int) $_REQUEST['pageNo'];
				Fb::warn("pageNo is SET:$pageNo", "actionGetUserPastPointsJSON()");
			}
			else
			{
				Fb::warn("pageNo is NOT set!", "actionGetUserPastPointsJSON()");
			}
			
			$offset = ($pageNo - 1) * Yii::app()->params->itemCountInDataListPage;
			$offset++;  // to not get the last location

			$dataProvider = UserWasHere::model()->getPastPointsDataProvider($userId, $pageNo, Yii::app()->params->itemCountInDataListPage);
				
			$out = $this->preparePastPointsJson($dataProvider);
			
			Fb::warn($out, "actionGetUserPastPointsJSON()");
		}
		else
		{
			$out = "userId is NOT set!";
		}
		
		echo $out;
	}
	
	public function actionSearch() {
		$model = new SearchForm();

		$dataProvider = null;
		if(isset($_REQUEST['SearchForm']))
		{
			$model->attributes = $_REQUEST['SearchForm'];
			if ($model->validate()) {
				$dataProvider = Users::model()->getSearchUserDataProvider(null, $model->keyword, "SearchForm[keyword]");
				
				//Fb::warn("totalItemCount: $dataProvider->totalItemCount", "actionSearch()");
				
				//$pagination = $dataProvider->getPagination();
				//Fb::warn("pageCount: $pagination->pageCount", "actionSearch()");
			}
		}
		
		if (Yii::app()->request->isAjaxRequest)
		{
			if (YII_DEBUG)
			{
				Yii::app()->clientscript->scriptMap['jquery.js'] = false;
			}
			else
			{
				Yii::app()->clientscript->scriptMap['jquery.min.js'] = false;
			}
		}
				
		//Yii::app()->clientScript->scriptMap['jquery.js'] = false;
		Yii::app()->clientScript->scriptMap['jquery.yiigridview.js'] = false;
		
		$this->renderPartial('searchResults',array('model'=>$model, 'dataProvider'=>$dataProvider), false, true);
	}

	public function actionSearchJSON() {
		$model = new SearchForm();
		$result = null;
		$dataProvider = null;
		$pageNo = 1;
		$upTo = 0;
		
		if(isset($_REQUEST['SearchForm']))
		{
			$model->attributes = $_REQUEST['SearchForm'];
			
			if (isset($_REQUEST['pageNo']) && $_REQUEST['pageNo'] > 0) {
				$pageNo = (int)$_REQUEST['pageNo'];
				
				//Fb::warn("pageNo is SET: $pageNo", "actionSearchJSON()");
			}
			
			//Verilen sayfa numarasina kadar olan tum sayfalara ait kisi listesi mi isteniyor
			if (isset($_REQUEST['upTo']) && $_REQUEST['upTo'] > 0) {
				$upTo = (int)$_REQUEST['upTo'];
			}			
						
			if ($model->validate()) {

				$dataProvider = Users::model()->getSearchUserDataProvider(null, $model->keyword, "SearchForm[keyword]");				
				$out = $this->prepareSearchUserResultJson($dataProvider, $pageNo, $upTo);				
			}
			else
			{
				$result = "-1"; //Model invalid
				$out = '{"result":"'.$result.'"}';
			}
		}
		else
		{
			$result = "-2"; //Search form not set
			$out = '{"result":"'.$result.'"}';			
		}
		
		echo $out;
		Yii::app()->end();
	}
	
	public function actionDeleteFriendShip(){
		//$result = 'Missing Data';
		if (isset($_REQUEST['friendId']))
		{
			$friendId = (int) $_REQUEST['friendId'];
			$friendShipStatus = -1;
			$message = null;

			$actionResult = Friends::model()->deleteFriendShip($friendId, $friendShipStatus);
			
			if ($actionResult == 1) {
				$this->unsetFriendIdList();
				
				//When deleting this friend, also all of the group memberships for the current owner should be deleted
				UserPrivacyGroupRelation::model()->deleteMemberByGroupOwner(Yii::app()->user->id, $friendId);
			}
			else if ($actionResult == 0)
			{
				$message = "Friend cannot be deleted!";
				$this->sendErrorMail('deleteFriendShipCannotBeDeleted', 'Error in actionDeleteFriendShip()', $message);				
			}
			else if ($actionResult == -1)
			{
				$message = "Friendship NOT found!";
				$this->sendErrorMail('deleteFriendShipNotFound', 'Error in actionDeleteFriendShip()', $message);			
			}			 
		}

		echo CJSON::encode(array(
				"result"=>$actionResult,
				"friendShipStatus"=>$friendShipStatus,
				"deletedFriendId"=>$friendId
		));
	}

	public function actionDeleteUser(){
		$result = 'Missing Data';
		if (isset($_REQUEST['userId']))
		{
			$userId = (int) $_REQUEST['userId'];

			$result = 'Error occured';
			if (Users::model()->deleteUser($userId)){
				$result = 1;
				$this->unsetFriendIdList();
				
				//Delete all relations of this user in the Friends table
				Friends::model()->deleteAllFriendShipRelations($userId);
				
				//Delete all relations of this user in the UserPrivacyGroupRelation table
				UserPrivacyGroupRelation::model()->deleteMemberFromAllGroups($userId);
			}
		}

		echo CJSON::encode(array(
				"result"=>$result,
		));

	}

	public function actionGetFriendRequestList(){

		// we look at the friend2 field because requester id is stored in friend1 field
		// and only friend who has been requested to be a friend can approve frienship

		Friends::model()->updateAll(array('isNew' => 0), 'friend2 = '.Yii::app()->user->id.' AND status = 0 AND isNew = 1');
		$dataProvider = Friends::model()->getFriendRequestDataProvider(Yii::app()->user->id, Yii::app()->params->itemCountInOnePage);
			
		if (Yii::app()->request->isAjaxRequest)
		{
			if (YII_DEBUG)
			{
				Yii::app()->clientscript->scriptMap['jquery.js'] = false;
			}
			else
			{
				Yii::app()->clientscript->scriptMap['jquery.min.js'] = false;
			}
		}		
		
		//Yii::app()->clientScript->scriptMap['jquery.js'] = false;
		Yii::app()->clientScript->scriptMap['jquery.yiigridview.js'] = false;
		
		//Complete solution for blinking problem at FireFox
		if (Yii::app()->request->getIsAjaxRequest()) {
			Yii::app()->clientScript->scriptMap['*.js'] = false;
			Yii::app()->clientScript->scriptMap['*.css'] = false;
		}
				
		$this->renderPartial('userListDialog',array('dataProvider'=>$dataProvider), false, true);
	}
	
	
	public function actionGetFriendRequestListJson(){		
		
		$callFor = null;
		$dataProvider = null;
		
		if(isset($_REQUEST['callFor']) && ($_REQUEST['callFor'] != NULL))
		{
			$callFor = $_REQUEST['callFor'];
		}
		else
		{
			//Uygulama guncellenince buraya hata maili eklenecek				
		}
		
		if($callFor == "notification")
		{
			$dataProvider = Friends::model()->getNewFriendRequestDataProvider(Yii::app()->user->id, Yii::app()->params->itemCountInOnePage);
		}
		else
		{
			Friends::model()->updateAll(array('isNew' => 0), 'friend2 = '.Yii::app()->user->id.' AND status = 0 AND isNew = 1');
			$dataProvider = Friends::model()->getFriendRequestDataProvider(Yii::app()->user->id, Yii::app()->params->itemCountInOnePage);
		}

		$out = $this->prepareSearchUserResultJson($dataProvider);
		//$out = $this-> prepareJson($sql, "userSearch" ,$userId);
		echo $out;
		Yii::app()->end();
	}
	
	public function actionApproveFriendShip(){
		$result = 'Missing Data';
// 		if (isset($_REQUEST['friendShipId']))
// 		{
// 			$friendShipId = (int) $_REQUEST['friendShipId'];
// 			// only friend2 can approve friendship because friend1 makes the request
// 			$friendShip = Friends::model()->findByPk($friendShipId, array('condition'=>'friend2=:friend2',
// 					'params'=>array(':friend2'=>Yii::app()->user->id,
// 					),
// 			)
// 			);
// 			$result = 'Error occured';
// 			if (Friends::model()->approveFriendShip($friendShipId, $userId) == true)
// 			{
// 				$result = 1;
// 				$this->unsetFriendIdList();
// 			}			
// 		}
		
		if (isset($_REQUEST['friendId']))
		{
			$friendId = (int) $_REQUEST['friendId'];
			// only friend2 can approve friendship because friend1 makes the request
			//$friendShip = Friends::model()->find('friend1=:friend1 AND friend2=:friend2', array(':friend1'=>$friendId, ':friend2'=>Yii::app()->user->id));
			
			$result = 'Error occured';			
			if (Friends::model()->approveFriendShip($friendId, Yii::app()->user->id) == true)
			{
				$result = 1;
				$this->unsetFriendIdList();
			}
			else
			{
				$message = "Friendship CANNOT be approved!";
				$this->sendErrorMail('approveFriendShipCannotBeApproved', 'Error in actionApproveFriendShip()', $message);				
			}
		}		
		echo CJSON::encode(array(
				"result"=>$result,
				"friendId"=>$friendId,
		));

	}

	public function actionAddAsFriend()
	{
		$result = "-100";
		$errorMessage = null;

		if(isset($_REQUEST['friendId'])) 
		{
			$friendId = (int)$_REQUEST['friendId'];
			
			$mobileLang = null;
			
			if(isset($_REQUEST['language']))
			{
				$mobileLang = $_REQUEST['language'];
			}
			
			$result = Friends::model()->addAsFriend(Yii::app()->user->id, $friendId);
			
			//Friends tablosuna ekleme başarılıysa mail at, yoksa boşuna mail atma
			if(1 == $result)
			{
				$requesterName = null;
				$requesterEmail = null;
				Users::model()->getUserInfo(Yii::app()->user->id, $requesterName, $requesterEmail);
					
				$friendCandidateName = null;
				$friendCandidateEmail = null;
				Users::model()->getUserInfo($friendId, $friendCandidateName, $friendCandidateEmail);
					
// 				$isTranslationRequired = false;
					
// 				if($mobileLang != null)
// 				{
// 					if($mobileLang == 'tr')
// 					{
// 						if(Yii::app()->language == 'tr')
// 						{
// 							$isTranslationRequired = false;
// 						}
// 						else
// 						{
// 							$isTranslationRequired = true;
// 						}
// 					}
// 					else
// 					{
// 						if(Yii::app()->language == 'tr')
// 						{
// 							$isTranslationRequired = true;
// 						}
// 						else
// 						{
// 							$isTranslationRequired = false;
// 						}
// 					}
// 				}
					
// 				if($isTranslationRequired == true)
// 				{
// 					if($mobileLang == 'tr')
// 					{
// 						Yii::app()->language = 'tr';
// 					}
// 					else
// 					{
// 						Yii::app()->language = 'en';
// 					}
// 				}
				
				$message = Yii::t('site', 'Hi').' '.$friendCandidateName.',<br/><br/>';
				$message .= $requesterName.', ';
				$message .= Yii::t('users', 'wants to be your friend at Traceper').'.'.'<br/><br/>';
				$message .= Yii::t('users', 'If you wish you could accept or reject this friendship request using the "Friendship Requests" menu of your mobile application or at address www.traceper.com.');
					
				//echo $message;
				
				if($this->SMTP_UTF8_mail(Yii::app()->params->noreplyEmail, 'Traceper', $friendCandidateEmail, $friendCandidateName, $requesterName.', '.Yii::t('users', 'wants to be your friend at Traceper'), $message))
				{
					//Mail gönderildi
				}
				else
				{
					//Mail gönderilirken hata oluştu
				}
				
// 				//Language recovery should be done after sending the mail, because some generic message is added also in SMTP_UTF8_mail()
// 				if($isTranslationRequired == true) //Recover the language if needed for mobile
// 				{
// 					if($mobileLang == 'tr')
// 					{
// 						Yii::app()->language = 'en';
// 					}
// 					else
// 					{
// 						Yii::app()->language = 'tr';
// 					}
// 				}				
			}
			else
			{
				switch ($result) {
					case 0:
						$errorMessage = "New friendship record cannot be saved!";
						break;
						
					case -1:
						$errorMessage = "Duplicate entry exception occured during friendship record save!";
						break;
						
					case -2:
						$errorMessage = "Unknown exception occured during friendship record save!";
						break;
						
					default:
						$errorMessage = "Unknown error occured!";
						break;							
				}

				$this->sendErrorMail('addAsFriendCannotAdd', 'Error in actionAddAsFriend()', $errorMessage);
			}						
		}
		else
		{
			$result = "-3"; //Missing parameter
			
			$errorMessage = "Missing parameter!";
			$this->sendErrorMail('addAsFriendMissingParameter', 'Error in actionAddAsFriend()', $errorMessage);
		}
		
		echo CJSON::encode(array(
				"result"=>$result,
		));
		
		Yii::app()->end();
	}
	
	public function actionAddAsFriendIfAlreadyMember()
	{
		$result = 0;
		$errorMessage = null;
		
		if (isset($_REQUEST['facebookIdOfFriendCandidate']))
		{
			$facebookIdOfFriendCandidate = (int)$_REQUEST['facebookIdOfFriendCandidate'];
			
			if (isset($_REQUEST['traceperIdOfFriendshipRequestingMember']))
			{
				$traceperIdOfFriendshipRequestingMember = (int)$_REQUEST['traceperIdOfFriendshipRequestingMember'];
				
				$friendCandidateRecord = Users::model()->find(array('condition'=>'fb_id=:fb_id', 'params'=>array(':fb_id'=>$facebookIdOfFriendCandidate)));

			    if($friendCandidateRecord != null) //The friend candidate with the given Facebook ID is also a Traceper member 
			    {
			    	$done = Friends::model()->addAsFriend($traceperIdOfFriendshipRequestingMember, $friendCandidateRecord->Id);
			    	
			    	if ($done == true) {
			    		$result = 1; //If the frienship is made successfully, return 1
			    	}
			    	else  if ($done == null) {
			    		$result = -1; //If there occurs a problem during friendship process, return -1
			    		
			    		$errorMessage = "Error occured during friendship process";
			    		$this->sendErrorMail('addAsFriendIfAlreadyMemberError1', 'Error in actionAddAsFriendIfAlreadyMember()', $errorMessage);			    		
			    	}
			    	else
			    	{
			    		$result = -1; //If there occurs a problem during friendship process, return -1
			    		
			    		$errorMessage = "Error occured during friendship process";
			    		$this->sendErrorMail('addAsFriendIfAlreadyMemberError2', 'Error in actionAddAsFriendIfAlreadyMember()', $errorMessage);			    		
			    	}					
			    }
			    else //The friend candidate with the given Facebook ID is not a Traceper member, so return 0
			    {
			    	$result = 0;
			    	
			    	$errorMessage = "The friend candidate with the given Facebook ID is not a Traceper member";
			    	$this->sendErrorMail('addAsFriendIfAlreadyMemberError3', 'Error in actionAddAsFriendIfAlreadyMember()', $errorMessage);			    	
			    }				
			}			
		}
		
		echo CJSON::encode(array(
				"result"=>$result,
		));
		Yii::app()->end();
	}
	
// 	public function actionUpload(){
// 		Fb::warn('actionUpload() called', "UsersController");
		
// 	    $file = CUploadedFile::getInstanceByName('file');
// 	    // Do your business ... save on file system for example,
// 	    // and/or do some db operations for example
// 	    $file->saveAs('profilePhotos/'.$file->getName());
// 	    // return the new file path
// 	    echo Yii::app()->baseUrl.'/images/'.$file->getName();
// 	}
	
// 	public function actionUpdate()
// 	{
// 		Fb::warn('actionUpdate() called', "UsersController");

// 		//$model=Users::model()->findByPk((int)$id);
		
// 		$model = new UploadProfilePhotoForm;

// 		if(isset($_POST['UploadProfilePhotoForm']))
// 		{
// 			//$_POST['Users']['profilePhoto'] = $model->profilePhoto;
// 			$model->attributes=$_POST['UploadProfilePhotoForm'];
			
// 			Fb::warn("Form SET", "UsersController");
	
// 			$uploadedFile=CUploadedFile::getInstance($model,'profilePhoto');
			
// 			//$model->profilePhoto = 1;
	
// 			if($model->validate())
// 			{
// 				Fb::warn("model VALID", "actionUpdate()");
				
// 				if(!empty($uploadedFile))  // check if uploaded file is set or not
// 				{
// 					Fb::warn("uploadedFile EXISTS", "actionUpdate()");
					
// 					$uploadedFile->saveAs(Yii::app()->basePath.'/../profilePhotos/'.Yii::app()->user->id.'.jpg');
// 				}
// 				else
// 				{
// 					Fb::warn("uploadedFile is EMPTY", "actionUpdate()");
// 				}
				
// 				//Yii::app()->end();
				
// 				//$this->redirect(array('admin'));
// 			}
// 			else
// 			{
// 				Fb::warn("model NOT VALID", "actionUpdate()");
// 			}
// 		}
// 		else
// 		{
// 			Fb::warn("Form NOT set", "UsersController");
// 		}
	
// 		//$this->render('update',array('model'=>$model));
		
// 		if (Yii::app()->request->isAjaxRequest)
// 		{
// 			if (YII_DEBUG)
// 			{
// 				Yii::app()->clientscript->scriptMap['jquery.js'] = false;
// 				Yii::app()->clientScript->scriptMap['jquery-ui.js'] = false;
// 			}
// 			else
// 			{
// 				Yii::app()->clientscript->scriptMap['jquery.min.js'] = false;
// 				Yii::app()->clientScript->scriptMap['jquery-ui.min.js'] = false;
// 			}
// 		}
		
// 		//Complete solution for blinking problem at FireFox
// 		if (Yii::app()->request->getIsAjaxRequest()) {
// 			Yii::app()->clientScript->scriptMap['*.js'] = false;
// 			Yii::app()->clientScript->scriptMap['*.css'] = false;
// 		}		
		
// 		$this->renderPartial('update',array('model'=>$model), false, true);
// 	}

	public function actionUpload()
	{		
		try 
		{
			Yii::import("ext.EAjaxUpload.qqFileUploader");
			
			$folder='profilePhotos/';// folder for uploaded files
			$allowedExtensions = array("jpg", "jpeg", "png", "gif");//array("jpg","jpeg","gif","exe","mov" and etc...
			$sizeLimit = 10 * 1024 * 1024;// maximum file size in bytes
			$uploader = new qqFileUploader($allowedExtensions, $sizeLimit);
			$result = $uploader->handleUpload($folder, Yii::app()->user->id);
			//$result = $uploader->handleUpload($folder, null);
			$return = htmlspecialchars(json_encode($result), ENT_NOQUOTES);
			
			//Fb::warn($result['filename'], "UsersController - filename");
			
			if(isset($result['error']) == false)
			{
				//$fileSize = filesize($folder.$result['filename']);//GETTING FILE SIZE
				$filename = $result['filename'];//GETTING FILE NAME
				$extension = $result['extension'];
				$filenameWithPath = $folder.$filename.$extension;
				
				$image = Yii::app()->image->load($filenameWithPath);
				$image->smart_resize(44, 48)->quality(75);
				//$image->save(); // or $image->save('images/small.jpg');
				
				if($extension != '.png')
				{
					$image->save($folder.$filename.'.png');
					unlink($filenameWithPath);
				}
				
				$profilePhotoStatus = Users::NO_TRACEPER_PROFILE_PHOTO_EXISTS;
				
				if(Yii::app()->user->fb_id == 0)
				{
					$profilePhotoStatus = Users::TRACEPER_PROFILE_PHOTO_EXISTS;
				}
				else
				{
					$profilePhotoStatus = Users::BOTH_PROFILE_PHOTOS_EXISTS_USE_TRACEPER;
				}
				
				Users::model()->setProfilePhotoStatus(Yii::app()->user->id, $profilePhotoStatus);
			}
			else
			{
				//Fb::warn('error EXISTS!', "UsersController - actionUpload()");
				
				if($result['error'] == 'File is unreadable')
				{
					$return = CJSON::encode(array("result"=>"-1")); //File Unreadable
				}
				else
				{
					$return = CJSON::encode(array("result"=>"-2")); //Unknown Photo Upload Error
				}
			}			
		}
		catch( Exception $e )
		{
			if ($e instanceof CException) 
			{
				if($e->getMessage() == 'image file unreadable')
				{
					$return = CJSON::encode(array("result"=>"-1")); //File Unreadable
					
					//Fb::warn('image file unreadable', "UsersController - actioUpload()");
					
					if(file_exists($filenameWithPath))
					{
						unlink($filenameWithPath);
					}
				}
				else
				{
					$return = CJSON::encode(array("result"=>"-2")); //Unknown Photo Upload Error
				}
			}
			else
			{
				$return = CJSON::encode(array("result"=>"-2")); //Unknown Photo Upload Error
			}			
		}
		
		//Fb::warn($return, "actionUpload() - return");
		
		echo $return;// it's array	
	}

	public function actionUseTraceperProfilePhoto()
	{
		//Fb::warn("actionUseTraceperProfilePhoto() called", "UsersController");
		
		Users::model()->setProfilePhotoStatus(Yii::app()->user->id, Users::BOTH_PROFILE_PHOTOS_EXISTS_USE_TRACEPER);
		
		//userAreaView icinde gelen tooltipster'larin calismasi icin jquery'nin yeniden yuklenmemesi gerekiyor (tooltipster jquery'ye bagli oldugu icin)
		if (Yii::app()->request->isAjaxRequest)
		{
			if (YII_DEBUG)
			{
				Yii::app()->clientscript->scriptMap['jquery.js'] = false;
				Yii::app()->clientScript->scriptMap['jquery-ui.js'] = false;
			}
			else
			{
				Yii::app()->clientscript->scriptMap['jquery.min.js'] = false;
				Yii::app()->clientScript->scriptMap['jquery-ui.min.js'] = false;
			}
		}		
		
		$this->renderPartial('//site/userAreaView',array('profilePhotoSource'=>'profilePhotos/'.Yii::app()->user->id.'.png?random='.time(), 'profilePhotoStatus'=>Users::BOTH_PROFILE_PHOTOS_EXISTS_USE_TRACEPER, 'profilePhotoStatusTooltipMessage'=>null, 'bothPhotoExists'=>'useTraceper', 'variablesDefined'=>true), false, true);		
	}

	public function actionUseFacebookProfilePhoto()
	{
		//Fb::warn("actionUseFacebookProfilePhoto() called", "UsersController");
		
		Users::model()->setProfilePhotoStatus(Yii::app()->user->id, Users::BOTH_PROFILE_PHOTOS_EXISTS_USE_FACEBOOK);
		
		//userAreaView icinde gelen tooltipster'larin calismasi icin jquery'nin yeniden yuklenmemesi gerekiyor (tooltipster jquery'ye bagli oldugu icin)
		if (Yii::app()->request->isAjaxRequest)
		{
			if (YII_DEBUG)
			{
				Yii::app()->clientscript->scriptMap['jquery.js'] = false;
				Yii::app()->clientScript->scriptMap['jquery-ui.js'] = false;
			}
			else
			{
				Yii::app()->clientscript->scriptMap['jquery.min.js'] = false;
				Yii::app()->clientScript->scriptMap['jquery-ui.min.js'] = false;
			}
		}		
		
		$this->renderPartial('//site/userAreaView',array('profilePhotoSource'=>'https://graph.facebook.com/'.Yii::app()->user->fb_id.'/picture?type=square', 'profilePhotoStatus'=>Users::BOTH_PROFILE_PHOTOS_EXISTS_USE_FACEBOOK, 'profilePhotoStatusTooltipMessage'=>null, 'bothPhotoExists'=>'useFacebook', 'variablesDefined'=>true), false, true);
	}

	public function actionViewProfilePhoto($variablesNotDefined = false)
	{
		$profilePhotoSource = null;
		$profilePhotoStatus = Users::model()->getProfilePhotoStatus(Yii::app()->user->id);
		$profilePhotoStatusTooltipMessage = null;
		$bothPhotoExists = null;
		
// 		if($variablesNotDefined == true)
// 		{
// 			Fb::warn("variablesNotDefined", "actionViewProfilePhoto()");
// 		}
// 		else
// 		{
// 			Fb::warn("variablesDefined", "actionViewProfilePhoto()");
// 		}
		
		switch($profilePhotoStatus)
		{
			case Users::NO_TRACEPER_PROFILE_PHOTO_EXISTS:
				{
					if(Yii::app()->user->fb_id == 0)
					{
						$profilePhotoSource = null;
						$profilePhotoStatusTooltipMessage = Yii::t('site', 'Click here to upload your profile photo');
					}
					else
					{
						$profilePhotoSource = 'https://graph.facebook.com/'+ Yii::app()->user->fb_id + '/picture?type=square';
						$profilePhotoStatusTooltipMessage = Yii::t('site', 'Click here to upload and set your profile photo. You will be able to set your profile photo as your Facebook profile photo again.');
					}
				}
				break;
					
			case Users::TRACEPER_PROFILE_PHOTO_EXISTS:
				{
					$profilePhotoSource = 'profilePhotos/'.Yii::app()->user->id.'.png?random='.time();										
					$profilePhotoStatusTooltipMessage = Yii::t('site', 'Click here to change your profile photo');
						
					//Fb::warn($profilePhotoStatusTooltipMessage, "TRACEPER_PROFILE_PHOTO_EXISTS");
				}
				break;
					
			case Users::BOTH_PROFILE_PHOTOS_EXISTS_USE_FACEBOOK:
				{
					$bothPhotoExists = 'useFacebook';
					$profilePhotoSource = 'https://graph.facebook.com/'.Yii::app()->user->fb_id.'/picture?type=square';
					$profilePhotoStatusTooltipMessage = null;
				}
				break;
					
			case Users::BOTH_PROFILE_PHOTOS_EXISTS_USE_TRACEPER:
				{
					$bothPhotoExists = 'useTraceper';
					$profilePhotoSource = 'profilePhotos/'.Yii::app()->user->id.'.png?random='.time();
					$profilePhotoStatusTooltipMessage = null;
				}
				break;					
		}
		
		//userAreaView icinde gelen tooltipster'larin calismasi icin jquery'nin yeniden yuklenmemesi gerekiyor (tooltipster jquery'ye bagli oldugu icin)
		if (Yii::app()->request->isAjaxRequest)
		{
			if (YII_DEBUG)
			{
				Yii::app()->clientscript->scriptMap['jquery.js'] = false;
				Yii::app()->clientScript->scriptMap['jquery-ui.js'] = false;
			}
			else
			{
				Yii::app()->clientscript->scriptMap['jquery.min.js'] = false;
				Yii::app()->clientScript->scriptMap['jquery-ui.min.js'] = false;
			}
		}		
	
		$this->renderPartial('//site/userAreaView',array('profilePhotoSource'=>$profilePhotoSource, 'profilePhotoStatus'=>$profilePhotoStatus, 'profilePhotoStatusTooltipMessage'=>$profilePhotoStatusTooltipMessage, 'bothPhotoExists'=>$bothPhotoExists, 'variablesDefined'=>($variablesNotDefined == false)), false, true);
	}	

// 	private function prepareJson($dataProvider){
	
// 		$rows = $dataProvider->getData();
// 		$itemCount = count($rows);
	
// 		Fb::warn($itemCount, "itemCount");
	
// 		$str = '';
// 		for ($i = 0; $i < $itemCount; $i++) {
// 			if ($i > 0)  {
// 				$str .= ",";
// 			}
// 			$str .= $this->getUserJsonItem($rows[$i]);
// 		}
	
// 		$pagination = $dataProvider->getPagination();
// 		//$pagination->setCurrentPage(1);
	
// 		$currentPage = $pagination->currentPage + 1;
// 		Fb::warn($currentPage, "currentPage");
// 		Fb::warn($pagination->pageCount, "pageCount");
// 		$str = '{"userlist": ['.$str.'], "pageNo":"'.$currentPage .'", "pageCount":"'.$pagination->pageCount.'"}';
	
// 		return $str;
// 	}	
		
	
	private function prepareJson($dataProvider, $par_updateType = null){ //Multisent prepareJson()
		
		//header('Content-Type: application/json; charset=UTF8'); //Bunu ajax request'i yaparken tanimlayinca hata olusuyor?

		$pagination = $dataProvider->getPagination();
		//Fb::warn($pagination->pageCount, "pageCount");
		$currentPage = $pagination->currentPage;
		
		$str = '';
		
		for ($k = $currentPage; $k < $pagination->pageCount; $k++) {
			//Fb::warn($k, "k");
			$pagination->setCurrentPage($k);
			//Fb::warn($pagination->currentPage, "currentPage");
			
			$rows = $dataProvider->getData(true);
			$itemCount = count($rows);
			
			//Fb::warn($itemCount, "itemCount");
						
			for ($i = 0; $i < $itemCount; $i++) {
				if (($i > 0) || ($k > 0))  {
					$str .= ",";
				}
				$str .= $this->getUserJsonItem($rows[$i]);
			}			
		}

		//$pagination->setCurrentPage(1);		
		
		//$currentPage = $pagination->currentPage + 1;
		///Fb::warn($currentPage, "currentPage");
				
		//$str = '{"userlist": ['.$str.'], "pageNo":"'.$currentPage .'", "pageCount":"'.$pagination->pageCount.'"}';
				
		if($par_updateType != null)
		{
			$str = '{"updateType":"'.$par_updateType.'", "userlist": ['.$str.'], "pageNo":"1", "pageCount":"1", "currentUser":"'.Yii::app()->user->id.'"}'; //Simdilik tek sayfada hepsi gonderiliyor
		}
		else
		{
			$str = '{"userlist": ['.$str.'], "pageNo":"1", "pageCount":"1", "currentUser":"'.Yii::app()->user->id.'"}'; //Simdilik tek sayfada hepsi gonderiliyor
		}

		return $str;
	}
	
	private function prepareSearchUserResultJson($dataProvider, $pageNo = 1, $upTo = 0) {
		$pagination = $dataProvider->getPagination();
		$str = '';
		$currentPage = 1;
		
// 		Fb::warn("pageNo: $pageNo", "prepareSearchUserResultJson()");
// 		Fb::warn("pagination->pageCount: $pagination->pageCount", "prepareSearchUserResultJson()");
// 		Fb::warn("pagination->currentPage: $pagination->currentPage", "prepareSearchUserResultJson()");
		
		if($upTo == 0) //Sadece ilgili sayfayi don
		{
			if(($pageNo > 1) && ($pageNo <= $pagination->pageCount))
			{
				$pagination->setCurrentPage($pageNo - 1); //Sets the zero-based index of the current page
// 				Fb::warn("pagination->setCurrentPage(pageNo - 1: $pageNo - 1)", "prepareSearchUserResultJson()");
// 				Fb::warn("pagination->pageCount: $pagination->pageCount", "prepareSearchUserResultJson()");
// 				Fb::warn("pagination->currentPage: $pagination->currentPage", "prepareSearchUserResultJson()");				
			}
			
			$row = $dataProvider->getData(true);
			$itemCount = count($row);
			
			for ($i = 0; $i < $itemCount; $i++) {
				$row[$i]['id'] = isset($row[$i]['id']) ? $row[$i]['id'] : null;
				$row[$i]['Name'] = isset($row[$i]['Name']) ? $row[$i]['Name'] : null;
				$row[$i]['fb_id']= isset($row[$i]['fb_id']) ? $row[$i]['fb_id'] : null;
				$row[$i]['account_type'] = isset($row[$i]['account_type']) ? $row[$i]['account_type'] : null;
				$row[$i]['status'] = isset($row[$i]['status']) ? $row[$i]['status'] : null;
				$row[$i]['requester'] = isset($row[$i]['requester']) ? $row[$i]['requester'] : null;
				
				if ($i > 0)  {
					$str .= ",";
				}				
					
				$str .= CJSON::encode(array(
						'id'=>$row[$i]['id'],
						'Name'=>$row[$i]['Name'],
						'fb_id'=>$row[$i]['fb_id'],
						'account_type'=>$row[$i]['account_type'],
						'status'=>$row[$i]['status'],
						'requester'=>$row[$i]['requester'],
				));
			}

			$currentPage = $pagination->currentPage + 1;
		}
		else //Verilen sayfa numarasina kadar olan tum sayfalara ait kisi listesi isteniyorsa
		{
			$pages = '';
			
			for ($j = 0; $j < $pageNo; $j++) 
			{			
				if($j < $pagination->pageCount)
				{
					$pagination->setCurrentPage($j); //Sets the zero-based index of the current page
				}
				
				$row = $dataProvider->getData(true);
				$itemCount = count($row);

				for ($i = 0; $i < $itemCount; $i++) {
					$row[$i]['id'] = isset($row[$i]['id']) ? $row[$i]['id'] : null;
					$row[$i]['Name'] = isset($row[$i]['Name']) ? $row[$i]['Name'] : null;
					$row[$i]['fb_id']= isset($row[$i]['fb_id']) ? $row[$i]['fb_id'] : null;
					$row[$i]['account_type'] = isset($row[$i]['account_type']) ? $row[$i]['account_type'] : null;
					$row[$i]['status'] = isset($row[$i]['status']) ? $row[$i]['status'] : null;
					$row[$i]['requester'] = isset($row[$i]['requester']) ? $row[$i]['requester'] : null;
						
					if (($j > 0) || ($i > 0))  {
						$str .= ",";
					}					
					
					$str .= CJSON::encode(array(
							'id'=>$row[$i]['id'],
							'Name'=>$row[$i]['Name'],
							'fb_id'=>$row[$i]['fb_id'],
							'account_type'=>$row[$i]['account_type'],
							'status'=>$row[$i]['status'],
							'requester'=>$row[$i]['requester'],
					));
				}

				if ($j > 0)  {
					$pages .= ",";
				}
								
				$pages .= $j + 1;
			}

			$currentPage ='['.$pages.']';
		}

		$result = null;
		
		if($pagination->pageCount > 0) //If there exists any result
		{
			$result = "1";
			$str = '{"result":"'.$result.'", "userlist": ['.$str.'], "pageNo":"'.$currentPage .'", "pageCount":"'.$pagination->pageCount.'", "totalUserCount":"'.$dataProvider->totalItemCount.'"}';
		}
		else
		{
			$result = "0";
			$str = '{"result":"'.$result.'"}';
		}

		return $str;
	}
	
	private function get_timeago($ptime)
	{
		$etime = time() - $ptime;
	
		if( $etime < 1 )
		{
			return Yii::t('users', 'less than 1 second ago');
			//return 'less than 1 second ago';
		}
	
		$a = array( 12 * 30 * 24 * 60 * 60  => Yii::t('users', 'year'),
		30 * 24 * 60 * 60       => Yii::t('users', 'month'),
		24 * 60 * 60            => Yii::t('users', 'day'),
		60 * 60             => Yii::t('users', 'hour'),
		60                  => Yii::t('users', 'minute'),
		1                   => Yii::t('users', 'second')
		);
		
		foreach( $a as $secs => $str )
		{
			$d = $etime / $secs;
	
			if( $d >= 1 )
			{
				$r = round( $d );
				return $r . ' ' . $str . ( $r > 1 ? Yii::t('common', 'plural') : '' ) . ' ' .Yii::t('users', 'ago');
				//return $r . ' ' . $str . ( $r > 1 ? 's' : '' ) . ' ' .'ago';
			}
		}
	}	
	
	private function preparePastPointsJson($dataProvider) {
		$rows = $dataProvider->getData();
		$itemCount = count($rows);
		
		$str = '';
		for ($i = 0; $i < $itemCount; $i++) {
			
			$rows[$i]['latitude'] = isset($rows[$i]['latitude']) ? $rows[$i]['latitude'] : null;
			$rows[$i]['longitude'] = isset($rows[$i]['longitude']) ? $rows[$i]['longitude'] : null;
			$rows[$i]['altitude'] = isset($rows[$i]['altitude']) ? $rows[$i]['altitude'] : null;
			$rows[$i]['dataArrivedTime'] = isset($rows[$i]['dataArrivedTime']) ? $rows[$i]['dataArrivedTime'] : null;
			$rows[$i]['deviceId'] = isset($rows[$i]['deviceId']) ? $rows[$i]['deviceId'] : null;
			$rows[$i]['dataCalculatedTime'] = isset($rows[$i]['dataCalculatedTime']) ? $rows[$i]['dataCalculatedTime'] : null;
			//$rows[$i]['locationSource'] = isset($rows[$i]['locationSource']) ? $rows[$i]['locationSource'] : null;
			$rows[$i]['address'] = isset($rows[$i]['address']) ? $rows[$i]['address'] : Yii::t('users', 'There is no address info');  
			$rows[$i]['country'] = isset($rows[$i]['country']) ? $rows[$i]['country'] : null;
			
			if ($i > 0) {
				$str .= ',';
			}
			
			$dataArrivedTimestamp = strtotime($rows[$i]['dataArrivedTime']);
			$dataCalculatedTimestamp = strtotime($rows[$i]['dataCalculatedTime']);

			if(Yii::app()->language == 'tr')
			{
				$rows[$i]['dataArrivedTime'] = strftime("%d ", $dataArrivedTimestamp).Yii::t('common', strftime("%b", $dataArrivedTimestamp)).strftime(" %Y %H:%M:%S", $dataArrivedTimestamp);
				$rows[$i]['dataCalculatedTime'] = strftime("%d ", $dataCalculatedTimestamp).Yii::t('common', strftime("%b", $dataCalculatedTimestamp)).strftime(" %Y %H:%M:%S", $dataCalculatedTimestamp);
			}			
			
			$str .= CJSON::encode(array(
						'latitude'=>$rows[$i]['latitude'],
						'longitude'=>$rows[$i]['longitude'],
						'altitude'=>$rows[$i]['altitude'],
						'calculatedTime'=>$rows[$i]['dataCalculatedTime'],
					    //'locationSource'=>$rows[$i]['locationSource'],
						'time'=>$dataArrivedTimestamp,
						'timestamp'=>$dataArrivedTimestamp,
						//'timeAgo'=>$this->get_timeago($dataArrivedTimestamp),
						'deviceId'=>$rows[$i]['deviceId'],
						'address'=>$rows[$i]['address'].(($rows[$i]['country'] != null)?(' / '.$this->string2upper(Yii::t('countries', $rows[$i]['country']))):"")
				));
		}
		
		$pagination = $dataProvider->getPagination();
		$currentPage = $pagination->currentPage + 1;
		$str='{"userwashere":['.$str.'], "pageNo":"'.$currentPage .'", "pageCount":"'.$pagination->pageCount.'"}';
		return $str;
		
	}

	private function getUserJsonItem($row) {
		$row['id'] = isset($row['id']) ? $row['id'] : "";
		//		$row->username = isset($row->username) ? $row->username : null;
		$row['isFriend'] = isset($row['isFriend']) ? $row['isFriend'] : 0;
		$row['realname'] = isset($row['Name']) ? $row['Name'] : "";
		$row['latitude'] = isset($row['latitude']) ? $row['latitude'] : "";
		$row['longitude'] = isset($row['longitude']) ? $row['longitude'] : "";
		$row['altitude'] = isset($row['altitude']) ? $row['altitude'] : "";
		$row['lastLocationAddress'] = isset($row['lastLocationAddress']) ? $row['lastLocationAddress'] : "";
		$row['lastLocationCountry'] = isset($row['lastLocationCountry']) ? $row['lastLocationCountry'] : "";
		$row['dataArrivedTime'] = isset($row['dataArrivedTime']) ? $row['dataArrivedTime'] : "";
		$row['message'] = isset($row['message']) ? $row['message'] : "";
		$row['deviceId'] = isset($row['deviceId']) ? $row['deviceId'] : "";
		$row['userType'] = isset($row['userType']) ? $row['userType'] : "";		
		$row['status_message'] = isset($row['status_message']) ? $row['status_message'] : "";
		$row['dataCalculatedTime'] = isset($row['dataCalculatedTime']) ? $row['dataCalculatedTime'] : "";
		$row['locationSource'] = isset($row['locationSource']) ? $row['locationSource'] : "";
		$row['gp_image'] = "";
		$row['fb_id'] = isset($row['fb_id']) ? $row['fb_id'] : "";
		$row['profilePhotoStatus'] = isset($row['profilePhotoStatus']) ? $row['profilePhotoStatus'] : "";
		$row['g_id'] = "";
		$row['account_type'] =  isset($row['account_type']) ? $row['account_type'] : "";
		$row['isVisible'] =  isset($row['isVisible']) ? $row['isVisible'] : "";
		
		//Fb::warn($row['id'], "user");
		
		$dataArrivedTimestamp = strtotime($row['dataArrivedTime']);
		$dataCalculatedTimestamp = strtotime($row['dataCalculatedTime']);		
		
		if(Yii::app()->language == 'tr')
		{
			$row['dataArrivedTime'] = strftime("%d ", $dataArrivedTimestamp).Yii::t('common', strftime("%b", $dataArrivedTimestamp)).strftime(" %Y %H:%M:%S", $dataArrivedTimestamp);
			$row['dataCalculatedTime'] = strftime("%d ", $dataCalculatedTimestamp).Yii::t('common', strftime("%b", $dataCalculatedTimestamp)).strftime(" %Y %H:%M:%S", $dataCalculatedTimestamp);
		}
				
		$bsk=   CJSON::encode( array(
				'user'=>$row['id'],
				'isFriend'=>$row['isFriend'],
				'realname'=>$row['realname'],
				'latitude'=>$row['latitude'],
				'longitude'=>$row['longitude'],
				'altitude'=>$row['altitude'],
				'address'=>$row['lastLocationAddress'].' / '.$this->string2upper(Yii::t('countries', $row['lastLocationCountry'])),
				'calculatedTime'=>$row['dataCalculatedTime'],
				'locationSource'=>$row['locationSource'],
				//'time'=>$row['dataArrivedTime'],
				'time'=>$dataArrivedTimestamp,
				'timestamp'=>$dataArrivedTimestamp,
				'message'=>$row['message'],
				'status_message'=>$row['status_message'],
				'deviceId'=>$row['deviceId'],
				'userType'=>$row['userType'],
				'gp_image'=>$row['gp_image'],
				'fb_id'=>$row['fb_id'],
				'profilePhotoStatus'=>$row['profilePhotoStatus'],
				'g_id'=>$row['g_id'],
				'account_type'=>$row['account_type'],
				'isVisible'=>$row['isVisible']
		));

		return $bsk;
	}	
}