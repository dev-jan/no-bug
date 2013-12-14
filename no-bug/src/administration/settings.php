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
	$alerts = "";
	
	if (isset($_POST["edited"])) {
		$settingsDA->setValues($_POST["admingroup"], $_POST["platformname"], $_POST["motd"]);
		$alerts = $alerts . '<div class="alert alert-success alert-dismissable">
					  				<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
					  				<strong>Successfull</strong> changed Settings</div>';
	}
?>	
<div id="main">
	<?php echo $alerts; ?>
	<ul class="nav nav-tabs">
		<li><a href="users.php">Users</a></li>
		<li><a href="groups.php">Groups</a></li>
		<li><a href="projects.php">Projects</a></li>
		<li class="active"><a href="settings.php">Global Settings</a></li>
	</ul>
	<h1>Global Settings</h1>
	<form action="#" class="userEditForm" method="post">
		<input type="hidden" name="edited" value="true" />
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
					<textarea name="motd" class="form-control" id="motd" onkeydown="resizeTextarea('motd')"><?php echo $settingsDA->getMotd(); ?></textarea>
				</td>
			</tr>
		</table>
		<script type="text/javascript" >
			resizeTextarea('motd');
		</script>
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