<?php
include_once 'db.php';
include_once dirname(__FILE__).'/permissionDA.php';
include_once dirname(__FILE__).'/taskpropDA.php';
include_once dirname(__FILE__).'/componentDA.php';
include_once dirname(__FILE__).'/projectDA.php';

class TaskDA {
	public function getTaskByID($absoluteId) {
		$db = new DB();
		$db->connect();
		
		$absoluteId = $db->esc($absoluteId);
		
		$sql="SELECT task.id AS id, task.summary AS summary, task.description AS description, 
				     task.createDate AS createDate, status.name AS statusname, status.id AS status_id,
				     tasktype.name AS tasktypname, `user`.prename AS prename, `user`.surname AS surname,
					 task.priority AS priority, `user`.id AS assigneeId, tasktype.id AS tasktypId,
					 project.name AS projectname, `component`.name AS componentName, `component`.id AS componentID,
					 project.id AS projectId, project.key AS projectkey, creator.prename AS cPrename, creator.surname AS cSurname,
					 `version`.name AS versionname, `version`.id AS versionid
				FROM task
				INNER JOIN status ON task.status_id = status.id
				INNER JOIN tasktype ON task.tasktype_id = tasktype.id
				LEFT JOIN `component` ON task.component_id = `component`.id
				LEFT JOIN `version` ON task.version_id = `version`.id
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
		
		$whereclause = "";
		
		if ($shownMenu == "all") {
			$whereclause = "WHERE task.project_id = ".$projectID . " AND task.active=1";
		}
		
		if ($shownMenu == "myopen") {
			$userId = $_SESSION['nobug'.RANDOMKEY.'userId'];
			$whereclause = "WHERE task.project_id = ".$projectID . " AND task.active=1 AND `status`.isDone = 0 AND `task`.assignee_id = $userId";
		}
		
		if ($shownMenu == "open") {
			$whereclause = "WHERE task.project_id = ".$projectID . " AND task.active=1 AND `status`.isDone = 0";
		}
		
		if ($shownMenu == "closed") {
			$whereclause = "WHERE task.project_id = ".$projectID . " AND task.active=1 AND `status`.isDone = 1";
		}
		
		if ($shownMenu == "unassigned") {
			$whereclause = "WHERE task.project_id = ".$projectID . " AND task.active=1 AND task.assignee_id is null";
		}
		
		$basesql = "SELECT task.id, task.summary, task.description, task.active, `status`.name,
		                 assignee.prename AS assigneePrename, assignee.surname AS assigneeSurname, assignee.id AS assigneeID,
						`status`.color, `component`.name AS componentName FROM task
					INNER JOIN `status` ON task.status_id = `status`.id
				    LEFT JOIN `user` AS assignee ON task.assignee_id = assignee.id
				    LEFT JOIN `component` AS `component` ON task.component_id = `component`.id
					$whereclause
					ORDER BY task.id DESC";
		return $db->query($basesql);
	}
	
	/**
	 * Return the tasks that matches to the searchstring (in description or summary of a task)
	 * @param <String> $searchquery String that the user entered
	 * @return <db-result> or null if there are no matches
	 */
	public function getTasksBySearchquery ($searchquery) {
		$db = new DB();
		$db->connect();
		
		$searchquery = $db->esc($searchquery);
		if ($searchquery == "") {
			return null;
		}
		
		$permDA = new PermissionDA();
		$allowedProjectOfUsers = $permDA->getAllAllowedProjects($_SESSION['nobug'.RANDOMKEY.'userId']);
		if ($allowedProjectOfUsers != null) {
			$projectWhere = "(";
			while ($oneProject = $allowedProjectOfUsers->fetch_assoc()) {
				if ($projectWhere == "(") {
					$projectWhere = $projectWhere . $oneProject["id"];
				}
				else {
					$projectWhere = $projectWhere . "," . $oneProject["id"];
				}
			}
			$projectWhere = $projectWhere . ")";
			
			$sql = "SELECT task.id, task.summary, task.description, task.active, `status`.name,
		                 assignee.prename AS assigneePrename, assignee.surname AS assigneeSurname, assignee.id AS assigneeID,
						`status`.color, `component`.name AS componentName, project.`key` AS `key`
					FROM task
					 INNER JOIN `status` ON task.status_id = `status`.id
				     LEFT JOIN `user` AS assignee ON task.assignee_id = assignee.id
				     LEFT JOIN `component` AS `component` ON task.component_id = `component`.id
				     LEFT JOIN `project` AS project ON task.project_id = project.id
					WHERE task.project_id IN $projectWhere AND concat(task.summary, task.description) LIKE '%$searchquery%'
					ORDER BY task.id DESC";
			return $db->query($sql);
		}
		else {
			return null;
		}
	}
	
