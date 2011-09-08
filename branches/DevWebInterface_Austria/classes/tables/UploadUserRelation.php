<?php

require_once 'BaseTable.php';

class UploadUserRelation extends BaseTable {

	function __construct($tablePrefix, $dbc) 
	{
	   parent::__construct($tablePrefix.'_'.self::table_name, $dbc);
    }
   	
	const table_name = 'upload_user_relation';
	const field_id='Id';
	const field_upload_id = 'upload_id';	
	const field_user_id = 'user_id';
}
?>
