<?php

/**
 * NewGroupForm class.
 * NewGroupForm is the data structure for keeping
 * the new groups form data. It is used by the 'createGroup' action of 'GroupsController'.
 */
class NewGroupForm extends CFormModel
{
	public $name;
	public $description;

	/**
	 * Declares the validation rules.
	 * The rules state that username and password are required,
	 * and password needs to be authenticated.
	 */
	public function rules()
	{
		return array(
			// username and password are required
			array('name', 'required','message'=>'Field cannot be blank!'),
			array('description', 'length', 'max'=>500),
		);
	}

	/**
	 * Declares attribute labels.
	 */
	public function attributeLabels()
	{
		return array(
			'name'=>Yii::t('general', 'Group Name'),
			'description'=>Yii::t('general', 'Group Description'),
		);
	}
}
