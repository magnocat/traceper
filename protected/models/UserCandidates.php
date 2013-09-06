<?php

/**
 * This is the model class for table "traceper_user_candidates".
 *
 * The followings are the available columns in table 'traceper_user_candidates':
 * @property integer $Id
 * @property string $email
 * @property string $realname
 * @property string $password
 * @property string $time
 * @property string $registrationMedium
 * @property string $preferredLanguage
 */
class UserCandidates extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @return UserCandidates the static model class
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
			array('email, realname, password, time', 'required'),
			array('email, realname', 'length', 'max'=>100),
			array('password', 'length', 'max'=>32),
			array('registrationMedium', 'length', 'max'=>10),
			array('preferredLanguage', 'length', 'max'=>20),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('Id, email, realname, password, time, registrationMedium, preferredLanguage', 'safe', 'on'=>'search'),
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
			'email' => 'Email',
			'realname' => 'Realname',
			'password' => 'Password',
			'time' => 'Time',
			'registrationMedium' => 'Registration Medium',
			'preferredLanguage' => 'Preferred Language',
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
		$criteria->compare('email',$this->email,true);
		$criteria->compare('realname',$this->realname,true);
		$criteria->compare('password',$this->password,true);
		$criteria->compare('time',$this->time,true);
		$criteria->compare('registrationMedium',$this->registrationMedium,true);
		$criteria->compare('preferredLanguage',$this->preferredLanguage,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
	
	public function saveUserCandidates($email, $password, $realname, $time, $registrationMedium, $preferredLanguage){
		$userCandidates = new UserCandidates;
	
	
		$userCandidates->email = $email;
		$userCandidates->realname = $realname;
		$userCandidates->password = $password;
		$userCandidates->time = $time;
		$userCandidates->registrationMedium = $registrationMedium;
		$userCandidates->preferredLanguage = $preferredLanguage;
	
		return $userCandidates->save();
	}
	
	function getCandidateInfoByEmail($email, &$password, &$name, &$registrationTime)
	{
		$userCandidate = UserCandidates::model()->find('email=:email', array(':email'=>$email));
		$result = false;
	
		if($userCandidate != null)
		{
			$password = $userCandidate->password;
			$name = $userCandidate->realname;
			$registrationTime = $userCandidate->time;
	
			$result = true;
		}
		else
		{
			$result = false;
		}
	
		return $result;
	}	
}