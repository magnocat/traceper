<?php

/**
 * UserIdentity represents the data needed to identity a user.
 * It contains the authentication method that checks if the provided
 * data can identity the user.
 */
class UserIdentity extends CUserIdentity
{
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
		$criteria->select='Id,realname,password';  
		$criteria->condition='email=:email';
		$criteria->params=array(':email'=>$this->username);
		$user = Users::model()->find($criteria); // $params is not needed
		
		if ($user == null) {
 			if ($this->facebookId !== "0") {
				$this->errorCode = self::ERROR_UNKNOWN_IDENTITY;
 			}
 			else {
 				$this->errorCode = self::ERROR_USERNAME_INVALID;
 			}
		}
		else if ($user->password == md5($this->password)) {
			$this->errorCode = self::ERROR_NONE;
			$this->realname = $user->realname;
			$this->userId = $user->Id;
		}
		else {
			$this->errorCode = self::ERROR_PASSWORD_INVALID;				
		}
		return $this->errorCode;
	}
	
	public function getName() {
		return $this->realname;
	}
	
	public function getId(){
		return $this->userId;
	}
	
}