<?php
include_once 'db.php';
include_once dirname(__FILE__).'/groupDA.php';
include_once dirname(__FILE__).'/../logger.php';

/**
 * DataAccess for project releated things (including project versions)
 */
class ProjectDA { 
	/**
	 * Print out all projects on the platform as table rows (used in global administration)
	 * @param <boolean> $reallyAll TRUE: Print out also the deactivated projects
	 */
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
	
	/**
	 * Returns the full project from unique project ID
	 * @param <Int> $projectID
	 * @return <Array> project values in a array
	 */
	public function getProject ($projectID) {
		$db = new DB();
		$db->connect();
		
		$projectID = $db->esc($projectID);
		$sql = "SELECT * FROM project WHERE active=1 AND id=".$projectID;
		$query = $db->query($sql);
		
		return $query->fetch_assoc();
	}
	
	/**
	 * Returns the full project from unique project ID (returns also a project if the project is disabled)
	 * @param <Int> $projectID 
	 */
	public function getProjectOnAdmin ($projectID) {
		$db = new DB();
		$db->connect();
		
		$projectID = $db->esc($projectID);
		$sql = "SELECT * FROM project WHERE id=".$projectID;
		$query = $db->query($sql);
		
		return $query->fetch_assoc();
	}
	
	/**
	 * Print the dropdown content of all active groups
	 * @param <Int> $selectedGroupID Group that will be selected in the dropdown
	 */
	public function printGroupSelect ($selectedGroupID) {
		$groupDA = new GroupDA();
		$groupDA->printGroupSelection($selectedGroupID);
	}
	
	/**
	 * Checks if a project is active or not
	 * @param <Int> $projectId
	 * @return <boolean> true if the project is active
	 */
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
	
	/**
	 * Update some general informations about a project
	 * @param <Int> $projectID ID of the Project to change
	 * @param <String> $name (new) Name of the project
	 * @param <String> $description (new) Description of the project
	 */
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
	
	/**
	 * Update the permission groups of a project
	 * @param <Int> $projectID ID of the Project to change
	 * @param <Int> $adminGroupID (new) group with admin permissions on the project
	 * @param <Int> $writeGroupID (new) group with write permissions on the project
	 * @param <Int> $readGroupID (new) group with read permissions on the project
	 */
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
	
	/**
	 * Checks if a project key is already taken
	 * @param <String> $key Project key to check
	 * @return boolean TRUE if the project key is not taken already
	 */
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
	
	/**
	 * Deactivate a project
	 * @param <Int> $projectId ID of the project to deactivate
	 */
	public function deactivateProject($projectId) {
		$db = new DB();
		$db->connect();
	
		$projectId = $db->esc($projectId);
		$db->query("UPDATE project SET active=0 WHERE project.id=$projectId");
		Logger::info("Project { id = $projectId } deactivated", null);
	}
	
	/**
	 * Activate a project
	 * @param <Int> $projectId ID of the project to activate
	 */
	public function activateProject($projectId) {
		$db = new DB();
		$db->connect();
	
		$projectId = $db->esc($projectId);
		$db->query("UPDATE project SET active=1 WHERE project.id=$projectId");
		Logger::info("Project { id = $projectId } activated", null);
	}
	
	/**
	 * Create a new project
	 * @param <Int> $key Key of the new project
	 * @param <String> $name Name of the new project
	 * @param <String> $description description of the new project
	 * @param <Int> $groupAdmID group that will administrate the new project
	 * @param <Int> $groupWriteID group with write access to the new project
	 * @param <Int> $groupReadID group with read only access to the new project
	 */
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
	
	/**
	 * Print out the project of the current logged in user (for the main page)
	 */
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
	
	/**
	 * Get the versions of a project
	 * @param <Int> $projectId Selected project
	 * @param <Boolean> $released show only released versions or only unreleased versions
	 * @return <dbResult> selected versions of a project
	 */
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
	
	/**
	 * Returns the newest version of a project
	 * @param <Int> $projectId selected project
	 * @return <String> Name of the newest version
	 */
	public function getNewestVersionOfProject ($projectId) {
		$db = new DB();
		$db->connect();
		
		$projectId = $db->esc($projectId);
				
		$sql = "SELECT * FROM `version` 
				WHERE project_id = ".$projectId."
				ORDER BY isReleased DESC, doneDate DESC
				LIMIT 1";
		$result = $db->query($sql);
		$version = "no version available";
		if ($result->num_rows != 0) {
			$dbversion = $result->fetch_assoc();
			$version = $dbversion["name"];
		}
		return $version;
	}
	
	/**
	 * Returns a version that matches to the given ID
	 * @param <Int> $versionId unique ID of the version
	 * @return <dbResult> version from the database
	 */
	public function getVersionById ($versionId) {
		$db = new DB();
		$db->connect();
		
		$versionId = $db->esc($versionId);
		
		$sql = "SELECT * FROM `version` WHERE id = $versionId";
		return $db->query($sql);
	}
	
	/**
	 * Create a new version
	 * @param <Int> $projectId project ID of the new version
	 * @param <String> $versionName name of the new version (e.g. v1.2)
	 * @param <String> $description short description of the new version
	 * @param <Boolean> $isReleased TRUE if the version is already released
	 * @param <String>|NULL $releaseDay Date the version will be released or was released
	 */
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
	
	/**
	 * Edit a existing version of a project
	 * @param <Int> $versionId ID of the version to change
	 * @param <String> $versionName (new) name of the version
	 * @param <String> $description (new) description of the version
	 * @param <Boolean> $isReleased is version released or not?
	 * @param <String> $releaseDate date the version will/was be released
	 */
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
	
	/**
	 * Delete a version (cannot be undone!)
	 * @param <Int> $versionId unique ID of the version to delete
	 */
	public function deleteVersion ($versionId) {
		$db = new DB();
		$db->connect();
	
		$versionId = $db->esc($versionId);
	
		$sql = "DELETE FROM `version` WHERE `id`='$versionId'";
		$db->query($sql);
	}
}