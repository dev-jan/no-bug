<?php
include_once 'db.php';
include_once dirname(__FILE__).'/permissionDA.php';
include_once dirname(__FILE__).'/taskpropDA.php';

class TaskDA {
	public function getTaskByID($absoluteId) {
		$db = new DB();
		$db->connect();
		
		$absoluteId = $db->esc($absoluteId);
		
		$sql="SELECT task.id AS id, task.summary AS summary, task.description AS description, 
				     task.createDate AS createDate, status.name AS statusname, status.id AS status_id,
				     tasktype.name AS tasktypname, `user`.prename AS prename, `user`.surname AS surname,
					 task.priority AS priority, `user`.id AS assigneeId, tasktype.id AS tasktypId,
					 project.name AS projectname,
					 project.id AS projectId, project.key AS projectkey, creator.prename AS cPrename, creator.surname AS cSurname
				FROM task
				INNER JOIN status ON task.status_id = status.id
				INNER JOIN tasktype ON task.tasktype_id = tasktype.id
				LEFT JOIN `user` ON task.assignee_id = `user`.id
				INNER JOIN `user` AS `creator` ON `creator`.id = task.creator_id
				INNER JOIN project ON task.project_id = project.id
				WHERE task.active=1 AND task.id = ".$absoluteId;
		return $db->query($sql);
	}
	
	public function getTasksQueryByProjectID ($projectID, $shownMenu) {
		$db = new DB();
		$db->connect();
		
		$projectID = $db->esc($projectID);
		$taskpropDA = new TaskpropDA();
		
		if ($shownMenu == "all") {
			$sql = "SELECT task.id, task.summary, task.description, task.active, `status`.name, 
						`status`.color FROM task
					INNER JOIN `status` ON task.status_id = `status`.id
					WHERE project_id = ".$projectID . " AND task.active=1
					ORDER BY task.id DESC";
			return $db->query($sql);
		}
		
		if ($shownMenu == "myopen") { //IMPLEMENT!!!!!!
			$userId = $_SESSION['nobug'.RANDOMKEY.'userId'];
			$sql = "SELECT task.id, task.summary, task.description, task.active, `status`.name, 
						`status`.color FROM task
					INNER JOIN `status` ON task.status_id = `status`.id
					WHERE project_id = ".$projectID . " AND task.active=1 AND `status`.isDone = 0 AND `task`.assignee_id = $userId
					ORDER BY task.id DESC";
			return $db->query($sql);
		}
		
		if ($shownMenu == "open") {
			$sql = "SELECT task.id, task.summary, task.description, task.active, `status`.name,
						`status`.color FROM task
					INNER JOIN `status` ON task.status_id = `status`.id
					WHERE project_id = ".$projectID . " AND task.active=1 AND `status`.isDone = 0
					ORDER BY task.id DESC";
			return $db->query($sql);
		}
		
		if ($shownMenu == "closed") {
			$sql = "SELECT task.id, task.summary, task.description, task.active, `status`.name,
						`status`.color FROM task
					INNER JOIN `status` ON task.status_id = `status`.id
					WHERE project_id = ".$projectID . " AND task.active=1 AND `status`.isDone = 1
					ORDER BY task.id DESC";
			return $db->query($sql);
		}
		
		if ($shownMenu == "unassigned") {
			$sql = "SELECT task.id, task.summary, task.description, task.active, `status`.name,
						`status`.color FROM task
					INNER JOIN `status` ON task.status_id = `status`.id
					WHERE project_id = ".$projectID . " AND task.active=1 AND task.assignee_id is null
					ORDER BY task.id DESC";
			return $db->query($sql);
		}
		return null;
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
				VALUES ('$summary', '$description', '$status', '$project', '".$_SESSION['nobug'.RANDOMKEY.'userId']."',
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
				VALUES ('$taskId', 'comment', '".$db->toDate(time())."', '$value', '".$_SESSION['nobug'.RANDOMKEY.'userId']."')";
		$db->query($sql);
	}
	
	public function printProjectSelect ($selectedProject) {
		$db = new DB();
		$db->connect();
				
		$permissionDA = new PermissionDA();
		$allowedProjects = $permissionDA->getAllAllowedProjects($_SESSION['nobug'.RANDOMKEY.'userId']);
		
		while ($oneRow = $allowedProjects->fetch_assoc()) {
			$selectedText = "";
			if ($selectedProject == $oneRow["id"]) {
				$selectedText = ' selected="selected" ';
			}
			echo '<option value="'.$oneRow["id"].'" '.$selectedText.'>'.$oneRow["name"].'</option>';
		}
	}
	
	public function printAssigneeSelect ($selectedAssignee, $projectId) {
		$db = new DB();
		$db->connect();
		
		$permissionDA = new PermissionDA();
		$users = $permissionDA->getUsersOfAProject($projectId);
		
		echo '<option value="0">-- nobudy --</option>';
		$counter = count($users);
		for ($x = 0; $x < $counter; $x++)
		{
			$oneRow = $users[$x];
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
	
	
	public function getOpenAssignedToMe () {
		$db = new DB();
		$db->connect();
		
		$openstatusTest = "";
		$openStatus = "SELECT * FROM `status` WHERE isDone = 0 AND active != 0";
		$openQuery = $db->query($openStatus);
		while ($oneStatus = $openQuery->fetch_assoc()) {
			if ($openstatusTest == "") {
				$openstatusTest = $oneStatus["id"];
			}
			else {
				$openstatusTest = $openstatusTest . ", " . $oneStatus["id"];
			}
		}
		
		$sql = "SELECT task.id, task.summary, project.key, `status`.color, `status`.name AS status FROM `no-bug`.task 
					INNER JOIN project ON project.id = task.project_id
					INNER JOIN `status` ON `status`.id = task.status_id		
				WHERE status_id IN ($openstatusTest) AND task.active !=0 AND assignee_id = " . $_SESSION['nobug'.RANDOMKEY.'userId'] . "
				ORDER BY project.name, task.id DESC 
				LIMIT 10";
		return $db->query($sql);
	}
	
	public function updateAssignee($taskId, $newAssigneeId) {
		$db = new DB();
		$db->connect();
		
		$taskId = $db->esc($taskId);
		$newAssigneeId = $db->esc($newAssigneeId);
		
		$sql = "UPDATE `task` SET `assignee_id`='$newAssigneeId' WHERE `id`='$taskId'";
		$db->query($sql);
	}
	
	public function deleteTask ($taskId) {
		$db = new DB();
		$db->connect();
		
		$taskId = $db->esc($taskId);
		
		$sql = "UPDATE `task` SET `active`='0' WHERE `id`=".$taskId;
		$db->query($sql);
	}
}