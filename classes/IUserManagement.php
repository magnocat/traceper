<?php

interface IUserManagement 
{
	public function inviteUser($email, $message);
	
	public function deleteUser($userId);
	
	public function addUserToGroup($userId, $groupId);
	
	public function deleteUserFromGroup($userId, $groupId);
	
	public function addGroup($groupName, $description);
	
	public function deleteGroup($groupId);
	
	public function changeGroupName($groupId, $groupName);
	
}