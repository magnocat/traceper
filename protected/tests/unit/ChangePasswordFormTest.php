<?php

require_once('bootstrap.php');

class ChangePasswordFormTest extends CDbTestCase
{
    public $fixtures=array(
        'users'=>'Users',
    );
   
    
	public function testRules()
	{
		$this->assertTrue($this->users("user1")->save());
		$password = rand(12323,24232323);
		
		$this->assertTrue(Users::model()->changePassword($this->users("user1")->Id, $password));
		
		$identity=new UserIdentity($this->users("user1")->email, $password);
		$this->assertEquals($identity->authenticate(), CUserIdentity::ERROR_NONE);
		
		Yii::app()->user->login($identity);		
		
		$model = new ChangePasswordForm;
		
		$newPassword = rand(19999,999999999);
		
		// //should be false because missing fields
		$this->assertFalse($model->validate());
		
		$model->currentPassword = $password;
		$this->assertFalse($model->validate());
		
		// //should be false because missing fields
		$model->newPassword = $newPassword;
		$this->assertFalse($model->validate());
		
		// //should be false because newpassword is different
		$model->newPasswordAgain = $newPassword."123123";
		$this->assertFalse($model->validate());
		
		
		$model->currentPassword = $password."12313";
		$model->newPassword = $newPassword;
		$model->newPasswordAgain = $newPassword;
		
		//should be false because current password incorrect
		$this->assertFalse($model->validate());
		
		$model->currentPassword = $password;
		
		//should be Ok
		$this->assertTrue($model->validate());
	}  
	 
}