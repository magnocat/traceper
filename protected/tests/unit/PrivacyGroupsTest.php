<?php

require_once("../bootstrap.php");

class PrivacyGroupsTest extends CDbTestCase
{
	
 	public $fixtures=array( 			
 			'privacyGroups'=>'PrivacyGroups',
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
	
	
	public function testGetGroupsList()
	{
		//save groups for testing...
		$this->assertTrue($this->privacyGroups("privacyGroup1")->save());
		
		$dataProvider = PrivacyGroups::model()->getGroupsList($this->privacyGroups("privacyGroup1")->owner, $this->privacyGroups("privacyGroup1")->type, 1);
		$result=$dataProvider->getData();
		$this->assertEquals($result[0]->name, $this->privacyGroups("privacyGroup1")->name);
		
		$this->assertTrue($this->privacyGroups("privacyGroup2")->save());
		$dataProvider = PrivacyGroups::model()->getGroupsList($this->privacyGroups("privacyGroup2")->owner, $this->privacyGroups("privacyGroup2")->type, 1);
		$result=$dataProvider->getData();
		$this->assertEquals($result[0]->name, $this->privacyGroups("privacyGroup2")->name);
	}
	
	public function testDeleteGroup()
	{
		//save groups for testing...
		$this->assertTrue($this->privacyGroups("privacyGroup3")->save());	
		$this->assertEquals(1,PrivacyGroups::model()->deleteGroup($this->privacyGroups("privacyGroup3")->id,$this->privacyGroups("privacyGroup3")->owner));
		
		//There is no group whose id is 5555
		$this->assertEquals(-1,PrivacyGroups::model()->deleteGroup(5555,$this->privacyGroups("privacyGroup3")->owner));
	}
	
	
	public function testUpdatePrivacySettings()
	{
		$allowToSeeMyPosition = 0;
		//save groups for testing...
		$this->assertTrue($this->privacyGroups("privacyGroup3")->save());
		
		$this->assertEquals(1,PrivacyGroups::model()->updatePrivacySettings($this->privacyGroups("privacyGroup3")->id,$allowToSeeMyPosition));
		
		$rows = PrivacyGroups::model()->findAll("name=:name AND owner=:owner AND type=:type", array(":name"=>$this->privacyGroups("privacyGroup3")->name, "owner"=>$this->privacyGroups("privacyGroup3")->owner, "type"=>$this->privacyGroups("privacyGroup3")->type));
		$this->assertEquals($allowToSeeMyPosition, $rows[0]->allowedToSeeOwnersPosition);
	}
	
}