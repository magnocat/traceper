<?php

/**
 * RegisterForm class.
 * RegisterForm is the data structure for registering
 * the user. It is used by the 'register' action of 'SiteController'.
 */
class RegisterForm extends CFormModel
{
	public $email;
	public $name;
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
			// username and password are required
			array('email, name, password, passwordAgain', 'required',
			'message'=>'Field cannot be blank!'),
			array('email', 'email', 'message'=>'E-mail not valid!'),			
			// password needs to be same
			array('passwordAgain', 'compare', 'compareAttribute'=>'password',
			'message'=>'Passwords not same!'),
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
}
