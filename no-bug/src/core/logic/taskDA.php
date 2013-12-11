<?php
include_once 'db.php';

class TaskDA {
	public function getTaskByID($absoluteId) {
		$db = new DB();
		$db->connect();
		
		$absoluteId = $db->esc($absoluteId);
		
		$sql="SELECT task.id AS id, task.summary AS summary, task.description AS description, 
				     task.createDate AS createDate, status.name AS statusname, status.id AS status_id,
				     tasktype.name AS tasktypname, `user`.prename AS prename, `user`.surname AS surname,
					 task.priority AS priority, `user`.id AS assigneeId, tasktype.id AS tasktypId,
					 project.id AS projectId, project.key AS projectkey
				FROM task
				INNER JOIN status ON task.status_id = status.id
				INNER JOIN tasktype ON task.tasktype_id = tasktype.id
				LEFT JOIN `user` ON task.assignee_id = `user`.id
				INNER JOIN project ON task.project_id = project.id
				WHERE task.id = ".$absoluteId;
		$query = $db->query($sql);	
		return $query->fetch_assoc();
	}
	
	public function getTasksQueryByProjectID ($projectID) {
		$db = new DB();
		$db->connect();
		
		$projectID = $db->esc($projectID);
		
		$sql = "SELECT task.id, task.summary, task.description, task.active, `status`.name FROM task 
					INNER JOIN `status` ON task.status_id = `status`.id
				WHERE project_id = ".$projectID . 
				" ORDER BY task.id DESC";
		return $db->query($sql);
	}
	
	public function createTask ($summary, $description, $project, $assignee, $type, $priority, $status) {
		$db = new DB();
		$db->connect();
		
		$summary = $db->esc($summary);
		$description = $db->esc($description);
		$project = $db->esc($project);
		$assignee = $db->esc($assignee);
		$type = $db->esc($type);
		$priority = $db->esc($priority);
		$status = $db->esc($status);
		
		if ($assignee == 0) {
			$assignee = "null";
		}
		else {
			$assignee = "'".$assignee."'";
		}
		
		$sql = "INSERT INTO `task` (`summary`, `description`, `status_id`, `project_id`, `creator_id`, 
				      `assignee_id`, `createDate`, `tasktype_id`, `priority`, `active`) 
				VALUES ('$summary', '$description', '$status', '$project', '".$_SESSION["userId"]."',
				$assignee, '".$db->toDate(time())."', '$type', '$priority', '1');";
		$db->query($sql);	
	}
	
	public function updateTask($taskid, $summary, $project, $assignee, $type, $priority, $status, $description) {
		$db = new DB();
		$db->connect();
		
		$taskid = $db->esc($taskid);
		$summary = $db->esc($summary);
		$description = $db->esc($description);
		$project = $db->esc($project);
		$assignee = $db->esc($assignee);
		$type = $db->esc($type);
		$priority = $db->esc($priority);
		$status = $db->esc($status);
		
		if ($assignee == 0) {
			$assignee = "null";
		}
		else {
			$assignee = "'".$assignee."'";
		}
		
		$sql = "UPDATE `task` 
				SET `summary`='$summary', `description`='$description', `status_id`='$status', 
				`project_id`='$project', `assignee_id`=$assignee, `tasktype_id`='$type', 
				`priority`='2' WHERE `id`='$taskid';
		";
		$db->query($sql);
	}
	
	public function createComment ($taskId, $value) {
		$db = new DB();
		$db->connect();
		
		$taskId = $db->esc($taskId);
		$value = $db->esc($value);
		
		$sql = "INSERT INTO `no-bug`.`changelog` 
				(`task_id`, `changedField`, `date`, `value`, `user_id`) 
				VALUES ('$taskId', 'comment', '".$db->toDate(time())."', '$value', '".$_SESSION["userId"]."')";
		$db->query($sql);
	}
	
	public function printProjectSelect ($selectedProject) {
		$db = new DB();
		$db->connect();
		
		$sql = "SELECT * FROM project WHERE active != 0";
		$query = $db->query($sql);
		
		while ($oneRow = $query->fetch_assoc()) {
			$selectedText = "";
			if ($selectedProject == $oneRow["id"]) {
				$selectedText = ' selected="selected" ';
			}
			echo '<option value="'.$oneRow["id"].'" '.$selectedText.'>'.$oneRow["name"].'</option>';
		}
	}
	
	public function printAssigneeSelect ($selectedAssignee) {
		$db = new DB();
		$db->connect();
	
		$sql = "SELECT * FROM `user` WHERE active != 0";
		$query = $db->query($sql);
		
		echo '<option value="0">-- nobudy --</option>';
		while ($oneRow = $query->fetch_assoc()) {
			$selectedText = "";
			if ($selectedAssignee == $oneRow["id"]) {
				$selectedText = ' selected="selected" ';
			}
			echo '<option value="'.$oneRow["id"].'" '.$selectedText.'>'.$oneRow["prename"].' '.$oneRow["surname"].'</option>';
		}
	}
	
	public function printTypeSelect ($selectedType) {
		$db = new DB();
		$db->connect();
	
		$sql = "SELECT * FROM tasktype";
		$query = $db->query($sql);
	
		while ($oneRow = $query->fetch_assoc()) {
			$selectedText = "";
			if ($selectedType == $oneRow["id"]) {
				$selectedText = ' selected="selected" ';
			}
			echo '<option value="'.$oneRow["id"].'" '.$selectedText.'>'.$oneRow["name"].'</option>';
		}
	}
	
	public function printStatusSelect ($selectedStatus) {
		$db = new DB();
		$db->connect();
	
		$sql = "SELECT * FROM status";
		$query = $db->query($sql);
	
		while ($oneRow = $query->fetch_assoc()) {
			$selectedText = "";
			if ($selectedStatus == $oneRow["id"]) {
				$selectedText = ' selected="selected" ';
			}
			echo '<option value="'.$oneRow["id"].'" '.$selectedText.'>'.$oneRow["name"].'</option>';
		}
	}
	
	public function printComments($taskid) {
		$db = new DB();
		$db->connect();
		
		$taskid = $db->esc($taskid);
		
		$sql = "SELECT * FROM changelog
				INNER JOIN `user` ON `user`.id = changelog.user_id
				WHERE changelog.task_id = " . $taskid . "  AND changelog.changedField = 'comment'";
		$query = $db->query($sql);
		
		while ($oneRow = $query->fetch_assoc()) {
			echo '
				<div class="media">
					<a class="pull-left" href="#">
						<img class="media-object img-circle" src="style/default_profil.png" alt="person" height="64" width="64">
					</a>
					<div class="media-body">
						<h4 class="media-heading">'.$oneRow["prename"].' '.$oneRow["surname"].'</h4>
						'.$oneRow["value"].'
					</div>
				</div>';
		}
	}
	
	public function printPrioritySelect () {
		echo '  <option value="1">1</option>
				<option value="2">2</option>
				<option value="3">3</option>
				<option value="4">4</option>
				<option value="5">5</option>';
	}
}