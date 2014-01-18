<?php
	define( 'ACTIVE_MENU', 'proj');
	include_once 'core/header.php';
	include_once 'core/logic/taskDA.php';
	include_once 'core/logic/permissionDA.php';
	$taskDA = new TaskDA();
	$permDA = new PermissionDA();

	$selectedTask = null;
	
	if (isset($_GET["t"])) {
		$selectedTask = $taskDA->getTaskByID($_GET["t"]);
		if ($selectedTask == null) {
			echo '<div class="alert alert-warning alert-dismissable" style="margin: 50px;">
			  <strong><i class="fa fa-question"></i> Not Found!</strong> This Task was eaten by King Kong...! </div>';
			include_once 'core/footer.php';
			die();
		}
		$selectedTask = $selectedTask->fetch_assoc();
		
		if (!$permDA->isReadOnProjectAllowed($selectedTask["projectId"])) {
			$permDA->echoPermissionDeniedAndDie();
		}
	}
	
	if (isset($_POST["edited"])) {
		if ($permDA->isWriteOnProjectAllowed($selectedTask["projectId"])) {
			$taskDA->updateTask($_GET["t"], $_POST["summary"], $_POST["projectSelect"], $_POST["assigneeSelect"],
					$_POST["typeSelect"], $_POST["prioritySelect"], $_POST["statusSelect"], $_POST["description"]);
			echo '<META HTTP-EQUIV="Refresh" Content="0; URL=task.php?t=' . $_GET["t"].'" >';  
			die();
		}
		else {
			$permDA->echoPermissionDeniedAndDie();
		}
	}
	
	if (isset($_GET["assignToMe"])) {
		if ($permDA->isWriteOnProjectAllowed($selectedTask["projectId"])) {
			$taskDA->updateAssignee($_GET["t"], $_SESSION['nobug'.RANDOMKEY.'userId']);
			echo '<META HTTP-EQUIV="Refresh" Content="0; URL=task.php?t=' . $_GET["t"].'" >';
			die();
		}
		else {
			$permDA->echoPermissionDeniedAndDie();
		}
	}
	
	if (isset($_GET["delete"])) {
		if ($permDA->isAdminOnProjectAllowed($selectedTask["projectId"])) {
			$taskDA->deleteTask($_GET["t"]);
			echo '<META HTTP-EQUIV="Refresh" Content="0; URL=project.php?p=' . $_GET["proj"] .'" >'; 
			die();
		}
	}
	
	if (isset($_POST["newCommentText"])) {
		if ($permDA->isWriteOnProjectAllowed($selectedTask["projectId"])) {
			$taskDA->createComment($_GET["t"], $_POST["newCommentText"]);
			echo '<META HTTP-EQUIV="Refresh" Content="0; URL=task.php?t=' . $_GET["t"].'" >';  
			die();
		}
		else {
			$permDA->echoPermissionDeniedAndDie();
		}
	}
	
	//Proceed create Task...
	if (isset($_POST["createNew"])) {
		if ($permDA->isWriteOnProjectAllowed($_GET["proj"])) {
			$taskDA->createTask($_POST["summary"], $_POST["description"],
					$_GET["proj"], $_POST["assigneeSelect"], $_POST["typeSelect"],
					$_POST["prioritySelect"], $_POST["statusSelect"]);
			echo '<META HTTP-EQUIV="Refresh" Content="0; URL=project.php?p=' . $_GET["proj"] .'" >';
			die();
		}
		else {
			$permDA->echoPermissionDeniedAndDie();
		}
	}
	
	if ($selectedTask != null) {
		//Show Task to Edit...
		if (isset($_GET["edit"]) && $permDA->isWriteOnProjectAllowed($selectedTask["projectId"])) {
	?>
<div id="main">
	<h1>Edit <small>NOBUG-<?php echo $selectedTask["id"]?> </small> <?php echo $selectedTask["summary"]?>...</h1>
	<form method="POST" action="" name="loginform">
		<input type="hidden" name="edited" value="true" />
		<div class="panel panel-primary" >
			<div class="panel-heading">General</div>
			<table class="table">
				<tr>
					<td>Summary: </td>
					<th><input type="text" class="form-control" name="summary" placeholder="Enter Summary here..." value="<?php echo $selectedTask["summary"];?>"></th>
					
					<td>Project: </td>
					<th>
						<select class="form-control" name="projectSelect">
							<?php $taskDA->printProjectSelect($selectedTask["projectId"]); ?>
						</select>
					</th>
				</tr>
				<tr>
					<td>Assignee: </td>
					<th>
						<select class="form-control" name="assigneeSelect">
							<?php $taskDA->printAssigneeSelect($selectedTask["assigneeId"], $selectedTask["projectId"]); ?>
						</select>
					</th>
					
					<td>Type: </td>
					<th>
						<select class="form-control" name="typeSelect">
							<?php $taskDA->printTypeSelect($selectedTask["tasktypId"]); ?>
						</select>
					</th>
				</tr>
				<tr>
					<td>Priority: </td>
					<th>
						<select class="form-control" name="prioritySelect">
							<?php $taskDA->printPrioritySelect($selectedTask["priority"]); ?>
						</select>
					</th>
					
					<td>Status: </td>
					<th>
						<select class="form-control" name="statusSelect">
							<?php $taskDA->printStatusSelect($selectedTask["status_id"]); ?>
						</select>
					</th>
				</tr>
			</table>
		</div>
		
		<div class="panel panel-primary" >
			<div class="panel-heading">Description</div>
			<div class="panel-body">
				<textarea name="description" class="form-control" placeholder="Description here..." id="desc" onkeydown="resizeTextarea('desc')" ><?php echo $selectedTask["description"]; ?></textarea>
			</div>
		</div>
		<button type="submit" class="btn btn-success">Save Changes</button>
		<a href="task.php?t=<?php echo $selectedTask["id"];?>" style="margin-left: 20px" >Abort</a>
	</form>
</div>
<script type="text/javascript" >
	resizeTextarea('desc');
</script>
	<?php 
		include_once 'core/footer.php';
		die();	
	} else {
	// Show ReadOnly Task...
?>
<div id="main">
	<ol class="breadcrumb">
	  <li><a href="index.php">Home</a></li>
	  <li><a href="project.php?p=<?php echo $selectedTask["projectId"]; ?>">Project <?php echo $selectedTask["projectname"]; ?></a></li>
	  <li class="active">Task #<?php echo $selectedTask["id"]; ?></li>
	</ol>
	
	<h1><small><?php echo $selectedTask["projectkey"].'-'.$selectedTask["id"]?> </small> <?php echo $selectedTask["summary"]?></h1>
	
	<?php if($permDA->isWriteOnProjectAllowed($selectedTask["projectId"])) { ?>
	<div style="margin-top: 20px; margin-bottom: 20px;">
		<form action="" method="get" style='display:inline;'>  
			<input type="hidden" name="t" value="<?php echo $selectedTask["id"];?>" />
			<input type="hidden" name="edit" value="true" />
			<button type="submit" class="btn btn-default"><i class="fa fa-pencil"></i> Edit Task...</button>
		</form>
		<form action="" method="get" style='display:inline;'>  
			<input type="hidden" name="t" value="<?php echo $selectedTask["id"];?>" />
			<input type="hidden" name="assignToMe" value="true" />
			<button type="submit" class="btn btn-default">Assign to Me...</button>
		</form>
		<?php if ($permDA->isAdminOnProjectAllowed($selectedTask["projectId"])) {
			echo '<form action="" method="get" style=\'display:inline;\'>  
					<input type="hidden" name="t" value="'.$selectedTask["id"].'" />
					<input type="hidden" name="delete" value="true" />
					<button type="submit" class="btn btn-danger"><i class="fa fa-trash-o"></i> Delete...</button>
				  </form>';
		}
			
		?>
	</div>
	<?php }?>
	<div class="panel panel-primary" >
		<div class="panel-heading">General</div>
		<table class="table">
			<tr>
				<td>Assignee: </td>
				<th><?php 
					if (isset($selectedTask["prename"]) && isset($selectedTask["surname"])) {
						echo $selectedTask["prename"].' '. $selectedTask["surname"];
					}
					else {
						echo '--- nobudy ---';
					}
				?></th>
				
				<td>Type: </td>
				<th><?php echo $selectedTask["tasktypname"]?></th>
			</tr>
			<tr>
				<td>Priority: </td>
				<th><?php echo $selectedTask["priority"]?></th>
				
				<td>Status: </td>
				<th><?php echo $selectedTask["statusname"]?></th>
			</tr>
			<tr>
					<td>Creator: </td>
					<th><?php echo $selectedTask["cPrename"] . " " . $selectedTask["cSurname"]; ?></th>
					<td>Created Date: </td>
					<th><?php echo $selectedTask["createDate"]?></th>
				</tr>
		</table>
	</div>
	
	<div class="panel panel-primary" >
		<div class="panel-heading">Description</div>
		<div class="panel-body">
			<?php echo nl2br($selectedTask["description"]); ?>
		</div>
	</div>
	
	<div class="panel panel-info" >
		<div class="panel-heading">Comments</div>
		<div class="panel-body">
			<?php echo $taskDA->printComments($selectedTask["id"]);?>
			<hr >
			<?php if ($permDA->isWriteOnProjectAllowed($selectedTask["projectId"])) { ?>
			<form action="" method="post" >
				<input type="hidden" name="t" value="<?php echo $selectedTask["id"];?>" />
				<textarea name="newCommentText" placeholder="New Comment..." class="form-control" ></textarea>
				<input type="submit" class="btn btn-success" value="New Comment" style="margin-top: 10px" />
			</form> 
			<?php  }?>
		</div>
	</div>
	
</div>
<?php 
	include_once 'core/footer.php';
	die();
	}
}

