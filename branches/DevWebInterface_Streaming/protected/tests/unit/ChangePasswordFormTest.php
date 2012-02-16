<?php

require_once('..\bootstrap.php');

class ChangePasswordFormTest extends CTestCase
{
    public $fixtures=array(
        'users'=>'Users',
    );
   
	public function testCheckCurrentPassword()
	{
	    $model = new ChangePasswordForm;
	    $model->currentPassword = 12345;
	    
	    $userIdentity = new UserIdentity('test@traceper.com',md5(12345));
		$duration=0;
		//Yii::app()->user->login($userIdentity, $duration);

		Yii::app()->user->id = 1;
		
		$this->assertTrue($model->checkCurrentPassword('arg1', 'arg2'));
		//Yii::app()->user->logout();
	}    
}