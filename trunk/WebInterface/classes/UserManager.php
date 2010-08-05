<?php

require_once("IUserManagement.php");
require_once("Base.php");

class UserManager extends Base implements IUserManagement
{
	
	function __construct($dbc){
		$this->dbc = $dbc;
	}	
	

	public function inviteUser($email)
	{
		$sql = sprintf('INSERT INTO traceper_invitedusers(email)
				VALUES("%s")', $email); 							  
		$out = FAILED;
		if ($this->dbc->query($sql) != false)
		{
			$out = SUCCESS;
		}	
		return $out;
	}
	
	public function registerUser($email, $name, $password){
		
		$password = md5($password);
		$sql = sprintf("INSERT INTO traceper_users (email, realname, password ) 
					    VALUE('%s','%s','%s')", $email, $name, $password);
	
		$result = false;
		if ($this->dbc->query($sql) != false){
			$result = true;
		}
		
		return $result;		
	}
	
	
	//TODO: group tablosunda kullanıcının kaydı olan grup varsa onlarda silinmeli
	public function deleteUser($userId)
	{
		$sql = sprintf('DELETE FROM traceper_users WHERE Id=%d',$userId);
		$result=false;
		if ($this->dbc->query($sql) != false){
			$result = true;
		}		
		return $result;
	}
	
	public function addUserToGroup($userId, $groupId){
		$sql = sprintf('INSERT INTO traceper_user_group_relation(userId, groupId)
						VALUES (%d, %d)', $userId, $groupId);
		
		$result = false;
		if ($this->dbc->query($sql) != false){
			$result = true;
		} 
		
		return $result;		
	}
	
	public function deleteUserFromGroup($userId, $groupId){
		$sql = sprintf('DELETE FROM traceper_user_group_relation 
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
		
		$sql = sprintf('INSERT INTO traceper_groups(name, description)
						VALUES ("%s", "%s")', $groupName, $description);
	     
	    $result = false;
		if	($this->dbc->query($sql) != false ){
			$result = true;
		}
		
		return $result;	
	}
	
	public function deleteGroup($groupId){
		$sql = sprintf('DELETE FROM traceper_groups 
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
		
		$sql = sprintf('UPDATE traceper_groups 
						SET name = "%s"
						WHERE Id = %d
						LIMIT 1', $groupName, $groupId);
		
		$result = false;
		if	($this->dbc->query($sql) != false ){
			$result = true;
		}
		
		return $result;	
	}

	
}