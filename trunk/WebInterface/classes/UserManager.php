<?php

require_once("IUserManagement.php");
require_once("AuthenticateManager.php");

class UserManager extends AuthenticateManager implements IUserManagement
{
//	private $tablePrefix;
	
	function __construct($dbc, $tdo, $tablePrefix ){
		parent::__construct($dbc, $tdo, $tablePrefix);	
	}


	public function inviteUser($emails, $message)
	{
		
		$emailArray= $this->splitEmails($emails);
		$arrayLenth = count($emailArray);
		$invitationSentCount = 0;
		
		for ($i = 0; $i < $arrayLenth; $i++)
		{		
	//		if ($this->check_email_address($emailArray[$i])) 
			{
				echo "<br/>email address:".$emailArray[$i];

/*				$sql = sprintf('INSERT INTO '.$this->tablePrefix.'_invitedusers(email)
						VALUES("%s")', $emailArray[$i]); 							  
				$out = FAILED;
				if ($this->dbc->query($sql) != false)
				{
					//send invitation mail 
					$invitationSentCount++;
				}
*/
			}
		}
		$out = $invitationSentCount;
		if ($arrayLenth == $invitationSentCount) {
			$out = true;
		}		
		return $out;
	}
	
	private function splitEmails($emails)
	{
		$emails = str_replace(array(" ",",", chr(13)),array(";",";",";1212"),$emails);
		$emails = str_replace(array(chr(13)), array(";".chr(13)),$emails);
		$emails = str_replace(array(";;"), array(";"),$emails);
		
		
		$emails = explode(";", $emails);					
		return $emails;
	}

