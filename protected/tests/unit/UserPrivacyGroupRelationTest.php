<?php

require_once("../bootstrap.php");

class UserPrivacyGroupRelationTest extends CDbTestCase
{
	
 	public $fixtures=array( 			
 			'users'=>'Users',
 			);
	
	
	public function testTableName()
	{
	    $this->assertEquals(UserPrivacyGroupRelation::model()->tableName(),'traceper_user_privacy_group_relation');  	    
	}
	
	/*
	public function testSaveGroupRelation() {
		$friendId = "5";
		$selectedFriendGroup = "123";
		$groupOwnerId = "6";
		
		$this->assertTrue(UserPrivacyGroupRelation::model()->saveGroupRelation($friendId, $selectedFriendGroup, $groupOwnerId));
	
		$rows = UserPrivacyGroupRelation::model()->findAll("groupOwner=:groupOwner", array(":groupOwner"=>$groupOwnerId));
	
		$this->assertEquals($rows[0]->userId, $friendId);
		$this->assertEquals($rows[0]->groupId, $selectedFriendGroup);
		$this->assertEquals($rows[0]->groupOwner, $groupOwnerId);
	
		/*
		try {
			//try to register same owner and group name, it should throw exception
			PrivacyGroups::model()->saveGroup($name, $id, "deneme traceper");
			$this->assertTrue(false);
		}
		catch (CDbException $exp){
			$this->assertTrue(true);
		}
		
	}*/
}