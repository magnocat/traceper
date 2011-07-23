<?php


require_once 'BaseTable.php';

class Rating extends BaseTable {

   function __construct() {
       parent::__construct(self::table_prefix.'_'.self::table_name);
   }	
	
	const table_prefix = 'traceper';
	const table_name = 'upload_rating';
	const field_upload_id = 'upload_id';
	const field_voting_count = 'voting_count';
	const field_points = 'points';	
}

$arr = array(Rating::field_voting_count => Rating::field_voting_count. "+1", Rating::field_points => Rating::field_points ."+23");

$condArr = array(Rating::field_upload_id => "1");

$rt = new Rating();

$rt->update($arr, $condArr);
//$rt->update($arr);

//Rating::update($arr);

