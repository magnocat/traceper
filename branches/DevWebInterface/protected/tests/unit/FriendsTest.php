<?php

require_once("../bootstrap.php");

class FriendsTest extends CDbTestCase
{
	
 	public $fixtures=array( 
 			'friends'=>'Friends', //For empty friends table
 			'users'=>'Users', 			
 			);
	
	
	public function testTableName()
	{
	    $this->assertEquals(Friends::model()->tableName(),'traceper_friends');  	    
	}
	
	public function testMakeFriends() {
		$this->assertTrue($this->users("user1")->save());
		$this->assertTrue($this->users("user2")->save());
		
		$friend1Id = Users::model()->getUserId($this->users("user1")->email);
		$friend2Id = Users::model()->getUserId($this->users("user2")->email);

		$this->assertTrue(Friends::model()->makeFriends($friend1Id, $friend2Id));
		
		$rows = Friends::model()->findAll('(friend1=:friend1 AND friend2=:friend2) OR (friend1=:friend2 AND friend2=:friend1)', array(':friend1'=>$friend1Id, ':friend2'=>$friend2Id));
		
		$this->assertEquals(count($rows), 1);
		$this->assertEquals($rows[0]->friend1Visibility, 1);
		$this->assertEquals($rows[0]->friend2Visibility, 1);
		$this->assertEquals($rows[0]->status, 1);
	}
	
	public function testDeleteFriendship(){
		$this->assertTrue(Friends::model()->deleteFriendShip($this->friends['friendship2']['Id'], $this->friends['friendship2']['friend1']));
		
		$this->assertFalse(Friends::model()->deleteFriendShip($this->friends['friendship2']['Id'], $this->friends['friendship2']['friend1']));
	}
	
	public function testGetFriendRequestDataProvider(){
		
		$dataProvider = Friends::model()->getFriendRequestDataProvider(6, 5);
		$rows = $dataProvider->getData();
		
		for ($i = 0; $i < count($rows); $i++) {
			$this->assertEquals($rows[$i]['status'], 0);
		}
		
	}
	
	public function testApproveFrienship(){
		$dataProvider = Friends::model()->getFriendRequestDataProvider(6, 5);
		$rows = $dataProvider->getData();
		
		for ($i = 0; $i < count($rows); $i++) 
		{
			  $this->assertTrue(Friends::model()->approveFriendship($rows[$i]['friendShipId'], 6));
		}
	}

	
	
}