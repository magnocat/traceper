<?php

/**
 * UpdateGroupForm class.
 * UpdateGroupForm is the data structure for keeping
 * the people in groups form data. It is used by the 'addUserToGroup' action of 'GroupsController'.
 */
class UpdateGroupForm extends CFormModel
{
	public $email;
	public $groupName;

	/**
	 * Declares the validation rules.
	 * The rules state that username and password are required,
	 * and password needs to be authenticated.
	 */
	public function rules()
	{
		return array(
			// username and password are required
			array('email, groupName', 'required','message'=>'Field cannot be blank!'),
		);
	}

	/**
	 * Declares attribute labels.
	 */
	public function attributeLabels()
	{
		return array(
			'email'=>Yii::t('general', 'E-Mail'),
			'groupName'=>Yii::t('general', 'Group Name'),
		);
	}
}
