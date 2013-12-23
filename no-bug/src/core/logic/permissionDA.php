<?php
include_once 'db.php';

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
		if ($db->query($allowedProjectsSql)->num_rows != 1) {
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
		if ($db->query($allowedProjectsSql)->num_rows != 1) {
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
		if ($db->query($sql)->num_rows > 0) {
			return true;
		}
		return false;
	}
	
	public function echoPermissionDeniedAndDie() {
		echo '<div class="alert alert-danger alert-dismissable">
			  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
			  <strong>Access Denied!</strong> You don\'t have the Permission to access this Page! </div>';
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
		
		$allowedProjectsSql = "SELECT * FROM project WHERE group_admin IN (" . $groupsSql . ") OR group_write IN (" . $groupsSql . ") OR group_read IN (" . $groupsSql . ")";
		return $db->query($allowedProjectsSql);
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