<?php

require_once("../bootstrap.php");

class UserCandidatesTest extends CDbTestCase
{
	
 	public $fixtures=array( 			
 			'users'=>'Users',
 			);
	
	
	public function testTableName()
	{
	    $this->assertEquals(UserCandidates::model()->tableName(),'traceper_user_candidates');  	    
	}
	
	public function testSaveUserCandidates() {
		$email = "test@test.com";
		$password = "1231231";
		$realname = "test";
		$time = date('Y-m-d h:i:s');
		$this->assertTrue(UserCandidates::model()->saveUserCandidates($email, $password, $realname, $time));
		
		$rows = UserCandidates::model()->findAll("email=:email", array(":email"=>$email));
		
		$this->assertEquals(count($rows), 1);
		$this->assertEquals($rows[0]->email, $email);
		$this->assertEquals($rows[0]->password, $password);
		$this->assertEquals($rows[0]->realname, $realname);
		$this->assertEquals($rows[0]->time, $time);
		
		/*
		try {
			//try to register same e-mail address, it should throw exception
			UserCandidates::model()->saveUserCandidates($email, "1232424", "deneme traceper", $time);
			$this->assertTrue(false);
		}
		catch (CDbException $exp){
			$this->assertTrue(true);
		}
		*/
		
		//try to register with missing parameters 
		$this->assertFalse(UserCandidates::model()->saveUserCandidates("dene@deneme.com", "", "", ""));
		$this->assertFalse(UserCandidates::model()->saveUserCandidates("", "1232424", "", ""));
		$this->assertFalse(UserCandidates::model()->saveUserCandidates("", "", "", ""));
		$this->assertFalse(UserCandidates::model()->saveUserCandidates("dene@deneme.com", "1232424", "", ""));
		$this->assertFalse(UserCandidates::model()->saveUserCandidates("", "1232424", "deneme traceper", ""));
		$this->assertFalse(UserCandidates::model()->saveUserCandidates("dene@deneme.com", "", "deneme traceper", ""));
		$this->assertFalse(UserCandidates::model()->saveUserCandidates("", "", "deneme traceper", ""));
		$this->assertFalse(UserCandidates::model()->saveUserCandidates("", "", "", $time));
		
	}
}