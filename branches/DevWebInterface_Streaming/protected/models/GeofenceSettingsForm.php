<?php

/**
 * GeofenceSettingsForm class.
 * GeofenceSettingsForm is the data structure for adjusting the geofence settings such as adding users to the viewers of geofence 
 * or deleting users from the viewers of geofence.
 */
class GeofenceSettingsForm extends CFormModel
{
	public $geofenceStatusArray;

	/**
	 * Declares the validation rules.
	 * The rules state that username and password are required,
	 * and password needs to be authenticated.
	 */
	public function rules()
	{
		return array(
			array('geofenceStatusArray', 'safe') //marks the associated attributes to be safe for massive assignments			
		);
	}

	/**
	 * Declares attribute labels.
	 */
	public function attributeLabels()
	{
		return array(
			'geofenceStatusArray'=>'Geofence Status',
		);
	}
}
