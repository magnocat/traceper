<?php

require_once("BaseTable.php");

class UploadComment extends BaseTable {

	function __construct($tablePrefix, $dbc) 
	{
	   parent::__construct($tablePrefix.'_'.self::table_name, $dbc);
    }
   	
	const table_name = 'upload_comment';
	const field_upload_id = 'upload_id';
	const field_photo_id='photo_id';
	const field_user_id = 'user_id';
	const field_comment_time = 'comment_time';
	const field_comment = 'comment';	
}
?>
