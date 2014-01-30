<?php
	define( 'ACTIVE_MENU', 'proj');
	include_once 'core/header.php';
	include_once 'core/logic/projectDA.php';
	include_once 'core/logic/permissionDA.php';
	include_once 'core/logic/taskpropDA.php';
	$projDA = new ProjectDA();
	$permDA = new PermissionDA();
	$taskpropDA = new TaskpropDA();
	
	if (isset($_GET["p"])) {
		if (!$permDA->isReadOnProjectAllowed($_GET["p"])) {
			$permDA->echoPermissionDeniedAndDie();
		}
		$selectedProject = $projDA->getProject($_GET["p"]);
		if ($selectedProject["id"] == "") {
			$permDA->echoPermissionDeniedAndDie();
		}
?>
<div id="main">
	<ol class="breadcrumb">
	  <li><a href="index.php">Home</a></li>
	  <li class="active">Project <?php echo $selectedProject["name"]; ?></li>
	</ol>

	<h1><?php echo $selectedProject["name"];?> <small><?php echo $selectedProject["version"]?></small></h1>
	<p style="float: right; text-align: right">
		<?php 
			if ($permDA->isAdminOnProjectAllowed($selectedProject["id"])) {
				echo '<i class="fa fa-rocket"></i> Administrator <br />';
				echo '<a href="projectmanager.php?p=' . $selectedProject["id"] . '">
						<i class="fa fa-tachometer"></i> Project Settings</a>';
			}
			else {
				if ($permDA->isWriteOnProjectAllowed($selectedProject["id"])) {
					echo '<i class="fa fa-coffee"></i> Developer';
				}
				else {
					echo '<i class="fa fa-eye"></i> Read Only';
				}
			}
		?>
	</p>
	<p><?php echo nl2br($selectedProject["description"]);?></p> 
	
	<span style="clear: both">&nbsp;<br /></span>
	<?php if ($permDA->isWriteOnProjectAllowed($selectedProject["id"])) { ?>
	<form action="task.php" method="get">
		<input type="hidden" name="new" value="true" />
		<input type="hidden" name="proj" value="<?php echo $_GET["p"]; ?>" />
		<button type="submit" class="btn btn-success pull-right"><i class="fa fa-plus-square"></i> New Task...</button>
	</form>
	<?php }?>
	<?php 
		$shownMenu = "";
		if (!isset($_GET["show"])) {
			$shownMenu = "myopen";
		}
		else {
			$shownMenu = $_GET["show"];
		}
	?>
	<div style="clear: both; margin-bottom: 30px;"></div>
	<ul class="nav nav-tabs nav-justified" style="margin-bottom: -45px;">
	  <li <?php if ($shownMenu == "myopen") {echo "class=\"active\"";}?>>
	  	<a href="project.php?p=<?php echo $_GET["p"];?>&show=myopen">My Open Tasks 
	  	<span class="badge"><?php echo $taskpropDA->getNumberOfTasksByMenu($selectedProject["id"], "myopen"); ?></span></a></li>
	  <li <?php if ($shownMenu == "unassigned") {echo "class=\"active\"";}?>>
	 	<a href="project.php?p=<?php echo $_GET["p"];?>&show=unassigned">Unassigned Tasks
	  	<span class="badge"><?php echo $taskpropDA->getNumberOfTasksByMenu($selectedProject["id"], "unassigned"); ?></span></a></li>
	  <li <?php if ($shownMenu == "open") {echo "class=\"active\"";}?>>
	  	<a href="project.php?p=<?php echo $_GET["p"];?>&show=open">Open Tasks
	  	<span class="badge"><?php echo $taskpropDA->getNumberOfTasksByMenu($selectedProject["id"], "open"); ?></span></a></li>
	  <li <?php if ($shownMenu == "closed") {echo "class=\"active\"";}?>>
	  	<a href="project.php?p=<?php echo $_GET["p"];?>&show=closed">Closed Tasks
	  	<span class="badge"><?php echo $taskpropDA->getNumberOfTasksByMenu($selectedProject["id"], "closed"); ?></span></a></li>
	  <li <?php if ($shownMenu == "all") {echo "class=\"active\"";}?>>
	  	<a href="project.php?p=<?php echo $_GET["p"];?>&show=all">All Tasks
	  	<span class="badge"><?php echo $taskpropDA->getNumberOfTasksByMenu($selectedProject["id"], "all"); ?></span></a></li>
	</ul>
	
	
	<div class="panel panel-default" style="margin-top: 60px;">
	  <div class="panel-heading">Tasks</div>
      <div>
      	<?php 
      		include_once 'core/logic/taskDA.php';
      		$taskDA = new TaskDA();
      		$projectsTask = $taskDA->getTasksQueryByProjectID($_GET["p"], $shownMenu);
      		$areTaskAvailable = false;
      		if ($projectsTask != null) {
      			while ($oneTask = $projectsTask->fetch_assoc()) {
					$component = $oneTask["componentName"];
					if ($component != "") {
						$component = '<span class="text-success pull-center" style="margin-left: 20px;">Component: '.$oneTask["componentName"].'</span>';
					}
					$assignee = $oneTask["assigneePrename"];
					if ($assignee == "") {
						$assignee = "none";
					}
					echo '<a href="task.php?t='.$oneTask["id"].'" class="list-group-item">
							<div>
				              <b>'.$selectedProject["key"].'-'.$oneTask["id"].'</b>: '.
				              $oneTask["summary"]. '' .
				                ' <span class="badge pull-right" style="background-color: '.$oneTask["color"].'">'.$oneTask["name"].'</span>
	  		                </div>
							<div style="margin-left: 10px;"><em>
	  							<span class="text-info">Assignee: '.$assignee.' '.$oneTask["assigneeSurname"].'</span>
	  							'.$component.'
						    </em></div>
						  </a>';
					$areTaskAvailable = true;
				}
      		}
      		if (!$areTaskAvailable) {
      			echo '<span class="list-group-item">No Tasks available...</span>';
      		}
      	?>
      </div>
	</div>
</div>
<?php 
	include 'core/footer.php';
	die();
	}
	
?>

<div id="main">
	<h1>All Projects</h1>
	<div class="list-group">
		<?php $projDA->printProjectsOnMainPage(); ?>
	</div>
</div>

<?php 
	include 'core/footer.php';
?>