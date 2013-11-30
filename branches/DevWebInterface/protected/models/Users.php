<?php

/**
 * This is the model class for table "traceper_users".
 *
 * The followings are the available columns in table 'traceper_users':
 * @property string $Id
 * @property string $password
 * @property string $group
 * @property string $latitude
 * @property string $longitude
 * @property string $altitude
 * @property integer $publicPosition
 * @property integer $authorityLevel
 * @property string $realname
 * @property string $email
 * @property string $dataArrivedTime
 * @property string $deviceId
 * @property string $status_message
 * @property integer $status_source
 * @property string $status_message_time
 * @property string $dataCalculatedTime
 * @property string $fb_id
 * @property string $g_id
 * @property integer $gender
 * @property integer $userType
 * @property integer $account_type
 * @property string $gp_image
 * @property string $lastLocationAddress
 * @property integer $minDataSentInterval
 * @property integer $minDistanceInterval
 * @property integer $autoSend
 * @property string $androidVer
 * @property string $appVer
 * @property string $registrationMedium
 * @property string $preferredLanguage
 * @property integer $termsAccepted
 *
 * The followings are the available model relations:
 * @property TraceperFriends[] $traceperFriends
 * @property TraceperFriends[] $traceperFriends1
 * @property TraceperUpload[] $traceperUploads
 * @property TraceperUserPrivacyGroupRelation[] $traceperUserPrivacyGroupRelations
 * @property TraceperUserWasHere[] $traceperUserWasHeres
 */
