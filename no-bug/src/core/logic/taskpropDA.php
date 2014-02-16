<?php
include_once 'db.php';
include_once dirname(__FILE__).'/../logger.php';

class TaskpropDA {
	public function getAllStatus () {
		$db = new DB();
		$db->connect();
		
		$sql = "SELECT * FROM `status` WHERE active = 1;";
		return $db->query($sql);
	}
	
	public function getAllTasktypes () {
		$db = new DB();
		$db->connect();
		
		$sql = "SELECT * FROM `tasktype`;";
		return $db->query($sql);
	}
	
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
	
	public function newTasktyp ($name) {
		$db = new DB();
		$db->connect();
		
		$name = $db->esc($name);
		$sql = "INSERT INTO `tasktype` (`name`) VALUES ('$name')";
		$db->query($sql);
		Logger::info("New Tasktyp { name = $name}", null);
	}
	
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
	
	public function deleteTasktype ($id) {
		$db = new DB();
		$db->connect();
		
		$id = $db->esc($id);
		$sql = "DELETE FROM `tasktype` WHERE `id`='$id'";
		$db->query($sql);
	}
	
	public function deleteStatus ($id) {
		$db = new DB();
		$db->connect();
	
		$id = $db->esc($id);
		$sql = "DELETE FROM `status` WHERE `id`='$id'";
		$db->query($sql);
	}
	
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