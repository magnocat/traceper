<?php
/**
 * GroupPrivacySettingsForm class.
 * GroupPrivacySettingsForm is the data structure for adjusting the group privacy settings such as users belong to the selected group 
 * are allowed to see the group owner's position.
 */
class GroupPrivacySettingsForm extends CFormModel
{
	public $allowToSeeMyPosition;

	/**
	 * Declares the validation rules.
	 * The rules state that username and password are required,
	 * and password needs to be authenticated.
	 */
	public function rules()
	{
		return array(
			array('allowToSeeMyPosition', 'boolean')			
		);
	}

	/**
	 * Declares attribute labels.
	 */
	public function attributeLabels()
	{
		return array(
			'allowToSeeMyPosition'=>Yii::t('groups', 'Allow to see my position'),
		);
	}
}