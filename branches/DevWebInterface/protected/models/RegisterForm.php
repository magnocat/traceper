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
	public $image;
	public $account_type;
	public $ac_id;

	/**
	 * Declares the validation rules.
	 * The rules state that username and password are required,
	 * and password needs to be authenticated.
	 */
	public function rules()
	{
		return array(
			// username and password are required
		/*
			array('email, name, password, account_type, ac_id, passwordAgain', 'required',
			'message'=>'Field cannot be blank!'),
		*/
			array('email, name, password, passwordAgain', 'required',
						'message'=>Yii::t('site', 'Please, enter the field')),
			array('email', 'email', 'message'=>Yii::t('site', 'E-mail not valid!')),			
			// password needs to be same
			array('passwordAgain', 'compare', 'compareAttribute'=>'password',
			'message'=>Yii::t('site', 'Passwords not same!')),
			
			array('ac_id', 'safe'),
				
			array('account_type', 'safe'),
			
			array('email', 'isExists'),
			
			//array('image', 'isExists')
		);
	}

	/**
	 * Declares attribute labels.
	 */
	public function attributeLabels()
	{
		return array(
			'register'=>Yii::t('common', 'Sign Up'),
			'email'=>Yii::t('site', 'E-mail'),
			'name'=>Yii::t('site', 'Name'),
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
			//$data = Users::model()->find($criteria);
			
			if(Users::model()->find($criteria) != null) 
			{
				$this->addError('email',Yii::t('site', 'E-mail is already registered!'));
			}
			else if(UserCandidates::model()->find($criteria) != null)
			{
				$this->addError('email',Yii::t('site', 'Registration incomplete, please request activation e-mail below'));
			}
			
// 			if ($data == null) {
// 				$data = UserCandidates::model()->find($criteria);
// 			}
// 			if ($data != null) {
// 				$this->addError('email',Yii::t('site', 'E-mail is already registered!'));				
// 			}							
		}
	}
}
