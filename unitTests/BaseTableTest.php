<?php
//require_once('..\includes\config.php');
require_once('..\classes\tables\BaseTable.php');
require_once('..\classes\MySQLOperator.php');

require_once('..\classes\tables\RatingPlugin.php');

class BaseTableTest extends PHPUnit_Framework_TestCase {
    
    private $baseTable;
    private $dbc;
	
	public function setUp()
    {
		$this->dbc = new MySQLOperator("localhost","root","","traceper");
    	$this->baseTable = new BaseTable("traceper_upload_rating", $this->dbc);
    }

    public function tearDown()
    {
        unset($this->baseTable);
    }	
	
	public function testUpdate() 
	{
		$updateArray = array(RatingPlugin::field_voting_count => RatingPlugin::field_voting_count. "+1", RatingPlugin::field_points => RatingPlugin::field_points ."+10");
		$condArr = array(RatingPlugin::field_upload_id => 1);	

		$this->assertEquals(1,$this->baseTable->update($updateArray, $condArr));
	}	
    
    public function testInsert() 
	{
		$fieldsArray = array(RatingPlugin::field_upload_id => 2, RatingPlugin::field_points => 100);

		$this->assertTrue($this->baseTable->insert($fieldsArray));
	}
	
	public function testSelect() 
	{
		$fieldsArray = array(RatingPlugin::field_points);
		$condArr = array(RatingPlugin::field_upload_id => 2);	

		$this->assertGreaterThan(0, $this->baseTable->select($fieldsArray, $condArr), 'No rows could be selected!');
	}	
	
	public function testDelete() 
	{
		$condArr = array(RatingPlugin::field_upload_id => 2);	

		$this->assertTrue($this->baseTable->delete($condArr));
	}	
}
?>