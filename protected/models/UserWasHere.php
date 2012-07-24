<?php

/**
 * This is the model class for table "traceper_user_was_here".
 *
 * The followings are the available columns in table 'traceper_user_was_here':
 * @property integer $Id
 * @property integer $userId
 * @property string $dataArrivedTime
 * @property string $latitude
 * @property string $altitude
 * @property string $longitude
 * @property string $deviceId
 * @property string $dataCalculatedTime
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
			array('userId, dataArrivedTime', 'required'),
			array('userId', 'numerical', 'integerOnly'=>true),
			array('latitude', 'length', 'max'=>8),
			array('altitude', 'length', 'max'=>15),
			array('longitude', 'length', 'max'=>9),
			array('deviceId', 'length', 'max'=>64),
			array('dataCalculatedTime', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('Id, userId, dataArrivedTime, latitude, altitude, longitude, deviceId, dataCalculatedTime', 'safe', 'on'=>'search'),
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
			'userId' => 'User',
			'dataArrivedTime' => 'Data Arrived Time',
			'latitude' => 'Latitude',
			'altitude' => 'Altitude',
			'longitude' => 'Longitude',
			'deviceId' => 'Device',
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

		$criteria->compare('Id',$this->Id);
		$criteria->compare('userId',$this->userId);
		$criteria->compare('dataArrivedTime',$this->dataArrivedTime,true);
		$criteria->compare('latitude',$this->latitude,true);
		$criteria->compare('altitude',$this->altitude,true);
		$criteria->compare('longitude',$this->longitude,true);
		$criteria->compare('deviceId',$this->deviceId,true);
		$criteria->compare('dataCalculatedTime',$this->dataCalculatedTime,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
	
	
	public function logLocation($userId, $latitude, $longitude, $altitude, $deviceId, $calculatedTime){
		$sql = sprintf('INSERT INTO '
				. $this->tableName() . '
				(userId, latitude, longitude, altitude, dataArrivedTime, deviceId, dataCalculatedTime)
				VALUES(%d,	%f, %f, %f, NOW(), "%s", "%s")
				',
				$userId, $latitude, $longitude, $altitude, $deviceId, $calculatedTime);
		$effectedRows = Yii::app()->db->createCommand($sql)->execute();
		
		$result = false;
		if ($effectedRows == 1) {
			$result = true;
		}
		return $result;		
	}
	
	
	public function getPastPointsDataProvider($userId, $offset, $itemCount)
	{
		$sql = 'SELECT
					longitude, latitude, deviceId,
					date_format(u.dataArrivedTime,"%d %b %Y %T") as dataArrivedTime, 
					date_format(u.dataCalculatedTime,"%d %b %Y %T") as dataCalculatedTime
				FROM ' . UserWasHere::model()->tableName() .' u
				WHERE
					userId = '. $userId . '
				ORDER BY
					Id DESC
				LIMIT '. $offset . ',' . $itemCount ;
		
		// subtract 1 to not get the last location into consideration
		$sqlPageCount = 'SELECT
				count(*)
				FROM '. UserWasHere::model()->tableName() .'
				WHERE
				userId = '. $userId;
		
		
		$count=Yii::app()->db->createCommand($sqlCount)->queryScalar();
		
		
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
		
		return $dataProvider;
		
		
	}
}