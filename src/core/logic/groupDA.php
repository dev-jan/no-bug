<?php
include_once 'db.php';

/**
 * DataAccess for operations related to groups
 */
class GroupDA {
	/**
	 * Prints a table of all groups
	 * @param <Boolean> $reallyAll print also deactivated groups?
	 */
	public function printAllGroupsTable($reallyAll) {
		$db = new DB();
		$db->connect();
		$allGroupsSql;
		if ($reallyAll) {
			$allGroupsSql = "SELECT * FROM `group`";
		}
		else {
			$allGroupsSql = "SELECT * FROM `group`
								WHERE `group`.active = 1";
		}
		
		$groupsQuery = $db->query($allGroupsSql);
		while ($oneGroup = $groupsQuery->fetch_assoc()) {
			if ($oneGroup["active"] == 1) {
				echo '<tr>';
			}
			else {
				echo '<tr class="danger">';
			}
			echo '		<td>'.$oneGroup["name"].'</td>
						<td>'.$this->getParentGroups($oneGroup["id"]).'</td>
						<td>'.$this->getChildEntitys($oneGroup["id"]).'</td>
						<td><form action="group.php?" method="GET">
							<input type="hidden" name="g" value="'.$oneGroup['id'].'" />
							<button type="submit" class="btn btn-default btn-sm"><i class="fa fa-pencil"></i> edit</button></form></td>
					</tr>';
		}
	}
	
	/**
	 * Returns a list of the parent groups of a group
	 * @param <Int> $groupID selected group
	 * @return string Linklist (as HTML)
	 */
	private function getParentGroups($groupID) {
		$return = "";
		$db = new DB();
		$db->connect();
		$parentSql = "SELECT grpparentnames.id AS parentID, grpparentnames.name AS parentName
					  FROM `group` AS grp
					 	LEFT OUTER JOIN group_group AS grpparent ON grpparent.group_child= grp.id
					 		LEFT OUTER JOIN `group` AS grpparentnames ON grpparentnames.id = grpparent.group_parent
					  WHERE grp.id = $groupID;";
		$parentQuery = $db->query($parentSql);
		while ($oneParent = $parentQuery->fetch_assoc()) {
			if ($oneParent["parentID"] != ""){
				$return = $return . '<a href="group.php?g='.$oneParent["parentID"].'"><i class="fa fa-users"></i> '.$oneParent["parentName"].'</a><br />';
			}
		}
		return $return;
	}
	
	/**
	 * Returns a list of the childs of a group (users and groups)
	 * @param <Int> $groupID selected group
	 * @return string Linklist (as HTML)
	 */
	private function getChildEntitys($groupID) {
		$return = "";
		$db = new DB();
		$db->connect();
		$childSql = "SELECT grpchildnames.id AS childID, grpchildnames.name AS childName
					 FROM `group` AS grp
						LEFT OUTER JOIN group_group AS grpchild ON grpchild.group_parent = grp.id
						LEFT OUTER JOIN `group` AS grpchildnames ON grpchildnames.id = grpchild.group_child
					 WHERE grp.id = $groupID;";
		$childQuery = $db->query($childSql);
		while ($oneChild = $childQuery->fetch_assoc()) {
			if ($oneChild["childID"] != "") {
				$return = $return . '<a href="group.php?g='.$oneChild["childID"].'"><i class="fa fa-users"></i> '.$oneChild["childName"].' </a><br />';
			} 
		}
		
		$childUsersSql = "SELECT user.id, user.username, user.prename, user.surname FROM user_group
							LEFT OUTER JOIN user ON user.id = user_group.user_id
						  WHERE user_group.group_id = $groupID";
		$childUserQuery = $db->query($childUsersSql);
		while ($oneUser = $childUserQuery->fetch_assoc()) {
			if ($oneUser["id"] != "") {
				$return = $return . '<a href="user.php?u='.$oneUser["id"].'"><i class="fa fa-user"></i> '.$oneUser["prename"].' '.$oneUser["surname"].' </a><br />';
			}
		}
		
		return $return;
	}
	
	/**
	 * Returns a specific group
	 * @param <Int> $groupID ID of the selected group
	 * @return <Array> Databaseobject as array (or NULL if the group not exists)
	 */
	public function getGroup($groupID) {
		$db = new DB();
		$db->connect();
		
		$groupID = $db->esc($groupID);
		$sql = "SELECT * FROM `group` WHERE `group`.id = $groupID";
		$query = $db->query($sql);
		
		if ($query->num_rows > 0) {
			return $query->fetch_assoc();
		}
		else {
			return null;
		}
	}
	
