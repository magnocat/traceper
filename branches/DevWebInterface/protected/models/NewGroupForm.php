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
	public $groupType;

	/**
	 * Declares the validation rules.
	 * The rules state that username and password are required,
	 * and password needs to be authenticated.
	 */
	public function rules()
	{
		return array(
			// username and password are required
			array('name', 'required','message'=>Yii::t('common', 'Field cannot be blank!')),
			array('groupType', 'required','message'=>Yii::t('groups', 'Please select a group type')),
			array('description', 'length', 'max'=>500),
			array('groupType', 'safe') //marks the associated attributes to be safe for massive assignments
		);
	}

	/**
	 * Declares attribute labels.
	 */
	public function attributeLabels()
	{
		return array(
			'name'=>Yii::t('groups', 'Group Name'),
			'description'=>Yii::t('groups', 'Group Description'),
			'groupType'=>Yii::t('groups', 'Group Type'),
		);
	}
}
