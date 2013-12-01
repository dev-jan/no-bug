<?php
	define( 'ACTIVE_MENU', 'administration');
	include_once '../core/header.php';
	include_once '../core/logic/groupDA.php';
	include_once '../core/logic/permissionDA.php';
	
	$permDA = new PermissionDA();
	if (!$permDA->isGeneralAdmininstrationAllowed()) {
		$permDA->echoPermissionDeniedAndDie();
	}
?>
<div id="main">
	<ul class="nav nav-tabs">
		<li><a href="users.php">Users</a></li>
		<li class="active"><a href="groups.php">Groups</a></li>
		<li><a href="projects.php">Projects</a></li>
		<li><a href="settings.php">Global Settings</a></li>
	</ul>
	<h1>Groups</h1>
	<form action="newGroup.php"><button type="submit" class="btn btn-success" style="float: right; margin-bottom: 10px;">New Group</button></form>
	<table class="table table-hover">
		<tr>
			<th>Name</th>
			<th>Member of</th>
			<th>Members</th>
			<th>actions</th>
		</tr>
		<?php 
			$groupDA = new GroupDA();
			$groupDA->printAllGroupsTable(false);
		?>
	</table>
</div>
<?php 
	include '../core/footer.php';
?>