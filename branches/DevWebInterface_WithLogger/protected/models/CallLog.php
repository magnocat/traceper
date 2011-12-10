<?php

/**
 * This is the model class for table "traceper_call_logg".
 *
 * The followings are the available columns in table 'traceper_call_logg':
 * @property integer $id
 * @property string $userid
 * @property string $contact
 * @property integer $number
 * @property string $latitude
 * @property string $longitude
 * @property string $begin
 * @property string $end
 * @property integer $type
 *
 * The followings are the available model relations:
 * @property TraceperUsers $user
 */
class CallLog extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @return CallLog the static model class
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
		return 'traceper_call_logg';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('userid, contact, number, begin, end, type', 'required'),
			array('number, type', 'numerical', 'integerOnly'=>true),
			array('userid', 'length', 'max'=>11),
			array('contact', 'length', 'max'=>255),
			array('latitude', 'length', 'max'=>8),
			array('longitude', 'length', 'max'=>9),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, userid, contact, number, latitude, longitude, begin, end, type', 'safe', 'on'=>'search'),
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
			'user' => array(self::BELONGS_TO, 'TraceperUsers', 'userid'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'userid' => 'Userid',
			'contact' => 'Contact',
			'number' => 'Number',
			'latitude' => 'Latitude',
			'longitude' => 'Longitude',
			'begin' => 'Begin',
			'end' => 'End',
			'type' => 'Type',
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

		$criteria->compare('id',$this->id);
		$criteria->compare('userid',$this->userid,true);
		$criteria->compare('contact',$this->contact,true);
		$criteria->compare('number',$this->number);
		$criteria->compare('latitude',$this->latitude,true);
		$criteria->compare('longitude',$this->longitude,true);
		$criteria->compare('begin',$this->begin,true);
		$criteria->compare('end',$this->end,true);
		$criteria->compare('type',$this->type);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}