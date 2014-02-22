<?php

/**
 * UserIdentity represents the data needed to identity a user.
 * It contains the authentication method that checks if the provided
 * data can identity the user.
 */
class UserIdentity extends CUserIdentity
{
	const ERROR_REGISTRATION_UNCOMPLETED=10;
	
	private $realname;
	private $userId;

	/**
	 * Authenticates a user.
	 * The example implementation makes sure if the username and password
	 * are both 'demo'.
	 * In practical applications, this should be changed to authenticate
	 * against some persistent user identity storage (e.g. database).
	 * @return boolean whether authentication succeeds.
	 */
	public function authenticate()
	{
		$criteria=new CDbCriteria;
		$criteria->select='Id,realname,password,fb_id';  
		$criteria->condition='email=:email';
		$criteria->params=array(':email'=>$this->username);
		$user = Users::model()->find($criteria); // $params is not needed
		
		if ($user->password == md5($this->password)) {
			$this->errorCode = self::ERROR_NONE;
			$this->realname = $user->realname;
			$this->userId = $user->Id;
			
			$this->setState('fb_id', $user->fb_id);
		}
		else {
			$this->errorCode = self::ERROR_PASSWORD_INVALID;				
		}
		
		return $this->errorCode;
	}
	
	public function directAuthenticate()
	{
		$criteria=new CDbCriteria;
		$criteria->select='Id,realname,password,fb_id';
		$criteria->condition='email=:email';
		$criteria->params=array(':email'=>$this->username);
		$user = Users::model()->find($criteria); // $params is not needed
		
		$result = false;
		
		if($user != null)
		{
			$this->realname = $user->realname;
			$this->userId = $user->Id;
			
			$this->setState('fb_id', $user->fb_id);

			$result = true;
		}
		
		return $result;
	}	
	
	public function getName() {
		return $this->realname;
	}
	
	public function getId(){
		return $this->userId;
	}
}