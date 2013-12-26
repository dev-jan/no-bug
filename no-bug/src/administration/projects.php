<?php
define( 'ACTIVE_MENU', 'administration');
include_once '../core/header.php';
include_once '../core/logic/projectDA.php';
include_once '../core/logic/permissionDA.php';
include_once '../core/logic/adminDA.php';

$permDA = new PermissionDA();
if (!$permDA->isGeneralAdmininstrationAllowed()) {
	$permDA->echoPermissionDeniedAndDie();
}

$projectDA = new ProjectDA();
?>
<div id="main">
	<?php 
	$adminDA = new AdminDA();
	$adminDA->getAdminMenu("projects.php");
	?>
	<h1><i class="fa fa-folder-open"></i> Projects</h1>
	<form action="newProject.php">
		<button type="submit" class="btn btn-success"
			style="float: right; margin-bottom: 10px;"><i class="fa fa-plus-square"></i> New Project</button>
	</form>
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