	private function check_email_address($email) 
	{
	  // First, we check that there's one @ symbol, and that the lengths are right
	  if (!preg_match("/^[^@]{1,64}@[^@]{1,255}$/", $email)) {
	    // Email invalid because wrong number of characters in one section, or wrong number of @ symbols.
	    return false;
	  }
	  // Split it into sections to make life easier
	  $email_array = explode("@", $email);
	  $local_array = explode(".", $email_array[0]);
	  for ($i = 0; $i < sizeof($local_array); $i++) {
	     if (!preg_match("^(([A-Za-z0-9!#$%&'*+/=?^_`{|}~-][A-Za-z0-9!#$%&'*+/=?^_`{|}~\.-]{0,63})|(\"[^(\\|\")]{0,62}\"))$", $local_array[$i])) {
	      return false;
	    }
	  }  
	  if (!preg_match("^\[?[0-9\.]+\]?$", $email_array[1])) { // Check if domain is IP. If not, it should be valid domain name
	    $domain_array = explode(".", $email_array[1]);
	    if (sizeof($domain_array) < 2) {
	        return false; // Not enough parts to domain
	    }
	    for ($i = 0; $i < sizeof($domain_array); $i++) {
	      if (!preg_match("^(([A-Za-z0-9][A-Za-z0-9-]{0,61}[A-Za-z0-9])|([A-Za-z0-9]+))$", $domain_array[$i])) {
	        return false;
	      }
	    }
	  }
	  return true;
	}
	public function registerUser($email, $name, $password)
	{
		$out = EMAIL_NOT_VALID;
		if (preg_match("/^([a-zA-Z0-9])+@([a-zA-Z0-9_-])+(\.[a-zA-Z0-9_-]+)+/", $email))
		{
			$sql = sprintf('SELECT Id
						FROM '.$this->tablePrefix.'_users
						WHERE email="%s"
						LIMIT 1', $email);
			$out = FAILED;
			if (($res = $this->dbc->query($sql)) != false)
			{
				
				$out = EMAIL_ALREADY_EXIST;
				if ($this->dbc->numRows($res) == 0)
				{
					$md5Password = md5($password);
					$time = date('Y-m-d h:i:s');
					$sql = sprintf('INSERT INTO '.$this->tablePrefix.'_user_candidates (email, realname, password, time )
					    VALUE("%s","%s","%s","%s")', $email, $name, $md5Password, $time);
					$key = md5($email.$time);
					$message = 'Hi '.$name.',<br/> <a href="'.WEB_ADDRESS.'?action=WebClientActivateAccountRequest&email='.$email.'&key='.$key.'">'.
					'Click here to activate your account</a> <br/>';
					$message .= '<br/> Your Password is :'.$password;
					$message .= '<br/> The Traceper Team';
					$headers  = 'MIME-Version: 1.0' . "\r\n";
					$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
					$headers  .= 'From: contact@traceper.com' . "\r\n";
					$out = FAILED;
					if ($this->dbc->query($sql) != false){
						$out = SUCCESS;
						mail($email, "traceper activation", $message, $headers);
					}
					else {
						if ($this->dbc->getErrorNo() == DB_ERROR_CODES::DB_KEY_DUPLICATE)
						{
							$sql = sprintf('UPDATE '.$this->tablePrefix.'_user_candidates
								SET time="%s", realname="%s", password="%s" 
								WHERE email="%s"
								LIMIT 1', $time, $name, $md5Password, $email);
							$out = FAILED;
							if ($this->dbc->query($sql) != false){
								$out = SUCCESS;
								mail($email, "traceper activation", $message, $headers);
							}

						}
					}
				}
			}
		}
		return $out;

	}


	public function activateAccount($reqArray)
	{
		$out = MISSING_PARAMETER;
		if (isset($reqArray['email']) && $reqArray['email']!=null &&
			isset($reqArray['key'])&& $reqArray['key']!=null )
		{
			$email = $reqArray['email'];		
			$key = $reqArray['key'];	
			
			$sql = sprintf('SELECT realname, time, password 
							FROM '.$this->tablePrefix.'_user_candidates
							WHERE email="%s" 
							LIMIT 1',$email);
			if (($result = $this->dbc->query($sql)) != false)
			{
				if (($row = $this->dbc->fetchObject($result)) != false){
				
					$generatedKey =  md5($email.$row->time);
					$out = KEYS_DONT_MATCH;
					if ($generatedKey == $key){
						$sql = sprintf('INSERT INTO '.$this->tablePrefix.'_users(email, realname, password)
										VALUES("%s","%s","%s")', $email, $row->realname, $row->password);
						
						if ($this->dbc->query($sql) != false){
							$out = SUCCESS;
							$sql = sprintf('DELETE FROM '.$this->tablePrefix.'_user_candidates 
											WHERE email="%s"', $email);
							//TODO: silinmezse bir sekilde anlamamiz gerekiyor
							$this->dbc->query($sql);							
						}
					}
										
				}
				else {
					$sql = sprintf('SELECT Id 
							FROM '.$this->tablePrefix.'_users
							WHERE email="%s" 
							LIMIT 1',$email);
					$out = EMAIL_NOT_FOUND;
					if (($result = $this->dbc->query($sql)) != false) {
						if ($this->dbc->numRows($result) == 1){
							$out = EMAIL_ALREADY_EXIST;
						}
					}	
			}
		}
		return $out;
	}
	}
		

	//TODO: group tablosunda kullanıcının kaydı olan grup varsa onlarda silinmeli
	public function deleteUser($userId)
	{
		$sql = sprintf('DELETE FROM '.$this->tablePrefix.'_users WHERE Id=%d',$userId);
		$result=false;
		if ($this->dbc->query($sql) != false){
			$result = true;
		}
		return $result;
	}

	public function addUserToGroup($userId, $groupId){
		$sql = sprintf('INSERT INTO '.$this->tablePrefix.'_user_group_relation(userId, groupId)
						VALUES (%d, %d)', $userId, $groupId);

		$result = false;
		if ($this->dbc->query($sql) != false){
			$result = true;
		}

		return $result;
	}

	public function deleteUserFromGroup($userId, $groupId){
		$sql = sprintf('DELETE FROM '.$this->tablePrefix.'_user_group_relation
						WHERE userId = %d AND 
							  groupId = %d
						LIMIT 1', $userId, $groupId);

		$result = false;
		if	($this->dbc->query($sql) != false ){
			$result = true;
		}

		return $result;
	}

	public function addGroup($groupName, $description){
		$groupname = self::checkVariable($groupName);

		$sql = sprintf('INSERT INTO '.$this->tablePrefix.'_groups(name, description)
						VALUES ("%s", "%s")', $groupName, $description);

		$result = false;
		if	($this->dbc->query($sql) != false ){
			$result = true;
		}

		return $result;
	}

	public function deleteGroup($groupId){
		$sql = sprintf('DELETE FROM '.$this->tablePrefix.'_groups
						WHERE id = %d 
						LIMIT 1', $groupId);

		$result = false;
		if	($this->dbc->query($sql) != false ){
			$result = true;
		}

		return $result;

	}

	public function changeGroupName($groupId, $groupName){
		$groupname = self::checkVariable($groupName);

		$sql = sprintf('UPDATE '.$this->tablePrefix.'_groups
						SET name = "%s"
						WHERE Id = %d
						LIMIT 1', $groupName, $groupId);

		$result = false;
		if	($this->dbc->query($sql) != false ){
			$result = true;
		}

		return $result;
	}
	
	public function isFriend($user1, $user2)
	{
						   	
 		$sql = 'SELECT status FROM '.$this->tablePrefix.'_friends
		 			WHERE ((friend1 = '. $user1 .' AND friend2 = '. $user2 .') OR
		 				   (friend1 = '. $user2 .' AND friend2 = '. $user1 .')) AND 
		 				  status = 1
		 		LIMIT 1';
 		$result = $this->dbc->query($sql);
 		$numRows = $this->dbc->numRows($result);
 		$isFriend = false;
 		if ($numRows == 1) {
 			$isFriend = true;
 		}
 		
 		return $isFriend;	
	}
	
	public function deleteFriendship($user1, $user2) {
		
		$user1 = (int) $user1;
		$user2 = (int) $user2;
		
		$sql = sprintf('DELETE FROM ' . $this->tablePrefix .'_friends 
						WHERE ((friend1 = %d  AND friend2 = %d ) OR
	 				   		   (friend1 = %d  AND friend2 = %d ))
	 				   	LIMIT 1', $user1, $user2, $user2, $user1);
		$result = false;
		if ($this->dbc->query($sql) != false) {
			$result = true;
		}
			
		return $result;	
	}
	
	public function addFriendRequest($friendId) 
	{
		$friendId = (int) $friendId;
		$sql = sprintf('INSERT INTO ' . $this->tablePrefix .'_friends(friend1, friend2) 
						VALUES (%d, %d)	', $this->getUserId(), $friendId);
		$result = false;
		if ($this->dbc->query($sql) != false) {
			$result = true;
		}
		return $result;
	}
	
	
	public function getUserInfo()
	{
		$userId = $this->getUserId();
		$sql = 'SELECT realname, latitude, longitude, deviceId, date_format(dataArrivedTime,"%d %b %Y %T") as time
				FROM '.$this->tablePrefix . '_users 
				WHERE Id = ' . $userId .'
				LIMIT 1';
		$result = $this->dbc->query($sql);
		$user = null;
		if ($row = $this->dbc->fetchObject($result)) {
			$user = $row;
		}
		return $user;
	}
	
	public function getFriendRequests($pageNo, $elementCountInAPage)
	{
		$out = UNAUTHORIZED_ACCESS;
		if ($this->isUserAuthenticated() == true)
		{
			$userId = $this->getUserId();
			if (isset($pageNo) && $pageNo > 0) {
				$pageNo = (int) $pageNo;
			}
			else {
				$pageNo = 1;
			}
			$offset = ($pageNo - 1) * $elementCountInAPage;
			
			$sql = 'SELECT tu.Id, tu.realname, 0 as isFriend
					FROM traceper_friends tf 
						LEFT JOIN traceper_users tu 
							ON tf.friend1 = tu.Id
					WHERE tf.status=0 AND 
							tf.friend2= ' . $userId . '
					LIMIT '. $offset . ' , '. $elementCountInAPage;
			
			$sqlPageCount = 'SELECT 
								ceil(count(Id)/'.$elementCountInAPage.')
							 FROM traceper_friends
							 WHERE status=0 AND 
						  			friend2= ' . $userId ;
			
			$pageCount = $this->dbc->getUniqueField($sqlPageCount);	
			
			
			$out = $this->prepareXML($sql, $pageNo, $pageCount);
		}
		return $out;
	}
	
	private function prepareXML($sql, $pageNo, $pageCount)
	{		
		$result = NULL;
		// if page count equal to 0 then there is no need to run query
//		echo $sql;
		if ($pageCount >= $pageNo && $pageCount != 0) {
			$result = $this->dbc->query($sql);		
		}
				
		$str = NULL;
		$userId = NULL;
		if ($result != NULL )
		{				
				while ( $row = $this->dbc->fetchObject($result) )
				{
					
						$str .= $this->getUserXMLItem($row);	

				}	
				header("Content-type: application/xml; charset=utf-8");
		
				$pageNo = $pageCount == 0 ? 0 : $pageNo;
		
				$pageStr = 'pageNo="'.$pageNo.'" pageCount="' . $pageCount .'"' ;
		
				$str = '<?xml version="1.0" encoding="UTF-8"?>'
					.'<page '. $pageStr . ' >'					
						. $str
				   .'</page>';
		}
		return $str;		
	}
	
	private function getUserXMLItem($row)
	{
		$row->Id = isset($row->Id) ? $row->Id : null;
//		$row->username = isset($row->username) ? $row->username : null;
		$row->isFriend = isset($row->isFriend) ? $row->isFriend : 0; 
		$row->realname = isset($row->realname) ? $row->realname : null;
		$row->latitude = isset($row->latitude) ? $row->latitude : null;
		$row->longitude = isset($row->longitude) ? $row->longitude : null;
		$row->altitude = isset($row->altitude) ? $row->altitude : null;
		$row->dataArrivedTime = isset($row->dataArrivedTime) ? $row->dataArrivedTime : null;
		$row->message = isset($row->message) ? $row->message : null;
		$row->deviceId = isset($row->deviceId) ? $row->deviceId : null;
		$row->status_message = isset($row->status_message) ? $row->status_message : null;
			
		$str = '<user>'
		. '<Id isFriend="'.$row->isFriend.'">'. $row->Id .'</Id>'
//		. '<username>' . $row->username . '</username>'
		. '<realname>' . $row->realname . '</realname>'
		. '<location latitude="' . $row->latitude . '"  longitude="' . $row->longitude . '" altitude="' . $row->altitude . '" />'
		. '<time>' . $row->dataArrivedTime . '</time>'
		. '<message>' . $row->message . '</message>'
		. '<status_message>' . $row->status_message . '</status_message>'
		. '<deviceId>' . $row->deviceId . '</deviceId>'
		.'</user>';
		
		return $str;
	}


}