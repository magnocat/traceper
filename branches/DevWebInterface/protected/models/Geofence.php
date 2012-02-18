<?php

/**
 * This is the model class for table "traceper_geofence".
 *
 * The followings are the available columns in table 'traceper_geofence':
 * @property integer $Id
 * @property integer $userId
 * @property string $name
 * @property string $description
 * @property string $point1Latitude
 * @property string $point1Longitude
 * @property string $point2Latitude
 * @property string $point2Longitude
 * @property string $point3Latitude
 * @property string $point3Longitude
 */
class Geofence extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @return Geofence the static model class
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
		return 'traceper_geofence';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('userId, name', 'required'),
			array('userId', 'numerical', 'integerOnly'=>true),
			array('name', 'length', 'max'=>45),
			array('description', 'length', 'max'=>500),
			array('point1Latitude, point2Latitude, point3Latitude', 'length', 'max'=>9),
			array('point1Longitude, point2Longitude, point3Longitude', 'length', 'max'=>9),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('Id, userId, name, description, point1Latitude, point1Longitude, point2Latitude, point2Longitude, point3Latitude, point3Longitude', 'safe', 'on'=>'search'),
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
			'name' => 'Name',
			'description' => 'Description',
			'point1Latitude' => 'Point1 Latitude',
			'point1Longitude' => 'Point1 Longitude',
			'point2Latitude' => 'Point2 Latitude',
			'point2Longitude' => 'Point2 Longitude',
			'point3Latitude' => 'Point3 Latitude',
			'point3Longitude' => 'Point3 Longitude',
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
		$criteria->compare('name',$this->name,true);
		$criteria->compare('description',$this->description,true);
		$criteria->compare('point1Latitude',$this->point1Latitude,true);
		$criteria->compare('point1Longitude',$this->point1Longitude,true);
		$criteria->compare('point2Latitude',$this->point2Latitude,true);
		$criteria->compare('point2Longitude',$this->point2Longitude,true);
		$criteria->compare('point3Latitude',$this->point3Latitude,true);
		$criteria->compare('point3Longitude',$this->point3Longitude,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}