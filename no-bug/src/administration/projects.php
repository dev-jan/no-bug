<?php
	define( 'ACTIVE_MENU', 'administration');
	include_once '../core/header.php';
	include_once '../core/logic/projectDA.php';
	include_once '../core/logic/permissionDA.php';
	
	$permDA = new PermissionDA();
	if (!$permDA->isGeneralAdmininstrationAllowed()) {
		$permDA->echoPermissionDeniedAndDie();
	}
	
	$projectDA = new ProjectDA();
?>
<div id="main">
	<ul class="nav nav-tabs">
		<li><a href="users.php">Users</a></li>
		<li><a href="groups.php">Groups</a></li>
		<li class="active"><a href="projects.php">Projects</a></li>
		<li><a href="settings.php">Global Settings</a></li>
	</ul>
	<h1>Projects</h1>
	<form action="newProject.php"><button type="submit" class="btn btn-success" style="float: right; margin-bottom: 10px;">New Project</button></form>
	<table class="table table-hover">
		<tr>
			<th>Key</th>
			<th>Name</th>
			<th>Description</th>
			<th>Groups</th>
			<th>Version</th>
			<th>Actions</th>
		</tr>
		<?php $projectDA->printAllProjects(); ?>
	</table>
</div>
<?php 
	include '../core/footer.php';
?>