<?php

require_once("../bootstrap.php");

class UserWasHereTest extends CDbTestCase
{
	
 	public $fixtures=array( 			
 			'users'=>'Users',
 			);
	
	
	public function testTableName()
	{
	    $this->assertEquals(UserWasHere::model()->tableName(),'traceper_user_was_here');  	    
	}
	

	public function testLogLocation()
	{
		
		$latitude = 12.123455;
		$longitude = 123.345566;
		$altitude = 12313;
		$deviceId = 3342232;
		$calculatedTime = "2012-12-02 12:01:01";
		
		$result = UserWasHere::model()->logLocation($this->users("user1")->Id, $latitude, $longitude, $altitude, $deviceId, $calculatedTime);
		$this->assertTrue($result);
		
		$rows = UserWasHere::model()->findAll(array(
    								 'select'=>'*',
									 'condition'=>'userId=:userId',
									 'params'=>array(':userId'=>$this->users("user1")->Id),
									 'order'=>'Id DESC;'
									));
		
		$this->assertEquals($rows[0]['latitude'], $latitude);
		$this->assertEquals($rows[0]['longitude'], $longitude);
		$this->assertEquals($rows[0]['altitude'], $altitude);
		$this->assertEquals($rows[0]['deviceId'], $deviceId);
		$this->assertEquals($rows[0]['dataCalculatedTime'], $calculatedTime);
		$this->assertEquals($rows[0]['dataArrivedTime'], date("Y-m-d H:i:s"));
	}

	public function testLogFunctionUnknownUserId(){
		$latitude = 12.123455;
		$longitude = 123.345566;
		$altitude = 12313;
		$deviceId = 3342232;
		$calculatedTime = "2012-12-02 12:01:01";
		
		try {
			// it should throw exception
			$result = UserWasHere::model()->logLocation(98989898, $latitude, $longitude, $altitude, $deviceId, $calculatedTime);
			$this->assertTrue(false);
		}
		catch (CDbException $exp){
			$this->assertTrue(true);
		}
		$rows = UserWasHere::model()->findAll(array(
				'select'=>'*',
				'condition'=>'userId=:userId',
				'params'=>array(':userId'=>$this->users("user1")->Id),
				'order'=>'Id DESC;'
		));
	}
}