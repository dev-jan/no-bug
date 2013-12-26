<?php
	define( 'ACTIVE_MENU', 'proj');
	include_once 'core/header.php';
	include_once 'core/logic/projectDA.php';
	include_once 'core/logic/permissionDA.php';
	$projDA = new ProjectDA();
	$permDA = new PermissionDA();
	
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
	<h1><?php echo $selectedProject["name"];?> <small><?php echo $selectedProject["version"]?></small></h1>
	<p style="float: right">
		<?php 
			if ($permDA->isAdminOnProjectAllowed($selectedProject["id"])) {
				echo '<i class="fa fa-rocket"></i> Administrator';
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
	
	<div class="panel panel-default" style="margin-top: 60px;">
	  <div class="panel-heading">Tasks</div>
      <div>
      	<?php 
      		include_once 'core/logic/taskDA.php';
      		$taskDA = new TaskDA();
      		$projectsTask = $taskDA->getTasksQueryByProjectID($_GET["p"]);
      		while ($oneTask = $projectsTask->fetch_assoc()) {
				echo '<a href="task.php?t='.$oneTask["id"].'" class="list-group-item"><b>'
				   .$selectedProject["key"].'-'.$oneTask["id"].'</b>: '.$oneTask["summary"].
				   ' <span class="badge pull-right" style="background-color: '.$oneTask["color"].'">'.$oneTask["name"].'</span> </a>';
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