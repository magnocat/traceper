<?php

/**
 * RegisterForm class.
 * RegisterForm is the data structure for registering
 * the user. It is used by the 'register' action of 'SiteController'.
 */
class RegisterNewStaffForm extends CFormModel
{
	public $name;
	public $email;
	public $password;
	public $passwordAgain;	

	/**
	 * Declares the validation rules.
	 * The rules state that username and password are required,
	 * and password needs to be authenticated.
	 */
	public function rules()
	{
		return array(
			array('name, email, password, passwordAgain', 'required',
			'message'=>Yii::t('site', 'Please, enter the field')),
			array('email', 'email', 'message'=>Yii::t('site', 'E-mail not valid!')),
			array('email', 'isExists'),
			// password needs to be same
			array('passwordAgain', 'compare', 'compareAttribute'=>'password',
			'message'=>Yii::t('site', 'Passwords not same!')),
		);
	}

	/**
	 * Declares attribute labels.
	 */
	public function attributeLabels()
	{
		return array(
			'name'=>Yii::t('site', 'Name'),	
			'email'=>Yii::t('site', 'E-mail'),
			'password'=>Yii::t('site', 'Password'),
			'passwordAgain'=>Yii::t('site', 'Password (Again)'),
		);
	}

	public function isExists($attribute,$params)
	{
		if(!$this->hasErrors())
		{
			$criteria=new CDbCriteria;
			$criteria->select='email';
			$criteria->condition='email=:email';
			$criteria->params=array(':email'=>$this->email);
			$data = Users::model()->find($criteria);
			if ($data == null) {
				$data = UserCandidates::model()->find($criteria);
			}
			if ($data != null) {
				$this->addError('email',Yii::t('site', 'E-mail is already registered!'));
			}
		}
	}	
}
