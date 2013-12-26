<?php
define( 'ACTIVE_MENU', 'main');
include_once 'core/header.php';
include_once 'core/logic/projectDA.php';
include_once 'core/logic/taskDA.php';
include_once 'core/logic/settingsDA.php';
?>
<div id="main">
	
	<?php
	$settingsDA = new SettingsDA();
	if ($settingsDA->getMotd() != "") {
		echo '<div class="panel panel-default"><div class="panel-body">'.$settingsDA->getMotd().'</div></div>';
	}
	?>		
	

	<div class="panel panel-primary">
		<div class="panel-heading">
			<h3 class="panel-title"><i class="fa fa-folder-open"></i> Your Projects</h3>
		</div>
		<div class="">
			<?php 
			$projDA = new ProjectDA();
			$projDA->printProjectsOnMainPage();
			?>
		</div>
	</div>

	<div class="panel panel-success">
		<div class="panel-heading">
			<h3 class="panel-title"><i class="fa fa-rocket"></i> My open Tasks</h3>
		</div>
		<div class="">
			<?php 
			$taskDA = new TaskDA();
			$myOpenTasks = $taskDA->getOpenAssignedToMe();
			if ($myOpenTasks == null) {
				echo '<span class="list-group-item">You have no open Tasks...</span>';
			}
			else {
				while ($oneTask = $myOpenTasks->fetch_assoc()) {
					echo '<a href="task.php?t='.$oneTask["id"].'" class="list-group-item"><b>'.$oneTask["key"].'-'.$oneTask["id"].'</b>: '.$oneTask["summary"].'<span class="badge pull-right" style="background-color: '.$oneTask["color"].'">'.$oneTask["status"].'</span></a> ';
				}
			}
			
			?>
		</div>
	</div>
</div>

<?php 
include 'core/footer.php';
?>
