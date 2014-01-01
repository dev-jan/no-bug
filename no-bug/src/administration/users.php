<?php
define( 'ACTIVE_MENU', 'administration');
include_once '../core/header.php';
include_once '../core/logic/userDA.php';
include_once '../core/logic/permissionDA.php';
include_once '../core/logic/adminDA.php';

$permDA = new PermissionDA();
if (!$permDA->isGeneralAdmininstrationAllowed()) {
	$permDA->echoPermissionDeniedAndDie();
}
?>
<div id="main">
<?php 
	$adminDA = new AdminDA();
	$adminDA->getAdminMenu("users.php");
	?>
	<h1><i class="fa fa-user"></i> Users</h1>
	<?php 
	if (isset($_GET["showDeactivated"])) {
		echo '<form action="#" method="get" style="float: left;"><button type="submit" class="btn btn-default"><i class="fa fa-eye-slash"></i> Show activated Users</button></form>';
	}
	else {
		echo '<form action="users.php" method="get" style="float: left;"><input type="hidden" name="showDeactivated" value="true" /><button type="submit" class="btn btn-default" ><i class="fa fa-eye"></i> Show all Users</button></form>';
	}
	?>
	<form action="newUser.php">
		<button type="submit" class="btn btn-success"
			style="float: right; margin-bottom: 10px;"><i class="fa fa-plus-square"></i> New User</button>
	</form>
	<table class="table table-hover">
		<tr>
			<th>Username</th>
			<th>Name</th>
			<th>email</th>
			<th>actions</th>
		</tr>

		<?php 
		$userDA = new UserDA();
		if (isset($_GET["showDeactivated"])) {
					$userDA->printAllUsersTable(true);
				}
				else {
					$userDA->printAllUsersTable(false);
				}



				?>
	</table>
</div>
<?php 
include '../core/footer.php';
?>