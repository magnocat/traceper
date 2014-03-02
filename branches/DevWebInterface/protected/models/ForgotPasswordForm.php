<?php

/**
 * ForgotPasswordForm class.
 * ForgotPasswordForm is the data structure for changing
 * user password. It is used by the 'forgotPassword' action of 'SiteController'.
 */
class ForgotPasswordForm extends CFormModel
{
	public $email;

	/**
	 * Declares the validation rules.
	 */
	public function rules()
	{
		return array(
			// email is required
			array('email', 'required','message'=>Yii::t('site', 'Please, enter the field')),
			array('email', 'email', 'message'=>Yii::t('site', 'E-mail not valid!')),
			array('email', 'isNotRegistered'),
			array('email', 'checkFacebook'),
		);
	}

	/**
	 * Declares attribute labels.
	 */
	public function attributeLabels()
	{
		return array(
			'email'=>Yii::t('site', 'E-mail'),
		);
	}

	public function isNotRegistered($attribute, $params)
	{
		if(!$this->hasErrors())
		{
			$criteria=new CDbCriteria;
			$criteria->select='email';
			$criteria->condition='email=:email';
			$criteria->params=array(':email'=>$this->email);
			$data = Users::model()->find($criteria);

			if(UserCandidates::model()->find($criteria) != null)
			{
				$this->addError('email',Yii::t('site', 'Registration incomplete, please request activation e-mail below the sign up form'));
			}						
			else if(Users::model()->find($criteria) == null)
			{
				$this->addError('email',Yii::t('site', 'This e-mail is not registered!'));
			}			
		}
	}

	public function checkFacebook($attribute,$params)
	{
		if(!$this->hasErrors())
		{
			if(Users::model()->isFacebookUser($this->email))
			{
				$this->addError('email',Yii::t('site', 'You are registered as Facebook user for our service, therefore you do not have to enter a Traceper password. You could use "Log in with facebook" button to log in to your Traceper account.'));
			}
		}
	}	
}
