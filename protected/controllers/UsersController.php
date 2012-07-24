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
								'takeMyLocation', 'getUserInfo',
								'getUserListJson'),
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
					UserWasHere::model()->logLocation(Yii::app()->user->id, $latitude, $longitude, $altitude, $deviceId, $calculatedTime);
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
		$userType = array();

		if (isset($_GET['userType']) && $_GET['userType'] != NULL)
		{
			$userType = $_GET['userType'];
		}

		if(Yii::app()->user->id != null)
		{
			$dataProvider = Users::model()->getListDataProvider($this->getFriendIdList(), $userType);
		}
		else
		{
			$dataProvider = null;
		}

		Yii::app()->clientScript->scriptMap['jquery.js'] = false;

		if(($userType == UserType::RealStaff) || ($userType == UserType::GPSStaff))
		{
			Yii::app()->clientScript->scriptMap['jquery.yiigridview.js'] = false;
		}

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

			$friendsResult = Users::model()->getFriendList(Yii::app()->user->id);
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

					$dataProvider = Users::model()->getListDataProvider($this->getFriendIdList(), null, $time, $offset, Yii::app()->params->itemCountInDataListPage);
					$out = $this->prepareXML2($dataProvider);
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

			$dataProvider = Users::model()->getListDataProvider($friendIdList, null, null, $offset, Yii::app()->params->itemCountInDataListPage, $friendCount);
			//$out = $this-> prepareXML($sql, $pageNo, $pageCount, "userList");
			$out = $this->prepareXML2($dataProvider);
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
				$dataProvider = Users::model()->getListDataProvider(array($userId), null, null, 0, 1, 1);
				$out = $this->prepareXML2($dataProvider);
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
				$dataProvider = Users::model()->getListDataProvider(array($userId), null, null, 0, 1, 1);
				$out = $this->prepareJson2($dataProvider);
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

			
		$friendCount = $this->getFriendCount() + 1; // +1 is for herself

			
		$friendIdList = $this->getFriendIdList();

		if ($friendIdList != -1) {
			$friendIdList .= ',' . Yii::app()->user->id;
		}
		else {
			$friendIdList = Yii::app()->user->id;
		}

		$dataProvider = Users::model()->getListDataProvider($friendIdList, null, null, $offset, $range, $friendCount);
		
		$out = $this->prepareJson2($dataProvider);	

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

			$dataProvider = UserWasHere::model()->getPastPointsDataProvider($userId, $offset, Yii::app()->params->itemCountInDataListPage);	

			$out = $this->preparePastPointsXML($dataProvider, $userId);
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

			$dataProvider = UserWasHere::model()->getPastPointsDataProvider($userId, $offset, $range);
				
			$out = $this->preparePastPointsJson($dataProvider);
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
				$dataProvider = Users::model()->getSearchUserDataProvider($this->getFriendIdList());
					
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

				$dataProvider = Users::model()->getSearchUserDataProvider($this->getFriendIdList());
				$out = $this->prepareSearchUserResultJson($dataProvider);
			}
		}
		echo $out;
	}
	public function actionDeleteFriendShip(){
		$result = 'Missing Data';
		if (isset($_REQUEST['friendShipId']))
		{
			$friendShipId = (int) $_REQUEST['friendShipId'];
			
			$done = Friends::model()->deleteFriendShip($friendShipId, Yii::app()->user->id);
			
			if ($done == true) {
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

			$result = 'Error occured';
			if (Users::model()->deleteUser($userId)){
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

		$dataProvider = Friends::model()->getFriendRequestDataProvider(Yii::app()->user->id, Yii::app()->params->itemCountInOnePage);
			
		Yii::app()->clientScript->scriptMap['jquery.js'] = false;
		Yii::app()->clientScript->scriptMap['jquery.yiigridview.js'] = false;
		$this->renderPartial('userListDialog',array('dataProvider'=>$dataProvider), false, true);
	}
	public function actionGetFriendRequestListJson(){		
		
		$dataProvider = Friends::model()->getFriendRequestDataProvider(Yii::app()->user->id, Yii::app()->params->itemCountInOnePage);
		
		$out = $this->prepareSearchUserResultJson($dataProvider);
		//$out = $this-> prepareJson($sql, "userSearch" ,$userId);
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
			if (Friends::model()->approveFriendShip($friendShipId, $userId) == true)
			{
				$result = 1;
				$this->unsetFriendIdList();
			}
			
		}
		echo CJSON::encode(array(
				"result"=>$result,
		));

	}

	public function actionAddAsFriend()
	{
		$result = 'Missing parameter';
		if (isset($_REQUEST['friendId'])) 
		{
			$friendId = (int)$_REQUEST['friendId'];
			
			$result = 'Error occured';
			$done = Friends::model()->addAsFriend(Yii::app()->user->id, $friendId);
			if ($done == true) {
				$result = 1;
			}
			else  if ($done == null) {
				$result = 0;
			}
		}
		echo CJSON::encode(array(
				"result"=>$result,
		));
		Yii::app()->end();
	}

	private function prepareXML2($dataProvider) {

		$rows = $dataProvider->getData();
		$itemCount = count($rows);

		$str = '';
		for ($i = 0; $i < $itemCount; $i++) {
			$str .= $this->getUserXMLItem2($rows[$i]);
		}

		$pagination = $dataProvider->getPagination();
		return $this->addXMLEnvelope($pagination->currentPage+1, $pagination->pageCount, $str, "");
	}
	
	private function preparePastPointsXML($dataProvider, $userId) {
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
			
			$str .= '<location latitude="'.$row['latitude'].'"  longitude="'. $row['longitude'] .'" altitude="'.$row['altitude'].'" calculatedTime="' . $row['dataCalculatedTime'] . '" >'
			.'<time>'. $row['dataArrivedTime'] .'</time>'
			.'<deviceId>'. $row['deviceId'] .'</deviceId>'
			.'</location>';
		}
		$extra = "";
		if ($userId != NULL) {
			$extra = ' userId="' . $userId .'"';
		}
		$pagination = $dataProvider->getPagination();
		return $this->addXMLEnvelope($pagination->currentPage+1, $pagination->pageCount, $str, $extra);
	}

	private function prepareXML($sql, $pageNo, $pageCount, $type="userList", $userId=NULL, $dataProvider = null)
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


	private function prepareJson2($dataProvider){

		$rows = $dataProvider->getData();
		$itemCount = count($rows);

		$str = '';
		for ($i = 0; $i < $itemCount; $i++) {
			$str .= $this->getUserJsonItem2($rows[$i]);
		}

		return $str;
	}
	
	private function prepareSearchUserResultJson($dataProvider){
		$rows = $dataProvider->getData();
		$itemCount = count($rows);
		
		$str = '';
		for ($i = 0; $i < $itemCount; $i++) {
			$row[$i]['id'] = isset($row[$i]['id']) ? $row[$i]['id'] : null;
			$row[$i]['Name'] = isset($row[$i]['Name']) ? $row[$i]['Name'] : null;
			
			$str .= CJSON::encode(array(
					'id'=>$row[$i]['id'],
					'Name'=>$row[$i]['Name'],
					'gp_image'=>$row[$i]['gp_image'],
					'fb_id'=>$row[$i]['fb_id'],
					'g_id'=>$row[$i]['g_id'],
					'account_type'=>$row[$i]['account_type'],
					'status'=>$row[$i]['status'],
					'friendShipId'=>$row[$i]['friendShipId'],
			)).',';
		}
		
		return $str;
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
			
			
			$str .= CJSON::encode(array(
						'latitude'=>$rows[$i]['latitude'],
						'longitude'=>$rows[$i]['longitude'],
						'altitude'=>$rows[$i]['altitude'],
						'calculatedTime'=>$rows[$i]['dataCalculatedTime'],
						'time'=>$rows[$i]['dataArrivedTime'],
						'deviceId'=>$rows[$i]['deviceId'],
				)).',';
		}
		$str='{"userwashere":['.$str.']}';
		return $str;
		
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

	private function getUserJsonItem2($row){
		$row['id'] = isset($row['id']) ? $row['id'] : null;
		//		$row->username = isset($row->username) ? $row->username : null;
		$row['isFriend'] = isset($row['isFriend']) ? $row['isFriend'] : 0;
		$row['realname'] = isset($row['Name']) ? $row['Name'] : null;
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

	private function getUserXMLItem2($row)
	{
		$row['id'] = isset($row['id']) ? $row['id'] : null;
		$row['fb_id'] = isset($row['fb_id']) ? $row['fb_id'] : null;
		//		$row->username = isset($row->username) ? $row->username : null;
		$row['isFriend'] = isset($row['isFriend']) ? $row['isFriend'] : 0;
		$row['realname'] = isset($row['Name']) ? $row['Name'] : null;
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