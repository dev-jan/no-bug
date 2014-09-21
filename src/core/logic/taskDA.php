<?php
include_once 'db.php';
include_once dirname(__FILE__).'/permissionDA.php';
include_once dirname(__FILE__).'/taskpropDA.php';
include_once dirname(__FILE__).'/componentDA.php';
include_once dirname(__FILE__).'/projectDA.php';

/**
 * DataAccess for all task stuff
 */
class TaskDA {
	/**
	 * Returns a task that matches to the given TaskID
	 * @param <Int> $taskID ID of the task
	 * @return <dbResult> database result with all important stuff releated to this task
	 */
	public function getTaskByID($taskID) {
		$db = new DB();
		$db->connect();
		
		$taskID = $db->esc($taskID);
		
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
				WHERE task.active=1 AND task.id = ".$taskID;
		return $db->query($sql);
	}
	
	/**
	 * Returns the task of a project and the selected menu
	 * @param <Int> $projectID
	 * @param <String> $shownMenu Taskmenu (all|myopen|open|closed|unassigned)
	 * @return <dbResult> all task that matches the criteria
	 */
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
	 * @return <dbResult> or null if there are no matches
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
	
	/**
	 * Return the tasks that are assigned with the version in the parameter
	 * @param <Int> $versionId Version to search
	 * @return <dbResult> or null if there are no matches
	 */
	public function getTasksByVersionID ($versionId) {
		$db = new DB();
		$db->connect();
	
		$versionId = $db->esc($versionId);
		if ($versionId == "") {
			return null;
		}
	
		$permDA = new PermissionDA();
		$projectDA = new ProjectDA();
		$selectedVersion = $projectDA->getVersionById($_GET["list"])->fetch_assoc();
		if ($permDA->isReadOnProjectAllowed($selectedVersion["project_id"])) {
			$sql = "SELECT task.id, task.summary, task.description, task.active, `status`.name,
			assignee.prename AS assigneePrename, assignee.surname AS assigneeSurname, assignee.id AS assigneeID,
			`status`.color, `component`.name AS componentName, project.`key` AS `key`
			FROM task
			INNER JOIN `status` ON task.status_id = `status`.id
			LEFT JOIN `user` AS assignee ON task.assignee_id = assignee.id
			LEFT JOIN `component` AS `component` ON task.component_id = `component`.id
			LEFT JOIN `project` AS project ON task.project_id = project.id
			WHERE task.version_id = $versionId
			ORDER BY task.id DESC";
			return $db->query($sql);
		}
		else {
			return null;
		}
	}
	
	/**
	 * Create a new Task
	 * @param <String> $summary summary of the new task (short description)
	 * @param <String> $description long description of the task
	 * @param <Int> $project projectID of the task
	 * @param <Int> $assignee assigneeID of the task
	 * @param <Int> $type tasktype (e.g. Bug, New Function)
	 * @param <Int> $priority priority of the new task
	 * @param <Int> $status statusID of the new task (e.g. Open, Done)
	 * @param <Int> $component componentID of the new task (e.g. Frontend)
	 * @param <Int> $version versionID of the new task (fixed version)
	 */
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
	
	/**
	 * Edit an existing task
	 * @param <Int> $taskid unique ID of the task to edit
	 * @param <String> $summary (new) summary of the task
	 * @param <Int> $project (new) projectID of the task
	 * @param <Int> $assignee (new) assigneeID of the task
	 * @param <Int> $type (new) tasktypID of the task
	 * @param <Int> $priority (new) priority of the task
	 * @param <Int> $status (new) statusID of the task
	 * @param <String> $description (new) description of the task
	 * @param <Int> $component (new) componentID of the task
	 * @param <Int> $versionId (new) versionID of the task
	 */
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
	
	/**
	 * Create a new comment on a task
	 * @param <Int> $taskId Task to comment
	 * @param <String> $value the comment
	 */
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
	
