<?php
	define( 'ACTIVE_MENU', 'administration');
	include_once '../core/header.php';
	include_once '../core/logic/groupDA.php'	;
	
	$groupDA = new GroupDA();
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
	<form action="#" class="userEditForm">
		<input type="hidden" name="general" value="true" />
		<table class="table">
			<tr>
				<th>Groupname:</th>
				<td><input type="text" class="form-control" id="editGroupname" value="<?php echo $selectedGroup["name"]; ?>"></td>
			</tr>
		</table>
		<button type="submit" class="btn btn-primary">Save</button>
	</form>
	
	<form action="#" class="userEditForm">
		<h2>> Group Members...</h2>
		<table class="table">
			<tr>
				<th>Name</th>
				<th>Action</th>
			</tr>
			<tr>
				<td>jan (User)</td>
				<td><button type="button" class="btn btn-danger" >Remove</button></td>
			</tr>
		</table>
	</form>
	<form action="#">
	<table class="table">
			<tr>
				<td>Add User: 
					<select class="form-control">
					<?php echo $groupDA->printGroupSelect();?>
					</select></td>
				<td><button type="button" class="btn btn-success" style="margin-top: 20px;">Add</button></td>
			</tr>
	</table>
	</form>
</div>
<?php 
	include '../core/footer.php';
?>