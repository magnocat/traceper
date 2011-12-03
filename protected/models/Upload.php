<?php

/**
 * This is the model class for table "traceper_upload".
 *
 * The followings are the available columns in table 'traceper_upload':
 * @property integer $Id
 * @property integer $userId
 * @property string $latitude
 * @property string $longitude
 * @property string $altitude
 * @property string $uploadTime
 * @property integer $publicData
 *
 * The followings are the available model relations:
 * @property TraceperFriends $user
 */
class Upload extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @return Upload the static model class
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
		return 'traceper_upload';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('userId, latitude, longitude, altitude, uploadTime', 'required'),
			array('userId, publicData', 'numerical', 'integerOnly'=>true),
			array('latitude', 'length', 'max'=>8),
			array('longitude', 'length', 'max'=>9),
			array('altitude', 'length', 'max'=>15),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('Id, userId, latitude, longitude, altitude, uploadTime, publicData', 'safe', 'on'=>'search'),
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
			'user' => array(self::BELONGS_TO, 'Users', 'userId'),
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
			'latitude' => 'Latitude',
			'longitude' => 'Longitude',
			'altitude' => 'Altitude',
			'uploadTime' => 'Upload Time',
			'publicData' => 'Public Data',
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
		$criteria->compare('userId',$this->userId);
		$criteria->compare('latitude',$this->latitude,true);
		$criteria->compare('longitude',$this->longitude,true);
		$criteria->compare('altitude',$this->altitude,true);
		$criteria->compare('uploadTime',$this->uploadTime,true);
		$criteria->compare('publicData',$this->publicData);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}