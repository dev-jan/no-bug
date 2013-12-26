<?php
define( 'ACTIVE_MENU', 'administration');
include_once '../core/header.php';
include_once '../core/logic/groupDA.php';
include_once '../core/logic/permissionDA.php';
include_once '../core/logic/adminDA.php';

$permDA = new PermissionDA();
if (!$permDA->isGeneralAdmininstrationAllowed()) {
	$permDA->echoPermissionDeniedAndDie();
}

$groupDA = new GroupDA();
$alerts = "";

if (isset($_POST["editGroupname"])) {
	$groupDA->updateGroupname($_GET["g"], $_POST["editGroupname"]);
	$alerts = $alerts . '<div class="alert alert-success alert-dismissable">
			<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
			<strong>Successfull</strong> changed Group "'.$_POST["editGroupname"].'"</div>';
}
if (isset($_POST["addGroup"])) {
	$groupDA->addGroupmember($_GET["g"], $_POST["newGroup"]);
}
if (isset($_POST["addUser"])) {
	$groupDA->addUsermember($_GET["g"], $_POST["newUser"]);
}
if (isset($_POST["groupId"])) {
	$groupDA->removeGroupmember($_GET["g"], $_POST["groupId"]);
}
if (isset($_POST["userId"])) {
	$groupDA->removeUsermember($_GET["g"], $_POST["userId"]);
}


$selectedGroup = $groupDA->getGroup($_GET["g"]);
if ($selectedGroup == null) {
	//header("Location: groups.php");
	die();
}
?>
<div id="main">
	<?php echo $alerts;?>
	<?php 
	$adminDA = new AdminDA();
	$adminDA->getAdminMenu("groups.php");
	?>
	<h1>
		<i class="fa fa-users"></i> Edit
		<?php echo $selectedGroup["name"]; ?>
		...
	</h1>
	<form action="?g=<?php echo $selectedGroup["id"]; ?>" class="userEditForm" method="post">
		<table class="table">
			<tr>
				<th>Groupname:</th>
				<td><input type="text" class="form-control" name="editGroupname"
					value="<?php echo $selectedGroup["name"]; ?>"></td>
			</tr>
		</table>
		<button type="submit" class="btn btn-primary">Save</button>
	</form>

	<form action="?g=<?php echo $selectedGroup["id"]; ?>" method="post" class="userEditForm">
		<input type="hidden" name="addGroup" value="true" />
		<table class="table" border="0" style="border: none">
			<tr>
				<td>Add Group: <select class="form-control" name="newGroup">
						<?php echo $groupDA->printGroupSelect();?>
				</select>
				</td>
				<td><button type="submit" class="btn btn-success"
						style="margin-top: 20px;">Add</button></td>
			</tr>
		</table>
	</form>
	<form action="?g=<?php echo $selectedGroup["id"]; ?>" method="post" class="userEditForm">
		<input type="hidden" name="addUser" value="true" />
		<table class="table" border="0">
			<tr>
				<td>Add User: <select class="form-control" name="newUser">
						<?php echo $groupDA->printUserSelect();?>
				</select>
				</td>
				<td><button type="submit" class="btn btn-success"
						style="margin-top: 20px;">Add</button></td>
			</tr>
		</table>
	</form>
	<form class="userEditForm">
		<h2><i class="fa fa-angle-double-right"></i> Group Members...</h2>
		<table class="table">
			<tr>
				<th>Name</th>
				<th>Action</th>
			</tr>
			<?php echo $groupDA->printGroupMembersAsTable($selectedGroup["id"]); ?>
		</table>
	</form>
</div>
<?php 
include '../core/footer.php';
?>