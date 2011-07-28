<?php
require_once('..\includes\config.php');
require_once('..\classes\tables\BaseTable.php');
require_once('..\classes\MySQLOperator.php');

class BaseTableTest extends PHPUnit_Framework_TestCase {
    
    private $baseTable;
    private $dbc;
	
	public function setUp()
    {
		$this->dbc = new MySQLOperator($dbHost,$dbUsername,$dbPassword,$dbName);
    	$this->baseTable = new BaseTable("traceper_upload_rating", $dbc);
    }

    public function tearDown()
    {
        unset($this->baseTable);
    }	
	
	public function testInsert() 
	{
		$fieldsArray = array(Rating::field_points);
		$values = array(30);	

		$this->assertTrue($this->baseTable->insert($fieldsArray, $values));
	}
}
?>