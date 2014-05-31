<?php
include_once 'db.php';
include_once dirname(__FILE__).'/../logger.php';

/**
 * DataAccess for the Taskproperties (Tasktype & Taskstatus)
 */
class TaskpropDA {
	/**
	 * Return all active status
	 * @return <dbResult> Active Status
	 */
	public function getAllStatus () {
		$db = new DB();
		$db->connect();
		
		$sql = "SELECT * FROM `status` WHERE active = 1;";
		return $db->query($sql);
	}
	
	/**
	 * Return all active tasktypes
	 * @return <dbResult> Active tasktypes
	 */
	public function getAllTasktypes () {
		$db = new DB();
		$db->connect();
		
		$sql = "SELECT * FROM `tasktype`;";
		return $db->query($sql);
	}
	
	/**
	 * Update the name of a tasktype
	 * @param <Int> $id Tasktype to update
	 * @param <String> $name New name of the tasktype
	 */
	public function updateTasktype ($id, $name) {
		$db = new DB();
		$db->connect();
		
		$id = $db->esc($id);
		$name = $db->esc($name);
		
		$sql = "UPDATE `tasktype` SET `name`='$name'  ".
				"WHERE `id`='$id'";
		$db->query($sql);
		Logger::info("Tasktype { id = $id, name = $name } updated", null);
	}
	
	/**
	 * Update the values of a status
	 * @param <Int> $id ID of the status to change
	 * @param <String> $name (new) name of the status
	 * @param <String> $color (new) color of the status
	 * @param <Int> $isDone (new) is this status a donestatus? YES=1 NO=0
	 */
	public function updateStatus ($id, $name, $color, $isDone) {
		$db = new DB();
		$db->connect();
		
		$id = $db->esc($id);
		$name = $db->esc($name);
		$color = $db->esc($color);
		$isDone = $db->esc($isDone);
		
		$sql = "UPDATE `status` SET `name`='$name', `color`='#$color', `isDone`='$isDone'   ".
				"WHERE `id`='$id'";
		$db->query($sql);
		Logger::info("Status { id = $id, name = $name, color = $color} updated", null);
	}
	
	/**
	 * Create a new tasktype
	 * @param <String> $name Name of the new tasktype
	 */
	public function newTasktyp ($name) {
		$db = new DB();
		$db->connect();
		
		$name = $db->esc($name);
		$sql = "INSERT INTO `tasktype` (`name`) VALUES ('$name')";
		$db->query($sql);
		Logger::info("New Tasktyp { name = $name}", null);
	}
	
	/**
	 * Create a new Status
	 * @param <String> $name Name of the new status
	 * @param <String> $color Color of the new status
	 * @param <Int> $isDone YES=1 NO=0
	 */
	public function newStatus ($name, $color, $isDone) {
		$db = new DB();
		$db->connect();
	
		$name = $db->esc($name);
		$color = $db->esc($color);
		$isDone = $db->esc($isDone);
		
		$sql = "INSERT INTO `status` (`name`, `color`, `isDone`, `active`) VALUES ('$name', '$color', '$isDone', '1')";
		$db->query($sql);
		Logger::info("New Status { name = $name, color = $color, isDone = $isDone }", null);
	}
	
	/**
	 * Delete an existing task
	 * @param <Int> $id ID of the tasktype to delete
	 */
	public function deleteTasktype ($id) {
		$db = new DB();
		$db->connect();
		
		$id = $db->esc($id);
		$sql = "DELETE FROM `tasktype` WHERE `id`='$id'";
		$db->query($sql);
	}
	
	/**
	 * Delete an existing status
	 * @param <Int> $id ID of the status to delete
	 */
	public function deleteStatus ($id) {
		$db = new DB();
		$db->connect();
	
		$id = $db->esc($id);
		$sql = "DELETE FROM `status` WHERE `id`='$id'";
		$db->query($sql);
	}
	
	/**
	 * Returns the number of existing tasks by menu
	 * @param <Int> $projectID Selected project
	 * @param <String> $menu (all|myopen|open|closed|unassigned)
	 * @return <Int> number of task of the selected menu
	 */
	public function getNumberOfTasksByMenu ($projectID, $menu) {
		$db = new DB();
		$db->connect();
		
		$projectID = $db->esc($projectID);
		if ($menu == "all") {
			$sql = "SELECT task.id FROM task
					WHERE project_id = ".$projectID . " AND task.active=1";
			return $db->query($sql)->num_rows;
		}
		
		if ($menu == "myopen") { 
			$userId = $_SESSION['nobug'.RANDOMKEY.'userId'];
			$sql = "SELECT task.id FROM task
					INNER JOIN `status` ON task.status_id = `status`.id
					WHERE project_id = ".$projectID . " AND `status`.isDone = 0 AND `task`.assignee_id = $userId AND task.active=1";
			return $db->query($sql)->num_rows;
		}
		
		if ($menu == "open") {
			$sql = "SELECT task.id FROM task
					INNER JOIN `status` ON task.status_id = `status`.id
					WHERE project_id = ".$projectID . " AND `status`.isDone = 0 AND task.active=1";
			return $db->query($sql)->num_rows;
		}
		
		if ($menu == "closed") {
			$sql = "SELECT task.id FROM task
					INNER JOIN `status` ON task.status_id = `status`.id
					WHERE project_id = ".$projectID . " AND `status`.isDone = 1 AND task.active=1 ";
			return $db->query($sql)->num_rows;
		}
		
		if ($menu == "unassigned") {
			$sql = "SELECT task.id FROM task
					INNER JOIN `status` ON task.status_id = `status`.id
					WHERE project_id = ".$projectID . " AND task.assignee_id is null AND task.active=1";
			return $db->query($sql)->num_rows;
		}
		return null;
	}
}