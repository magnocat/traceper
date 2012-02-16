<?php

/**
 * This is the model class for table "traceper_user_privacy_group_relation".
 *
 * The followings are the available columns in table 'traceper_user_privacy_group_relation':
 * @property string $Id
 * @property integer $groupOwner
 * @property string $userId
 * @property string $groupId
 *
 * The followings are the available model relations:
 * @property TraceperUsers $user
 */
class UserPrivacyGroupRelation extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @return UserPrivacyGroupRelation the static model class
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
		return 'traceper_user_privacy_group_relation';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('groupOwner, userId, groupId', 'required'),
			array('groupOwner', 'numerical', 'integerOnly'=>true),
			array('userId, groupId', 'length', 'max'=>10),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('Id, groupOwner, userId, groupId', 'safe', 'on'=>'search'),
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
			'groupOwner' => 'Group Owner',
			'userId' => 'User',
			'groupId' => 'Group',
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
		$criteria->compare('groupOwner',$this->groupOwner);
		$criteria->compare('userId',$this->userId,true);
		$criteria->compare('groupId',$this->groupId,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}