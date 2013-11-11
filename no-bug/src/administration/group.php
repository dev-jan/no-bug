<?php
	define( 'ACTIVE_MENU', 'administration');
	include_once '../core/header.php';
	include_once '../core/logic/groupDA.php'	;
	
	$groupDA = new GroupDA();
	
	if (isset($_POST["editGroupname"])) {
		$groupDA->updateGroupname($_GET["g"], $_POST["editGroupname"]);
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
	<ul class="nav nav-tabs">
		<li><a href="users.php">Users</a></li>
		<li class="active"><a href="groups.php">Groups</a></li>
		<li><a href="projects.php">Projects</a></li>
		<li><a href="settings.php">Global Settings</a></li>
	</ul>
	<h1>Edit <?php echo $selectedGroup["name"]; ?>...</h1>
	<form action="?g=<?php echo $selectedGroup["id"]; ?>" class="userEditForm" method="post">
		<table class="table">
			<tr>
				<th>Groupname:</th>
				<td><input type="text" class="form-control" name="editGroupname" value="<?php echo $selectedGroup["name"]; ?>"></td>
			</tr>
		</table>
		<button type="submit" class="btn btn-primary">Save</button>
	</form>
	
	<form action="?g=<?php echo $selectedGroup["id"]; ?>" method="post" class="userEditForm" >
		<input type="hidden" name="addGroup" value="true" />
		<table class="table" border="0" style="border: none">
				<tr>
					<td>Add Group: 
						<select class="form-control" name="newGroup">
						<?php echo $groupDA->printGroupSelect();?>
						</select></td>
					<td><button type="submit" class="btn btn-success" style="margin-top: 20px;">Add</button></td>
				</tr>
		</table>
	</form>
	<form action="?g=<?php echo $selectedGroup["id"]; ?>" method="post" class="userEditForm">
		<input type="hidden" name="addUser" value="true" />
		<table class="table" border="0">
				<tr>
					<td>Add User: 
						<select class="form-control" name="newUser">
						<?php echo $groupDA->printUserSelect();?>
						</select></td>
					<td><button type="submit" class="btn btn-success" style="margin-top: 20px;">Add</button></td>
				</tr>
		</table>
	</form>
	
	<form action="#" class="userEditForm">
		<h2>> Group Members...</h2>
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