	public function createTask ($summary, $description, $project, $assignee, $type, $priority, $status, $component, $version) {
		$db = new DB();
		$db->connect();
		
		$summary = $db->esc($summary);
		$description = $db->esc($description);
		$project = $db->esc($project);
		$assignee = $db->esc($assignee);
		$type = $db->esc($type);
		$priority = $db->esc($priority);
		$status = $db->esc($status);
		$component = $db->esc($component);
		$version = $db->esc($version);
		
		if ($assignee == 0) {
			$assignee = "null";
		}
		else {
			$assignee = "'".$assignee."'";
		}
		
		if ($component == 0) {
			$component = "null";
		}
		
		if ($version == 0) {
			$version = "null";
		}
		
		$sql = "INSERT INTO `task` (`summary`, `description`, `status_id`, `project_id`, `creator_id`, 
				      `assignee_id`, `createDate`, `tasktype_id`, `priority`, `active`, `component_id`, `version_id`) 
				VALUES ('$summary', '$description', '$status', '$project', '".$_SESSION['nobug'.RANDOMKEY.'userId']."',
				$assignee, '".$db->toDate(time())."', '$type', '$priority', '1', $component, $version);";
		$db->query($sql);	
	}
	
	public function updateTask($taskid, $summary, $project, $assignee, $type, $priority, $status, $description, $component, $versionId) {
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
		$component = $db->esc($component);
		$versionId = $db->esc($versionId);
		
		if ($assignee == 0) {
			$assignee = "null";
		}
		else {
			$assignee = "'".$assignee."'";
		}
		if ($component == 0) {
			$component = "null";
		}
		if ($versionId == 0) {
			$versionId = "null";
		}
		
		$sql = "UPDATE `task` 
				SET `summary`='$summary', `description`='$description', `status_id`='$status', 
				`project_id`='$project', `assignee_id`=$assignee, `tasktype_id`='$type', 
				`priority`='$priority', `component_id`=$component, `version_id`=$versionId
				WHERE `id`='$taskid';
		";
		$db->query($sql);
	}
	
	public function createComment ($taskId, $value) {
		$db = new DB();
		$db->connect();
		
		$taskId = $db->esc($taskId);
		$value = $db->fixDoubleSpace($db->esc($value));
		
		$sql = "INSERT INTO `comment` 
				(`task_id`, `date`, `value`, `user_id`) 
				VALUES ('$taskId', '".$db->toDate(time())."', '$value', '".$_SESSION['nobug'.RANDOMKEY.'userId']."')";
		$db->query($sql);
	}
	
	public function printProjectSelect ($selectedProject = "") {
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
	
	public function printAssigneeSelect ($selectedAssignee = "", $projectId) {
		$db = new DB();
		$db->connect();
		
		$permissionDA = new PermissionDA();
		$users = $permissionDA->getUsersOfAProject($projectId);
		
		echo '<option value="0">-- nobody --</option>';
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
	
	public function printTypeSelect ($selectedType = "") {
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
	
	public function printStatusSelect ($selectedStatus = "") {
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
	
	public function printComponentSelect ($selectedComponent = "", $projectId) {
		$db = new DB();
		$db->connect();
		
		$componentDA = new ComponentDA();
		$components = $componentDA->getComponents($projectId);
		
		echo '<option value="0">-- none --</option>';
		while ($oneComponent = $components->fetch_assoc()) {
			$selectedText = "";
			if ($selectedComponent == $oneComponent["id"]) {
				$selectedText = ' selected="selected" ';
			}
			echo '<option value="'.$oneComponent["id"].'" '.$selectedText.'>'.$oneComponent["name"].'</option>';
		}
	}
	
	public function printVersionSelect ($selectedVersion = "", $projectId) {
		$db = new DB();
		$db->connect();
		
		$projectDA = new ProjectDA();
		$versions = $projectDA->getVersionsOfProject($projectId, false);
		
		echo '<option value="0">-- none --</option>';
		while ($oneVersion = $versions->fetch_assoc()) {
			$selectedText = "";
			if ($selectedVersion == $oneVersion["id"]) {
				$selectedText = ' selected="selected" ';
			}
			echo '<option value="'.$oneVersion["id"].'" '.$selectedText.'>'.$oneVersion["name"].'</option>';
		}
		$releasedVersions = $projectDA->getVersionsOfProject($projectId, true);
		while ($oneVersion = $releasedVersions->fetch_assoc()) {
			$selectedText = "";
			if ($selectedVersion == $oneVersion["id"]) {
				$selectedText = ' selected="selected" ';
			}
			echo '<option value="'.$oneVersion["id"].'" '.$selectedText.'>'.$oneVersion["name"].' (released)</option>';
		}
	}
	
	public function printComments($taskid) {
		$db = new DB();
		$db->connect();
		
		$taskid = $db->esc($taskid);
		
		$sql = "SELECT * FROM comment
				INNER JOIN `user` ON `user`.id = comment.user_id
				WHERE comment.task_id = " . $taskid;
		$query = $db->query($sql);
		
		while ($oneRow = $query->fetch_assoc()) {
			echo '
				<div class="media">
					<a class="pull-left" href="#">
						<img class="media-object img-circle" src="style/default_profil.png" alt="person" height="64" width="64">
					</a>
					<div class="media-body">
						<h4 class="media-heading">'.$oneRow["prename"].' '.$oneRow["surname"].'</h4>
						'.nl2br($oneRow["value"]).'
					</div>
				</div>';
		}
	}
	
	public function printPrioritySelect ($selectedPriority = "") {
		$selectedText = ' selected="selected" ';
		if ($selectedPriority == "1") {
			echo '<option value="1"' . $selectedText . '>1</option>';
		}
		else {
			echo '<option value="1">1</option>';
		}
		if ($selectedPriority == "2") {
			echo '<option value="2"' . $selectedText . '>2</option>';
		}
		else {
			echo '<option value="2">2</option>';
		}
		if ($selectedPriority == "3") {
			echo '<option value="3"' . $selectedText . '>3</option>';
		}
		else {
			echo '<option value="3">3</option>';
		}
		if ($selectedPriority == "4") {
			echo '<option value="4"' . $selectedText . '>4</option>';
		}
		else {
			echo '<option value="4">4</option>';
		}
		if ($selectedPriority == "5") {
			echo '<option value="5"' . $selectedText . '>5</option>';
		}
		else {
			echo '<option value="5">5</option>';
		}
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
		
		$sql = "SELECT task.id, task.summary, project.key, `status`.color, `status`.name AS status FROM task 
					INNER JOIN project ON project.id = task.project_id
					INNER JOIN `status` ON `status`.id = task.status_id		
				WHERE status_id IN ($openstatusTest) AND task.active !=0 AND assignee_id = " . $_SESSION['nobug'.RANDOMKEY.'userId'] . "
				ORDER BY project.name, task.id DESC 
				LIMIT 10";
		return $db->query($sql);
	}
	
	public function getOpenAssignedToMeCount() {
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
		
		$sql = "SELECT count(task.id) AS id FROM task
		INNER JOIN project ON project.id = task.project_id
		INNER JOIN `status` ON `status`.id = task.status_id
		WHERE status_id IN ($openstatusTest) AND task.active !=0 AND assignee_id = " . $_SESSION['nobug'.RANDOMKEY.'userId'] . "
		LIMIT 10";
		return $db->query($sql)->fetch_assoc()["id"];
	}
	
	public function updateAssignee($taskId, $newAssigneeId) {
		$db = new DB();
		$db->connect();
		
		$taskId = $db->esc($taskId);
		$newAssigneeId = $db->esc($newAssigneeId);
		
		$sql = "UPDATE `task` SET `assignee_id`='$newAssigneeId' WHERE `id`='$taskId'";
		$db->query($sql);
	}
	
	public function changeStatus($taskId, $newStatusId) {
		$db = new DB();
		$db->connect();
	
		$taskId = $db->esc($taskId);
		$newStatusId = $db->esc($newStatusId);
	
		$sql = "UPDATE `task` SET `status_id`='$newStatusId' WHERE `id`='$taskId'";
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
