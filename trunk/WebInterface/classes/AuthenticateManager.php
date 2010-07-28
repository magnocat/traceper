<?php
/********************************************
*
*	Filename:	Authenticator.php
*	Author:		Ahmet Oguz Mermerkaya
*	E-mail:		ahmetmermerkaya@hotmail.com
*	Begin:		Sunday, May 16, 2010  17:37
*
*********************************************/
require_once('Base.php');

class AuthenticateManager extends Base{
	
	private $tablePrefix;
	private $userId = NULL;
	//it checks the last user authentication time, if more than
	// $userCheckInterval passes, it authenticates that user again
	private $userCheckInterval = "600"; // seconds
	const username = 'authmanager_username';
	const userId = 'authmanager_userId';
	const password = 'authmanager_password';
	const authTime = 'authmanager_authTime';
	const daysDataStored = 'authmanager_daysDataStored';
	const realname = 'authmanager_realname';
		
	public function __construct($dbc, $tdo, $tablePrefix){
		$this->dbc = $dbc;
		$this->tablePrefix = $tablePrefix;
		$this->tdo = $tdo;
		if ($this->tdo != NULL){
			$this->userId = $this->tdo->getValue(self::userId);
		}
	}
	
	public function authenticateUser($username, $password, $keepUserLoggedIn = false){
		
		$username = $this->checkVariable($username);
		$password = $this->checkVariable($password);
		
		$sql = sprintf('SELECT Id, realname 
						FROM ' . $this->tablePrefix .'_users
						WHERE email="%s" AND password="%s"
						LIMIT 1', $username, $password);
		
		$result = $this->dbc->query($sql);
		if ($result != false) 
		{
			if (($row = $this->dbc->fetchObject($result)) != false) 
			{
				$this->userId = $row->Id;			
				$realname = $row->realname;
			
				if ($this->userId != NULL &&
					$this->tdo != NULL)
				{
					$daysDataStored = 0;
					if ($keepUserLoggedIn == true) {
						$daysDataStored = 7;
					}
					$this->tdo->save(self::userId,   $this->userId, $daysDataStored);
					$this->tdo->save(self::realname, $realname, $daysDataStored);			
					$this->tdo->save(self::username, $username, $daysDataStored);
					$this->tdo->save(self::password, $password, $daysDataStored);						
					$this->tdo->save(self::daysDataStored, $daysDataStored, $daysDataStored);									
					$this->tdo->save(self::authTime, time());
				}
			}
		}
		return $this->userId;		
	}	
	
	public function getUserLevel(){
		
	}
	
	public function getRealName()
	{
		$value = NULL;
		if ($this->userId != NULL &&
			$this->tdo != NULL)
		{
			$value = $this->tdo->getValue(self::realname);
		}
		return $value;
	}
	
	
	public function isUserAuthenticated(){
		$authenticated = false;
		if ($this->userId != NULL)
		{
			if (($this->tdo->getValue(self::authTime) + $this->userCheckInterval) < time()) 
			{
				if ($this->authenticateUser($this->tdo->getValue(self::username), $this->tdo->getValue(self::password)) != NULL)
				{
					$authenticated = true;
				}	
			}
			else {
				$authenticated = true;
			}
		}	
		return $authenticated;
	}
	
	public function getUserId(){
		return $this->userId;
	}
	
	public function changePassword($newPassword, $currentPassword)
	{
		$newPassword = md5($newPassword);
		$sql = sprintf('UPDATE ' . $this->tablePrefix .'_users
						SET password = "%s"
						WHERE Id = %d AND password = "%s" 
						LIMIT 1', $newPassword, $this->getUserId(), md5($currentPassword));
		$out = FAILED;
		if ($this->dbc->query($sql) !== false){
			$out = CURRENT_PASSWORD_DOESNT_MATCH;
			if ($this->dbc->getAffectedRows() == 1) {
				$out = SUCCESS;
				$daysDataStored = $this->tdo->getValue(self::daysDataStored);
				if ($daysDataStored == NULL){
					$daysDataStored = 0;
				}
				$this->tdo->save(self::password, $newPassword, $daysDataStored);
			}
		}
		
		return $out;	
	}
	
	public function sendNewPassword($email){
		
		$email = $this->checkVariable($email);
		
		$sql = sprintf('SELECT Id 
						FROM ' . $this->tablePrefix .'_users
						WHERE email="%s"
						LIMIT 1', $email);
		
		$Id = $this->dbc->getUniqueField($sql);
		$out = EMAIL_NOT_FOUND;
		if ($Id != NULL)
		{
			$password = $this->generatePassword(8, 8);
			$sql = sprintf('UPDATE ' . $this->tablePrefix .'_users
							SET password=MD5("%s") 
							WHERE Id = %d
							LIMIT 1', $password, $Id);
			if ($this->dbc->query($sql) !== false){
				$message = 'Hi,<br/> Your new traceper password is ' . $password
							.'<br/><br/> traceper team';
				$headers  = 'MIME-Version: 1.0' . "\r\n";
				$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
				$headers  .= 'From: contact@traceper.com' . "\r\n";
				mail($email, "new password", $message, $headers);
				$out = SUCCESS;
			}
			
		}
		return $out;
	}
	
	private function generatePassword($length=9, $strength=0) {
		$vowels = 'aeuy';
		$consonants = 'bdghjmnpqrstvz';
		if ($strength & 1) {
			$consonants .= 'BDGHJLMNPQRSTVWXZ';
		}
		if ($strength & 2) {
			$vowels .= "AEUY";
		}
		if ($strength & 4) {
			$consonants .= '23456789';
		}
		if ($strength & 8) {
			$consonants .= '@#$%';
		}
	 
		$password = '';
		$alt = time() % 2;
		for ($i = 0; $i < $length; $i++) {
			if ($alt == 1) {
				$password .= $consonants[(rand() % strlen($consonants))];
				$alt = 0;
			} else {
				$password .= $vowels[(rand() % strlen($vowels))];
				$alt = 1;
			}
		}
		return $password;
}
	
	
	
}
?>