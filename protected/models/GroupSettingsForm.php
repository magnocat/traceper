<?php

/**
 * GroupSettingsForm class.
 * GroupSettingsForm is the data structure for adjusting the group settings such as adding users to groups 
 * or deleting users from groups.
 */
class GroupSettingsForm extends CFormModel
{
	public $groupStatusArray;

	/**
	 * Declares the validation rules.
	 * The rules state that username and password are required,
	 * and password needs to be authenticated.
	 */
	public function rules()
	{
		return array(
		);
	}

	/**
	 * Declares attribute labels.
	 */
	public function attributeLabels()
	{
		return array(
			'groupStatusArray'=>'Group Status',
		);
	}
}
