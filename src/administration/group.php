<?php
/* Description: Show details of a group and edit them (e.g. groupname, members)  */

// Include core files
define( 'ACTIVE_MENU', 'administration'); 
include_once '../core/header.php';
include_once '../core/logic/groupDA.php';
include_once '../core/logic/permissionDA.php';
include_once '../core/logic/adminDA.php';

// DataAccess initialisation
$permDA = new PermissionDA();
$groupDA = new GroupDA();
$adminDA = new AdminDA();
$alerts = "";

// Check if the user is allowed to access this page
if (!$permDA->isGeneralAdmininstrationAllowed()) {
	$permDA->echoPermissionDeniedAndDie();
}

// Check if the request shout handle a groupname change
if (isset($_POST["editGroupname"])) {
	$groupDA->updateGroupname($_GET["g"], $_POST["editGroupname"]);
	$alerts = $alerts . '<div class="alert alert-success alert-dismissable">
			<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
			<strong>Successfull</strong> changed Group "'.$_POST["editGroupname"].'"</div>';
}
// Check if the user want to add a group
if (isset($_POST["addGroup"])) {
	$groupDA->addGroupmember($_GET["g"], $_POST["newGroup"]);
}
// Check if the request is to add a user to a group
if (isset($_POST["addUser"])) {
	$groupDA->addUsermember($_GET["g"], $_POST["newUser"]);
}
// Check if the user want to remove a group from a group
if (isset($_POST["groupId"])) {
	$groupDA->removeGroupmember($_GET["g"], $_POST["groupId"]);
}
// Check if the user want to remove a user from a group
if (isset($_POST["userId"])) {
	$groupDA->removeUsermember($_GET["g"], $_POST["userId"]);
}

// Check if the user want to deactivate a group
if (isset($_POST["deactivate"])) {
	$groupDA->deactivateGroup($_GET["g"]);
}
// Check if the user want to activate a group
if (isset($_POST["activate"])) {
	$groupDA->activateGroup($_GET["g"]);
}

// Get the requested group from the database
@$selectedGroup = $groupDA->getGroup($_GET["g"]);
if ($selectedGroup == null) {
	echo '<div class="alert alert-warning alert-dismissable" style="margin: 50px;">
			  <strong><i class="fa fa-question"></i> Not Found!</strong> This Group not found, you maybe misstype? 
				Chuck Norris doesn\'t call the wrong number. You answer the wrong phone.';
	include_once '../core/footer.php';
	die();
}
?>
<div id="main">
	<?php 
	echo $alerts; // show alerts if something happen
	$adminDA->getAdminMenu("groups.php");
	?>
	<h1>
		<i class="fa fa-users"></i> Edit <?php echo $selectedGroup["name"]; ?>...
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
			<?php echo $groupDA->returnGroupMembersAsTable($selectedGroup["id"]); ?>
		</table>
	</form>
	
	<form action="#" method="post">
		<?php 
		if ($groupDA->isGroupActive($selectedGroup["id"])) {
			echo '<input type="hidden" name="deactivate" value="true" />';
			echo '<button type="submit" class="btn btn-danger">Deactivate Group</button>';
		}
		else {
			echo '<input type="hidden" name="activate" value="true" />';
			echo '<button type="submit" class="btn btn-success">Activate Group</button>';
		}
		?>
	</form>	
</div>
<?php 
include '../core/footer.php';