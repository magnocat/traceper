<?php

require_once("../bootstrap.php");

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
		$email = "test@test.com";
		$password = "1231231";
		$realname = "test";
		$userType = UserType::RealUser;
		$accountType = 0;
		$this->assertTrue(Users::model()->saveUser($email, $password, $realname, $userType, $accountType));
		
		$rows = Users::model()->findAll("email=:email", array(":email"=>$email));
		
		$this->assertEquals(count($rows), 1);
		$this->assertEquals($rows[0]->email, $email);
		$this->assertEquals($rows[0]->password, $password);
		$this->assertEquals($rows[0]->realname, $realname);
		$this->assertEquals($rows[0]->userType, $userType);
		$this->assertEquals($rows[0]->account_type, $accountType);
		
		
		try {
			//try to register same e-mail address, it should throw exception
			Users::model()->saveUser($email, "1232424", "deneme traceper", UserType::GPSStaff, 0);
			$this->assertTrue(false);
		}
		catch (CDbException $exp){
			$this->assertTrue(true);
		}
		//try to register with missing parameters 
		$this->assertFalse(Users::model()->saveUser("dene@deneme.com", "", "", "", ""));
		$this->assertFalse(Users::model()->saveUser("", "1232424", "", "", ""));
		$this->assertFalse(Users::model()->saveUser("", "", "", "", ""));
		$this->assertFalse(Users::model()->saveUser("dene@deneme.com", "1232424", "", "", ""));
		$this->assertFalse(Users::model()->saveUser("", "1232424", "deneme traceper", "", ""));
		$this->assertFalse(Users::model()->saveUser("dene@deneme.com", "", "deneme traceper", "", ""));
		$this->assertFalse(Users::model()->saveUser("", "", "deneme traceper", "", ""));
		$this->assertFalse(Users::model()->saveUser("", "", "", UserType::GPSDevice, ""));
		$this->assertFalse(Users::model()->saveUser("", "", "", "", 1));
		
		//try to register with a wrong formatted email address
		$this->assertFalse(Users::model()->saveUser("denedeneme.com", "1232424", "deneme traceper", UserType::RealStaff, 0));

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
	
	public function testGetUserIdReturnsInteger()
	{
		$this->assertTrue($this->users("user1")->save());
		
		//Check whether the method returns an integer value when the queried email exits in DB
		$this->assertInternalType("integer", Users::model()->getUserId($this->users("user1")->email));
	}
	
	public function testGetUserIdReturnsNullForInvalidEmail()
	{
		$this->assertTrue($this->users("user1")->save());
		$this->assertTrue($this->users("user2")->save());

		//Check whether the method returns null value, when the queried email does not exist in DB
		$this->assertNull(Users::model()->getUserId("invalidEmail"));
	}

	public function testGetUserIdReturnsTrueIdForGivenEmail()
	{
		$this->assertTrue($this->users("user1")->save());
		$this->assertTrue($this->users("user2")->save());

		//Check whether the method returns the true Id for the given e-mail
		$this->assertEquals($this->users("user1")->Id, Users::model()->getUserId($this->users("user1")->email));
		$this->assertEquals($this->users("user2")->Id, Users::model()->getUserId($this->users("user2")->email));
	}	

	public function testDeleteUserReturnsNullForNonExistingId()
	{
		$this->assertTrue($this->users("user1")->save()); //Id:1
		$this->assertTrue($this->users("user2")->save()); //Id:2
	
		//Check whether the method returns null value, when the queried Id does not exist in DB
		$this->assertNull(Users::model()->deleteUser("1231310"));
		$this->assertNull(Users::model()->deleteUser("12323"));
	}

	public function testDeleteUser()
	{
		$this->assertTrue($this->users("user1")->save()); //Id:1
		$this->assertTrue($this->users("user2")->save()); //Id:2
	
		//Check whether the method returns true, when the queried Id exists in DB
		$this->assertTrue(Users::model()->deleteUser($this->users("user1")->Id));
		$this->assertTrue(Users::model()->deleteUser($this->users("user2")->Id));
		//TODO: how we can be sure that whether these users are deleted.
	}	
	
	
	public function testSaveFacebookUser() {
		$email = "test@test.com";
		$password = "1231231";
		$realname = "test";
		$fbId = "1212121";
		$accountType = 0;
		$this->assertTrue(Users::model()->saveFacebookUser($email, $password, $realname, $fbId, $accountType));
	
		$rows = Users::model()->findAll("email=:email", array(":email"=>$email));
	
		$this->assertEquals(count($rows), 1);
		$this->assertEquals($rows[0]->email, $email);
		$this->assertEquals($rows[0]->password, $password);
		$this->assertEquals($rows[0]->realname, $realname);
		$this->assertEquals($rows[0]->fb_id, $fbId);
		$this->assertEquals($rows[0]->account_type, $accountType);
	
	
		try {
			//try to register same facaebook id and same email address
			$this->assertFalse(Users::model()->saveFacebookUser($email, "1232424", "deneme traceper", $fbId, 0));
			$this->assertTrue(false);
		}
		catch (CDbException $exp){
			$this->assertTrue(true);
		}
		
		//try to register same facaebook id differen mail address
		$this->assertFalse(Users::model()->saveFacebookUser("qerqr@safdsf.com", "1232424", "deneme traceper", $fbId, 0));
	
	}
	//TODO: testSaveGPSUser and testSaveGPUser functions should be written
	public function testSaveFacebookUserMissingParams(){
		//try to register with missing parameters
		$this->assertFalse(Users::model()->saveFacebookUser("", "1232424", "deneme traceper", 123123189, 0));
		$this->assertFalse(Users::model()->saveFacebookUser("asdf@safdsf.com", "", "deneme traceper", 123123189, 0));
		$this->assertFalse(Users::model()->saveFacebookUser("asdf@safdsf.com", "1232424", "", 123123189, 0));
		$this->assertFalse(Users::model()->saveFacebookUser("asdf@safdsf.com", "1232424", "deneme traceper", null, 0));
		$this->assertFalse(Users::model()->saveFacebookUser("", "", "deneme traceper", 123123189, 0));
		$this->assertFalse(Users::model()->saveFacebookUser("asdf@safdsf.com", "1232424", "deneme traceper", "", 0));
		
		//try to register with a wrong formatted email address
		$this->assertFalse(Users::model()->saveFacebookUser("asd534fsafdsf.com", "1232424", "deneme traceper", 123123189, 0));
	}
	
	
	public function testGetListDataProvider(){
		
		$id = array(1,3,5,7,8,15,18);
		
		$dataProvider = Users::model()->getListDataProvider(implode(",", $id));
		
		$this->assertEquals($dataProvider->getTotalItemCount(), count($id));
		
		$rows = $dataProvider->getData();
		for ($i = 0; $i < count($id); $i++) {
			$this->assertEquals($rows[$i]['id'], $id[$i]);
		}
	}
	
	public function testGetListDataProvider_userType() {
		$id = array(1,3,5,7,8,15,18);
		
		$userType = array(UserType::RealUser);
		$dataProvider = Users::model()->getListDataProvider(implode(",", $id), $userType);
		
		$rows = $dataProvider->getData();
		for ($i = 0; $i < count($rows); $i++) {
			$this->assertEquals($rows[$i]['userType'], UserType::RealUser);
		}
		
		$userType = array(UserType::GPSDevice);
		$dataProvider = Users::model()->getListDataProvider(implode(",", $id), $userType);
		
		$rows = $dataProvider->getData();
		for ($i = 0; $i < count($rows); $i++) {
			$this->assertEquals($rows[$i]['userType'], UserType::GPSDevice);
		}
		
		$userType = array(UserType::GPSDevice, UserType::RealUser);
		$dataProvider = Users::model()->getListDataProvider(implode(",", $id), $userType);
		
		$rows = $dataProvider->getData();
		for ($i = 0; $i < count($rows); $i++) {
			if ($rows[$i]['userType'] == UserType::GPSDevice ||
				$rows[$i]['userType'] == UserType::RealUser) {
				$this->assertTrue(true);
			}
			else {
				$this->assertTrue(false);
			}
		}
		
	}
	
	
	public function testGetListDataProvider_time() {
		$id = array(1,3,5,7,8,15,18);
		$time = time();
		$date = date('Y-m-d H:i:s', $time);
 		$idList = implode(',', $id);
		$limit = 5;
 		
		$sql = 'UPDATE ' .Users::model()->tableName() . '
				SET dataArrivedTime = "'. $date.'" 
				WHERE Id in ('.$idList.')
				LIMIT '. $limit;

		$effectedRows = Yii::app()->db->createCommand($sql)->execute();
		
		$time = $time-10;
		$dataProvider = Users::model()->getListDataProvider(implode(",", $id), null, $time);
		
		
		$this->assertEquals($dataProvider->getTotalItemCount(), $limit); 
	}
	
	public function testSetUserPositionPublicity()
	{
		$this->assertTrue($this->users("user1")->save());
		$this->assertTrue($this->users("user2")->save());
		
		Users::model()->setUserPositionPublicity($this->users("user1")->Id, false);
		$user=Users::model()->findByPk($this->users("user1")->Id);
		$this->assertEquals($user->publicPosition, 0);
	
		Users::model()->setUserPositionPublicity($this->users("user2")->Id, true);
		$user=Users::model()->findByPk($this->users("user2")->Id);
		$this->assertEquals($user->publicPosition, 1);
	}
	
	public function testIsUserPositionPublic()
	{
		$this->assertTrue($this->users("user1")->save());
		$this->assertTrue($this->users("user2")->save());

		$this->assertTrue(Users::model()->isUserPositionPublic($this->users("user1")->Id));
		$this->assertFalse(Users::model()->isUserPositionPublic($this->users("user2")->Id));		
		
		Users::model()->setUserPositionPublicity($this->users("user1")->Id, false);
		$this->assertFalse(Users::model()->isUserPositionPublic($this->users("user1")->Id));
		
		Users::model()->setUserPositionPublicity($this->users("user2")->Id, true);
		$this->assertTrue(Users::model()->isUserPositionPublic($this->users("user2")->Id));
	}

	public function testSetAuthorityLevel()
	{
		$this->assertTrue($this->users("user1")->save());
		$this->assertTrue($this->users("user2")->save());
	
		Users::model()->setAuthorityLevel($this->users("user1")->Id, AuthorityLevel::AuthorizedUser);
		$user=Users::model()->findByPk($this->users("user1")->Id);
		$this->assertEquals($user->authorityLevel, AuthorityLevel::AuthorizedUser);
	
		Users::model()->setAuthorityLevel($this->users("user2")->Id, AuthorityLevel::SuperUser);
		$user=Users::model()->findByPk($this->users("user2")->Id);
		$this->assertEquals($user->authorityLevel, AuthorityLevel::SuperUser);
	}

	public function testGetAuthorityLevel()
	{
		$this->assertTrue($this->users("user1")->save());
		$this->assertTrue($this->users("user2")->save());
	
		$this->assertEquals(Users::model()->getAuthorityLevel($this->users("user1")->Id), AuthorityLevel::UnauthorizedUser);
		$this->assertEquals(Users::model()->getAuthorityLevel($this->users("user2")->Id), AuthorityLevel::StandardUser);
	
		Users::model()->setAuthorityLevel($this->users("user1")->Id, AuthorityLevel::StandardUser);
		$this->assertEquals(Users::model()->getAuthorityLevel($this->users("user1")->Id), AuthorityLevel::StandardUser);
	
		Users::model()->setAuthorityLevel($this->users("user2")->Id, AuthorityLevel::UnauthorizedUser);
		$this->assertEquals(Users::model()->getAuthorityLevel($this->users("user2")->Id), AuthorityLevel::UnauthorizedUser);
	}	
		
	//TODO: Problem in offset and limit parameters
// 	public function testGetListDataProvider_offset_limit(){
// 		$id = array(1,3,5,7,8,15,18);
// 		$idList = implode(',', $id);
// 		$limit = 5;
// 		$dataProvider = Users::model()->getListDataProvider(implode(",", $id), null, null, null, null);
// 		$rows = $dataProvider->getData();
// 		$this->assertEquals(count($rows), $limit);
// 	}
	
	
	
}