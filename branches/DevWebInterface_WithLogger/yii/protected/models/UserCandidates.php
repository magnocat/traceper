<?php

/**
 * This is the model class for table "traceper_user_candidates".
 *
 * The followings are the available columns in table 'traceper_user_candidates':
 * @property string $Id
 * @property string $email
 * @property string $realname
 * @property string $password
 * @property string $time
 *
 * The followings are the available model relations:
 * @property TraceperFriends[] $traceperFriends
 * @property TraceperFriends[] $traceperFriends1
 */
class UserCandidates extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @return Users the static model class
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
		return 'traceper_user_candidates';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('email, realname, password', 'required'),
			array('email', 'length', 'max'=>100),
			array('password', 'length', 'max'=>32),
			array('realname', 'length', 'max'=>80),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('Id, email, realname, password, time', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array();
		
//		return array(
//			'traceperFriends' => array(self::HAS_MANY, 'Friends', 'friend2'),
//			'traceperFriends1' => array(self::HAS_MANY, 'Friends', 'friend1'),
//		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'Id' => 'ID',
			'email' => 'E-mail',
			'realname' => 'Realname',
			'password' => 'Password',
			'time'=> 'Time',
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
		$criteria->compare('email',$this->email,true);
		$criteria->compare('realname',$this->realname,true);
		$criteria->compare('password',$this->password,true);
		$criteria->compare('time',$this->time,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}