<?php

require_once 'BaseTable.php';

class Rating extends BaseTable {

	function __construct($tablePrefix, $dbc) 
	{
	   parent::__construct($tablePrefix.'_'.self::table_name, $dbc);
    }
   	
	const table_name = 'upload_rating';
	const field_upload_id = 'upload_id';
	const field_voting_count = 'voting_count';
	const field_points = 'points';	
}

?>
