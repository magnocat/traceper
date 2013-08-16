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
			'message'=>Yii::t('site', 'Please, enter the field')),
			// current password needs to be checked
			array('currentPassword', 'checkCurrentPassword'),			
			// password needs to be same
			array('newPasswordAgain', 'compare', 'compareAttribute'=>'newPassword',
			'message'=>Yii::t('site', 'Passwords not same!')),
			
			array('newPassword', 'checkLength'),
				
			array('newPassword', 'compare', 'compareAttribute'=>'currentPassword',
				  'operator'=>'!=',	'message'=>Yii::t('site', 'Same with current password!')),
		);
	}

	/**
	 * Declares attribute labels.
	 */
	public function attributeLabels()
	{
		return array(
			'currentPassword'=>Yii::t('site', 'Current Password'),
			'newPassword'=>Yii::t('site', 'New Password'),
			'newPasswordAgain'=>Yii::t('site', 'New Password (Again)'),
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
				$this->addError('currentPassword', Yii::t('site', 'Password incorrect!'));	
			}
		}
	}	
	
	public function checkLength($attribute,$params)
	{
		//if(!$this->hasErrors())
		{
			if(strlen($this->newPassword) < 5)
				//if(true)
			{
				$this->addError('newPassword',Yii::t('site', 'Minimum 5 characters'));
			}
		}
	}	
}
