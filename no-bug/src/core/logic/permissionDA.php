<?php
include_once 'db.php';
include_once dirname(__FILE__).'/groupDA.php';

class PermissionDA {	

	public function isReadOnProjectAllowed ($projectId) {
		$db = new DB();
		$db->connect();
		$userId = $db->esc($_SESSION['nobug'.RANDOMKEY.'userId']);
		$groupsSql = "";
		
		$groupsArray = $this->getAllGroups($userId);
		$groupCounter = count($groupsArray);		
		for ($x = 0; $x < $groupCounter; $x++)
		{
			if ($x == 0) {
				$groupsSql = $groupsSql . "'".$groupsArray[$x]."'";
			}
			$groupsSql = $groupsSql . ",'".$groupsArray[$x]."'";
		}
		
		$allowedProjectsSql = "SELECT * FROM project WHERE (group_read IN (" . $groupsSql . ") OR group_write IN (" . $groupsSql . ") OR group_admin IN (" . $groupsSql . "))" .
								" AND id = " . $projectId;
		$query = $db->query($allowedProjectsSql);
		if ($query == null) {
			return false;
		}
		if ($query->num_rows == 0) {
			return false;
		}
		return true;
	}
	
	public function isWriteOnProjectAllowed ($projectId) {
		$db = new DB();
		$db->connect();
		$userId = $db->esc($_SESSION['nobug'.RANDOMKEY.'userId']);
		$groupsSql = "";
		
		$groupsArray = $this->getAllGroups($userId);
		$groupCounter = count($groupsArray);		
		for ($x = 0; $x < $groupCounter; $x++)
		{
			if ($x == 0) {
				$groupsSql = $groupsSql . "'".$groupsArray[$x]."'";
			}
			$groupsSql = $groupsSql . ",'".$groupsArray[$x]."'";
		}
		
		$allowedProjectsSql = "SELECT * FROM project WHERE (group_write IN (" . $groupsSql . ") OR group_admin IN (" . $groupsSql . "))" .
								" AND id = " . $projectId;
		$query = $db->query($allowedProjectsSql);
		if ($query == null) {
			return false;
		}
		if ($query->num_rows != 1) {
			return false;
		}
		return true;
	}
	
	public function isAdminOnProjectAllowed ($projectId) {
		$db = new DB();
		$db->connect();
		$userId = $db->esc($_SESSION['nobug'.RANDOMKEY.'userId']);
		$groupsSql = "";
		
		$groupsArray = $this->getAllGroups($userId);
		$groupCounter = count($groupsArray);		
		for ($x = 0; $x < $groupCounter; $x++)
		{
			if ($x == 0) {
				$groupsSql = $groupsSql . "'".$groupsArray[$x]."'";
			}
			$groupsSql = $groupsSql . ",'".$groupsArray[$x]."'";
		}
		
		$allowedProjectsSql = "SELECT * FROM project WHERE (group_admin IN (" . $groupsSql . "))".
								" AND id = " . $projectId;
		$query = $db->query($allowedProjectsSql);
		if ($query == null) {
			return false;
		}
		if ($query->num_rows != 1) {
			return false;
		}
		return true;
	}
	
	public function isGeneralAdmininstrationAllowed () {
		$db = new DB();
		$db->connect();
		$userId = $db->esc($_SESSION['nobug'.RANDOMKEY.'userId']);
		
		$groupsSql = "";
		$groupsArray = $this->getAllGroups($userId);
		$groupCounter = count($groupsArray);
		for ($x = 0; $x < $groupCounter; $x++)
		{
			if ($x == 0) {
				$groupsSql = $groupsSql . "'".$groupsArray[$x]."'";
			}
			$groupsSql = $groupsSql . ",'".$groupsArray[$x]."'";
		}
		
		$sql = "SELECT value FROM setting WHERE `key` = 'global.admingroup' AND value IN (" . $groupsSql . ")";
		$query = $db->query($sql);
		if ($query == null) {
			return false;
		}
		if ($query->num_rows > 0) {
			return true;
		}
		return false;
	}
	
	public function echoPermissionDeniedAndDie() {
		echo '<div class="alert alert-danger alert-dismissable" style="margin: 50px;">
			  <strong><i class="fa fa-lock"></i> Access Denied!</strong> You don\'t have the Permission to access this Page! </div>';
		include dirname(__FILE__).'/../footer.php';
		die();
	}
	
	public function getAllAllowedProjects ($userId) {
		$db = new DB();
		$db->connect();
		$userId = $db->esc($userId);
		$groupsSql = "";
		
		$groupsArray = $this->getAllGroups($userId);
		$groupCounter = count($groupsArray);		
		for ($x = 0; $x < $groupCounter; $x++)
		{
			if ($x == 0) {
				$groupsSql = $groupsSql . "'".$groupsArray[$x]."'";
			}
			$groupsSql = $groupsSql . ",'".$groupsArray[$x]."'";
		}
		
		$allowedProjectsSql = "SELECT * FROM project WHERE active=1 AND (group_admin IN (" . $groupsSql . ") OR group_write IN (" . $groupsSql . ") OR group_read IN (" . $groupsSql . "))";
		return $db->query($allowedProjectsSql);
	}
	
	public function getUsersOfAProject ($projectId) {
		$db = new DB();
		$db->connect();
		$projectId = $db->esc($projectId);
		$userarray = array();
		
		$currentProject = $db->query("SELECT * FROM project WHERE id = ".$projectId);
		$allUsersSql = "SELECT * FROM `user` WHERE active=1";
		$allUsers = $db->query($allUsersSql);
		while ($oneUser = $allUsers->fetch_assoc()) {
			$allowedProjectOfUser = $this->getAllAllowedProjects($oneUser["id"]);
			if ($allowedProjectOfUser != null) {
				while ($oneProjectOfUser = $allowedProjectOfUser->fetch_assoc()) {
					if ($oneProjectOfUser["id"] == $projectId) {
						$userarray[] = $oneUser;
						break 1;
					}
				}
			}
		}
		return $userarray;
	}
	
	public function isGroupInList ($groupId, $groupsArray) {
		$count = count($groupsArray) - 1;
		
		$x = 0;
		while ($x <= $count) {
			if ($groupId == $groupsArray[$x]) {
				return true;
			}
			$x++;
		}
		
	} 
	
	public function getAllGroups ($userId) {
		$db = new DB();
		$db->connect();
		$userId = $db->esc($userId);
		$allGroups = array();
		
		$sqlGetDirectUserGroups = "SELECT * FROM user_group WHERE user_id = " . $userId; 
		$directGroupsResult = $db->query($sqlGetDirectUserGroups);
		while ($oneGroup = $directGroupsResult->fetch_assoc()) {
			$allGroups[] = $oneGroup["group_id"];
			$allGroups = $this->getParentGroups($oneGroup["group_id"], $db, $allGroups);
		}
		return array_merge(array_unique($allGroups));
	}
	
	private function getParentGroups ($groupId, $db, $allGroupsArray) {
		$groupId = $db->esc($groupId);
		$getParentsSql = "SELECT * FROM group_group WHERE group_group.group_child = " . $groupId;
		$parentQuery = $db->query($getParentsSql);
		
		while ($oneGroup = $parentQuery->fetch_assoc()) {
			if (!in_array($oneGroup["group_parent"], $allGroupsArray)) {
				$allGroupsArray[] = $oneGroup["group_parent"];
				$allGroupsArray = $this->getParentGroups($oneGroup["group_parent"], $db, $allGroupsArray);
			}
		}
		return $allGroupsArray;
	}
}