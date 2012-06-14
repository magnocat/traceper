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
			//name and deviceID (IMEI no) are required
			array('name, email, password, passwordAgain', 'required',
			'message'=>'Field cannot be blank!'),
			array('email', 'email', 'message'=>'E-mail not valid!'),
			// password needs to be same
			array('passwordAgain', 'compare', 'compareAttribute'=>'password',
			'message'=>'Passwords not same!'),
			array('email', 'isExists'),
		);
	}

	/**
	 * Declares attribute labels.
	 */
	public function attributeLabels()
	{
		return array(
			'email'=>Yii::t('general', 'E-mail'),
			'passwordAgain'=>Yii::t('general', 'Password (Again)'),
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
				$this->addError('email','E-mail is already registered!');
			}
		}
	}	
}
