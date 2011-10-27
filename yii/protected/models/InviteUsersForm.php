<?php

/**
 * InviteUsersForm class.
 * InviteUsersForm is the data structure for inviting user's friends
 * to the web site. It is used by the 'inviteUsers' action of 'SiteController'.
 */
class InviteUsersForm extends CFormModel
{
	public $emails;
	public $message;

	/**
	 * Declares the validation rules.
	 * The rules state that username and password are required,
	 * and password needs to be authenticated.
	 */
	public function rules()
	{
		return array(
			// username and password are required
			array('emails', 'required', 'message'=>'Field cannot be blank!'),
			array('emails', 'ext.MultiEmailValidator', 'delimiter'=>',', 'min'=>1, 'max'=>10),
		);
	}

	/**
	 * Declares attribute labels.
	 */
	public function attributeLabels()
	{
		return array(
			'emails'=>Yii::t('general', 'E-mails'),
			'message'=>Yii::t('general', 'Message for your friends'),
		);
	}	
}
