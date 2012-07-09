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
		$type = GroupType::FriendGroup;
		$ownerId = "1234";
		$description = "Testing SaveGroup() in PrivacyGroups.php";
		
		PrivacyGroups::model()->deleteAll();
		$this->assertTrue(PrivacyGroups::model()->saveGroup($name, $type, $ownerId, $description));
	
		$rows = PrivacyGroups::model()->findAll("name=:name AND owner=:owner AND type=:type", array(":name"=>$name, "owner"=>$ownerId, "type"=>$type));
	
		$this->assertEquals($rows[0]->name, $name);
		$this->assertEquals($rows[0]->type, $type);
		$this->assertEquals($rows[0]->owner, $ownerId);
		$this->assertEquals($rows[0]->description, $description);
	
		try {
			//try to register same owner and group name, it should throw exception
			PrivacyGroups::model()->saveGroup($name, $type, $ownerId, "deneme traceper");
			$this->assertTrue(false);
		}
		catch (CDbException $exp){
			$this->assertTrue(true);
		}
	}
	
			
}