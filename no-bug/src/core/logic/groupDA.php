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
		
		$childUsersSql = "SELECT user.id, user.username, user.prename, user.surname FROM user_group
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
		echo '<option id="0">Select Group</option>';
		$db = new DB();
		$db->connect();
		
		$sql = "SELECT * FROM `group` WHERE `group`.active != 0";
		$query = $db->query($sql);
		while ($oneGroup = $query->fetch_assoc()) {
			echo '<option id="'.$oneGroup["id"].'">'.$oneGroup["name"].'</option>';
		}
	}
}