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
			array('publicPosition, authorityLevel, status_source, gender, userType, account_type', 'numerical', 'integerOnly'=>true),
			array('password', 'length', 'min'=>5, 'max'=>32),
			array('group', 'length', 'max'=>10),
			array('latitude', 'length', 'max'=>8),
			array('longitude', 'length', 'max'=>9),
			array('altitude', 'length', 'max'=>15),
			array('realname', 'length', 'max'=>80),
			array('email', 'length', 'max'=>100),
			/*
			array('email', 'length', 'min'=>5,'max'=>100),
			array('email', 'email', 'message'=>Yii::t('site', 'E-mail not valid!')),
			*/
			array('deviceId', 'length', 'max'=>64),
			array('status_message', 'length', 'max'=>128),
			array('fb_id, g_id', 'length', 'max'=>50),
			array('gp_image', 'length', 'max'=>255),
			array('dataArrivedTime, status_message_time, dataCalculatedTime', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('Id, password, group, latitude, longitude, altitude, publicPosition, authorityLevel, realname, email, dataArrivedTime, deviceId, status_message, status_source, status_message_time, dataCalculatedTime, fb_id, g_id, gender, userType, account_type, gp_image', 'safe', 'on'=>'search'),
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

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
	
	public function updateLocation($latitude, $longitude, $altitude, $deviceId, $calculatedTime, $userId){
	
		$sql = sprintf('UPDATE '
				. $this->tableName() .'
				SET
				latitude = %f , '
				.'	longitude = %f , '
				.'	altitude = %f ,	'
				.'	dataArrivedTime = NOW(), '
				.'	deviceId = "%s"	,'
				.'  dataCalculatedTime = "%s" '
				.' WHERE '
				.' 	Id = %d '
				.' LIMIT 1;',
				$latitude, $longitude, $altitude, $deviceId, $calculatedTime, $userId);
	
		$effectedRows = Yii::app()->db->createCommand($sql)->execute();
		return $effectedRows;
	}
	
	public function saveUser($email, $password, $realname, $userType, $accountType){
		$users=new Users;
	
		$users->email = $email;
		$users->realname = $realname;
		$users->password = $password;
		$users->userType = $userType;
		$users->account_type = $accountType;
	
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
	
	public function saveFacebookUser($email, $password, $realname, $fb_id, $accountType){
		if ($fb_id == null || $fb_id == 0) {
			return false;
		}
		$users=new Users;
		
		$users->email = $email;
		$users->realname = $realname;
		$users->password = $password;
		$users->fb_id = $fb_id;
		$users->account_type = $accountType;
	
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
		if(Users::model()->updateByPk($Id, array("password"=>md5($password)))) {
			$result = true;
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
	
	public function getListDataProvider($IdList, $userType=null, $time=null, $offset=null, $itemCount=null, $totalItemCount = null)
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
	
		$sqlCount = 'SELECT count(*)
		FROM '.  Users::model()->tableName() . ' u
		WHERE Id in ('. $IdList.')';
	
		$sql = 'SELECT  u.Id as id, u.realname as Name, u.latitude, u.longitude, u.altitude,
		u.userType, u.deviceId,
		date_format(u.dataArrivedTime,"%d %b %Y %T") as dataArrivedTime,
		date_format(u.dataCalculatedTime,"%d %b %Y %T") as dataCalculatedTime,
		u.account_type
		FROM '.  Users::model()->tableName() . ' u
		WHERE Id in ('. $IdList.')';
	
	
		if ($time != null) {
			$timeSql = ' AND unix_timestamp(u.dataArrivedTime) >= '. $time;
			$sqlCount .= $timeSql;
			$sql .= $timeSql;
		}
	
		if ($userTypeSqlPart != '') {
			$sqlCount .= ' AND '. $userTypeSqlPart;
			$sql .= ' AND ' . $userTypeSqlPart;
		}
	
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
						'pageSize'=>$itemCount,
				),
		));
	
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
		WHERE '. $IdListSql .' u.realname like "%'. $text .'%"' ;
	
		$sql = 'SELECT  u.Id as id, u.realname as Name, u.latitude, u.longitude, u.altitude,
		u.userType, u.deviceId,
		date_format(u.dataArrivedTime,"%d %b %Y %T") as dataArrivedTime,
		date_format(u.dataCalculatedTime,"%d %b %Y %T") as dataCalculatedTime,
		u.account_type, "0" as status
		FROM '.  Users::model()->tableName() . ' u
		WHERE '. $IdListSql .' u.realname like "%'. $text .'%"';
	
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
	
	public function getFriendList($Id)
	{
	
		//TODO: this function should be moved to Friends model
		$sql = 'SELECT IF( friend1 != '. $Id.', friend1, friend2 ) as friend '
		.' FROM ' .  Friends::model()->tableName()
		.' WHERE '
		.' ((friend1='.Yii::app()->user->id.' AND friend2Visibility=1)'
		.' OR (friend2='.Yii::app()->user->id.' AND friend1Visibility=1)'
		.' ) '
		.' AND STATUS = 1';
		$friendsResult = Yii::app()->db->createCommand($sql)->queryAll();
	
		return $friendsResult;
	}
	
	public function setUserPositionPublicity($userId, $isPublic)
	{
		$user=Users::model()->findByPk($userId);
		$user->publicPosition = ($isPublic == true)?1:0;
	
		return $user->save();
	}
	
	public function isUserPositionPublic($userId)
	{
		$user=Users::model()->findByPk($userId);
	
		return ($user->publicPosition == 1)?true:false;
	}
	
	public function setAuthorityLevel($userId, $level)
	{
		$user=Users::model()->findByPk($userId);
		$user->authorityLevel = $level;
	
		return $user->save();
	}
	
	public function getAuthorityLevel($userId)
	{
		$user=Users::model()->findByPk($userId);
	
		return $user->authorityLevel;
	}
	
}