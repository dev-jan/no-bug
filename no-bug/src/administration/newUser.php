<?php
	define( 'ACTIVE_MENU', 'administration');
	include_once '../core/header.php';
	include_once '../core/logic/userDA.php';
	include_once '../core/logic/permissionDA.php';
	
	$permDA = new PermissionDA();
	if (!$permDA->isGeneralAdmininstrationAllowed()) {
		$permDA->echoPermissionDeniedAndDie();
	}
	
	$userDA = new UserDA();
	
	if (isset($_POST["createUser"])) {
		if ($_POST["newPassword"] == $_POST["newPassword2"]) {
			$userDA->createUser($_POST["newUsername"], $_POST["newPrename"], $_POST["newSurname"], $_POST["newEmail"], $_POST["newPassword"]);
		}
		else {
			//TODO: Error Message
		}
	}
	
?>
	<div id="main">
		<ul class="nav nav-tabs">
			<li class="active"><a href="users.php">Users</a></li>
			<li><a href="groups.php">Groups</a></li>
			<li><a href="projects.php">Projects</a></li>
			<li><a href="settings.php">Global Settings</a></li>
		</ul>
		<h1>New User...</h1>
		<form action="" class="userEditForm" method="post">
			<input type="hidden" name="createUser" value="true" />
			<h2>> General</h2>
			<table class="table userEditTable">
				<tr>
					<th>Username:</th>
					<td><input type="text" class="form-control" name="newUsername" placeholder="Enter Username"></td>
				</tr>
				<tr>
					<th>Prename:</th>
					<td><input type="text" class="form-control" name="newPrename" placeholder="Enter Prename"></td>
				</tr>
				<tr>
					<th>Surname:</th>
					<td><input type="text" class="form-control" name="newSurname" placeholder="Enter Surname"></td>
				</tr>
				<tr>
					<th>Email:</th>
					<td><input type="text" class="form-control" name="newEmail" placeholder="Enter Email"></td>
				</tr>
				<tr>
					<th>New Password:</th>
					<td><input type="password" class="form-control" name="newPassword" placeholder="Password"></td>
				</tr>
				<tr>
					<th>New Password:</th>
					<td><input type="password" class="form-control" name="newPassword2" placeholder="Retype"></td>
				</tr>
			</table>
			<button type="submit" class="btn btn-primary">Create User!</button>
		</form>
	</div>
<?php 
	include '../core/footer.php';
?>