	/**
	 * Print out the dropdown content of all project of the current user
	 * @param <String> $selectedProject Selected Project (will be preselected in the dropdown)
	 */
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
	
	/**
	 * Print out the dropdown content of the assignee select
	 * @param <String> $selectedAssignee current assignee (preselected)
	 * @param <Int> $projectId ID of the current project
	 */
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
	
	/**
	 * Print out the dropdown content of the available tasktypes
	 * @param <String> $selectedType Tasktype that will be preselected
	 */
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
	
	/**
	 * Print out the dropdown content of the available status
	 * @param <String> $selectedStatus StatusID that will be preselected
	 */
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
	
	/**
	 * Print out the dropdown content of the project components
	 * @param <String> $selectedComponent component that will be preselected
	 * @param <Int> $projectId ProjectID of the current project
	 */
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
	
	/**
	 * Print out the dropdown content of all available versions of the project
	 * @param <String> $selectedVersion ID of the version that will be preselected
	 * @param <Int> $projectId Selected Project
	 */
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
	
	/**
	 * Print all available comments of a task
	 * @param <Int> $taskid Selected Task
	 */
	public function printComments($taskid) {
		$db = new DB();
		$db->connect();
		
		$taskid = $db->esc($taskid);
		
		$sql = "SELECT * FROM comment
				INNER JOIN `user` ON `user`.id = comment.user_id
				WHERE comment.task_id = " . $taskid;
		$query = $db->query($sql);

		while ($oneRow = $query->fetch_assoc()) {
			//build gravatar url form mail
			$gravatar_url = "http://www.gravatar.com/avatar/" . md5( strtolower( trim( $oneRow["email"] ) ) ) . "?s=80&d=mm";
			
			echo '
				<div class="media">
					<a class="pull-left" href="#">
						<img class="media-object img-circle" src="' . $gravatar_url . '" alt="person" height="64" width="64">
					</a>
					<div class="media-body">
						<h4 class="media-heading">'.$oneRow["prename"].' '.$oneRow["surname"].'</h4>
						'.nl2br($oneRow["value"]).'
					</div>
				</div>';
		}
	}
	
	/**
	 * Print out the dropdown content of a priority select
	 * @param <Int> $selectedPriority The preselected priority
	 */
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
	
	/**
	 * Returns all tasks that are open and assigned to the current user
	 * @return <dbResult> Tasks
	 */
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
	
	/**
	 * Get the number of open tasks of the current user
	 */
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
	
	/**
	 * Change the assignee of a task
	 * @param <Int> $taskId ID of the task to change
	 * @param <Int> $newAssigneeId ID of the new assignee
	 */
	public function updateAssignee($taskId, $newAssigneeId) {
		$db = new DB();
		$db->connect();
		
		$taskId = $db->esc($taskId);
		$newAssigneeId = $db->esc($newAssigneeId);
		
		$sql = "UPDATE `task` SET `assignee_id`='$newAssigneeId' WHERE `id`='$taskId'";
		$db->query($sql);
	}
	
	/**
	 * Change the status of a task
	 * @param <Int> $taskId ID of the task to change
	 * @param <Int> $newStatusId ID of the new status
	 */
	public function changeStatus($taskId, $newStatusId) {
		$db = new DB();
		$db->connect();
	
		$taskId = $db->esc($taskId);
		$newStatusId = $db->esc($newStatusId);
	
		$sql = "UPDATE `task` SET `status_id`='$newStatusId' WHERE `id`='$taskId'";
		$db->query($sql);
	}
	
	/**
	 * Deactivate a task (for normal users the task is now deleted)
	 * @param <Int> $taskId
	 */
	public function deleteTask ($taskId) {
		$db = new DB();
		$db->connect();
		
		$taskId = $db->esc($taskId);
		
		$sql = "UPDATE `task` SET `active`='0' WHERE `id`=".$taskId;
		$db->query($sql);
	}
}
