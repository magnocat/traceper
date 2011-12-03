<?php

class SearchForm extends CFormModel
{
	public $keyword;
	
	/**
	 * Declares the validation rules.
	 * The rules state that username and password are required,
	 * and password needs to be authenticated.
	 */
	public function rules()
	{
		return array(
			array('keyword', 'required','message'=>'Field cannot be blank!'),
	//		array('keyword', 'length', 'min'=> 3),
		);
	}
}
?>