	/**
	 * Checks if a group is active or not
	 * @param <Int> $groupID selected group
	 * @return boolean True if the group is active
	 */
	public function isGroupActive ($groupID) {
		$db = new DB();
		$db->connect();
		
		$groupID = $db->esc($groupID);
		$query = $db->query("SELECT active FROM `group` WHERE id=$groupID");
		$result = $query->fetch_assoc();
		if ($result["active"] == 1) {
			return true;
		}
		return false;
	}
	
	/**
	 * Prints the dropdown content of all activated groups
	 */
	public function printGroupSelect() {
		echo '<option id="0">--- Select Group ---</option>';
		$db = new DB();
		$db->connect();
		
		$sql = "SELECT * FROM `group` WHERE `group`.active != 0";
		$query = $db->query($sql);
		while ($oneGroup = $query->fetch_assoc()) {
			echo '<option value="'.$oneGroup["id"].'">'.$oneGroup["name"].'</option>';
		}
	}
	
	/**
	 * Prints the dropdown content of all activated users
	 */
	public function printUserSelect() {
		echo '<option id="0">--- Select User ---</option>';
		$db = new DB();
		$db->connect();
	
		$sql = "SELECT * FROM `user` WHERE `user`.active != 0";
		$query = $db->query($sql);
		while ($oneUser = $query->fetch_assoc()) {
			echo '<option value="'.$oneUser["id"].'">'.$oneUser["username"].'</option>';
		}
	}
	/**
	 * Returns the groupmembers (users and groups) of a group
	 * @param <Int> $groupID selected group
	 * @return string Tablecontent (as HTML)
	 */
	public function returnGroupMembersAsTable ($groupID) {
		$db = new DB();
		$db->connect();
		
		$groupID = $db->esc($groupID);
		$return = "";
		
		$membersSql = "SELECT grpchildnames.id AS childID, grpchildnames.name AS childName
						 FROM `group` AS grp
							LEFT OUTER JOIN group_group AS grpchild ON grpchild.group_parent = grp.id
							LEFT OUTER JOIN `group` AS grpchildnames ON grpchildnames.id = grpchild.group_child
						 WHERE grp.id = $groupID;";
		$groupQuery = $db->query($membersSql);
		while ($oneMember = $groupQuery->fetch_assoc()) {
			if ($oneMember["childID"] != "") {
				$return = $return . '<tr>
										<td><a href="group.php?g='.$oneMember["childID"].'"><i class="fa fa-users"></i> '.$oneMember["childName"].'</a></td>
										<td><form action="?g='.$groupID.'" method="post">
												<input type="hidden" name="groupId" value="'.$oneMember["childID"].'" />
												<button type="submit" class="btn btn-danger" ><i class="fa fa-trash-o"></i> Remove</button>
											</form></td>
									 </tr>';
			}
		}
		
		$childUsersSql = "SELECT user.id, user.username, user.prename, user.surname FROM user_group
							LEFT OUTER JOIN user ON user.id = user_group.user_id
						  WHERE user_group.group_id = $groupID";
		$userQuery = $db->query($childUsersSql);
		while ($oneMember = $userQuery->fetch_assoc()) {
			if ($oneMember["id"] != "") {
				$return = $return . '<tr>
										<td><a href="user.php?u='.$oneMember["id"].'" ><i class="fa fa-user"></i> '.$oneMember["username"].' </a></td>
										<td><form action="?g='.$groupID.'" method="post">
												<input type="hidden" name="userId" value="'.$oneMember["id"].'" />
												<button type="submit" class="btn btn-danger" ><i class="fa fa-trash-o"></i> Remove</button>
											</form></td>
									 </tr>';
			}
		}
		
		return $return;
	}
	
	/**
	 * Deactivate a group
	 * @param <Int> $groupId id of the group to deactivate
	 */
	public function deactivateGroup ($groupId) {
		$db = new DB();
		$db->connect();
		
		$groupId = $db->esc($groupId);
		$sql = "UPDATE `group` SET active='0'
						WHERE id=$groupId";
		$db->query($sql);
	}
	
	/**
	 * Activate a group
	 * @param <Int> $groupId id of the group to activate
	 */
	public function activateGroup ($groupId) {
		$db = new DB();
		$db->connect();
	
		$groupId = $db->esc($groupId);
		$sql = "UPDATE `group` SET active='1'
		WHERE id=$groupId";
		$db->query($sql);
	}
	
