<?php
include_once 'db.php';
include_once dirname(__FILE__).'/permissionDA.php';
include_once dirname(__FILE__).'/../logger.php';


class ComponentDA { 
	/**
	 * Returns all components of a specified project
	 * @param <int> $projectId ID of the current project
	 * @return <dbObj> Reference to all components of the project
	 */
	public function getComponents ($projectId) {
		$db = new DB();
		$db->connect();
		
		$projectId = $db->esc($projectId);
		
		$sql = "SELECT * FROM component WHERE project_id = $projectId AND active != 0";
		return $db->query($sql);
	}
	/**
	 * Create a new Component
	 * @param <String> $name Name of the new Component
	 * @param <String> $description Description of the new Component
	 * @param <int> $projectId The project that will be assigned with the new component
	 */
	public function createComponent ($name, $description, $projectId) {
		$db = new DB();
		$db->connect();
		
		$name = $db->esc($name);
		$description = $db->esc($description);
		$projectId = $db->esc($projectId);
		
		$permDA = new PermissionDA();
		if ($permDA->isAdminOnProjectAllowed($projectId)) {
			$sql = "INSERT INTO `component` (`name`, `description`, `project_id`, `active`)
					VALUES ('$name', '$description', $projectId, '1');";
			$db->query($sql);
			Logger::info("Component Created for Project { id = $projectId, name = $name}", null);
		}
		else {
			$permDA->echoPermissionDeniedAndDie();
		}	
	}
	
	/**
	 * Change the name of a component
	 * @param <int> $componentid Component to change
	 * @param <String> $newName New name of the component
	 * @param <int> $projectId project of the component (for checking permissions)
	 */
	public function updateName ($componentid, $newName, $projectId) {
		$db = new DB();
		$db->connect();
		
		$componentid = $db->esc($componentid);
		$newName = $db->esc($newName);
		
		$permDA = new PermissionDA();
		if ($permDA->isAdminOnProjectAllowed($projectId)) {
			$sql = "UPDATE `component` SET `name`='$newName' WHERE `id`='$componentid'";
			$db->query($sql);
		}
		else {
			$permDA->echoPermissionDeniedAndDie();
		}
	}
	
	/**
	 * Deactivate a component
	 * @param <int> $componentId ID of the component to deactivate
	 * @param <int> $projectId project of the component (just for checking permissions)
	 */
	public function deactivateComponent ($componentId, $projectId) {
		$db = new DB();
		$db->connect();
		
		$componentid = $db->esc($componentId);
		$projectId = $db->esc($projectId);
		
		$permDA = new PermissionDA();
		if ($permDA->isAdminOnProjectAllowed($projectId)) {
			$sql = "UPDATE `component` SET `active`= 0 WHERE `id`='$componentid'";
			$db->query($sql);
			Logger::info("Component Deactivated for Project { id = $projectId, component = $componentId}", null);
		}
		else {
			$permDA->echoPermissionDeniedAndDie();
		}
	}
}