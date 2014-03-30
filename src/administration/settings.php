<?php
define( 'ACTIVE_MENU', 'administration');
include_once '../core/header.php';
include_once '../core/logic/settingsDA.php';
include_once '../core/logic/permissionDA.php';
include_once '../core/logic/adminDA.php';

$permDA = new PermissionDA();
if (!$permDA->isGeneralAdmininstrationAllowed()) {
	$permDA->echoPermissionDeniedAndDie();
}

$settingsDA = new SettingsDA();
$alerts = "";

if (isset($_POST["edited"])) {
	$settingsDA->setValues($_POST["admingroup"], $_POST["platformname"], $_POST["motd"], $_POST["tracker"]);
	$alerts = $alerts . '<div class="alert alert-success alert-dismissable">
			<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
			<strong>Successfull</strong> changed Settings</div>';
}
?>
<div id="main">
	<?php echo $alerts; ?>
	<?php 
	$adminDA = new AdminDA();
	$adminDA->getAdminMenu("settings.php");
	?>
	<h1><i class="fa fa-globe"></i> Global Settings</h1>
	<form action="#" class="userEditForm" method="post">
		<input type="hidden" name="edited" value="true" />
		<h2><i class="fa fa-angle-double-right"></i> General</h2>
		<table class="table">
			<tr>
				<th>Global Admin Group:</th>
				<td><select class="form-control" name="admingroup">
						<?php $settingsDA->printGlobalAdminGroupSelect(); ?>
				</select>
				</td>
			</tr>
			<tr>
				<th>Name of this Platform:</th>
				<td><input type="text" class="form-control" name="platformname"
					value="<?php echo $settingsDA->getPlatformName(); ?>" />
				</td>
			</tr>
			<tr>
				<th>Message of the Day:</th>
				<td><textarea name="motd" class="form-control" id="motd"
						onkeydown="resizeTextarea('motd')"><?php echo $settingsDA->getMotd(); ?></textarea>
				</td>
			</tr>
			<tr>
				<th>Custom Tracking Code:</th>
				<td><textarea name="tracker" class="form-control" id="tracker"
						onkeydown="resizeTextarea('tracker')"><?php echo $settingsDA->getTrackingCode(); ?></textarea>
				</td>
			</tr>
		</table>
		<script type="text/javascript">
			resizeTextarea('motd');
			resizeTextarea('tracker');
		</script>
		<button type="submit" class="btn btn-primary">Save Changes</button>
	</form>

	<form action="#" class="userEditForm">
		<h2><i class="fa fa-angle-double-right"></i> Infos</h2>
		<table class="table userEditTable">
			<?php $settingsDA->printServerInfos(); ?>
		</table>
	</form>
	
	<button class="btn btn-success" data-toggle="modal" data-target="#aboutModal">
	  About...
	</button>
	<a href="update.php" class="btn btn-default">Check for updates...</a>
	
	<div class="modal fade" id="aboutModal" tabindex="-1" role="dialog" aria-labelledby="aboutModalLabel" aria-hidden="true">
	  <div class="modal-dialog">
	    <div class="modal-content">
	      <div class="modal-header">
	        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
	        <h4 class="modal-title" id="aboutModalLabel"><i class="fa fa-code"></i> About the Developers of no-bug...</h4>
	      </div>
	      <div class="modal-body">
	        Hello :)
	        <p>We are two young developers from switzerland. "no-bug" was a project we build in our free time to train our
	        php & mySQL skills. One of our goal was to build a simple, stylish and feature-rich bugtracking platform.
	        We have chosen to pubish this platform as Open-Source (and of course <i class="fa fa-usd"></i>free<i class="fa fa-usd"></i>
	        to use) on github, so other developers can use our bugtracking platform for free. If you can, you are allowed
	        to fork this platform on github and make your own changes. </p>
	        <p>
	        </p>
	      </div>
	      <div class="modal-footer">
	        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
	      </div>
	    </div>
	  </div>
	</div>
	
</div>
<?php 
include '../core/footer.php';
?>