	/**
	 * Change the groupname of a group
	 * @param <Int> $groupId selected group id
	 * @param <Int> $newGroupname the new groupname
	 */
	public function updateGroupname ($groupId, $newGroupname) {
		$db = new DB();
		$db->connect();
		
		$groupId = $db->esc($groupId);
		$newGroupname = $db->esc($newGroupname);
		$updateSql = "UPDATE `group` SET name='$newGroupname'
						WHERE id=$groupId";
		$db->query($updateSql);
	}
	
	/**
	 * Add a member (group!) to a group 
	 * @param <Int> $parentID
	 * @param <Int> $childID
	 */
	public function addGroupmember ($parentID, $childID) {
		$db = new DB();
		$db->connect();
		
		$parentID = $db->esc($parentID);
		$childID = $db->esc($childID);
		
		$insertSql = "INSERT INTO `group_group` (`group_child`, `group_parent`) VALUES ('".$childID."', '".$parentID."');";
		$db->query($insertSql);
	}
	
	/**
	 * Add a member (user!) to a group
	 * @param <Int> $parentID
	 * @param <Int> $userID
	 */
	public function addUsermember ($parentID, $userID) {
		$db = new DB();
		$db->connect();
	
		$parentID = $db->esc($parentID);
		$childID = $db->esc($userID);
	
		$insertSql = "INSERT INTO `user_group` (`user_id`, `group_id`) VALUES ('".$childID."', '".$parentID."');";
		$db->query($insertSql);
	}
	
	/**
	 * Remove a member (group!) from a group
	 * @param <Int> $parentID
	 * @param <Int> $childID
	 */
	public function removeGroupmember ($parentID, $childID) {
		$db = new DB();
		$db->connect();
		
		$parentID = $db->esc($parentID);
		$childID = $db->esc($childID);
		
		$removeSql = "DELETE FROM `group_group` WHERE `group_child`='".$childID."' AND `group_parent`='".$parentID."';";
		$db->query($removeSql);
	}
	
	/**
	 * Remove a member (user!) from a group
	 * @param <Int> $parentID
	 * @param <Int> $childID
	 */
	public function removeUsermember ($parentID, $childID) {
		$db = new DB();
		$db->connect();
	
		$parentID = $db->esc($parentID);
		$childID = $db->esc($childID);
	
		$removeSql = "DELETE FROM `user_group` WHERE `user_id`='".$childID."' AND `group_id`='".$parentID."';";
		$db->query($removeSql);
	}
	
	/**
	 * Create a new group
	 * @param <String> $groupname Name of the new group
	 */
	public function addGroup ($groupname) {
		$db = new DB();
		$db->connect();
		
		$groupname = $db->esc($groupname);
		$currentUserId = $db->esc($_SESSION['nobug'.RANDOMKEY.'userId']);
		
		$insertSql = "INSERT INTO `group` (`name`, `active`, `meta_creator_id`, `meta_creatDate`)
						 VALUES ('$groupname', '1', '$currentUserId', '".$db->toDate(time())."')";
		
		$db->query($insertSql);
	}
	
	/**
	 * Print the dropdown content of all active groups
	 * @param <Int> $selectedGroupID Group that will be selected in the dropdown
	 */
	public function printGroupSelection ($selectedGroupID) {
		$db = new DB();
		$db->connect();
	
		$sql = "SELECT * FROM `group` WHERE active != 0";
		$query = $db->query($sql);
		while ($oneGroup = $query->fetch_assoc()) {
			$selectedText = "";
			if ($oneGroup["id"] == $selectedGroupID) {
				$selectedText = ' selected="selected" ';
			}
			echo '<option value="'.$oneGroup["id"].'"'.$selectedText.'>'.$oneGroup["name"].'</option>';
		}
	}
	
	/**
	 * Prints the dropdown content of all groups from a user
	 * @param <Int> $userId Selected user
	 */
	public function printGroupsOfUser($userId) {
		$db = new DB();
		$db->connect();
		$userId = $db->esc($userId);
		
		$sql = "SELECT * FROM user_group
					INNER JOIN `group` ON `group`.id = user_group.group_id
				WHERE user_id = " . $userId;
		$query = $db->query($sql);
		while ($oneGroup = $query->fetch_assoc()) {
			echo '<a href="group.php?g=' . $oneGroup["id"] . '"><i class="fa fa-users"></i> ' . $oneGroup["name"] . '</a><br />';
		}
	}
}