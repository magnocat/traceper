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

			if ($data == null) {
				$this->addError('email',Yii::t('site', 'This e-mail is not registered!'));
			}
		}
	}	
}
