<?php
include_once 'db.php';
include_once dirname(__FILE__).'/groupDA.php';
include_once dirname(__FILE__).'/../logger.php';

class ProjectDA { 
	public function printAllProjects ($reallyAll = false) {
		$db = new DB();
		$db->connect();
		
		if ($reallyAll) {
			$getAllProjSql = "SELECT * FROM project";
		}
		else {
			$getAllProjSql = "SELECT * FROM project WHERE active != 0";
		}
		
		
		$allProjQuery = $db->query($getAllProjSql);
		while ($oneProj = $allProjQuery->fetch_assoc()) {
			
			$getAdminSql = "SELECT * FROM `group` WHERE id = " . $oneProj["group_admin"];
			$getWriteSql = "SELECT * FROM `group` WHERE id = " . $oneProj["group_write"];
			$getReadSql  = "SELECT * FROM `group` WHERE id = " . $oneProj["group_read"];
			
			$getAdminQuery = $db->query($getAdminSql)->fetch_assoc();
			$getWriteQuery = $db->query($getWriteSql)->fetch_assoc();
			$getReadQuery = $db->query($getReadSql)->fetch_assoc();
			
			$deactivatedText = "";
			if ($oneProj["active"] == 0) {
				$deactivatedText = ' class="danger" ';
			}
			
			echo '<tr'.$deactivatedText.'>
					<td><a href="../project.php?p='.$oneProj["id"].'">'.$oneProj["key"].'</a></td>
					<td>'.$oneProj["name"].'</td>
					<td>'.$oneProj["description"].'</td>
					<td>
						Admin: <a href="group.php?g='.$getAdminQuery["id"].'">'.$getAdminQuery["name"].'</a> <br />
						Write: <a href="group.php?g='.$getWriteQuery["id"].'">'.$getWriteQuery["name"].'</a> <br />
						Read : <a href="group.php?g='.$getReadQuery["id"].'">'.$getReadQuery["name"].'</a> <br />
					</td>
					<td><a href="../version.php?p='.$oneProj["id"].'">'.$this->getNewestVersionOfProject($oneProj["id"]).'</a></td>
					<td>
						<form action="project.php?" method="get" >
						 	<input type="hidden" name="p" value="'.$oneProj["id"].'" />
							<button type="submit" class="btn btn-default btn-sm" ><i class="fa fa-pencil"></i> edit</button>
						 	<a href="../projectmanager.php?p='.$oneProj["id"].'" class="btn btn-default btn-sm" style="margin-top: 5px;" ><i class="fa fa-tachometer"></i> Project Settings</a>
						</form>
					</td>
				  </tr>';
		}
	}
	
	public function getProject ($projectID) {
		$db = new DB();
		$db->connect();
		
		$projectID = $db->esc($projectID);
		$sql = "SELECT * FROM project WHERE active=1 AND id=".$projectID;
		$query = $db->query($sql);
		
		return $query->fetch_assoc();
	}
	
