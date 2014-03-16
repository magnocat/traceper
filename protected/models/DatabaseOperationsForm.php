<?php

class DatabaseOperationsForm extends CFormModel
{
	public $selectSql;
	public $selectAllSql;
	public $updateSql;
	public $deleteSql;

	/**
	 * Declares the validation rules.
	 * The rules state that username and password are required,
	 * and password needs to be authenticated.
	 */
	public function rules()
	{
		return array(
			//These attributes should be defined as safe in order to be usable
			array('selectSql, selectAllSql, updateSql, deleteSql', 'safe')				
		);
	}

	/**
	 * Declares attribute labels.
	 */
	public function attributeLabels()
	{
		return array();
	}
}

?>