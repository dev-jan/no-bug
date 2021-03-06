<?php
/* Description: Show details of a project and edit them */

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
$projDA = new ProjectDA();
$alerts = "";

// Check if the user want to update some general values of a project
if (isset($_POST["general"])) {
	$projDA->updateGeneral($_GET["p"], $_POST["editName"], $_POST["editDescription"]);
	$alerts = $alerts . '<div class="alert alert-success alert-dismissable">
			<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
			<strong>Successfull</strong> changed Project </div>';
}

// Check if the user want to update the permission groups of a project
if (isset($_POST["groups"])) {
	$projDA->updateGroups ($_GET["p"], $_POST["adminselect"], $_POST["writeselect"], $_POST["readselect"]);
	$alerts = $alerts . '<div class="alert alert-success alert-dismissable">
			<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
			<strong>Successfull</strong> changed Groups of the Project</div>';
}

// Check if the user want to activate a project
if (isset($_POST["activate"])) {
	$projDA->activateProject($_GET["p"]);
	$alerts = $alerts . '<div class="alert alert-success alert-dismissable">
			<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
			<strong>Successfull</strong> activate Project </div>';
}

// Check if the user want to deactivate a project
if (isset($_POST["deactivate"])) {
	$projDA->deactivateProject($_GET["p"]);
	$alerts = $alerts . '<div class="alert alert-success alert-dismissable">
			<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
			<strong>Successfull</strong> deactivate Project </div>';
}

// Get the requested project from the database
$selectedProject = $projDA->getProjectOnAdmin($_GET["p"]);
if ($selectedProject == null) {
	header("Location: projects.php");
	die();
}

?>
<div id="main">
	<?php echo $alerts;?>
	<?php 
	$adminDA = new AdminDA();
	$adminDA->getAdminMenu("projects.php");
	?>
	<h1>
		<i class="fa fa-folder-open"></i> Edit Project <?php echo $selectedProject["name"]; ?>
		(<?php echo $selectedProject["key"]; ?>)...
	</h1>
	<form action="?p=<?php echo $selectedProject["id"]; ?>"
		class="userEditForm" method="post">
		<input type="hidden" name="general" value="true" />
		<h2><i class="fa fa-angle-double-right"></i> General</h2>
		<table class="table">
			<tr>
				<th>Name:</th>
				<td><input type="text" class="form-control" name="editName"
					value="<?php echo $selectedProject["name"]; ?>"></td>
			</tr>
			<tr>
				<th>Description:</th>
				<td><textarea class="form-control" rows="3" name="editDescription"><?php echo $selectedProject["description"]; ?></textarea></td>
			</tr>
		</table>
		<button type="submit" class="btn btn-primary">Save Changes</button>
	</form>

	<form action="?p=<?php echo $selectedProject["id"]; ?>"
		class="userEditForm" method="post">
		<input type="hidden" name="groups" value="true" />
		<h2><i class="fa fa-angle-double-right"></i> Groups</h2>
		<table class="table userEditTable">
			<tr>
				<th>Admin Group:</th>
				<td><select class="form-control" name="adminselect">
						<?php $projDA->printGroupSelect($selectedProject["group_admin"]);?>
				</select>
				</td>
			</tr>
			<tr>
				<th>Write Group:</th>
				<td><select class="form-control" name="writeselect">
						<?php $projDA->printGroupSelect($selectedProject["group_write"]);?>
				</select>
				</td>
			</tr>
			<tr>
				<th>Read Group:</th>
				<td><select class="form-control" name="readselect">
						<?php $projDA->printGroupSelect($selectedProject["group_read"]);?>
				</select>
				</td>
			</tr>
		</table>
		<button type="submit" class="btn btn-warning">Change Groups</button>
	</form>
	
	<form action="#" method="post">
		<?php 
		if ($projDA->isProjectActive($selectedProject["id"])) {
			echo '<input type="hidden" name="deactivate" value="true" />';
			echo '<button type="submit" class="btn btn-danger">Deactivate Project</button>';
		}
		else {
			echo '<input type="hidden" name="activate" value="true" />';
			echo '<button type="submit" class="btn btn-success">Activate Project</button>';
		}
		?>
	</form> 
</div>
<?php 
include '../core/footer.php';
?>