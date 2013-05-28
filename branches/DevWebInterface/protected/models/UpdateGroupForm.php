<?php

/**
 * UpdateGroupForm class.
 * UpdateGroupForm is the data structure for keeping
 * the people in groups form data. It is used by the 'addUserToGroup' action of 'GroupsController'.
 */
class UpdateGroupForm extends CFormModel
{
	public $userId;
	public $groupId;

	/**
	 * Declares the validation rules.
	 * The rules state that username and password are required,
	 * and password needs to be authenticated.
	 */
	public function rules()
	{
		return array(
			// username and password are required
			array('userId, groupId', 'required', 'message'=>Yii::t('site', 'Please, enter the field')),
		);
	}

	/**
	 * Declares attribute labels.
	 */
	public function attributeLabels()
	{
		return array(
			'userId'=>Yii::t('general', 'User ID'),
			'groupId'=>Yii::t('general', 'Group ID'),
		);
	}
}