if (isset($_GET["new"]) && $permDA->isWriteOnProjectAllowed($_GET["proj"])) {
	//Create Task window
?>
<div id="main">
	<h1>New Task...</h1>
	<form method="POST" action="" name="loginform">
		<input type="hidden" name="createNew" value="true" />
		<div class="panel panel-primary" >
			<div class="panel-heading">General</div>
			<table class="table">
				<tr>
					<td>Summary: </td>
					<th><input type="text" class="form-control" name="summary" placeholder="Enter Summary here..."></th>
					
					<td>Project: </td>
					<th>
						<?php 
								include_once 'core/logic/projectDA.php';
								$projDA = new ProjectDA();
								$project = $projDA->getProject($_GET["proj"]);
								echo $project["name"]; 
						?>
					</th>
				</tr>
				<tr>
					<td>Assignee: </td>
					<th>
						<select class="form-control" name="assigneeSelect">
							<?php $taskDA->printAssigneeSelect(null, $_GET["proj"]); ?>
						</select>
					</th>
					
					<td>Type: </td>
					<th>
						<select class="form-control" name="typeSelect">
							<?php $taskDA->printTypeSelect(); ?>
						</select>
					</th>
				</tr>
				<tr>
					<td>Priority: </td>
					<th>
						<select class="form-control" name="prioritySelect">
							<?php $taskDA->printPrioritySelect(); ?>
						</select>
					</th>
					
					<td>Status: </td>
					<th>
						<select class="form-control" name="statusSelect">
							<?php $taskDA->printStatusSelect(); ?>
						</select>
					</th>
				</tr>
			</table>
		</div>
		
		<div class="panel panel-primary" >
			<div class="panel-heading">Description</div>
			<div class="panel-body">
				<textarea name="description" class="form-control" placeholder="Description here..." id="desc" onkeydown="resizeTextarea('desc')" ></textarea>
			</div>
		</div>
		<button type="submit" class="btn btn-success" data-loading-text="Loading..." >Create Task</button>
	</form>
</div>
<?php 
	include_once 'core/footer.php';
	die();	
}
?>