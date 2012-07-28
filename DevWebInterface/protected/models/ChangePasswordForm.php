<?php

/**
 * ChangePasswordForm class.
 * ChangePasswordForm is the data structure for changing
 * user password. It is used by the 'changePassword' action of 'SiteController'.
 */
class ChangePasswordForm extends CFormModel
{
	public $currentPassword;
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
			array('currentPassword, newPassword, newPasswordAgain', 'required',
			'message'=>'Field cannot be blank!'),
			// current password needs to be checked
			array('currentPassword', 'checkCurrentPassword'),			
			// password needs to be same
			array('newPasswordAgain', 'compare', 'compareAttribute'=>'newPassword',
			'message'=>'Passwords not same!'),
			
			array('newPassword', 'compare', 'compareAttribute'=>'currentPassword',
				  'operator'=>'!=',	'message'=>'Same with current password!'),
		);
	}

	/**
	 * Declares attribute labels.
	 */
	public function attributeLabels()
	{
		return array(
			'currentPassword'=>Yii::t('general', 'Current Password'),
			'newPassword'=>Yii::t('general', 'New Password'),
			'newPasswordAgain'=>Yii::t('general', 'New Password(again)'),
		);
	}
	
	/**
	 * Checks the current password.
	 */
	public function checkCurrentPassword($attribute,$params)
	{
		if(!$this->hasErrors())
		{						
			$result = Users::model()->findByPk(Yii::app()->user->id,'password=:password', array(':password'=> md5($this->currentPassword)));
			
			if($result == NULL)
			{
				$this->addError('currentPassword','Password incorrect!');	
			}
		}
	}	
}
