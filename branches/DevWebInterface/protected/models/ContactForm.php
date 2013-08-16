<?php

/**
 * ContactForm class.
 * ContactForm is the data structure for keeping
 * contact form data. It is used by the 'contact' action of 'SiteController'.
 */
class ContactForm extends CFormModel
{
	public $firstName;
	public $lastName;
	public $subject;		
	public $email;	
	public $detail;
	
	public $verifyCode;

	/**
	 * Declares the validation rules.
	 */
	public function rules()
	{
		return array(
			// name, email, subject and body are required
			(Yii::app()->user->isGuest == true)?array('firstName, lastName, email, subject, detail', 'required','message'=>Yii::t('site', 'Please, enter the field')):array('subject, detail', 'required','message'=>Yii::t('site', 'Please, enter the field')),
			
			// email has to be a valid email address
			array('email', 'email'),
			
			// verifyCode needs to be entered correctly
			//array('verifyCode', 'captcha', 'allowEmpty'=>!CCaptcha::checkRequirements()),
				
			array('verifyCode', 'CaptchaExtendedValidator', 'allowEmpty'=>!CCaptcha::checkRequirements()),
		);
	}

	/**
	 * Declares customized attribute labels.
	 * If not declared here, an attribute would have a label that is
	 * the same as its name with the first letter in upper case.
	 */
	public function attributeLabels()
	{
		return array(
			'firstName'=>Yii::t('site', 'First Name'),
			'lastName'=>Yii::t('site', 'Last Name'),
			'subject'=>Yii::t('site', 'Subject'),
			'email'=>Yii::t('site', 'E-mail'),
			'detail'=>Yii::t('site', 'Detail'),				
			'verifyCode'=>Yii::t('site', 'Verification Code'),
		);
	}
}