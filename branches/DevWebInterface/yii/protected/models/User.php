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
 * @property string $realname
 * @property string $email
 * @property string $dataArrivedTime
 * @property string $deviceId
 * @property string $status_message
 * @property integer $status_source
 * @property string $status_message_time
 * @property string $dataCalculatedTime
 */
class User extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @return User the static model class
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
			array('password, realname, email', 'required'),
			array('status_source', 'numerical', 'integerOnly'=>true),
			array('password', 'length', 'max'=>32),
			array('group', 'length', 'max'=>10),
			array('latitude', 'length', 'max'=>8),
			array('longitude', 'length', 'max'=>9),
			array('altitude', 'length', 'max'=>15),
			array('realname', 'length', 'max'=>80),
			array('email', 'length', 'max'=>100),
			array('deviceId', 'length', 'max'=>64),
			array('status_message', 'length', 'max'=>128),
			array('dataArrivedTime, status_message_time, dataCalculatedTime', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('Id, password, group, latitude, longitude, altitude, realname, email, dataArrivedTime, deviceId, status_message, status_source, status_message_time, dataCalculatedTime', 'safe', 'on'=>'search'),
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
			'realname' => 'Realname',
			'email' => 'Email',
			'dataArrivedTime' => 'Data Arrived Time',
			'deviceId' => 'Device',
			'status_message' => 'Status Message',
			'status_source' => 'Status Source',
			'status_message_time' => 'Status Message Time',
			'dataCalculatedTime' => 'Data Calculated Time',
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
		$criteria->compare('realname',$this->realname,true);
		$criteria->compare('email',$this->email,true);
		$criteria->compare('dataArrivedTime',$this->dataArrivedTime,true);
		$criteria->compare('deviceId',$this->deviceId,true);
		$criteria->compare('status_message',$this->status_message,true);
		$criteria->compare('status_source',$this->status_source);
		$criteria->compare('status_message_time',$this->status_message_time,true);
		$criteria->compare('dataCalculatedTime',$this->dataCalculatedTime,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}