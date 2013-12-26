<?php
define( 'ACTIVE_MENU', 'administration');
include_once '../core/header.php';
include_once '../core/logic/taskpropDA.php';
include_once '../core/logic/permissionDA.php';
include_once '../core/logic/adminDA.php';

$alerts = "";

$permDA = new PermissionDA();
if (!$permDA->isGeneralAdmininstrationAllowed()) {
	$permDA->echoPermissionDeniedAndDie();
}

$taskpropDA = new TaskpropDA();

//Typeedit
if (isset($_POST["typeId"])) {
	$taskpropDA->updateTasktype($_POST["typeId"], $_POST["typname"]);
}

//Statusedit
if (isset($_POST["statusId"])) {
	$taskpropDA->updateStatus($_POST["statusId"], $_POST["statusname"], $_POST["color"]);
}

?>

<div id="main">
	<?php echo $alerts;?>
	<?php 
	$adminDA = new AdminDA();
	$adminDA->getAdminMenu("taskproperties.php");
	?>
	<h1><i class="fa fa-tasks"></i> Taskproperties...</h1>

	<h2>Tasktypes</h2>
	<ul class="list-group">
		<?php 
		$tasktypes = $taskpropDA->getAllTasktypes();
		if ($tasktypes != null) {
			while ($oneType = $tasktypes->fetch_assoc()) {
				echo '
					<li class="list-group-item" style="padding-bottom: 30px;">
					<form action="" method="post">
						<input type="hidden" name="typeId" value="'.$oneType["id"].'" /> 
						<input type="text"
						    name="typname" class="form-control" value="'.$oneType["name"].'"
						    style="width: 250px; float: left;" />
						<button type="submit" class="btn" style="float: left; margin-left: 10px;">
						<i class="fa fa-check"></i>
						</button>
						<span style="clear: both;">&nbsp;</span>
					</form>
					</li>
					';
			}
		}
		?>
	</ul>


	<h2>Status</h2>
	<script type="text/javascript" src="../js/jscolor/jscolor.js"></script>
	<ul class="list-group">
	<?php 
	$taskstatus = $taskpropDA->getAllStatus();
	if ($taskstatus != null) {
		while ($oneStatus = $taskstatus->fetch_assoc()) {
			echo '
				<li class="list-group-item" style="padding-bottom: 30px;">
					<form action="" method="post">
						<input type="hidden" name="statusId" value="'.$oneStatus["id"].'" /> 
						<input type="text"
							name="statusname" class="form-control" value="'.$oneStatus["name"].'"
							style="width: 250px; float: left;" /> 
						<input
							class="color form-control" name="color" value="'.$oneStatus["color"].'"
							style="width: 150px; float: left; margin-left: 10px;" />
						<button type="submit" class="btn"
							style="float: left; margin-left: 10px;">
							<i class="fa fa-check"></i>
						</button>
						<span style="clear: both;">&nbsp;</span>
					</form>
				</li>
				';
		}
	}
	?>
	</ul>
</div>
<?php 
include '../core/footer.php';
?>

