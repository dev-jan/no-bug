<?php
	define( 'ACTIVE_MENU', 'administration');
	include_once '../core/header.php';
	include_once '../core/logic/userDA.php';
	include_once '../core/logic/permissionDA.php';
	
	$permDA = new PermissionDA();
	if (!$permDA->isGeneralAdmininstrationAllowed()) {
		$permDA->echoPermissionDeniedAndDie();
	}
?>
	<div id="main">
		<ul class="nav nav-tabs">
			<li class="active"><a href="users.php">Users</a></li>
			<li><a href="groups.php">Groups</a></li>
			<li><a href="projects.php">Projects</a></li>
			<li><a href="settings.php">Global Settings</a></li>
		</ul>
		<h1>Users</h1>
		<?php 
			if (isset($_GET["showDeactivated"])) {
				echo '<form action="#" method="get" style="float: left;"><button type="submit" class="btn btn-default">Show activated Users</button></form>';
			}
			else {
				echo '<form action="users.php" method="get" style="float: left;"><input type="hidden" name="showDeactivated" value="true" /><button type="submit" class="btn btn-default" >Show all Users</button></form>';
			}
		?>
		<form action="newUser.php"><button type="submit" class="btn btn-success" style="float: right; margin-bottom: 10px;">New User</button></form>
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