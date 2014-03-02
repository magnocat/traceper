<?php

/**
 * ActivationNotReceivedForm class.
 * ActivationNotReceivedForm is the data structure for resending
 * a activation link. It is used by the 'activationNotReceived' action of 'SiteController'.
 */
class ActivationNotReceivedForm extends CFormModel
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
			array('email', 'checkFacebook'),
			array('email', 'isRegistered'),
			array('email', 'isNotCandidate'),
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
	
	public function isRegistered($attribute, $params)
	{
		if(!$this->hasErrors())
		{
			$criteria=new CDbCriteria;
			$criteria->select='email';
			$criteria->condition='email=:email';
			$criteria->params=array(':email'=>$this->email);
			$data = Users::model()->find($criteria);
	
			if ($data != null) {
				$this->addError('email',Yii::t('site', 'You are already registered for our service. If you do not remember your password, you could request to determine a new one by the link "Forgot Password?".'));
			}
		}
	}	

	public function isNotCandidate($attribute, $params)
	{
		if(!$this->hasErrors())
		{
			$criteria=new CDbCriteria;
			$criteria->select='email';
			$criteria->condition='email=:email';
			$criteria->params=array(':email'=>$this->email);
			$data = UserCandidates::model()->find($criteria);

			if ($data == null) {
				$this->addError('email',Yii::t('site', 'There has been a problem with your registration process. Please try to sign up for Traceper again.'));
			}
		}
	}

	public function checkFacebook($attribute,$params)
	{
		if(!$this->hasErrors())
		{
			$appVersion = null;
	
			if(Users::model()->isFacebookUser($this->email, $appVersion))
			{
				//1.0.16'dan sonraki uygulamalarda bu kontrolü yap, eski uygulamalari bozma
				if($appVersion > "1.0.16")
				{
					$this->addError('email',Yii::t('site', 'You are already registered as Facebook user for our service. You could use "Log in with facebook" button to log in to your Traceper account.'));
				}
			}
		}
	}	
}
