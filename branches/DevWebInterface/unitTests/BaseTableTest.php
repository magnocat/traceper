<?php
//require_once('..\includes\config.php');
require_once('..\classes\tables\BaseTable.php');
require_once('..\classes\MySQLOperator.php');

require_once('..\classes\tables\Rating.php');

class BaseTableTest extends PHPUnit_Framework_TestCase {
    
    private $baseTable;
    private $dbc;
	
	public function setUp()
    {
		$this->dbc = new MySQLOperator("localhost","root","","php");
    	$this->baseTable = new BaseTable("traceper_upload_rating", $this->dbc);
    }

    public function tearDown()
    {
        unset($this->baseTable);
    }	
	
	public function testUpdate() 
	{
		$updateArray = array(Rating::field_voting_count => Rating::field_voting_count. "+1", Rating::field_points => Rating::field_points ."+10");
		$condArr = array(Rating::field_upload_id => "20");	

		$this->assertTrue($this->baseTable->update($updateArray, $condArr));
	}	
    
    public function testInsert() 
	{
		$fieldsArray = array(Rating::field_points);
		$values = array(30);	

		$this->assertTrue($this->baseTable->insert($fieldsArray, $values));
	}
	
	public function testSelect() 
	{
		$fieldsArray = array(Rating::field_points);
		$condArr = array(Rating::field_upload_id => "2");	

		$this->assertGreaterThan(0, $this->baseTable->select($fieldsArray, $condArr), 'No rows could be selected!');
	}	
}
?>