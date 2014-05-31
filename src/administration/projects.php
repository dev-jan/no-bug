<?php
/* Description: Show a overview of the available projects */

// Include core files
define( 'ACTIVE_MENU', 'administration');
include_once '../core/header.php';
include_once '../core/logic/projectDA.php';
include_once '../core/logic/permissionDA.php';
include_once '../core/logic/adminDA.php';

// Check if the user is allowed to access this page
$permDA = new PermissionDA();
if (!$permDA->isGeneralAdmininstrationAllowed()) {
	$permDA->echoPermissionDeniedAndDie();
}

// DataAccess initialisation
$projectDA = new ProjectDA();

?>
<div id="main">
	<?php 
	$adminDA = new AdminDA();
	$adminDA->getAdminMenu("projects.php");
	?>
	<h1><i class="fa fa-folder-open"></i> Projects</h1>
	<?php 
	if (isset($_GET["showDeactivated"])) {
		echo '<form action="#" method="get" style="float: left;"><button type="submit" class="btn btn-default"><i class="fa fa-eye-slash"></i> Show activated Projects</button></form>';
	}
	else {
		echo '<form action="#" method="get" style="float: left;"><input type="hidden" name="showDeactivated" value="true" /><button type="submit" class="btn btn-default" ><i class="fa fa-eye"></i> Show all Projects</button></form>';
	}
	?>
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
		<?php 
		if (isset($_GET["showDeactivated"])) {
			$projectDA->printAllProjects(true);
		}
		else {
			$projectDA->printAllProjects();
		}
		
		?>
	</table>
</div>
<?php 
include '../core/footer.php';