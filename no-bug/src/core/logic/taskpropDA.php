<?php
include_once 'db.php';

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
	}
	
	public function updateStatus ($id, $name, $color) {
		$db = new DB();
		$db->connect();
		
		$id = $db->esc($id);
		$name = $db->esc($name);
		$color = $db->esc($color);
		
		$sql = "UPDATE `status` SET `name`='$name', `color`='#$color'   ".
				"WHERE `id`='$id'";
		$db->query($sql);
	}
}