class Users extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return Users the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'traceper_users';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('password, realname, email, account_type', 'required'),
			array('publicPosition, authorityLevel, status_source, gender, userType, account_type, minDataSentInterval, minDistanceInterval, autoSend, termsAccepted', 'numerical', 'integerOnly'=>true),
			array('password', 'length', 'max'=>32),
			array('group, latitude, appVer, registrationMedium', 'length', 'max'=>10),
			array('longitude', 'length', 'max'=>11),
			array('altitude', 'length', 'max'=>15),
			array('realname', 'length', 'max'=>80),
			array('email', 'length', 'max'=>100),
			array('deviceId', 'length', 'max'=>64),
			array('status_message', 'length', 'max'=>128),
			array('fb_id, g_id', 'length', 'max'=>50),
			array('gp_image', 'length', 'max'=>255),
			array('androidVer, preferredLanguage', 'length', 'max'=>20),
			array('dataArrivedTime, status_message_time, dataCalculatedTime, lastLocationAddress', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('Id, password, group, latitude, longitude, altitude, publicPosition, authorityLevel, realname, email, dataArrivedTime, deviceId, status_message, status_source, status_message_time, dataCalculatedTime, fb_id, g_id, gender, userType, account_type, gp_image, lastLocationAddress, minDataSentInterval, minDistanceInterval, autoSend, androidVer, appVer, registrationMedium, preferredLanguage, termsAccepted', 'safe', 'on'=>'search'),
		);		
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
			'traceperFriends' => array(self::HAS_MANY, 'TraceperFriends', 'friend1'),
			'traceperFriends1' => array(self::HAS_MANY, 'TraceperFriends', 'friend2'),
			'traceperUploads' => array(self::HAS_MANY, 'TraceperUpload', 'userId'),
			'traceperUserPrivacyGroupRelations' => array(self::HAS_MANY, 'TraceperUserPrivacyGroupRelation', 'userId'),
			'traceperUserWasHeres' => array(self::HAS_MANY, 'TraceperUserWasHere', 'userId'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'Id' => 'ID',
			'password' => 'Password',
			'group' => 'Group',
			'latitude' => 'Latitude',
			'longitude' => 'Longitude',
			'altitude' => 'Altitude',
			'publicPosition' => 'Public Position',
			'authorityLevel' => 'Authority Level',
			'realname' => 'Realname',
			'email' => 'Email',
			'dataArrivedTime' => 'Data Arrived Time',
			'deviceId' => 'Device',
			'status_message' => 'Status Message',
			'status_source' => 'Status Source',
			'status_message_time' => 'Status Message Time',
			'dataCalculatedTime' => 'Data Calculated Time',
			'fb_id' => 'Fb',
			'g_id' => 'G',
			'gender' => 'Gender',
			'userType' => 'User Type',
			'account_type' => 'Account Type',
			'gp_image' => 'Gp Image',
			'lastLocationAddress' => 'Last Location Address',
			'minDataSentInterval' => 'Min Data Sent Interval',
			'minDistanceInterval' => 'Min Distance Interval',
			'autoSend' => 'Auto Send',
			'androidVer' => 'Android Ver',
			'appVer' => 'App Ver',
			'registrationMedium' => 'Registration Medium',
			'preferredLanguage' => 'Preferred Language',
			'termsAccepted' => 'Terms Accepted',
		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
	 */
	public function search()
	{
		// Warning: Please modify the following code to remove attributes that
		// should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('Id',$this->Id,true);
		$criteria->compare('password',$this->password,true);
		$criteria->compare('group',$this->group,true);
		$criteria->compare('latitude',$this->latitude,true);
		$criteria->compare('longitude',$this->longitude,true);
		$criteria->compare('altitude',$this->altitude,true);
		$criteria->compare('publicPosition',$this->publicPosition);
		$criteria->compare('authorityLevel',$this->authorityLevel);
		$criteria->compare('realname',$this->realname,true);
		$criteria->compare('email',$this->email,true);
		$criteria->compare('dataArrivedTime',$this->dataArrivedTime,true);
		$criteria->compare('deviceId',$this->deviceId,true);
		$criteria->compare('status_message',$this->status_message,true);
		$criteria->compare('status_source',$this->status_source);
		$criteria->compare('status_message_time',$this->status_message_time,true);
		$criteria->compare('dataCalculatedTime',$this->dataCalculatedTime,true);
		$criteria->compare('fb_id',$this->fb_id,true);
		$criteria->compare('g_id',$this->g_id,true);
		$criteria->compare('gender',$this->gender);
		$criteria->compare('userType',$this->userType);
		$criteria->compare('account_type',$this->account_type);
		$criteria->compare('gp_image',$this->gp_image,true);
		$criteria->compare('lastLocationAddress',$this->lastLocationAddress,true);
		$criteria->compare('minDataSentInterval',$this->minDataSentInterval);
		$criteria->compare('minDistanceInterval',$this->minDistanceInterval);
		$criteria->compare('autoSend',$this->autoSend);
		$criteria->compare('androidVer',$this->androidVer,true);
		$criteria->compare('appVer',$this->appVer,true);
		$criteria->compare('registrationMedium',$this->registrationMedium,true);
		$criteria->compare('preferredLanguage',$this->preferredLanguage,true);
		$criteria->compare('termsAccepted',$this->termsAccepted);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
	
	public function updateLocation($latitude, $longitude, $altitude, $calculatedTime, $userId){
	
		$sql = sprintf('UPDATE '
				. $this->tableName() .'
				SET
				latitude = %f , '
				.'	longitude = %f , '
				.'	altitude = %f ,	'
				.'	dataArrivedTime = NOW(), '
				.'  dataCalculatedTime = "%s" '
				.' WHERE '
				.' 	Id = %d '
				.' LIMIT 1;',
				$latitude, $longitude, $altitude, $calculatedTime, $userId);
	
		$effectedRows = Yii::app()->db->createCommand($sql)->execute();
		//$effectedRows = 0;
		return $effectedRows;
	}
	
	public function updateLocationWithAddress($latitude, $longitude, $altitude, $address, $calculatedTime, $userId){
	
		$sql = sprintf('UPDATE '
				. $this->tableName() .'
				SET
				latitude = %f , '
				.'	longitude = %f , '
				.'	altitude = %f ,	'
				.'	lastLocationAddress = "%s" , '
				.'	dataArrivedTime = NOW(), '
				.'  dataCalculatedTime = "%s" '
				.' WHERE '
				.' 	Id = %d '
				.' LIMIT 1;',
				$latitude, $longitude, $altitude, $address, $calculatedTime, $userId);
	
		$effectedRows = Yii::app()->db->createCommand($sql)->execute();
		//$effectedRows = 0;
		return $effectedRows;
	}
	
	public function updateLocationTime($userId, $par_time)
	{
		$sql = sprintf('UPDATE '
				. $this->tableName() .'
				SET
				dataArrivedTime = NOW(), '
				.' dataCalculatedTime = "%s" '
				.' WHERE '
				.' 	Id = %d '
				.' LIMIT 1;',
				$par_time, $userId);
	
		$effectedRows = Yii::app()->db->createCommand($sql)->execute();
	
		$result = false;
	
		if ($effectedRows == 1) {
			$result = true;
		}
	
		return $result;
	}
	
	public function saveUser($email, $password, $realname, $userType, $accountType, $registrationMedium, $preferredLanguage){
		$users=new Users;
	
		$users->email = $email;
		$users->realname = $realname;
		$users->password = $password;
		$users->userType = $userType;
		$users->account_type = $accountType;
		$users->registrationMedium = $registrationMedium;
		$users->preferredLanguage = $preferredLanguage;
		$users->termsAccepted = 1;
	
		return $users->save();
	}
	
	public function isFacebookUserRegistered($email, $facebookId) {
		$user = Users::model()->find('email=:email AND fb_id=:facebookId', array(':email'=>$email, ':facebookId'=>$facebookId));
		$result = false;
		if ($user != null) {
			$result = true;
		}
		return $result;
	}
	
	public function isUserRegistered($email) {
		$user = Users::model()->find('email=:email', array(':email'=>$email));
		$result = false;
		if ($user != null) {
			$result = true;
		}
		return $result;
	}
	
	public function getNameByEmail($email) {
		$user = Users::model()->find('email=:email', array(':email'=>$email));
		$name = "";
		if ($user != null) {
			$name = $user->realname;
		}
		return $name;
	}
	
	public function saveFacebookUser($email, $password, $realname, $fb_id, $accountType, $registrationMedium, $preferredLanguage){
		if ($fb_id == null || $fb_id == 0) {
			return false;
		}
	
		$users=new Users;
	
		$users->email = $email;
		$users->realname = $realname;
		$users->password = $password;
		$users->fb_id = $fb_id;
		$users->account_type = $accountType;
		$users->registrationMedium = $registrationMedium;
		$users->preferredLanguage = $preferredLanguage;
		$users->termsAccepted = 1;

		$result = $users->save();
		return $result;
	}
	
	public function saveGPUser($email, $password, $realname, $g_id, $accountType, $gp_image){
		$users=new Users;
	
		$users->email = $email;
		$users->realname = $realname;
		$users->password = $password;
		$users->g_id = $fb_id;
		$users->account_type = $accountType;
		$users->gp_image = $gp_image;
	
		return $users->save();
	}
	
	public function saveGPSUser($deviceID, $password, $realname, $userType, $accountType){
		$users=new Users;
	
		$users->email = $deviceID;
		$users->deviceId = $deviceID;
		$users->realname = $realname;
		$users->password = $password;
		$users->userType = $userType;
		$users->account_type = $accountType;
	
		return $users->save();
	}
	
	public function changePassword($Id, $password) {
		$result = false;
	
		$user = Users::model()->findByPk($Id);
	
		if($user->password == md5($password)) //If same password
		{
			$result = true;
		}
		else
		{
			if(Users::model()->updateByPk($Id, array("password"=>md5($password)))) {
				$result = true;
			}
		}
	
		return $result;
	}
	
	public function getUserId($email){
		$user = Users::model()->find('email=:email', array(':email'=>$email));
		$result = null;
	
		if($user != null)
		{
			$result = (int)$user->Id;
		}
	
		return $result;
	}
	
	public function deleteUser($userId){
		//TODO: why we dont use deleteByPk
		$result = null;
		$user = Users::model()->findByPk($userId);
	
		if($user != null)
		{
			$result = $user->delete();
		}
	
		return $result;
	}
	
	public function getListDataProvider($IdList, $userType=null, $newFriendId=null, $time=null, $offset=null, $itemCount=null, $totalItemCount = null)
	{
		$userTypeSqlPart = '';
		if ($userType != null) {
			$userTypeCount = count($userType);
			for ($i = 0; $i < $userTypeCount; $i++) {
				if ($userTypeSqlPart != '')
				{
					$userTypeSqlPart .= ' OR ';
				}
				$userTypeSqlPart .= ' u.userType = "'.$userType[$i].'" ';
			}
		}
	
		//echo $IdList;
	
		$sqlCount = 'SELECT count(*)
		FROM '.  Users::model()->tableName() . ' u
		WHERE ((Id in ('. $IdList.')';
		
// 		$sql = 'SELECT  u.Id as id, u.realname as Name, u.latitude, u.longitude, u.altitude, u.lastLocationAddress,
// 		u.userType, u.deviceId,
// 		date_format(u.dataArrivedTime,"%d %b %Y %T") as dataArrivedTime,
// 		date_format(u.dataCalculatedTime,"%d %b %Y %T") as dataCalculatedTime,
// 		u.account_type, u.fb_id
// 		FROM '.  Users::model()->tableName() . ' u
// 		WHERE ((Id in ('. $IdList.')';
		
		$sql = 'SELECT  u.Id as id, u.realname as Name, u.latitude, u.longitude, u.altitude, u.lastLocationAddress,
		u.userType, u.deviceId, IF(f.friend1 = '.Yii::app()->user->id.', f.friend2Visibility, f.friend1Visibility) as isVisible,
		date_format(u.dataArrivedTime,"%d %b %Y %T") as dataArrivedTime,
		date_format(u.dataCalculatedTime,"%d %b %Y %T") as dataCalculatedTime,
		u.account_type, u.fb_id
		FROM '.  Users::model()->tableName() . ' u 
		LEFT JOIN ' . Friends::model()->tableName() . ' f 
		ON (f.friend1 = '. Yii::app()->user->id .' AND f.friend2 = u.Id)  OR 
		(f.friend1 = u.Id AND f.friend2 = '. Yii::app()->user->id .') 
		WHERE ((u.Id in ('. $IdList.')';		
	
		if ($time != null) {
			$timeSql = ' AND unix_timestamp(u.dataArrivedTime) >= '. $time.')';
			$sqlCount .= $timeSql;
			$sql .= $timeSql;
				
			if ($newFriendId != null) {
				$newFriendIdSql = ' OR Id = '.$newFriendId.')';
				$sqlCount .= $newFriendIdSql;
				$sql .= $newFriendIdSql;
			}
			else
			{
				$closeParanthesisSql = ')';
				$sqlCount .= $closeParanthesisSql;
				$sql .= $closeParanthesisSql;
			}
		}
		else {
			$doubleCloseParanthesisSql = '))';
			$sqlCount .= $doubleCloseParanthesisSql;
			$sql .= $doubleCloseParanthesisSql;
		}
	
		if ($userTypeSqlPart != '') {
			$sqlCount .= ' AND ('. $userTypeSqlPart. ')';
			$sql .= ' AND (' . $userTypeSqlPart. ')';
		}
	
		//echo '</br>$sqlCount: '.$sqlCount.'</br>';
	
		//	if ($offset !== null && $itemCount !== null) {
		//		$sql .= ' LIMIT ' . $offset . ' , ' . $itemCount;
		//	}
	
		$count = $totalItemCount;
	
		if ($count == null) {
			$count=Yii::app()->db->createCommand($sqlCount)->queryScalar();
		}
	
		$dataProvider = new CSqlDataProvider($sql, array(
				'totalItemCount'=>$count,
				'sort'=>array(
						'attributes'=>array(
								'id', 'Name',
						),
				),
				'pagination'=>array(
						'pageSize'=>Yii::app()->session['usersPageSize'], //(int)(($_SESSION['screen_height'] - 155)/42),
						'itemCount'=>$count
				),
		));
	
		//echo ($_SESSION['screen_height'] - 140)/45;
	
		return $dataProvider;
	}
	
	
	public function getSearchUserDataProvider($IdList, $text, $searchIndex)
	{
		$IdListSql = ' ';
		if ($IdList != null){
			$IdListSql = ' Id in ('. $IdList.') AND ';
		}
	
		$sqlCount = 'SELECT count(*)
		FROM '.  Users::model()->tableName() . ' u
		WHERE '. $IdListSql .' u.realname like "%'. $text .'%" AND u.Id <> '.Yii::app()->user->id ; //Aramada kullanıcının kendisi çıkmasın diye
	
		$sql = 'SELECT  u.Id as id, u.realname as Name,
		u.userType, u.fb_id, u.account_type, IF(f.friend1 = '.Yii::app()->user->id.', f.friend2Visibility, f.friend1Visibility) as isVisible,
		IFNULL(f.status, -1) as status, IF(f.friend1 = '.Yii::app()->user->id.', true, false) as requester
		FROM '.  Users::model()->tableName() . ' u
		LEFT JOIN ' . Friends::model()->tableName() . ' f
		ON (f.friend1 = '. Yii::app()->user->id .' AND f.friend2 = u.Id)  OR
		(f.friend1 = u.Id AND f.friend2 = '. Yii::app()->user->id .')
		WHERE '. $IdListSql .' u.realname like "%'. $text .'%" AND u.Id <> '.Yii::app()->user->id; //Aramada kullanıcının kendisi çıkmasın diye
		
		$count = Yii::app()->db->createCommand($sqlCount)->queryScalar();
	
		$dataProvider = new CSqlDataProvider($sql, array(
				'totalItemCount'=>$count,
				'sort'=>array(
						'attributes'=>array(
								'id', 'Name',
						),
				),
				//	'params'=>array($searchIndex=>$text),
				'pagination'=>array(
						'pageSize'=>Yii::app()->params->itemCountInOnePage,
						'params'=>array(CHtml::encode('SearchForm[keyword]')=>$text),
				),
		));
	
		return $dataProvider;
	}
	
	public function getFriendList($Id, $friendUserType=null)
	{
		$userTypeSqlPart = '';
		if ($friendUserType != null) {
			$userTypeCount = count($friendUserType);
			for ($i = 0; $i < $userTypeCount; $i++) {
				if ($userTypeSqlPart != '')
				{
					$userTypeSqlPart .= ' OR ';
				}
				$userTypeSqlPart .= ' u.userType = "'.$friendUserType[$i].'" ';
			}
		}		
		
		//TODO: this function should be moved to Friends model
		$sql = 'SELECT IF( f.friend1 != '. $Id.', f.friend1, f.friend2 ) as friend '
		.' FROM ' .  Friends::model()->tableName(). ' f		
		LEFT JOIN ' . Users::model()->tableName() . ' u
		ON IF( f.friend1 != '. $Id.', (u.Id = f.friend1), (u.Id = f.friend2) ) '		
		.' WHERE '
		.' ((f.friend1='.Yii::app()->user->id.')'
		.' OR (f.friend2='.Yii::app()->user->id.')'
		.' ) '
		.' AND STATUS = 1';
		
		
		
		
		
		if ($userTypeSqlPart != '') {
			$sql .= ' AND (' . $userTypeSqlPart. ')';
		}		
		
		$friendsResult = Yii::app()->db->createCommand($sql)->queryAll();
	
		//echo "Friend Count: ".count($friendsResult);
	
		return $friendsResult;
	}
	
	public function getVisibleFriendList($Id, $friendUserType=null)
	{
		$userTypeSqlPart = '';
		if ($friendUserType != null) {
			$userTypeCount = count($friendUserType);
			for ($i = 0; $i < $userTypeCount; $i++) {
				if ($userTypeSqlPart != '')
				{
					$userTypeSqlPart .= ' OR ';
				}
				$userTypeSqlPart .= ' u.userType = "'.$friendUserType[$i].'" ';
			}
		}		
		
		//TODO: this function should be moved to Friends model
		$sql = 'SELECT IF( f.friend1 != '. $Id.', f.friend1, f.friend2 ) as friend '
		.' FROM ' .  Friends::model()->tableName() .' f
		LEFT JOIN ' . Users::model()->tableName() . ' u
		ON IF( f.friend1 != '. $Id.', (u.Id = f.friend1), (u.Id = f.friend2) ) '		
		.' WHERE '
		.' ((f.friend1='.Yii::app()->user->id.' AND f.friend2Visibility=1)'
		.' OR (f.friend2='.Yii::app()->user->id.' AND f.friend1Visibility=1)'
		.' ) '
		.' AND STATUS = 1';
		
		if ($userTypeSqlPart != '') {
			$sql .= ' AND (' . $userTypeSqlPart. ')';
		}
				
		$friendsResult = Yii::app()->db->createCommand($sql)->queryAll();		
	
		//echo "Friend Count: ".count($friendsResult);
	
		return $friendsResult;
	}	
	
	public function setUserPositionPublicity($userId, $isPublic)
	{
		$result = false;
	
		if(Users::model()->updateByPk($userId, array("publicPosition"=>($isPublic == true)?1:0))) {
			$result = true;
		}
	
		return $result;
	}
	
	public function isUserPositionPublic($userId)
	{
		$user=Users::model()->findByPk($userId);
	
		return ($user->publicPosition == 1)?true:false;
	}
	
	public function setAuthorityLevel($userId, $level)
	{
		$result = false;
	
		if(Users::model()->updateByPk($userId, array("authorityLevel"=>$level))) {
			$result = true;
		}
	
		return $result;
	}
	
	public function getAuthorityLevel($userId)
	{
		$user=Users::model()->findByPk($userId);
	
		return $user->authorityLevel;
	}
	
	public function getUserInfo($userId, &$par_name, &$par_email)
	{
		$found = false;
		$user=Users::model()->findByPk($userId);
	
		if($user != null)
		{
			$par_name = $user->realname;
			$par_email = $user->email;
			$found = true;
		}
	
		return $found;
	}
	
	public function getLoginRequiredValues($userId, &$par_minDataSentInterval, &$par_minDistanceInterval, &$par_facebookId, &$par_autoSend, &$par_deviceId, &$par_androidVer, &$par_appVer, &$preferredLanguage)
	{
		$user = Users::model()->findByPk($userId);
		$result = false;
	
		if($user != null)
		{
			$par_minDataSentInterval = $user->minDataSentInterval;
			$par_minDistanceInterval = $user->minDistanceInterval;
			$par_facebookId = $user->fb_id;
			$par_autoSend = $user->autoSend;
			$par_deviceId = $user->deviceId;
			$par_androidVer = $user->androidVer;
			$par_appVer = $user->appVer;
			$preferredLanguage = $user->preferredLanguage;
	
			$result = true;
		}
		else
		{
			$result = false;
		}
	
		return $result;
	}
	
	public function isTermsAccepted($email)
	{
		$user = Users::model()->find('email=:email', array(':email'=>$email));
		$result = false;
	
		if($user != null)
		{
			if($user->termsAccepted == 1)
			{
				$result = true;
			}
			else
			{
				$result = false;
			}	
		}
		else
		{
			$result = false;
		}
	
		return $result;
	}

	public function setTermsAccepted($email)
	{
		$user = Users::model()->find('email=:email', array(':email'=>$email));		
		$user->termsAccepted = 1;

		return $user->save();
	}	
	
	public function getMinimumIntervalValues($userId, &$par_minDistanceInterval, &$par_minDataSentInterval)
	{
		$user=Users::model()->findByPk($userId);
		$result = false;
	
		if($user != null)
		{
			$par_minDistanceInterval = $user->minDistanceInterval;
			$par_minDataSentInterval = $user->minDataSentInterval;
	
			$result = true;
		}
		else
		{
			$result = false;
		}
	
		return $result;
	}
	
	public function updateProfileItemsNotNull($userId, $par_realname, $par_password, $par_gender, $par_minDataSentInterval, $par_minDistanceInterval, $par_autoSend)
	{
		$user=Users::model()->findByPk($userId);
	
		// 		$result = false;
		// 		$paramsArray = array();
	
		if($par_realname != null)
		{
			//$paramsArray = array_merge($paramsArray, array("realname"=>$par_realname));
	
			$user->realname = $par_realname;
		}
	
		if($par_password != null)
		{
			//$paramsArray = array_merge($paramsArray, array("password"=>$par_password));
	
			$user->password = $par_password;
		}
	
		if($par_gender != null)
		{
			//$paramsArray = array_merge($paramsArray, array("gender"=>$par_gender));
	
			$user->gender = $par_gender;
		}
	
		if($par_minDataSentInterval != null)
		{
			//$paramsArray = array_merge($paramsArray, array("minDataSentInterval"=>$par_minDataSentInterval));
	
			$user->minDataSentInterval = $par_minDataSentInterval;
		}
	
		if($par_minDistanceInterval != null)
		{
			//$paramsArray = array_merge($paramsArray, array("minDistanceInterval"=>$par_minDistanceInterval));
	
			$user->minDistanceInterval = $par_minDistanceInterval;
		}
	
		if($par_autoSend != null)
		{
			//$paramsArray = array_merge($paramsArray, array("autoSend"=>$par_autoSend));
	
			$user->autoSend = $par_autoSend;
		}
	
		// 		if(Users::model()->updateByPk($userId, $paramsArray)) {
		// 			$result = true;
		// 		}
	
		return $user->save();
	}
	
	public function updateLoginSentItemsNotNull($userId, $par_deviceId, $par_androidVer, $par_appVer, $par_preferredLanguage)
	{
		$user=Users::model()->findByPk($userId);
	
		if($par_deviceId != null)
		{
			$user->deviceId = $par_deviceId;
		}
	
		if($par_androidVer != null)
		{
			$user->androidVer = $par_androidVer;
		}
	
		if($par_appVer != null)
		{
			$user->appVer = $par_appVer;
		}
	
		if($par_preferredLanguage != null)
		{
			$user->preferredLanguage = $par_preferredLanguage;
		}
	
		return $user->save();
	}
	
	public function setDeviceId($userId, $par_deviceId)
	{
		$result = false;
	
		if(Users::model()->updateByPk($userId, array("deviceId"=>$par_deviceId))) {
			$result = true;
		}
	
		return $result;
	}
	
	public function setAndroidVersion($userId, $par_androidVer)
	{
		$result = false;
	
		if(Users::model()->updateByPk($userId, array("androidVer"=>$par_androidVer))) {
			$result = true;
		}
	
		return $result;
	}
	
	public function setApplicationVersion($userId, $par_appVer)
	{
		$result = false;
	
		if(Users::model()->updateByPk($userId, array("appVer"=>$par_appVer))) {
			$result = true;
		}
	
		return $result;
	}	
}