<?php
include_once 'db.php';

class GroupDA {
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
			echo '	<tr>
						<td>'.$oneGroup["name"].'</td>
						<td>'.$this->printParentEntitys($oneGroup["id"]).'</td>
						<td>'.$this->printChildEntitys($oneGroup["id"]).'</td>
						<td><form action="group.php?" method="GET">
							<input type="hidden" name="g" value="'.$oneGroup['id'].'" />
							<button type="submit" class="btn btn-default btn-sm">edit</button></form></td>
					</tr>';
		}
	}
	
	private function printParentEntitys($groupID) {
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
				$return = $return . '<a href="group.php?g='.$oneParent["parentID"].'">'.$oneParent["parentName"].' (Group)</a><br />';
			}
		}
		return $return;
	}
	
	private function printChildEntitys($groupID) {
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
				$return = $return . '<a href="group.php?g='.$oneChild["childID"].'">'.$oneChild["childName"].' (Group)</a><br />';
			} 
		}
		
		$childUsersSql = "SELECT user.id AS id, user.username, user.prename, user.surname FROM user_group
							LEFT OUTER JOIN user ON user.id = user_group.user_id
						  WHERE user_group.group_id = $groupID";
		$childUserQuery = $db->query($childUsersSql);
		while ($oneUser = $childUserQuery->fetch_assoc()) {
			if ($oneUser["id"]) {
				$return = $return . '<a href="user.php?u="'.$oneUser["id"].'">'.$oneUser["prename"].' '.$oneUser["surname"].' (User)</a><br />';
			}
		}
		
		return $return;
	}
	
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
	
	public function printUserSelect() {
		echo '<option id="0">--- Select User ---</option>';
		$db = new DB();
		$db->connect();
	
		$sql = "SELECT * FROM `user` WHERE `user`.activ != 0";
		$query = $db->query($sql);
		while ($oneUser = $query->fetch_assoc()) {
			echo '<option value="'.$oneUser["id"].'">'.$oneUser["username"].'</option>';
		}
	}
	
	public function printGroupMembersAsTable ($groupID) {
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
										<td>'.$oneMember["childName"].' (Group)</td>
										<td><form action="?g='.$groupID.'" method="post">
												<input type="hidden" name="groupId" value="'.$oneMember["childID"].'" />
												<button type="submit" class="btn btn-danger" >Remove</button>
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
										<td>'.$oneMember["username"].' (User)</td>
										<td><form action="?g='.$groupID.'" method="post">
												<input type="hidden" name="userId" value="'.$oneMember["id"].'" />
												<button type="submit" class="btn btn-danger" >Remove</button>
											</form></td>
									 </tr>';
			}
		}
		
		return $return;
	}
	
	public function updateGroupname ($groupId, $newGroupname) {
		$db = new DB();
		$db->connect();
		
		$groupId = $db->esc($groupId);
		$newGroupname = $db->esc($newGroupname);
		$updateSql = "UPDATE `group` SET name='$newGroupname'
						WHERE id=$groupId";
		$db->query($updateSql);
	}
	
	public function addGroupmember ($parentID, $childID) {
		$db = new DB();
		$db->connect();
		
		$parentID = $db->esc($parentID);
		$childID = $db->esc($childID);
		
		$insertSql = "INSERT INTO `group_group` (`group_child`, `group_parent`) VALUES ('".$childID."', '".$parentID."');";
		$db->query($insertSql);
	}
	
	public function addUsermember ($parentID, $userID) {
		$db = new DB();
		$db->connect();
	
		$parentID = $db->esc($parentID);
		$childID = $db->esc($userID);
	
		$insertSql = "INSERT INTO `user_group` (`user_id`, `group_id`) VALUES ('".$childID."', '".$parentID."');";
		$db->query($insertSql);
	}
	
	public function removeGroupmember ($parentID, $childID) {
		$db = new DB();
		$db->connect();
		
		$parentID = $db->esc($parentID);
		$childID = $db->esc($childID);
		
		$removeSql = "DELETE FROM `group_group` WHERE `group_child`='".$childID."' AND `group_parent`='".$parentID."';";
		$db->query($removeSql);
	}
	
	public function removeUsermember ($parentID, $childID) {
		$db = new DB();
		$db->connect();
	
		$parentID = $db->esc($parentID);
		$childID = $db->esc($childID);
	
		$removeSql = "DELETE FROM `user_group` WHERE `user_id`='".$childID."' AND `group_id`='".$parentID."';";
		$db->query($removeSql);
	}
	
	public function addGroup ($groupname) {
		$db = new DB();
		$db->connect();
		
		$groupname = $db->esc($groupname);
		$currentUserId = $db->esc($_SESSION["userId"]);
		
		$insertSql = "INSERT INTO `no-bug`.`group` (`name`, `active`, `meta_creator_id`, `meta_creatDate`)
						 VALUES ('$groupname', '1', '$currentUserId', '".$this->toDate(time())."')";
		
		$db->query($insertSql);
	}
	
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
	
	public function toDate($unixTimestamp){
		return date("Y-m-d", $unixTimestamp);
	}
}