<?php

require_once("../bootstrap.php");

class UserPrivacyGroupRelationTest extends CDbTestCase
{
	
 	public $fixtures=array( 			
 			'users'=>'Users',
 			'privacyGroups'=>'PrivacyGroups',
 			'userPrivacyGroupRelation'=>'UserPrivacyGroupRelation',
 			);
	
	
	public function testTableName()
	{
	    $this->assertEquals(UserPrivacyGroupRelation::model()->tableName(),'traceper_user_privacy_group_relation');  	    
	}
	
	
	public function testSaveGroupRelation() {
		
		//save user for testing...
		$this->assertTrue($this->users("user_privacy_group_test")->save());
		//save friend for testing...
		$this->assertTrue($this->users("user2")->save());
		//save groups for testing...
		$this->assertTrue($this->privacyGroups("user_privacy_group_test")->save());
		
		UserPrivacyGroupRelation::model()->deleteAll();
		$this->assertTrue(UserPrivacyGroupRelation::model()->saveGroupRelation($this->users("user2")->Id, $this->privacyGroups("user_privacy_group_test")->id, $this->users("user_privacy_group_test")->Id));
	
		
		$rows = UserPrivacyGroupRelation::model()->findAll("groupOwner=:groupOwner", array(":groupOwner"=>$this->users("user_privacy_group_test")->Id));
	
		$this->assertEquals($rows[0]->userId, $this->users("user2")->Id);
		$this->assertEquals($rows[0]->groupId, $this->privacyGroups("user_privacy_group_test")->id);
		$this->assertEquals($rows[0]->groupOwner, $this->users("user_privacy_group_test")->Id);
		
		try {
			//try to register same parameters, it should throw exception
			$this->assertTrue(UserPrivacyGroupRelation::model()->saveGroupRelation($this->users("user2")->Id, $this->privacyGroups("user_privacy_group_test")->id, $this->users("user_privacy_group_test")->Id));
			$this->assertTrue(false);
		}
		catch (CDbException $exp){
			$this->assertTrue(true);
		}
	}
	
	
	public function testGetGroupMembersCount()
	{
		//save userPrivacyGroupRelation for testing...
		$this->assertTrue($this->userPrivacyGroupRelation("user_privacy_group_relation1")->save());
		$this->assertTrue($this->userPrivacyGroupRelation("user_privacy_group_relation2")->save());
		$this->assertTrue($this->userPrivacyGroupRelation("user_privacy_group_relation3")->save());
	
		$this->assertEquals(2, UserPrivacyGroupRelation::model()->getGroupMembersCount($this->userPrivacyGroupRelation("user_privacy_group_relation1")->groupId));
	}
	
	
	public function testDeleteGroup()
	{
		//save userPrivacyGroupRelation for testing...
		$this->assertTrue($this->userPrivacyGroupRelation("user_privacy_group_relation1")->save());
			
		UserPrivacyGroupRelation::model()->deleteGroup($this->userPrivacyGroupRelation("user_privacy_group_relation1")->groupId);
		$rows = UserPrivacyGroupRelation::model()->findAll("groupOwner=:groupOwner", array(":groupOwner"=>$this->userPrivacyGroupRelation("user_privacy_group_relation1")->groupId));
		$this->assertEquals(0, count($rows));
	}
	
	public function testDeleteGroupMember()
	{
		//save userPrivacyGroupRelation for testing...
		$this->assertTrue($this->userPrivacyGroupRelation("user_privacy_group_relation1")->save());
		$this->assertEquals(1,UserPrivacyGroupRelation::model()->deleteGroupMember($this->userPrivacyGroupRelation("user_privacy_group_relation1")->userId,$this->userPrivacyGroupRelation("user_privacy_group_relation1")->groupId));
	
		//There is no group member whose id is 5555
		$this->assertEquals(-1,UserPrivacyGroupRelation::model()->deleteGroupMember(5555,$this->userPrivacyGroupRelation("user_privacy_group_relation1")->groupId));
	}
}