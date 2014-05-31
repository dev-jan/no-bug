<?php
/* Description: Create a new user */

// Include core files
define( 'ACTIVE_MENU', 'administration');
include_once '../core/header.php';
include_once '../core/logic/userDA.php';
include_once '../core/logic/permissionDA.php';
include_once '../core/logic/adminDA.php';

// Check if the user is allowed to access this page
$permDA = new PermissionDA();
if (!$permDA->isGeneralAdmininstrationAllowed()) {
	$permDA->echoPermissionDeniedAndDie();
}

// DataAccess initialisation
$userDA = new UserDA();
$alerts = "";

// Check if the user request to create a new user
if (isset($_POST["createUser"])) {
	if ($_POST["newPassword"] == $_POST["newPassword2"]) {
		$userDA->createUser($_POST["newUsername"], $_POST["newPrename"], $_POST["newSurname"], $_POST["newEmail"], $_POST["newPassword"]);
		$alerts = $alerts . '<div class="alert alert-success alert-dismissable">
				<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
				<strong>Successfull</strong> created user "'.$_POST["newUsername"].'"</div>';
	}
	else {
		$alerts = $alerts . '<div class="alert alert-danger alert-dismissable">
				<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
				<strong>Failed</strong> to create user "'.$_POST["newUsername"].'", because the passwords not match</div>';
	}
}

?>
<div id="main">
	<?php echo $alerts; //Show alerts if something happen ?>
	<?php 
	$adminDA = new AdminDA();
	$adminDA->getAdminMenu("users.php");
	?>
	<h1><i class="fa fa-user"></i> New User...</h1>
	<form action="" class="userEditForm" method="post">
		<input type="hidden" name="createUser" value="true" />
		<table class="table userEditTable">
			<tr>
				<th>Username:</th>
				<td><input type="text" class="form-control" name="newUsername"
					placeholder="Enter Username"></td>
			</tr>
			<tr>
				<th>Prename:</th>
				<td><input type="text" class="form-control" name="newPrename"
					placeholder="Enter Prename"></td>
			</tr>
			<tr>
				<th>Surname:</th>
				<td><input type="text" class="form-control" name="newSurname"
					placeholder="Enter Surname"></td>
			</tr>
			<tr>
				<th>Email:</th>
				<td><input type="text" class="form-control" name="newEmail"
					placeholder="Enter Email"></td>
			</tr>
			<tr>
				<th>New Password:</th>
				<td><input type="password" class="form-control" name="newPassword"
					placeholder="Password"></td>
			</tr>
			<tr>
				<th>New Password:</th>
				<td><input type="password" class="form-control" name="newPassword2"
					placeholder="Retype"></td>
			</tr>
		</table>
		<button type="submit" class="btn btn-primary">Create User!</button>
	</form>
</div>
<?php 
include '../core/footer.php';