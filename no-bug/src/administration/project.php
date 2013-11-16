<?php
	define( 'ACTIVE_MENU', 'administration');
	include_once '../core/header.php';
	include_once '../core/logic/projectDA.php';
	
	$projDA = new ProjectDA();
	
	if (isset($_POST["general"])) {
		$projDA->updateGeneral($_GET["p"], $_POST["editName"], $_POST["editDescription"], $_POST["editVersion"]);
	}
	
	if (isset($_POST["groups"])) {
		$projDA->updateGroups ($_GET["p"], $_POST["adminselect"], $_POST["writeselect"], $_POST["readselect"]);
	}
	
	
	$selectedProject = $projDA->getProject($_GET["p"]);
	if ($selectedProject == null) {
		//header("Location: projects.php");
		die();
	}
	
	
	
?>
<div id="main">
	<ul class="nav nav-tabs">
		<li><a href="users.php">Users</a></li>
		<li><a href="groups.php">Groups</a></li>
		<li class="active"><a href="projects.php">Projects</a></li>
		<li><a href="settings.php">Global Settings</a></li>
	</ul>
	<h1>Edit Project <?php echo $selectedProject["name"]; ?> (<?php echo $selectedProject["key"]; ?>)...</h1>
	<form action="?p=<?php echo $selectedProject["id"]; ?>" class="userEditForm" method="post">
		<input type="hidden" name="general" value="true" />
		<h2>> General</h2>
		<table class="table">
			<tr>
				<th>Name:</th>
				<td><input type="text" class="form-control" name="editName" value="<?php echo $selectedProject["name"]; ?>"></td>
			</tr>
			<tr>
				<th>Description:</th>
				<td><textarea class="form-control" rows="3" name="editDescription" ><?php echo $selectedProject["description"]; ?></textarea></td>
			</tr>
			<tr>
				<th>Version:</th>
				<td><input type="text" class="form-control" name="editVersion" value="<?php echo $selectedProject["version"]; ?>"></td>
			</tr>
		</table>
		<button type="submit" class="btn btn-primary">Save Changes</button>
	</form>
	
	<form action="?p=<?php echo $selectedProject["id"]; ?>" class="userEditForm" method="post">
		<input type="hidden" name="groups" value="true" />
		<h2>> Groups</h2>
		<table class="table userEditTable">
			<tr>
				<th>Admin Group: </th>
				<td>
					<select class="form-control" name="adminselect">
					  <?php $projDA->printGroupSelect($selectedProject["group_admin"]);?>
					</select>
				</td>
			</tr>
			<tr>
				<th>Write Group: </th>
				<td>
					<select class="form-control" name="writeselect">
					  <?php $projDA->printGroupSelect($selectedProject["group_write"]);?>
					</select>
				</td>
			</tr>
			<tr>
				<th>Read Group: </th>
				<td>
					<select class="form-control" name="readselect">
					  <?php $projDA->printGroupSelect($selectedProject["group_read"]);?>
					</select>
				</td>
			</tr>
		</table>
		<button type="submit" class="btn btn-warning">Change Groups</button>
	</form>
</div>
<?php 
	include '../core/footer.php';
?>