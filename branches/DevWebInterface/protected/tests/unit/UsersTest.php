<?php


require_once("bootstrap.php");

class UsersTest extends CDbTestCase
{
	
 	public $fixtures=array( 			
 			'users'=>'Users',
 			);
	
	
	public function testTableName()
	{
	    $this->assertEquals(Users::model()->tableName(),'traceper_users');  	    
	}
	
	public function testSaveUser() {
		$email = "deneme@denem.com";
		$password = "1231231";
		$realname = "traceper deneme";
		$this->assertTrue(Users::model()->saveUser($email, $password, $realname));
		
		$rows = Users::model()->findAll("email=:email", array(":email"=>$email));
		
		$this->assertEquals(count($rows), 1);
		
		try {
			//try to register same e-mail address, it should throw exception
			Users::model()->saveUser($email, "1232424", "deneme traceper");
			$this->assertTrue(false);
		}
		catch (CDbException $exp){
			$this->assertTrue(true);
			
		}
		//try to register with missing parameters 
		$this->assertFalse(Users::model()->saveUser("dene@deneme.com", "", ""));
		$this->assertFalse(Users::model()->saveUser("", "1232424", ""));
		$this->assertFalse(Users::model()->saveUser("", "", ""));
		$this->assertFalse(Users::model()->saveUser("dene@deneme.com", "1232424", ""));
		$this->assertFalse(Users::model()->saveUser("", "1232424", "deneme traceper"));
		$this->assertFalse(Users::model()->saveUser("dene@deneme.com", "", "deneme traceper"));
		$this->assertFalse(Users::model()->saveUser("", "", "deneme traceper"));
		
		//try to register with a wrong formatted email address
		$this->assertFalse(Users::model()->saveUser("denedeneme.com", "1232424", "deneme traceper"));
	}
	
	
	public function testUpdateLocation(){
		
		$this->assertTrue($this->users("user1")->save());
		
		$rows = Users::model()->findAll("email=:email", array(":email"=>$this->users("user1")->email));
		
		$this->assertEquals(count($rows), 1);
		
		$latitude = 12.123455;
		$longitude = 123.345566;
		$altitude = 12313;
		$deviceId = 3342232;
		$calculatedTime = "2012-12-02 12:01:01";
		$userId = $rows[0]['Id'];
		
		$effectedRows = Users::model()->updateLocation($latitude, $longitude, $altitude, $deviceId, $calculatedTime, $userId);
		
		$this->assertEquals($effectedRows, 1);
		
		$result = Users::model()->findByPk($rows[0]['Id']);
		
		//testing if it is saved accurately
		$this->assertEquals($latitude, $result->latitude);
		$this->assertEquals($longitude, $result->longitude);
		$this->assertEquals($altitude, $result->altitude);
		$this->assertEquals($deviceId, $result->deviceId);
		$this->assertEquals($calculatedTime, $result->dataCalculatedTime);
		
		
		$latitude = -89.123433;
		$longitude = -179.123233;
		
		$effectedRows = Users::model()->updateLocation($latitude, $longitude, $altitude, $deviceId, $calculatedTime, $userId);
		
		$result = Users::model()->findByPk($rows[0]['Id']);
		
		//testing if it is saved accurately
		$this->assertEquals($latitude, $result->latitude);
		$this->assertEquals($longitude, $result->longitude);
		$this->assertEquals($altitude, $result->altitude);
		$this->assertEquals($deviceId, $result->deviceId);
		$this->assertEquals($calculatedTime, $result->dataCalculatedTime);
	}
	
	public function testChangePassword()
	{
		//register user for testing...
		$this->assertTrue($this->users("user1")->save());
		$password = rand(1232323, 989899999);
		$this->assertTrue(Users::model()->changePassword($this->users("user1")->Id, $password));

		// try to login with new password to check if it is changed correctly...
		$identity=new UserIdentity($this->users("user1")->email, $password);
		$this->assertEquals($identity->authenticate(), CUserIdentity::ERROR_NONE);
	}
	
	
}