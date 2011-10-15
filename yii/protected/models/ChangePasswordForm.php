<?php

/**
 * ChangePasswordForm class.
 * ChangePasswordForm is the data structure for changing
 * user pasword. It is used by the 'changePassword' action of 'SiteController'.
 */
class ChangePasswordForm extends CFormModel
{
	public $currentPassword;
	public $newPassword;
	public $newPasswordAgain;

	private $_identity;

	/**
	 * Declares the validation rules.
	 * The rules state that username and password are required,
	 * and password needs to be authenticated.
	 */
	public function rules()
	{
		return array(
			// username and password are required
			array('currentPassword', 'newPassword', 'newPasswordAgain', 'required'),
			// password needs to be authenticated
			array('newPassword', 'compare', 'newPasswordAgain'),
		);
	}

	/**
	 * Declares attribute labels.
	 */
	public function attributeLabels()
	{
		return array(
			'currentPassword'=>Yii::t('general', 'Current Password:'),
			'newPassword'=>Yii::t('general', 'New Password:'),
			'newPasswordAgain'=>Yii::t('general', 'New Password(again):'),
		);
	}
}
