<?php

require_once("../bootstrap.php");

class InvitedUsersTest extends CDbTestCase
{
	
 	public $fixtures=array( 			
 			'users'=>'Users',
 			);
	
	
	public function testTableName()
	{
	    $this->assertEquals(InvitedUsers::model()->tableName(),'traceper_invitedusers');  	    
	}
	
	public function testSaveInvitedUsers() {
		$email = "test@test.com";
		$dt = date('Y-m-d h:i:s');
		$this->assertTrue(InvitedUsers::model()->saveInvitedUsers($email, $dt));
		
		$rows = InvitedUsers::model()->findAll("email=:email", array(":email"=>$email));
		
		$this->assertEquals(count($rows), 1);
		$this->assertEquals($rows[0]->email, $email);
		$this->assertEquals($rows[0]->dt, $dt);
		/*
		try {
			//try to register same e-mail address, it should throw exception
			InvitedUsers::model()->saveInvitedUsers($email, "1232424", "deneme traceper", $time);
			$this->assertTrue(false);
		}
		catch (CDbException $exp){
			$this->assertTrue(true);
		}
		*/
		
		//try to register with missing parameters 
		$this->assertFalse(InvitedUsers::model()->saveInvitedUsers("", $dt));
	}
}