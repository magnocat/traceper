<?php

require_once('..\bootstrap.php');

class UsersTest extends CDbTestCase
{
	public function testTableName()
	{
	    // insert a comment in pending status
	    $users=new Users;
	
	    $this->assertEquals($users->tableName(),'traceper_users');
	}
}