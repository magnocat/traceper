<?php

/**
 * This is the model class for table "traceper_user_was_here".
 *
 * The followings are the available columns in table 'traceper_user_was_here':
 * @property integer $Id
 * @property string $userId
 * @property string $dataArrivedTime
 * @property string $latitude
 * @property string $altitude
 * @property string $longitude
 * @property string $deviceId
 * @property string $dataCalculatedTime
 * @property string $address
 * @property string $country
 * @property integer $locationSource
 *
 * The followings are the available model relations:
 * @property TraceperUsers $user
 */
class UserWasHere extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'traceper_user_was_here';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('userId, dataArrivedTime', 'required'),
			array('locationSource', 'numerical', 'integerOnly'=>true),
			array('userId, longitude', 'length', 'max'=>11),
			array('latitude', 'length', 'max'=>10),
			array('altitude', 'length', 'max'=>15),
			array('deviceId', 'length', 'max'=>64),
			array('country', 'length', 'max'=>20),
			array('dataCalculatedTime, address', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('Id, userId, dataArrivedTime, latitude, altitude, longitude, deviceId, dataCalculatedTime, address, country, locationSource', 'safe', 'on'=>'search'),
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
			'user' => array(self::BELONGS_TO, 'TraceperUsers', 'userId'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'Id' => 'ID',
			'userId' => 'User',
			'dataArrivedTime' => 'Data Arrived Time',
			'latitude' => 'Latitude',
			'altitude' => 'Altitude',
			'longitude' => 'Longitude',
			'deviceId' => 'Device',
			'dataCalculatedTime' => 'Data Calculated Time',
			'address' => 'Address',
			'country' => 'Country',
			'locationSource' => 'Location Source',
		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 *
	 * Typical usecase:
	 * - Initialize the model fields with values from filter form.
	 * - Execute this method to get CActiveDataProvider instance which will filter
	 * models according to data in model fields.
	 * - Pass data provider to CGridView, CListView or any similar widget.
	 *
	 * @return CActiveDataProvider the data provider that can return the models
	 * based on the search/filter conditions.
	 */
	public function search()
	{
		// @todo Please modify the following code to remove attributes that should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('Id',$this->Id);
		$criteria->compare('userId',$this->userId,true);
		$criteria->compare('dataArrivedTime',$this->dataArrivedTime,true);
		$criteria->compare('latitude',$this->latitude,true);
		$criteria->compare('altitude',$this->altitude,true);
		$criteria->compare('longitude',$this->longitude,true);
		$criteria->compare('deviceId',$this->deviceId,true);
		$criteria->compare('dataCalculatedTime',$this->dataCalculatedTime,true);
		$criteria->compare('address',$this->address,true);
		$criteria->compare('country',$this->country,true);
		$criteria->compare('locationSource',$this->locationSource);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return UserWasHere the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
	
	public function logLocation($userId, $latitude, $longitude, $altitude, $deviceId, $arrivedTime, $calculatedTime, $address, $country, $locationSource){
		// 		$sql = sprintf('INSERT INTO '
		// 				. $this->tableName() . '
		// 				(userId, latitude, longitude, altitude, dataArrivedTime, deviceId, dataCalculatedTime, address, country)
		// 				VALUES(%d,	%f, %f, %f, NOW(), "%s", "%s", "%s", "%s")
		// 				',
		// 				$userId, $latitude, $longitude, $altitude, $deviceId, $calculatedTime, $address, $country);
	
		$sql = sprintf('INSERT INTO '
				. $this->tableName() . '
				(userId, latitude, longitude, altitude, dataArrivedTime, deviceId, dataCalculatedTime, address, country, locationSource)
				VALUES(%d,	%f, %f, %f, "%s", "%s", "%s", "%s", "%s", %d)
				',
				$userId, $latitude, $longitude, $altitude, $arrivedTime, $deviceId, $calculatedTime, $address, $country, $locationSource);
	
		$effectedRows = Yii::app()->db->createCommand($sql)->execute();
	
		$result = false;
	
		if ($effectedRows == 1) {
			$result = true;
		}
	
		return $result;
	
		// 		$userWasHere = new UserWasHere;
	
		// 		$userWasHere->userId = $userId;
		// 		$userWasHere->latitude = $latitude;
		// 		$userWasHere->longitude = $longitude;
		// 		$userWasHere->altitude = $altitude;
		// 		$userWasHere->dataArrivedTime = $arrivedTime;
		// 		$userWasHere->deviceId = $deviceId;
		// 		$userWasHere->dataCalculatedTime = $calculatedTime;
		// 		$userWasHere->address = $address;
		// 		$userWasHere->country = $country;
		// 		$userWasHere->locationSource = $locationSource;
	
		// 		return $userWasHere->save();
	}
	
	public function getPastPointsDataProvider($userId, $pageNo, $itemCount)
	{
		$sql = 'SELECT
		longitude, latitude, deviceId, address, country, locationSource,
		date_format(u.dataArrivedTime,"%d %b %Y %T") as dataArrivedTime,
		date_format(u.dataCalculatedTime,"%d %b %Y %T") as dataCalculatedTime
		FROM ' . UserWasHere::model()->tableName() .' u
		WHERE
		userId = '. $userId . '
		ORDER BY
		Id DESC';
	
		// subtract 1 to not get the last location into consideration
		$sqlCount = 'SELECT
		count(*)
		FROM '. UserWasHere::model()->tableName() .'
		WHERE
		userId = '. $userId;
	
		$count=Yii::app()->db->createCommand($sqlCount)->queryScalar();
	
		$pageNo = $pageNo - 1; //Since CPagination's page index starts from 0
		$dataProvider = new CSqlDataProvider($sql, array(
				'totalItemCount'=>$count,
				'sort'=>array(
						'attributes'=>array(
								'id', 'Name',
						),
				),
				'pagination'=>array(
						'pageSize'=>$itemCount,
						'currentPage'=>$pageNo
				),
		));
	
		return $dataProvider;
	}
	
	public function getMostRecentLocation($userId, &$par_latitude, &$par_longitude, &$par_altitude, &$par_address)
	{
		//$sql = 'SELECT latitude, longitude, altitude FROM traceper_user_was_here WHERE userId = '.$userId.' ORDER BY dataArrivedTime DESC LIMIT 1';
		//$userWasHere = UserWasHere::model()->findBySql($sql);
	
		$userWasHere = UserWasHere::model()->find(array('order'=>'dataArrivedTime DESC', 'limit'=>1, 'condition'=>'userId=:userId', 'params'=>array(':userId'=>$userId)));
	
		$result = false;
	
		if($userWasHere != null)
		{
			$par_latitude = $userWasHere->latitude;
			$par_longitude = $userWasHere->longitude;
			$par_altitude = $userWasHere->altitude;
			$par_address = $userWasHere->address;
	
			$result = true;
		}
		else
		{
			$result = false;
		}
	
		return $result;
	}	
}