	public function getProjectOnAdmin ($projectID) {
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
	
	public function isProjectActive($projectId) {
		$db = new DB();
		$db->connect();
	
		$projectId = $db->esc($projectId);
		$query = $db->query("SELECT active FROM project WHERE project.id=$projectId");
		$result = $query->fetch_assoc();
		if ($result["active"] == 1) {
			return true;
		}
		return false;
	}
	
	public function updateGeneral($projectID, $name, $description) {
		$db = new DB();
		$db->connect();
		
		$name = $db->esc($name);
		$description = $db->fixDoubleSpace($db->esc($description));
		$projectID = $db->esc($projectID);
		
		$sql = "UPDATE project SET `name`= '$name', description = '$description'
				WHERE id = $projectID";
		Logger::info("Update General Settings for Project { id = $projectID, newName=$name, desc=$description }", null);
		$db->query($sql);
	}
	
	public function updateGroups ($projectID, $adminGroupID, $writeGroupID, $readGroupID) {
		$db = new DB();
		$db->connect();
		
		$adminGroupID = $db->esc($adminGroupID);
		$writeGroupID = $db->esc($writeGroupID);
		$readGroupID = $db->esc($readGroupID);
		$projectID = $db->esc($projectID);
		
		$sql = "UPDATE project SET group_admin = '$adminGroupID', group_write = '$writeGroupID', group_read = '$readGroupID'
				WHERE id = $projectID";
		Logger::info("Update Permisson Groups for Project { id = $projectID }", null);
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
	
	public function deactivateProject($projectId) {
		$db = new DB();
		$db->connect();
	
		$projectId = $db->esc($projectId);
		$db->query("UPDATE project SET active=0 WHERE project.id=$projectId");
		Logger::info("Project { id = $projectId } deactivated", null);
	}
	
	public function activateProject($projectId) {
		$db = new DB();
		$db->connect();
	
		$projectId = $db->esc($projectId);
		$db->query("UPDATE project SET active=1 WHERE project.id=$projectId");
		Logger::info("Project { id = $projectId } activated", null);
	}
	
	public function createProject ($key, $name, $description, $groupAdmID, $groupWriteID, $groupReadID) {
		$db = new DB();
		$db->connect();
		
		$key = $db->esc($key);
		$name = $db->esc($name);
		$description = $db->fixDoubleSpace($db->esc($description));
		$groupAdmID = $db->esc($groupAdmID);
		$groupWriteID = $db->esc($groupWriteID);
		$groupReadID = $db->esc($groupReadID);
		
		$sql = "INSERT INTO project (`key`, `name`, `description`, `active`, `group_admin`, `group_write`, `group_read`, `meta_creatorID`, `meta_createDate`) 
				VALUES ('$key', '$name', '$description', 1, '$groupAdmID', '$groupWriteID', '$groupReadID', '".$_SESSION['nobug'.RANDOMKEY.'userId']."', '".$db->toDate(time())."')";
		$query = $db->query($sql);
		Logger::info("New Project { name = $name, desc = $description } created", null);
	}
	
	public function printProjectsOnMainPage () {
		$db = new DB();
		$db->connect();
		
		include_once dirname(__FILE__).'/permissionDA.php';
		$permissionDA = new PermissionDA();
		$allProjQuery = $permissionDA->getAllAllowedProjects($_SESSION['nobug'.RANDOMKEY.'userId']);
		if ($allProjQuery != null) {
			while ($oneProj = $allProjQuery->fetch_assoc()) {
				$sql = "SELECT * FROM task WHERE project_id = ".$oneProj["id"] . " AND active=1";
				$taskcount = $db->query($sql)->num_rows;
				echo '<a href="project.php?p='.$oneProj["id"].'" class="list-group-item"><h4>'.$oneProj["name"].' ('.$oneProj["key"].')</h4>'.$oneProj["description"].' <span class="badge pull-right">'.$taskcount.'</span></a>';
			}
		}
		else {
			echo '<p class="list-group-item" >No Projects found...</p>';
		}
	}
	
	public function getVersionsOfProject ($projectId, $released) {
		$db = new DB();
		$db->connect();
		
		$projectId = $db->esc($projectId);
		$releasedString = 0;
		if ($released) {
			$releasedString = 1;
		}
		
		$sql = "SELECT * FROM `version` WHERE project_id = " . $projectId . " AND isReleased = " . $releasedString . " ORDER BY doneDate";
		return $db->query($sql);
	}
	
	public function getNewestVersionOfProject ($projectId) {
		$db = new DB();
		$db->connect();
		
		$projectId = $db->esc($projectId);
				
		$sql = "SELECT * FROM `version` 
				WHERE project_id = ".$projectId."
				ORDER BY isReleased, doneDate
				LIMIT 1";
		$result = $db->query($sql);
		$version = "no version available";
		if ($result->num_rows != 0) {
			$dbversion = $result->fetch_assoc();
			$version = $dbversion["name"];
		}
		return $version;
	}
	
	public function createNewVersionForProject ($projectId, $versionName, $description, $isReleased, $releaseDay = null) {
		$db = new DB();
		$db->connect();
		
		$projectId = $db->esc($projectId);
		$versionName = $db->esc($versionName);
		$description = $db->esc($description);
		$isReleased = $db->esc($isReleased);
		if ($releaseDay != null) {
			$releaseDay = "'".$db->esc($releaseDay)."'";
		}
		else {
			$releaseDay = "null";
		}
		
		$sql = "INSERT INTO `version` (`name`, `isReleased`, `doneDate`, `description`, `project_id`) 
		         VALUES ('$versionName', '$isReleased', $releaseDay, '$description', '$projectId');";
		$db->query($sql);
	}
	
	public function editVersion ($versionId, $versionName, $description, $isReleased, $releaseDate = null) {
		$db = new DB();
		$db->connect();
		
		$versionId = $db->esc($versionId);
		$versionName = $db->esc($versionName);
		$description = $db->esc($description);
		$isReleased = $db->esc($isReleased);
		if ($releaseDate != null) {
			$releaseDate = "'".$db->esc($releaseDate)."'";
		}
		else {
			$releaseDate = "null";
		}
		
		$sql = "UPDATE `version` SET `name`='$versionName', `isReleased`='$isReleased', 
				     `doneDate`=$releaseDate, `description`='$description' WHERE `id`='$versionId'";
		$db->query($sql);
	}
	
	public function deleteVersion ($versionId) {
		$db = new DB();
		$db->connect();
	
		$versionId = $db->esc($versionId);
	
		$sql = "DELETE FROM `version` WHERE `id`='$versionId'";
		$db->query($sql);
	}
}