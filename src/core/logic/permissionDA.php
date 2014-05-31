<?php
include_once 'db.php';
include_once dirname(__FILE__).'/groupDA.php';
include_once dirname(__FILE__).'/../logger.php';

/**
 * DataAccess for permission stuff
 */
class PermissionDA {	
	/**
	 * Checks if the logged in user is allowed to view a project
	 * @param <Int> $projectId project to check
	 * @return boolean TRUE if the user has read access to this project
	 */
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
	
	/**
	 * Checks if the logged in user is allowed to change a project
	 * @param <Int> $projectId project to check
	 * @return boolean TRUE if the user has write access to this project
	 */
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
	
	/**
	 * Checks if the logged in user is allowed to administrate a project
	 * @param <Int> $projectId project to check
	 * @return boolean TRUE if the user has admin access to this project
	 */
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
	
	/**
	 * Checks if the logged in user has admin rights on the entire platform
	 * @return boolean TRUE if the user has global admin rights
	 */
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
	
	/**
	 * Prints out a nice error banner and quit the pageloading
	 */
	public function echoPermissionDeniedAndDie() {
		echo '<div class="alert alert-danger alert-dismissable" style="margin: 50px;">
			  <strong><i class="fa fa-lock"></i> Access Denied!</strong> You don\'t have the Permission to access this Page! </div>';
		Logger::warn("Site Access denied { " . $_SERVER['REQUEST_URI'] . " }", null);
		include dirname(__FILE__).'/../footer.php';
		die();
	}
	
	/**
	 * Get all projects of a user in which the user (at least) has read permission
	 * @param <Int> $userId Id of the selected user
	 * @return <dbResult> Database result
	 */
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
	
	/**
	 * Get all projects of a user in which the user (at least) has write permission
	 * @param <Int> $userId
	 * @return <dbResult> Database result
	 */
	public function getWriteAllowedProjects ($userId) {
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
		
		$allowedProjectsSql = "SELECT * FROM project WHERE active=1 AND (group_admin IN (" . $groupsSql . ") OR group_write IN (" . $groupsSql . "))";
		return $db->query($allowedProjectsSql);
	}
	
	/**
	 * Returns all users (with access) of a project
	 * @param <Int> $projectId Id of the selected project
	 * @return <Array> Array of all users of the project
	 */
	public function getUsersOfAProject ($projectId) {
		$db = new DB();
		$db->connect();
		$projectId = $db->esc($projectId);
		$userarray = array();
		
		$currentProject = $db->query("SELECT * FROM project WHERE id = ".$projectId);
		$allUsersSql = "SELECT * FROM `user` WHERE active=1";
		$allUsers = $db->query($allUsersSql);
		while ($oneUser = $allUsers->fetch_assoc()) {
			$allowedProjectOfUser = $this->getWriteAllowedProjects($oneUser["id"]);
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
	
	/**
	 * Checks if a group is in a list of groups
	 * @param <Int> $groupId Group to search
	 * @param <Int> $groupsArray Array to search in it
	 * @return boolean TRUE if the group is in the array
	 */
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
	
	/**
	 * Get all groups that a user is member of
	 * @param <Int> $userId selected user
	 * @return <Array> array with all groups of a user in it
	 */
	public function getAllGroups ($userId) {
		$db = new DB();
		$db->connect();
		$userId = $db->esc($userId);
		$allGroups = array();
		
		$sqlGetDirectUserGroups = "SELECT * FROM user_group 
			  LEFT JOIN `group` ON `group`.id = user_group.group_id
			  WHERE active = 1 AND user_id = " . $userId; 
		$directGroupsResult = $db->query($sqlGetDirectUserGroups);
		while ($oneGroup = $directGroupsResult->fetch_assoc()) {
			$allGroups[] = $oneGroup["group_id"];
			$allGroups = $this->getParentGroups($oneGroup["group_id"], $db, $allGroups);
		}
		return array_merge(array_unique($allGroups));
	}
	
	/**
	 * Returns all parent groups of a group (really all) Warning: This is a recursive function!
	 * @param <Int> $groupId group to finds his parents
	 * @param <mySQLdb> $db database connection (already opened)
	 * @param <Array> $allGroupsArray Group with the already founded parent groups in it
	 * @return <Array> Array with all parent groups
	 */
	private function getParentGroups ($groupId, $db, $allGroupsArray) {
		$groupId = $db->esc($groupId);
		$getParentsSql = "SELECT * FROM group_group 
			  LEFT JOIN `group` ON `group`.id = group_group.group_parent
			  WHERE group_group.group_child = " . $groupId;
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