<?php

/**
 * This is the model class for table "traceper_privacy_groups".
 *
 * The followings are the available columns in table 'traceper_privacy_groups':
 * @property string $id
 * @property string $name
 * @property string $owner
 * @property string $description
 * @property integer $allowedToSeeOwnersPosition
 */
class PrivacyGroups extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @return PrivacyGroups the static model class
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
		return 'traceper_privacy_groups';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('name, owner', 'required'),
			array('allowedToSeeOwnersPosition', 'numerical', 'integerOnly'=>true),
			array('name', 'length', 'max'=>45),
			array('owner', 'length', 'max'=>10),
			array('description', 'length', 'max'=>500),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, name, owner, description, allowedToSeeOwnersPosition', 'safe', 'on'=>'search'),
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
			'id' => 'ID',
			'name' => 'Name',
			'owner' => 'Owner',
			'description' => 'Description',
			'allowedToSeeOwnersPosition' => 'Allowed To See Owners Position',
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

		$criteria->compare('id',$this->id,true);
		$criteria->compare('name',$this->name,true);
		$criteria->compare('owner',$this->owner,true);
		$criteria->compare('description',$this->description,true);
		$criteria->compare('allowedToSeeOwnersPosition',$this->allowedToSeeOwnersPosition);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}