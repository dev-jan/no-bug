<?php
define( 'ACTIVE_MENU', 'administration');
include_once '../core/header.php';
include_once '../core/logic/userDA.php';
$userDA = new UserDA();
include_once '../core/logic/groupDA.php';
$groupDA = new GroupDA();
include_once '../core/logic/permissionDA.php';
include_once '../core/logic/adminDA.php';
$alerts = "";

$permDA = new PermissionDA();
if (!$permDA->isGeneralAdmininstrationAllowed()) {
	$permDA->echoPermissionDeniedAndDie();
}

if(!isset($_GET["u"])) {
	//header("Location: users.php");
	die();
}

// Event General->Save Changes
if (isset($_POST["general"])) {
	if (isset($_POST["editUsername"]) && ($userDA->getUser($_GET["u"])["username"] != $_POST["editUsername"])) {
		if (!$userDA->updateUsername($_GET["u"], $_POST["editUsername"])) {
			$alerts = '<div class="alert alert-danger alert-dismissable">
					<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
					<strong>Failed</strong> to changed the username to '.$_POST["editUsername"].' because it already exists.</div>';
		}
	}
	if (isset($_POST["editPrename"])) {
		$userDA->updatePrename($_GET["u"], $_POST["editPrename"]);
	}
	if (isset($_POST["editSurname"])) {
		$userDA->updateSurname($_GET["u"], $_POST["editSurname"]);
	}
	if (isset($_POST["editEmail"])) {
		$userDA->updateEmail($_GET["u"], $_POST["editEmail"]);
	}
	$alerts = $alerts . '<div class="alert alert-success alert-dismissable">
			<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
			<strong>Successfull</strong> changed usersettings </div>';
}

// Event PwReset
if (isset($_POST["pwReset"])) {
	if ($_POST["pwreset1"] == $_POST["pwreset2"]) {
		$userDA->updatePassword($_GET["u"], $_POST["pwreset1"]);
		$alerts = $alerts . '<div class="alert alert-success alert-dismissable">
				<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
				<strong>Successfull</strong> changed Password </div>';
	}
	else {
		$alerts = $alerts . '<div class="alert alert-danger alert-dismissable">
				<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
				<strong>Failed!</strong> Passwords not match </div>';
	}
}

// Events Groupmembership
if (isset($_POST["addGroupSelect"])) {
	$groupDA->addUsermember($_POST["addGroupSelect"], $_GET["u"]);
}

// Event Activate/Deactivate User
if (isset($_POST["activate"])) {
	$userDA->activateUser($_GET["u"]);
	$alerts = $alerts . '<div class="alert alert-success alert-dismissable">
			<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
			<strong>Successfull</strong> activate User </div>';
}
if (isset($_POST["deactivate"])) {
	$userDA->deactivateUser($_GET["u"]);
	$alerts = $alerts . '<div class="alert alert-success alert-dismissable">
			<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
			<strong>Successfull</strong> deactivate User </div>';
}


$selectedUser = $userDA->getUser($_GET["u"]);

if ($selectedUser == null) {
	//header("Location: users.php");
	die();
}




?>
<div id="main">
	<?php echo $alerts; ?>
	<?php 
	$adminDA = new AdminDA();
	$adminDA->getAdminMenu("users.php");
	?>
	<h1>
		<i class="fa fa-user"></i> Edit
		<?php echo $selectedUser["prename"].' '.$selectedUser["surname"]; ?>
		...
	</h1>
	<form action="?u=<?php echo $selectedUser["id"]; ?>"
		class="userEditForm" method="post">
		<input type="hidden" name="general" value="true" />
		<h2><i class="fa fa-angle-double-right"></i> General</h2>
		<table class="table userEditTable">
			<tr>
				<th>Username:</th>
				<td><input type="text" class="form-control" name="editUsername"
					value="<?php echo $selectedUser["username"];?>"></td>
			</tr>
			<tr>
				<th>Prename:</th>
				<td><input type="text" class="form-control" name="editPrename"
					value="<?php echo $selectedUser["prename"];?>"></td>
			</tr>
			<tr>
				<th>Surname:</th>
				<td><input type="text" class="form-control" name="editSurname"
					value="<?php echo $selectedUser["surname"];?>"></td>
			</tr>
			<tr>
				<th>Email:</th>
				<td><input type="text" class="form-control" name="editEmail"
					value="<?php echo $selectedUser["email"];?>"></td>
			</tr>
		</table>
		<button type="submit" class="btn btn-primary">Save Changes</button>
	</form>


	<form action="#" class="userEditForm" method="post">
		<input type="hidden" name="pwReset" value="true" />
		<h2><i class="fa fa-angle-double-right"></i> Password Reset...</h2>
		<table class="table userEditTable">
			<tr>
				<th>New Password:</th>
				<td><input type="password" class="form-control" name="pwreset1"
					placeholder="Password"></td>
			</tr>
			<tr>
				<th>New Password:</th>
				<td><input type="password" class="form-control" name="pwreset2"
					placeholder="Retype"></td>
			</tr>
		</table>
		<button type="submit" class="btn btn-warning">Change Password</button>
	</form>

	<form class="userEditForm">
		<h2><i class="fa fa-angle-double-right"></i> Member of...</h2>
	</form>
	<table class="table">
		<tr>
			<th>Member of this Groups:</th>
			<th>Add new Group...</th>
		</tr>
		<tr>
			<td><?php 
			$groupDA->printGroupsOfUser($_GET["u"]);
			?>
			</td>
			<td>Add Group:
				<form action="#" method="post">
					<select class="form-control" name="addGroupSelect">
						<?php 
						$groupDA->printGroupSelect();
						?>
					</select>
					<button type="submit" class="btn btn-success"
						style="margin-top: 10px;">Add</button>
				</form>
			</td>
		</tr>
	</table>

	<form action="" class="userEditForm">
		<h2><i class="fa fa-angle-double-right"></i> Permissions on Projects...</h2>
	</form>
	<?php echo $userDA->printPermissionTable($selectedUser["id"]); ?>

	<form action="#" method="post">
		<?php 
		if ($userDA->isUserActive($selectedUser["id"])) {
					echo '<input type="hidden" name="deactivate" value="true" />';
					echo '<button type="submit" class="btn btn-danger">Deactivate User</button>';
				}
				else {
					echo '<input type="hidden" name="activate" value="true" />';
					echo '<button type="submit" class="btn btn-success">Activate User</button>';
				}
				?>
	</form>
</div>
<?php 
include '../core/footer.php';
?>