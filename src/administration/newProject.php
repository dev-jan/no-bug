<?php
/* Description: Create a new project */

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

// Check if the user request to create a new project
if (isset($_POST["createProject"])) {
	if ($projDA->checkProjectKey($_POST["newKey"])) {
		$projDA->createProject($_POST["newKey"], $_POST["newName"], $_POST["newDescription"], $_POST["adminselect"], $_POST["writeselect"], $_POST["readselect"]);
		$alerts = $alerts . '<div class="alert alert-success alert-dismissable">
				<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
				<strong>Successfull</strong> created Project "'.$_POST["newName"].'"</div>';
	}
	else {
		$alerts = $alerts . '<div class="alert alert-danger alert-dismissable">
				<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
				<strong>Failed</strong> to create Project "'.$_POST["newUsername"].'", because KEY already exists.</div>';
	}
}

?>
<div id="main">
	<?php echo $alerts; //Show alerts if something happen ?>
	<?php 
	$adminDA = new AdminDA();
	$adminDA->getAdminMenu("projects.php");
	?>
	<h1><i class="fa fa-folder-o"></i> New Project...</h1>
	<form action="" class="userEditForm" method="post">
		<input type="hidden" name="createProject" value="true" />
		<h2><i class="fa fa-angle-double-right"></i> General</h2>
		<table class="table userEditTable">
			<tr>
				<th>KEY:</th>
				<td><input type="text" class="form-control" name="newKey"
					placeholder="Enter Project KEY">
				</td>
			</tr>
			<tr>
				<th>Name:</th>
				<td><input type="text" class="form-control" name="newName"
					placeholder="Enter Project Name"></td>
			</tr>
			<tr>
				<th>Description:</th>
				<td><input type="text" class="form-control" name="newDescription"
					placeholder="Enter a Description"></td>
			</tr>
			<tr>
				<th>Admin Group:</th>
				<td><select class="form-control" name="adminselect">
						<?php $projDA->printGroupSelect("");?>
				</select>
				</td>
			</tr>
			<tr>
				<th>Write Group:</th>
				<td><select class="form-control" name="writeselect">
						<?php $projDA->printGroupSelect("");?>
				</select>
				</td>
			</tr>
			<tr>
				<th>Read Group:</th>
				<td><select class="form-control" name="readselect">
						<?php $projDA->printGroupSelect("");?>
				</select>
				</td>
			</tr>
		</table>
		<button type="submit" class="btn btn-primary">Create Project!</button>
	</form>
</div>
<?php
include '../core/footer.php';