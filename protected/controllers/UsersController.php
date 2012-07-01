<?php

class UsersController extends Controller
{
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
                'actions'=>array('addAsFriend', 'approveFriendShip', 
                				 'deleteFriendShip','getFriendRequestList',
        						 'getUserPastPointsXML', 'getUserListXML', 'search',
        						 'takeMyLocation', 'getUserInfo'),
        		'users'=>array('?'),
            )
        );
    }
    
	
	/*
	 * this action is used by mobile clients
	 */
	public function actionTakeMyLocation()
	{
		$result = "Missing parameter";
		$resultArray = array("result"=>$result);
		if (isset($_REQUEST['latitude']) && $_REQUEST['latitude'] != NULL
		&& isset($_REQUEST['longitude']) && $_REQUEST['longitude'] != NULL
		&& isset($_REQUEST['altitude']) && $_REQUEST['altitude'] != NULL
		&& isset($_REQUEST['deviceId']) && $_REQUEST['deviceId'] != NULL
		&& isset($_REQUEST['time']) && $_REQUEST['time'] != NULL
		)
		{
			$latitude = (float) $_REQUEST['latitude'];
			$longitude = (float) $_REQUEST['longitude'];
			$altitude = (float) $_REQUEST['altitude'];
			$deviceId = $_REQUEST['deviceId'];
			$calculatedTime = date('Y-m-d H:i:s',  $_REQUEST['time']);

			$result = "No valid user id";	
			if (Yii::app()->user->id != false) 
			{
				$result = "Unknown Error";
				if (Users::model()->updateLocation($latitude, $longitude, $altitude, 
													$deviceId, $calculatedTime, Yii::app()->user->id) == 1)
				{
					$sqlWasHere = sprintf('INSERT INTO '
									. UserWasHere::model()->tableName() . '
									(userId, latitude, longitude, altitude, dataArrivedTime, deviceId, dataCalculatedTime)
		    						VALUES(%d,	%f, %f, %f, NOW(), "%s", "%s") 
									',
								  	Yii::app()->user->id, $latitude, $longitude, $altitude, $deviceId, $calculatedTime);
					Yii::app()->db->createCommand($sqlWasHere)->execute();
					$result = "1";
				}
			}
			
		}
		$resultArray = array("result"=>$result);
		if ($result == "1") {
			$resultArray = array_merge($resultArray, array(                 			
									"minDataSentInterval"=> Yii::app()->params->minDataSentInterval,
									"minDistanceInterval"=> Yii::app()->params->minDistanceInterval,
							));
		}
		echo CJSON::encode(
                 		$resultArray
		);
		//$this->redirect(array('geofence/checkGeofenceBoundaries', 'friendId' => Yii::app()->user->id, 'friendLatitude' => $latitude, 'friendLongitude' => $longitude));
		Yii::app()->end();
	}

	public function actionGetFriendList()
	{
		$userType = UserType::RealUser;
		
		if (isset($_GET['userType']) && $_GET['userType'] != NULL)
		{
			$userType = $_GET['userType'];		
		}	
								
		if(Yii::app()->user->id != null)
		{
			$sqlCount = 'SELECT count(*)
						 FROM '. Friends::model()->tableName() . ' f
						 LEFT JOIN ' . Users::model()->tableName() . ' u
						 ON u.Id = IF(f.friend1 != '.Yii::app()->user->id.', f.friend1, f.friend2)						  
						 WHERE (friend1 = '.Yii::app()->user->id.' 
						 OR friend2 ='.Yii::app()->user->id.') AND status= 1 AND u.userType = "'.$userType.'"';
			
// 			if (isset($_GET['userType']) && $_GET['userType'] != NULL)
// 			{
// 				$sqlCount = $sqlCount.' AND u.userType = "'.$userType.'"';
// 			}
	
			$count=Yii::app()->db->createCommand($sqlCount)->queryScalar();
	
			$sql = 'SELECT u.Id as id, u.realname as Name, f.Id as friendShipId, date_format(u.dataArrivedTime,"%d %b %Y %T") as dataArrivedTime, date_format(u.dataCalculatedTime,"%d %b %Y %T") as dataCalculatedTime
					FROM '. Friends::model()->tableName() . ' f 
					LEFT JOIN ' . Users::model()->tableName() . ' u
						ON u.Id = IF(f.friend1 != '.Yii::app()->user->id.', f.friend1, f.friend2)
					WHERE (friend1 = '.Yii::app()->user->id.' 
							OR friend2='.Yii::app()->user->id.') AND status= 1 AND u.userType = "'.$userType.'"';
			
// 			if (isset($_GET['userType']) && $_GET['userType'] != NULL)
// 			{
// 				$sql = $sql.' AND u.userType = "'.$userType.'"';
// 			}			
			
			$dataProvider = new CSqlDataProvider($sql, array(
			    											'totalItemCount'=>$count,
														    'sort'=>array(
							        							'attributes'=>array(
							             									'id', 'Name',
			),
			),
														    'pagination'=>array(
														        'pageSize'=>Yii::app()->params->itemCountInOnePage,
			),
			));		
		}
		else
		{
			$dataProvider = null;
		}

		Yii::app()->clientScript->scriptMap['jquery.js'] = false;
	//	Yii::app()->clientScript->scriptMap['jquery.yiigridview.js'] = false;
		$this->renderPartial('usersInfo',array('dataProvider'=>$dataProvider,'model'=>new SearchForm(), 'userType'=>$userType), false, true);
	}
	
	private function unsetFriendIdList() {
		unset(Yii::app()->session['friendList']);
	}
	
	private function getFriendCount() {
		if (isset(Yii::app()->session['friendList'])== false) {
			$this->getFriendIdList();
		}
		
		return Yii::app()->session['friendCount'];
	}
	
	private function getFriendArray() {
		if (isset(Yii::app()->session['friendList'])== false) {
			$this->getFriendIdList();
		}
		
		return Yii::app()->session['friendArray'];
	}
	
	
	private function getFriendIdList() {
	
		if (isset(Yii::app()->session['friendList']) == false) {
			$sql = "SELECT IF( friend1 != ". Yii::app()->user->id .", friend1, friend2 ) as friend \n"
	    			. "FROM traceper_friends\n"
				    . "WHERE \n"
				    . "((friend1=".Yii::app()->user->id." AND friend2Visibility=1)\n"
				    . "OR (friend2=".Yii::app()->user->id." AND friend1Visibility=1)\n"
				    . ")\n"
				    . "AND STATUS =1";
				    
			$friendsResult = Yii::app()->db->createCommand($sql)->queryAll();
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
		
		return Yii::app()->session['friendList'];	
	}
	
	

	/**
	 * this function returns users and images in xml format
	 */
	public function actionGetUserListXML()
	{
		//if (Yii::app()->user->isGuest) {
			//return;
		//}
		$pageNo = 1;
		if (isset($_REQUEST['pageNo']) && $_REQUEST['pageNo'] > 0) {
			$pageNo = (int) $_REQUEST['pageNo'];
		}
		$offset = ($pageNo - 1) * Yii::app()->params->itemCountInDataListPage;
		$out = '';
		$dataFetchedTimeKey = "UsersController.dataFetchedTime";
		if (isset($_REQUEST['list'])) {
			if ($_REQUEST['list'] == "onlyUpdated")
			{
				$time = Yii::app()->session[$dataFetchedTimeKey];
				if ($time !== null && $time !== false)
				{
					
					$friendCount = $this->getFriendCount() + 1; // +1 is for herself
					$friendIdList = $this->getFriendIdList();
				
					if ($friendIdList != -1) {
						$friendIdList .= ',' . Yii::app()->user->id;
					}
					else {
						$friendIdList = Yii::app()->user->id;
					}
					
					$sqlPageCount = 'SELECT ceil(count(*)/'.Yii::app()->params->itemCountInDataListPage.')
									 FROM '. Users::model()->tableName() . ' u 
									 WHERE u.Id IN ('. $friendIdList .')
								  		AND unix_timestamp(u.dataArrivedTime) >= '. $time;
					
					$pageCount = Yii::app()->db->createCommand($sqlPageCount)->queryScalar();
					
					$sql = 'SELECT u.Id as id, u.realname,u.latitude, u.longitude, u.altitude, 
								date_format(u.dataArrivedTime, "%H:%i %d/%m/%Y") as dataArrivedTime, 
								date_format(u.dataCalculatedTime, "%H:%i %d/%m/%Y") as dataCalculatedTime,
								1 isFriend
							FROM '. Users::model()->tableName() . ' u 
							WHERE u.Id IN ('. $friendIdList .')
								  AND unix_timestamp(u.dataArrivedTime) >= '. $time . '
							LIMIT ' . $offset . ' , ' . Yii::app()->params->itemCountInDataListPage;
					
					$out = $this->prepareXML($sql, $pageNo, $pageCount, "userList");
				}

			}
		}
		else {

			$friendCount = $this->getFriendCount() + 1; // +1 is for herself
			$pageCount = ceil($friendCount / Yii::app()->params->itemCountInDataListPage);
					
			$friendIdList = $this->getFriendIdList();
									
			if ($friendIdList != -1) {
				$friendIdList .= ',' . Yii::app()->user->id;
			}
			else {
				$friendIdList = Yii::app()->user->id;
			}
					
			$sql = 'SELECT u.Id as id, u.realname,u.latitude, u.longitude, u.altitude, u.fb_id,
						date_format(u.dataArrivedTime, "%H:%i %d/%m/%Y") as dataArrivedTime, 
						date_format(u.dataCalculatedTime, "%H:%i %d/%m/%Y") as dataCalculatedTime,
						1 isFriend
					FROM '. Users::model()->tableName() . ' u
					WHERE u.Id IN ('. $friendIdList .')
					LIMIT ' . $offset . ' , ' . Yii::app()->params->itemCountInDataListPage;

		$out = $this-> prepareXML($sql, $pageNo, $pageCount, "userList");
		}
		echo $out;
		Yii::app()->session[$dataFetchedTimeKey] = time();
		Yii::app()->end();
	}
	
	public function actionGetUserInfo()
	{
		$out = "Missing parameter";
		if (isset($_REQUEST['userId']) && $_REQUEST['userId'] > 0) {
			
			$userId = (int) $_REQUEST['userId'];
			
			$friendArray = $this->getFriendArray(); 
			$out = "No permission to get this user location";
			if ($userId == Yii::app()->user->id || array_search($userId,$friendArray) != false)
			{
				$sql = 'SELECT u.Id as id, u.realname,u.latitude, u.longitude, u.altitude, u.fb_id,  
							date_format(u.dataArrivedTime, "%H:%i %d/%m/%Y") as dataArrivedTime, 
							date_format(u.dataCalculatedTime, "%H:%i %d/%m/%Y") as dataCalculatedTime,
							1 isFriend
					FROM ' . Users::model()->tableName() . ' u 
					WHERE  u.Id = '. $userId .'							
					LIMIT 1' ;
				$out = $this-> prepareXML($sql, 1, 1, "userList");
			}
			
		}
		
		echo $out;
		Yii::app()->end();
	}
	public function actionGetUserInfoJSON()
	{
		$out = "Missing parameter";
		if (isset($_REQUEST['userId']) && $_REQUEST['userId'] > 0) {
			
			$userId = (int) $_REQUEST['userId'];
			
			$friendArray = $this->getFriendArray(); 
			$out = "No permission to get this user location";
			if ($userId == Yii::app()->user->id || array_search($userId,$friendArray) != false)
			{
				$sql = 'SELECT u.Id as id, u.realname,u.latitude, u.longitude, u.altitude,  
							date_format(u.dataArrivedTime, "%H:%i %d/%m/%Y") as dataArrivedTime, 
							date_format(u.dataCalculatedTime, "%H:%i %d/%m/%Y") as dataCalculatedTime,
							1 isFriend
					FROM ' . Users::model()->tableName() . ' u 
					WHERE  u.Id = '. $userId .'							
					LIMIT 1' ;
				$out = $this-> prepareJson($sql, "userInfo",$userId);
			}
			
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
		$email = $_REQUEST['email'];
		$password = $_REQUEST['password'];
		if (!empty($_REQUEST['offset'])){
			$offset = (int) $_REQUEST['offset'];
			}else{
			$offset = 0;
			}
			if(!empty($_REQUEST['range'])){
			$range= (int) $_REQUEST['range'];
			}else{
			$range=29;
			}
		
		$sql = sprintf('SELECT Id
								FROM '.  Users::model()->tableName() .' 
							WHERE email = "%s" 
						  		  AND 
						  		  password = "%s"
							LIMIT 1', $email, md5($password));
		$userId = Yii::app()->db->createCommand($sql)->queryScalar();
		
		$result = "Email or password not correct";
		if ($userId == false) {
			return;
		}
		

	/*
		$out = '';
		$dataFetchedTimeKey = "UsersController.dataFetchedTime";
		
	
			$sql = 'SELECT u.Id as id, u.realname,u.latitude, u.longitude, u.altitude, f.Id as friendShipId, u.dataArrivedTime, u.dataCalculatedTime, u.gp_image, u.fb_id, u.g_id, u.account_type,
						1 isFriend
				FROM '. Friends::model()->tableName() . ' f 
				LEFT JOIN ' . Users::model()->tableName() . ' u
					ON u.Id = IF(f.friend1 != '.$userId .', f.friend1, f.friend2)
				WHERE ((f.friend1 = '. $userId .'  AND f.friend2Visibility = 1) 
										OR (f.friend2 ='. $userId .'  AND f.friend1Visibility = 1)) AND status= 1
				 ' ;

			/* 
			 	$sql = 'SELECT u.Id as id, u.realname,u.latitude, u.longitude, u.altitude, f.Id as friendShipId, u.dataArrivedTime, u.dataCalculatedTime,
						1 isFriend
				FROM '. Friends::model()->tableName() . ' f 
				LEFT JOIN ' . Users::model()->tableName() . ' u
					ON u.Id = IF(f.friend1 != '.Yii::app()->user->id.', f.friend1, f.friend2)
				WHERE (friend1 = '.Yii::app()->user->id.' 
						OR friend2 ='.Yii::app()->user->id.') AND status= 1
				 ' ;
			 
			

		$out = $this-> prepareJson($sql, "userList");
		
		echo $out;
		Yii::app()->session[$dataFetchedTimeKey] = time();
		Yii::app()->end();
		
		
		*/
		
		
			$friendCount = $this->getFriendCount() + 1; // +1 is for herself
		
					
			$friendIdList = $this->getFriendIdList();
				
			if ($friendIdList != -1) {
				$friendIdList .= ',' . Yii::app()->user->id;
			}
			else {
				$friendIdList = Yii::app()->user->id;
			}
/*					
			$sql = 'SELECT u.Id as id, u.realname,u.latitude, u.longitude, u.altitude, u.gp_image, u.fb_id, u.g_id, u.account_type,
						date_format(u.dataArrivedTime, "%H:%i %d/%m/%Y") as dataArrivedTime, 
						date_format(u.dataCalculatedTime, "%H:%i %d/%m/%Y") as dataCalculatedTime,
						1 isFriend
					FROM '. Users::model()->tableName() . ' u
					WHERE u.Id IN ('. $friendIdList .')
					LIMIT ' . $offset . ' , ' . $range;
*/
			$sql = 'SELECT u.Id as id, u.realname,u.latitude, u.longitude, u.altitude, u.gp_image, u.fb_id, u.g_id, u.account_type,
						date_format(u.dataArrivedTime, "%H:%i %d/%m/%Y") as dataArrivedTime, 
						date_format(u.dataCalculatedTime, "%H:%i %d/%m/%Y") as dataCalculatedTime,
						1 isFriend
					FROM '. Users::model()->tableName() . ' u
					WHERE u.Id IN ('. $friendIdList .')
					LIMIT ' . $offset . ' , ' . $range;
			
		$out = $this-> prepareJson($sql, "userList");

		echo $out;
		Yii::app()->session[$dataFetchedTimeKey] = time();
		Yii::app()->end();
		
	}
	
	public function actionGetUserPastPointsXML(){

		if (isset($_REQUEST['userId']))
		{
			$userId = (int) $_REQUEST['userId'];
			$pageNo = 1;
			if (isset($_REQUEST['pageNo']) && $_REQUEST['pageNo'] > 0) {
				$pageNo = (int) $_REQUEST['pageNo'];
			}
			$offset = ($pageNo - 1) * Yii::app()->params->itemCountInDataListPage;
			$offset++;  // to not get the last location
			$sql = 'SELECT
							longitude, latitude, deviceId, 
							date_format(u.dataArrivedTime,"%d %b %Y %T") as dataArrivedTime, date_format(u.dataCalculatedTime,"%d %b %Y %T") as dataCalculatedTime
					FROM ' . UserWasHere::model()->tableName() .' u
					WHERE 
						userId = '. $userId . '
					ORDER BY 
						Id DESC
					LIMIT '. $offset . ','
					. Yii::app()->params->itemCountInDataListPage;
						
					// subtract 1 to not get the last location into consideration
					$sqlPageCount = 'SELECT
									ceil((count(Id)-1)/ '. Yii::app()->params->itemCountInDataListPage .')
							 FROM '. UserWasHere::model()->tableName() .'
							 WHERE 
								 	userId = '. $userId;				
					$pageCount = Yii::app()->db->createCommand($sqlPageCount)->queryScalar();
						
					$out = $this->prepareXML($sql, $pageNo, $pageCount, "userPastLocations", $userId);
		}
		echo $out;
	}
	public function actionGetUserPastPointsJSON(){

		if (isset($_REQUEST['userId']))
		{
			$userId = (int) $_REQUEST['userId'];
			if (!empty($_REQUEST['offset'])){
			$offset = (int) $_REQUEST['offset'];
			}else{
			$offset = 0;
			}
			if(!empty($_REQUEST['range'])){
			$range= (int) $_REQUEST['range'];
			}else{
			$range=29;
			}
						
			$sql = 'SELECT
							longitude, latitude, deviceId, 
							date_format(u.dataArrivedTime,"%d %b %Y %T") as dataArrivedTime, date_format(u.dataCalculatedTime,"%d %b %Y %T") as dataCalculatedTime
					FROM ' . UserWasHere::model()->tableName() .' u
					WHERE 
						userId = '. $userId . '
					ORDER BY 
						Id DESC
					LIMIT '. $offset . ','
					. $range ;
				
					$out = $this->PrepareJson($sql, "userPastLocations", $userId);
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

				$sqlCount = 'SELECT count(*)
					 FROM '. Users::model()->tableName() . ' u 
					 WHERE realname like "%'. $model->keyword .'%"';

				$count=Yii::app()->db->createCommand($sqlCount)->queryScalar();

				/*
				 * if status is 0 it means friend request made but not yet confirmed
				 * if status is 1 it means friend request is confirmed.
				 * if status is -1 it means there is no relation of any kind between users.
				 */
				$sql = 'SELECT u.Id as id, u.realname as Name, f.Id as friendShipId,
								 IF(f.status = 0 OR f.status = 1, f.status, -1) as status,
								 IF(f.friend1 = '. Yii::app()->user->id .', true, false ) as requester
						FROM '. Users::model()->tableName() . ' u 
						LEFT JOIN '. Friends::model()->tableName().' f 
							ON  (f.friend1 = '. Yii::app()->user->id .' 
								 AND f.friend2 =  u.Id)
								 OR 
								 (f.friend1 = u.Id 
								 AND f.friend2 = '. Yii::app()->user->id .' ) 
						WHERE u.realname like "%'. $model->keyword .'%"' ;

				$dataProvider = new CSqlDataProvider($sql, array(
		    											'totalItemCount'=>$count,
													    'sort'=>array(
						        							'attributes'=>array(
						             									'id', 'Name',
															),
														 ),
														'params'=>array(CHtml::encode('SearchForm[keyword]')=>$model->attributes['keyword']),
													    'pagination'=>array(
													        'pageSize'=>Yii::app()->params->itemCountInOnePage,
															'params'=>array(CHtml::encode('SearchForm[keyword]')=>$model->attributes['keyword']),
				),
				));
					
			}
		}
		Yii::app()->clientScript->scriptMap['jquery.js'] = false;
		Yii::app()->clientScript->scriptMap['jquery.yiigridview.js'] = false;
		$this->renderPartial('searchResults',array('model'=>$model, 'dataProvider'=>$dataProvider), false, true);
	}
	
	public function actionSearchJSON() {
	$model = new SearchForm();
	$userId= Yii::app()->user->id;
	$out = "missing parameter!";
	$dataProvider = null;
		if(isset($_REQUEST['SearchForm']))
		{
			$model->attributes = $_REQUEST['SearchForm'];
			if ($model->validate()) {

			$sqlCount = 'SELECT count(*)
				FROM '. Users::model()->tableName() . ' u 
				WHERE realname like "%'. $model->keyword .'%"';

			$count=Yii::app()->db->createCommand($sqlCount)->queryScalar();

/*
* if status is 0 it means friend request made but not yet confirmed
* if status is 1 it means friend request is confirmed.
* if status is -1 it means there is no relation of any kind between users.
*/
			$sql = 'SELECT u.Id as id, u.realname as Name, f.Id as friendShipId,u.gp_image, u.fb_id, u.g_id, u.account_type,
				IF(f.status = 0 OR f.status = 1, f.status, -1) as status,
				IF(f.friend1 = '. Yii::app()->user->id .', true, false ) as requester
				FROM '. Users::model()->tableName() . ' u 
				LEFT JOIN '. Friends::model()->tableName().' f 
				ON  (f.friend1 = '. Yii::app()->user->id .' 
				AND f.friend2 =  u.Id)
				OR 
				(f.friend1 = u.Id 
				AND f.friend2 = '. Yii::app()->user->id .' ) 
				WHERE u.realname like "%'. $model->keyword .'%" LIMIT 0,29' ;

				$out = $this-> prepareJson($sql, "userSearch" ,$userId);
		}
	}
	echo $out;
}
	public function actionDeleteFriendShip(){
		$result = 'Missing Data';
		if (isset($_REQUEST['friendShipId']))
		{
			$friendShipId = (int) $_REQUEST['friendShipId'];
			$friendShip = Friends::model()->findByPk($friendShipId, array('condition'=>'friend1=:friend1 OR
																		  friend2=:friend2',
																		 'params'=>array(':friend1'=>Yii::app()->user->id,
																						':friend2'=>Yii::app()->user->id,
			),
			)
			);
			$result = 'Error occured';
			if ($friendShip != null && $friendShip->delete()){
				$result = 1;
				$this->unsetFriendIdList();
			}
		}


		echo CJSON::encode(array(
								"result"=>$result,
		));

	}
	
	public function actionDeleteUser(){
		$result = 'Missing Data';
		if (isset($_REQUEST['userId']))
		{
			$userId = (int) $_REQUEST['userId'];
			$user = Users::model()->findByPk($userId);

			$result = 'Error occured';
			if ($user != null && $user->delete()){
				$result = 1;
				$this->unsetFriendIdList();
			}
		}
		
		echo CJSON::encode(array(
				"result"=>$result,
		));
	
	}	

	public function actionGetFriendRequestList(){

		// we look at the friend2 field because requester id is stored in friend1 field
		// and only friend who has been requested to be a friend can approve frienship
		$sqlCount = 'SELECT count(*)
					 FROM '. Friends::model()->tableName() . ' f 
					 WHERE friend2 = '.Yii::app()->user->id.' 
						   AND status= 0';

		$count=Yii::app()->db->createCommand($sqlCount)->queryScalar();

		/**
		 * because we use same view in listing users, we put requester field as false
		 * to make view show approve link,
		 * requester who make friend request cannot approve request
		 */
		$sql = 'SELECT u.Id as id, u.realname as Name, f.Id as friendShipId, f.status,
					   false as requester	   
				FROM '. Friends::model()->tableName() . ' f 
				LEFT JOIN ' . Users::model()->tableName() . ' u
					ON u.Id = f.friend1
				WHERE friend2='.Yii::app()->user->id.' AND status= 0'  ;

		$dataProvider = new CSqlDataProvider($sql, array(
		    											'totalItemCount'=>$count,
													    'sort'=>array(
						        							'attributes'=>array(
						             									'id', 'Name',
		),
		),
													    'pagination'=>array(
													        'pageSize'=>Yii::app()->params->itemCountInOnePage,
		),
		));
			
		Yii::app()->clientScript->scriptMap['jquery.js'] = false;
		Yii::app()->clientScript->scriptMap['jquery.yiigridview.js'] = false;
		$this->renderPartial('userListDialog',array('dataProvider'=>$dataProvider), false, true);

	}
	public function actionGetFriendRequestListJson(){

		// we look at the friend2 field because requester id is stored in friend1 field
		// and only friend who has been requested to be a friend can approve frienship
		$sqlCount = 'SELECT count(*)
					 FROM '. Friends::model()->tableName() . ' f 
					 WHERE friend2 = '.Yii::app()->user->id.' 
						   AND status= 0';

		$count=Yii::app()->db->createCommand($sqlCount)->queryScalar();

		/**
		 * because we use same view in listing users, we put requester field as false
		 * to make view show approve link,
		 * requester who make friend request cannot approve request
		 */
		$sql = 'SELECT u.Id as id, u.realname as Name, f.Id as friendShipId, f.status, u.gp_image, u.fb_id, u.g_id, u.account_type,
					   false as requester	   
				FROM '. Friends::model()->tableName() . ' f 
				LEFT JOIN ' . Users::model()->tableName() . ' u
					ON u.Id = f.friend1
				WHERE friend2='.Yii::app()->user->id.' AND status= 0'  ;

		$out = $this-> prepareJson($sql, "userSearch" ,$userId);
		echo $out;

	}
	public function actionApproveFriendShip(){
		$result = 'Missing Data';
		if (isset($_REQUEST['friendShipId']))
		{

			$friendShipId = (int) $_REQUEST['friendShipId'];
			// only friend2 can approve friendship because friend1 makes the request
			$friendShip = Friends::model()->findByPk($friendShipId, array('condition'=>'friend2=:friend2',
																		  'params'=>array(':friend2'=>Yii::app()->user->id,
			),
			)
			);
			$result = 'Error occured';
			if ($friendShip != null){
				$friendShip->status = 1;
				if ($friendShip->save()) {
					$result = 1;
					$this->unsetFriendIdList();
				}
			}
		}
		echo CJSON::encode(array(
								"result"=>$result,
		));

	}

	public function actionAddAsFriend()
	{
		$result = 'Missing parameter';
		if (isset($_REQUEST['friendId'])) {
			$friendId = (int)$_REQUEST['friendId'];

			$friend = new Friends();
			$friend->friend1 = Yii::app()->user->id;
			$friend->friend1Visibility = 1; //default visibility setting is visible
			$friend->friend2 = $friendId;
			$friend->friend2Visibility = 1; //default visibility setting is visible
			$friend->status = 0;
			$result = 'Error occured';
			try
			{
			if ($friend->save()) {
				$result = 1;
						echo CJSON::encode(array(
								"result"=>$result,
									));
			}
			}
			catch (Exception $e)
			{
					if($e->getCode() == Yii::app()->params->duplicateEntryDbExceptionCode) //Duplicate Entry
						{
				$result = 0;
						echo CJSON::encode(array(
								"result"=>$result,
									));
						}
				Yii::app()->end();		
			}
			
		}

	}

	private function prepareXML($sql, $pageNo, $pageCount, $type="userList", $userId=NULL)
	{
		$dataReader = NULL;
		// if page count equal to 0 then there is no need to run query
		//		echo $sql;
		if ($pageCount >= $pageNo && $pageCount != 0) {
			$dataReader = Yii::app()->db->createCommand($sql)->query();
		}


		$str = NULL;
		if ($dataReader != NULL )
		{
			if ($type == "userList")
			{
				while ( $row = $dataReader->read() )
				{
					$str .= $this->getUserXMLItem($row);
				}
			}
			else if ($type == "userPastLocations")
			{
				while ( $row = $dataReader->read() )
				{
					$row['latitude'] = isset($row['latitude']) ? $row['latitude'] : null;
					$row['longitude'] = isset($row['longitude']) ? $row['longitude'] : null;
					$row['altitude'] = isset($row['altitude']) ? $row['altitude'] : null;
					$row['dataArrivedTime'] = isset($row['dataArrivedTime']) ? $row['dataArrivedTime'] : null;
					$row['deviceId'] = isset($row['deviceId']) ? $row['deviceId'] : null;
					$row['dataCalculatedTime'] = isset($row['dataCalculatedTime']) ? $row['dataCalculatedTime'] : null;
	
					
					$str .= '<location latitude="'.$row['latitude'].'"  longitude="'. $row['longitude'] .'" altitude="'.$row['altitude'].'" calculatedTime="' . $row['dataCalculatedTime'] . '" >'
					.'<time>'. $row['dataArrivedTime'] .'</time>'
					.'<deviceId>'. $row['deviceId'] .'</deviceId>'
					.'</location>';
				}
			}
		}


		$extra = "";
		if ($type == "userPastLocations" && $userId != NULL) {
			$extra = ' userId="' . $userId .'"';
		}
		$pageNo = $pageCount == 0 ? 0 : $pageNo;
		/*		$out = '<?xml version="1.0" encoding="UTF-8"?>'
		 //				.'<page '. $pageStr . ' >'
		 //					. $str
		 //			   .'</page>';
		 */
		return $this->addXMLEnvelope($pageNo, $pageCount, $str, $extra);
	}

	public function PrepareJson($sql, $type, $userId=NULL)
	{
	

			$dataReader = Yii::app()->db->createCommand($sql)->query();
		


		$str = NULL;
		if ($dataReader != NULL )
		{
			if ($type == "userList")
			{

				while ( $row = $dataReader->read() )
				{			
					//$row = $dataReader->read();
					$str .= $this->getUserJsonItem($row).',';
				}
				$str='{"userlist":['.$str.']}';
			
				/*
				$JSON["userlist"]=array();

				array_push($JSON["userlist"],$str);

				$str= json_encode($JSON);
				*/
			}
			else if ($type == "userSearch")
			{
			while ( $row = $dataReader->read() )
				{
			$row['id'] = isset($row['id']) ? $row['id'] : null;
			$row['Name'] = isset($row['Name']) ? $row['Name'] : null;
				
				$str .= CJSON::encode(array(
							'id'=>$row['id'],
							'Name'=>$row['Name'],
		                   	'gp_image'=>$row['gp_image'],
		                    'fb_id'=>$row['fb_id'],
		                    'g_id'=>$row['g_id'],		
                            'account_type'=>$row['account_type'],
							'status'=>$row['status'],
							'friendShipId'=>$row['friendShipId'],  
                            )).',';
				}
		
				$str='{"userSearch":['.$str.']}';
				
			}
			else if ($type == "userInfo")
			{
				
					$row = $dataReader->read();
					$str .= $this->getUserJsonItem($row);
			
				
			}
			else if ($type == "userPastLocations")
			{
				while ( $row = $dataReader->read() )
				{
					$row['latitude'] = isset($row['latitude']) ? $row['latitude'] : null;
					$row['longitude'] = isset($row['longitude']) ? $row['longitude'] : null;
					$row['altitude'] = isset($row['altitude']) ? $row['altitude'] : null;
					$row['dataArrivedTime'] = isset($row['dataArrivedTime']) ? $row['dataArrivedTime'] : null;
					$row['deviceId'] = isset($row['deviceId']) ? $row['deviceId'] : null;
					$row['dataCalculatedTime'] = isset($row['dataCalculatedTime']) ? $row['dataCalculatedTime'] : null;
	
					
					$str .= CJSON::encode(array(
							'latitude'=>$row['latitude'],
							'longitude'=>$row['longitude'],
                    		'altitude'=>$row['altitude'],
							'calculatedTime'=>$row['dataCalculatedTime'],
							'time'=>$row['dataArrivedTime'],
							'deviceId'=>$row['deviceId'],		
                            )).',';
					
				}
				$str='{"userwashere":['.$str.']}';
			}
		}


	$out =$str;
	return $out;
		
	}
	
	private function addXMLEnvelope($pageNo, $pageCount, $str, $extra = ""){
			
		$pageStr = 'pageNo="'.$pageNo.'" pageCount="' . $pageCount .'"' ;

		header("Content-type: application/xml; charset=utf-8");
		$out = '<?xml version="1.0" encoding="UTF-8"?>'
		.'<page '. $pageStr . '  '. $extra .' >'
		. $str
		.'</page>';

		return $out;
	}

	private function getUserJsonItem($row){
		$row['id'] = isset($row['id']) ? $row['id'] : null;
		//		$row->username = isset($row->username) ? $row->username : null;
		$row['isFriend'] = isset($row['isFriend']) ? $row['isFriend'] : 0;
		$row['realname'] = isset($row['realname']) ? $row['realname'] : null;
		$row['latitude'] = isset($row['latitude']) ? $row['latitude'] : null;
		$row['longitude'] = isset($row['longitude']) ? $row['longitude'] : null;
		$row['altitude'] = isset($row['altitude']) ? $row['altitude'] : null;
		$row['dataArrivedTime'] = isset($row['dataArrivedTime']) ? $row['dataArrivedTime'] : null;
		$row['message'] = isset($row['message']) ? $row['message'] : null;
		$row['deviceId'] = isset($row['deviceId']) ? $row['deviceId'] : null;
		$row['status_message'] = isset($row['status_message']) ? $row['status_message'] : null;
		$row['dataCalculatedTime'] = isset($row['dataCalculatedTime']) ? $row['dataCalculatedTime'] : null;
			
		
		                   $bsk=   CJSON::encode( array(
                            'user'=>$row['id'],
                            'isFriend'=>$row['isFriend'],
                    		'realname'=>$row['realname'],
							'latitude'=>$row['latitude'],
							'longitude'=>$row['longitude'],
                    		'altitude'=>$row['altitude'],
							'calculatedTime'=>$row['dataCalculatedTime'],
							'time'=>$row['dataArrivedTime'],
							'message'=>$row['message'],
                    		'status_message'=>$row['status_message'],
							'deviceId'=>$row['deviceId'],
		                   	'gp_image'=>$row['gp_image'],
		                    'fb_id'=>$row['fb_id'],
		                    'g_id'=>$row['g_id'],		
                            'account_type'=>$row['account_type'], 
		                   ));
                            

                            
                           // '{"userlist":[{"user":"18","isFriend":"g","realname":"17","latitude":"17.000000","longitude":"0.000000","altitude":"0.000000","calculatedTime":"0000-00-00 00:00:00","time":"0000-00-00 00:00:00","message":null,"status_message":null,"deviceId":null}]}';
                            
		
                                        
		return $bsk;
	}
	
	private function getUserXMLItem($row)
	{
		$row['id'] = isset($row['id']) ? $row['id'] : null;
		$row['fb_id'] = isset($row['fb_id']) ? $row['fb_id'] : null;
		//		$row->username = isset($row->username) ? $row->username : null;
		$row['isFriend'] = isset($row['isFriend']) ? $row['isFriend'] : 0;
		$row['realname'] = isset($row['realname']) ? $row['realname'] : null;
		$row['latitude'] = isset($row['latitude']) ? $row['latitude'] : null;
		$row['longitude'] = isset($row['longitude']) ? $row['longitude'] : null;
		$row['altitude'] = isset($row['altitude']) ? $row['altitude'] : null;
		$row['dataArrivedTime'] = isset($row['dataArrivedTime']) ? $row['dataArrivedTime'] : null;
		$row['message'] = isset($row['message']) ? $row['message'] : null;
		$row['deviceId'] = isset($row['deviceId']) ? $row['deviceId'] : null;
		$row['status_message'] = isset($row['status_message']) ? $row['status_message'] : null;
		$row['dataCalculatedTime'] = isset($row['dataCalculatedTime']) ? $row['dataCalculatedTime'] : null;
			
	
		$str = '<user>'
		. '<Id isFriend="'.$row['isFriend'].'">'. $row['id'] .'</Id>'
		//		. '<username>' . $row->username . '</username>'
		. '<fb_id>' . $row['fb_id'] . '</fb_id>'
		. '<realname>' . $row['realname'] . '</realname>'
		. '<location latitude="' . $row['latitude'] . '"  longitude="' . $row['longitude'] . '" altitude="' . $row['altitude'] . '" calculatedTime="' . $row['dataCalculatedTime'] . '"/>'
		. '<time>' . $row['dataArrivedTime'] . '</time>'
		. '<message>' . $row['message'] . '</message>'
		. '<status_message>' . $row['status_message'] . '</status_message>'
		. '<deviceId>' . $row['deviceId'] . '</deviceId>'
		.'</user>';

		return $str;
	}



}