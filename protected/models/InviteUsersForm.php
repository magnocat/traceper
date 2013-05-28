<?php

/**
 * InviteUsersForm class.
 * InviteUsersForm is the data structure for inviting user's friends
 * to the web site. It is used by the 'inviteUsers' action of 'SiteController'.
 */
class InviteUsersForm extends CFormModel
{
	public $emails;
	public $invitationMessage;

	/**
	 * Declares the validation rules.
	 * The rules state that username and password are required,
	 * and password needs to be authenticated.
	 */
	public function rules()
	{
		return array(
			// username and password are required
			array('emails', 'required', 'message'=>Yii::t('site', 'Please, enter the field')),
			array('emails', 'ext.MultiEmailValidator', 'delimiter'=>',', 'min'=>1, 'max'=>10),
			array('invitationMessage', 'length', 'max'=>500), //Bu alanýn alanýn düzgün çalýþmasý en azýndan bir rule tanýmlamak gerekiyor
		);
	}

	/**
	 * Declares attribute labels.
	 */
	public function attributeLabels()
	{
		return array(
			'emails'=>Yii::t('site', 'E-mails'),
			'invitationMessage'=>Yii::t('site', 'Message for your friends'),
		);
	}	
}
