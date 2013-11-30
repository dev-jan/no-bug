<?php
include_once 'db.php';
include_once dirname(__FILE__).'/groupDA.php';

class ProjectDA { 
	public function printAllProjects () {
		$db = new DB();
		$db->connect();
		
		$getAllProjSql = "SELECT * FROM project WHERE active != 0";
		
		$allProjQuery = $db->query($getAllProjSql);
		while ($oneProj = $allProjQuery->fetch_assoc()) {
			
			$getAdminSql = "SELECT * FROM `group` WHERE id = " . $oneProj["group_admin"];
			$getWriteSql = "SELECT * FROM `group` WHERE id = " . $oneProj["group_write"];
			$getReadSql  = "SELECT * FROM `group` WHERE id = " . $oneProj["group_read"];
			
			$getAdminQuery = $db->query($getAdminSql)->fetch_assoc();
			$getWriteQuery = $db->query($getWriteSql)->fetch_assoc();
			$getReadQuery = $db->query($getReadSql)->fetch_assoc();
			
			echo '<tr>
					<td>'.$oneProj["key"].'</td>
					<td>'.$oneProj["name"].'</td>
					<td>'.$oneProj["description"].'</td>
					<td>
						Admin: <a href="group.php?g='.$getAdminQuery["id"].'">'.$getAdminQuery["name"].'</a> <br />
						Write: <a href="group.php?g='.$getWriteQuery["id"].'">'.$getWriteQuery["name"].'</a> <br />
						Read : <a href="group.php?g='.$getReadQuery["id"].'">'.$getReadQuery["name"].'</a> <br />
					</td>
					<td>'.$oneProj["version"].'</td>
					<td><form action="project.php?" method="get" >
							<input type="hidden" name="p" value="'.$oneProj["id"].'" />
							<button type="submit" class="btn btn-default btn-sm" >edit</button></form></td>
				  </tr>';
		}
	}
	
	public function getProject ($projectID) {
		$db = new DB();
		$db->connect();
		
		$projectID = $db->esc($projectID);
		$sql = "SELECT * FROM project WHERE id=".$projectID;
		$query = $db->query($sql);
		
		return $query->fetch_assoc();
	}
	
	public function printGroupSelect ($selectedGroupID) {
		$groupDA = new GroupDA();
		$groupDA->printGroupSelection($selectedGroupID);
	}
	
	public function updateGeneral($groupID, $name, $description, $version) {
		$db = new DB();
		$db->connect();
		
		$name = $db->esc($name);
		$description = $db->esc($description);
		$version = $db->esc($version);
		$groupID = $db->esc($groupID);
		
		$sql = "UPDATE project SET `name`= '$name', description = '$description', version = '$version'
				WHERE id = $groupID";
		$db->query($sql);
	}
	
	public function updateGroups ($groupID, $adminGroupID, $writeGroupID, $readGroupID) {
		$db = new DB();
		$db->connect();
		
		$adminGroupID = $db->esc($adminGroupID);
		$writeGroupID = $db->esc($writeGroupID);
		$readGroupID = $db->esc($readGroupID);
		$groupID = $db->esc($groupID);
		
		$sql = "UPDATE project SET group_admin = '$adminGroupID', group_write = '$writeGroupID', group_read = '$readGroupID'
				WHERE id = $groupID";
		$db->query($sql);
	}
	
	public function checkProjectKey ($key) {
		$db = new DB();
		$db->connect();
		
		$key = $db->esc($key);
		
		$sql = "SELECT * FROM project WHERE `key` ='$key'";
		$query = $db->query($sql);
		if ($query->num_rows == 0) {
			return true;
		}
		else {
			return false;
		}
	}
	
	public function createProject ($key, $name, $description, $version, $groupAdmID, $groupWriteID, $groupReadID) {
		$db = new DB();
		$db->connect();
		
		$key = $db->esc($key);
		$name = $db->esc($name);
		$description = $db->esc($description);
		$version = $db->esc($version);
		$groupAdmID = $db->esc($groupAdmID);
		$groupWriteID = $db->esc($groupWriteID);
		$groupReadID = $db->esc($groupReadID);
		
		$sql = "INSERT INTO project (`key`, `name`, `description`, `version`, `active`, `group_admin`, `group_write`, `group_read`, `meta_creatorID`, `meta_createDate`) 
				VALUES ('$key', '$name', '$description', '$version', 1, '$groupAdmID', '$groupWriteID', '$groupReadID', '".$_SESSION["userId"]."', '14.11.13')";
		$query = $db->query($sql);
	}
	
	public function printProjectsOnMainPage () {
		$db = new DB();
		$db->connect();
		
		$getAllProjSql = "SELECT * FROM project WHERE active != 0";
		
		$allProjQuery = $db->query($getAllProjSql);
		while ($oneProj = $allProjQuery->fetch_assoc()) {
			$sql = "SELECT * FROM task WHERE project_id = ".$oneProj["id"];
			$taskcount = $db->query($sql)->num_rows;
			echo '<a href="project.php?p='.$oneProj["id"].'" class="list-group-item"><h4>'.$oneProj["name"].' ('.$oneProj["key"].')</h4>'.$oneProj["description"].' <span class="badge pull-right">'.$taskcount.'</span></a>';
		}
	}
}