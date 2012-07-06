<?php

require_once("../bootstrap.php");

class PrivacyGroupsTest extends CDbTestCase
{
	
 	public $fixtures=array( 			
 			'users'=>'Users',
 			);
	
	
	public function testTableName()
	{
	    $this->assertEquals(PrivacyGroups::model()->tableName(),'traceper_privacy_groups');  	    
	}
	
	public function testSaveGroup() {
		$name = "testSaveGroup";
		$id = "1234";
		$description = "Testing SaveGroup() in PrivacyGroups.php";
		
		$this->assertTrue(PrivacyGroups::model()->saveGroup($name, $id, $description));
	
		$rows = PrivacyGroups::model()->findAll("name=:name", array(":name"=>$name));
	
		$this->assertEquals($rows[0]->name, $name);
		$this->assertEquals($rows[0]->owner, $id);
		$this->assertEquals($rows[0]->description, $description);
	
		try {
			//try to register same owner and group name, it should throw exception
			PrivacyGroups::model()->saveGroup($name, $id, "deneme traceper");
			$this->assertTrue(false);
		}
		catch (CDbException $exp){
			$this->assertTrue(true);
		}
	}
	
			
}