<?php
	define( 'ACTIVE_MENU', 'proj');
	include_once 'core/header.php';
	include_once 'core/logic/projectDA.php';
	$projDA = new ProjectDA();
	
	if (isset($_GET["p"])) {
		$selectedProject = $projDA->getProject($_GET["p"]);
?>
<div id="main">
	<h1><?php echo $selectedProject["name"];?> <small><?php echo $selectedProject["version"]?></small></h1>
	<p><?php echo $selectedProject["description"];?></p> 
	
	<form action="task.php" method="get">
		<input type="hidden" name="new" value="true" />
		<input type="hidden" name="proj" value="<?php echo $_GET["p"]; ?>" />
		<button type="submit" class="btn btn-success pull-right">New Task...</button>
	</form>
	
	<div class="panel panel-default" style="margin-top: 60px;">
	  <div class="panel-heading">Tasks</div>
      <div>
      	<?php 
      		include_once 'core/logic/taskDA.php';
      		$taskDA = new TaskDA();
      		$projectsTask = $taskDA->getTasksQueryByProjectID($_GET["p"]);
      		while ($oneTask = $projectsTask->fetch_assoc()) {
				echo '<a href="task.php?t='.$oneTask["id"].'" class="list-group-item"><b>'.$selectedProject["key"].'-'.$oneTask["id"].'</b>: '.$oneTask["summary"].'</a>';
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