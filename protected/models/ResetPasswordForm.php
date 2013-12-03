<?php

/**
 * ResetPasswordForm class.
 * ResetPasswordForm is the data structure for reseting
 * user password. It is used by the 'resetPassword' action of 'SiteController'.
 */
class ResetPasswordForm extends CFormModel
{
	public $token;
	public $newPassword;
	public $newPasswordAgain;

	/**
	 * Declares the validation rules.
	 * The rules state that currentPassword, newPassword and newPasswordAgain are required,
	 * currentPassword needs to be checked.
	 */
	public function rules()
	{
		return array(
			// username and password are required
			array('newPassword, newPasswordAgain', 'required',
			'message'=>Yii::t('site', 'Please, enter the field')),
			
			// password needs to be same
			array('newPasswordAgain', 'compare', 'compareAttribute'=>'newPassword',
			'message'=>Yii::t('site', 'Passwords not same!')),
				
			array('newPassword', 'length', 'min'=>5, 'message'=>Yii::t('site', 'Minimum 5 characters')),
				
			array('token', 'safe'),
		);
	}

	/**
	 * Declares attribute labels.
	 */
	public function attributeLabels()
	{
		return array(
			'resetPassword'=>Yii::t('site', 'Reset Your Password'),
			'newPassword'=>Yii::t('site', 'New Password'),
			'newPasswordAgain'=>Yii::t('site', 'New Password (Again)'),
		);
	}	
}
