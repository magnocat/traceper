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
 * @property integer $lineNumber
 *
 * The followings are the available model relations:
 * @property TraceperUsers $user
 */
class UserWasHere extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @return UserWasHere the static model class
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
			array('userId, dataArrivedTime, lineNumber', 'required'),
			array('lineNumber', 'numerical', 'integerOnly'=>true),
			array('userId', 'length', 'max'=>11),
			array('latitude', 'length', 'max'=>8),
			array('altitude', 'length', 'max'=>15),
			array('longitude', 'length', 'max'=>9),
			array('deviceId', 'length', 'max'=>64),
			array('dataCalculatedTime', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('Id, userId, dataArrivedTime, latitude, altitude, longitude, deviceId, dataCalculatedTime, lineNumber', 'safe', 'on'=>'search'),
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
			'lineNumber' => 'Line Number',
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

		$criteria->compare('Id',$this->Id);
		$criteria->compare('userId',$this->userId,true);
		$criteria->compare('dataArrivedTime',$this->dataArrivedTime,true);
		$criteria->compare('latitude',$this->latitude,true);
		$criteria->compare('altitude',$this->altitude,true);
		$criteria->compare('longitude',$this->longitude,true);
		$criteria->compare('deviceId',$this->deviceId,true);
		$criteria->compare('dataCalculatedTime',$this->dataCalculatedTime,true);
		$criteria->compare('lineNumber',$this->lineNumber);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}