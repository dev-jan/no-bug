<?php
	define( 'ACTIVE_MENU', 'administration');
	include_once '../core/header.php';
	include_once '../core/logic/settingsDA.php';
	include_once '../core/logic/permissionDA.php';
	
	$permDA = new PermissionDA();
	if (!$permDA->isGeneralAdmininstrationAllowed()) {
		$permDA->echoPermissionDeniedAndDie();
	}
	
	$settingsDA = new SettingsDA();
?>	
<div id="main">
	<ul class="nav nav-tabs">
		<li><a href="users.php">Users</a></li>
		<li><a href="groups.php">Groups</a></li>
		<li><a href="projects.php">Projects</a></li>
		<li class="active"><a href="settings.php">Global Settings</a></li>
	</ul>
	<h1>Global Settings</h1>
	<form action="#" class="userEditForm">
		<h2>> General</h2>
		<table class="table">
			<tr>
				<th>Global Admin Group:</th>
				<td>
					<select class="form-control" name="admingroup" >
					  <?php $settingsDA->printGlobalAdminGroupSelect(); ?>
					</select>
				</td>
			</tr>
			<tr>
				<th>Name of this Platform:</th>
				<td>
					<input type="text" class="form-control" name="platformname" value="<?php echo $settingsDA->getPlatformName(); ?>" />
				</td>
			</tr>
			<tr>
				<th>Message of the Day:</th>
				<td>
					<textarea name="motd" class="form-control" ><?php echo $settingsDA->getMotd(); ?></textarea>
				</td>
			</tr>
		</table>
		<button type="submit" class="btn btn-primary">Save Changes</button>
	</form>
	
	<form action="#" class="userEditForm">
		<h2>> Infos</h2>
		<table class="table userEditTable">
			<?php $settingsDA->printServerInfos(); ?>
		</table>
	</form>
</div>
<?php 
	include '../core/footer.php';
?>