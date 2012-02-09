<?php

/**
 * This is the model class for table "traceper_user_privacy_geofence_relation".
 *
 * The followings are the available columns in table 'traceper_user_privacy_geofence_relation':
 * @property string $Id
 * @property integer $geofenceOwner
 * @property string $userId
 * @property string $geofenceId
 */
class UserPrivacyGeofenceRelation extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @return UserPrivacyGeofenceRelation the static model class
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
		return 'traceper_user_privacy_geofence_relation';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('geofenceOwner, userId, geofenceId', 'required'),
			array('geofenceOwner', 'numerical', 'integerOnly'=>true),
			array('userId, geofenceId', 'length', 'max'=>10),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('Id, geofenceOwner, userId, geofenceId', 'safe', 'on'=>'search'),
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
			'geofenceOwner' => 'Geofence Owner',
			'userId' => 'User',
			'geofenceId' => 'Geofence',
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
		$criteria->compare('geofenceOwner',$this->geofenceOwner);
		$criteria->compare('userId',$this->userId,true);
		$criteria->compare('geofenceId',$this->geofenceId,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}