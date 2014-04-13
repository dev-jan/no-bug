<?php
	define( 'ACTIVE_MENU', 'proj');
	include_once 'core/header.php';
	include_once 'core/logic/projectDA.php';
	include_once 'core/logic/permissionDA.php';
	include_once 'core/logic/taskpropDA.php';
	$projDA = new ProjectDA();
	$permDA = new PermissionDA();
	$taskpropDA = new TaskpropDA();
?>
<div id="main">
	<div style="padding-top: 50px; padding-bottom: 20px;">
		<form action="" method="get" style="margin-left: auto; margin-right: auto; width: 600px;">
			<input type="text" name="s" class="form-control" style="display: block; width: 500px; float: left;" 
			       placeholder="Search for tasks..." value="<?php if (isset($_GET["s"])) {echo $_GET["s"];} ?>" />
			<button type="submit" class="btn btn-default" style="float: left; margin-left: 20px;">Search</button>
		</form>
		<div style="clear: both;"></div>
	</div>
	
	<?php 
	if (isset($_GET["s"])) {
	?>
	<div class="panel panel-default" style="margin-top: 60px;">
	  <div class="panel-heading">Tasks</div>
      <div>
      	<?php 
      		include_once 'core/logic/taskDA.php';
      		$taskDA = new TaskDA();
      		$searchedTasks = $taskDA->getTasksBySearchquery($_GET["s"]);
      		$areTaskAvailable = false;
      		if ($searchedTasks != null) {
      			while ($oneTask = $searchedTasks->fetch_assoc()) {
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
				              <b>'.$oneTask["key"].'-'.$oneTask["id"].'</b>: '.
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
      			echo '<span class="list-group-item">No Tasks founded...</span>';
      		}
      	?>
      </div>
	</div>
	<?php }?>
</div>
<?php
include 'core/footer.php';
?>