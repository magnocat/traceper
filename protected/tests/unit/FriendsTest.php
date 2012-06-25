<?php


require_once("bootstrap.php");

class FriendssTest extends CDbTestCase
{
	
 	public $fixtures=array( 
 			'users'=>'Users',
 			//'friends'=>'Friends',
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
		
		$rows = Friends::model()->findAll(array('condition'=>'(friend1=:friend1 AND friend2=:friend2) OR (friend1=:friend2 AND friend2=:friend1)',
				                                'params'=>array(':friend1'=>$friend1Id, ':friend1'=>$friend2Id)
		                                       )
		                                 );
		
		$this->assertEquals(count($rows), 1);
		$this->assertEquals($rows[0]->friend1Visibility, 1);
		$this->assertEquals($rows[0]->friend2Visibility, 1);
		$this->assertEquals($rows[0]->status, 1);
	}

	
	
}