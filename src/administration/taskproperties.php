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

//Edit...
if (isset($_POST["typeId"])) {
	$taskpropDA->updateTasktype($_POST["typeId"], $_POST["typname"]);
}
$isDone = 0;
if (isset($_POST["isDone"])) {
	$isDone = 1;
}
if (isset($_POST["statusId"])) {
	$taskpropDA->updateStatus($_POST["statusId"], $_POST["statusname"], $_POST["color"], $isDone);
}

//New...
if (isset($_POST["newType"])) {
	$taskpropDA->newTasktyp($_POST["typname"]);
}
$isDone = 0;
if (isset($_POST["isDone"])) {
	$isDone = 1;
}
if (isset($_POST["newStatus"])) {
	$taskpropDA->newStatus($_POST["statusname"], $_POST["color"], $isDone);
}

//Delete...
if (isset($_POST["deleteStatus"])) {
	$taskpropDA->deleteStatus($_POST["deleteStatus"]);
}

if (isset($_POST["deleteTasktype"])) {
	$taskpropDA->deleteTasktype($_POST["deleteTasktype"]);
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
					<li class="list-group-item">
					<form action="" method="post" style="display: inline">
						<input type="hidden" name="typeId" value="'.$oneType["id"].'" /> 
						<input type="text"
						    name="typname" class="form-control" value="'.$oneType["name"].'"
						    style="width: 250px; float: left;" />
						<button type="submit" class="btn btn-default" style="float: left; margin-left: 10px;">
						<i class="fa fa-check"></i>
						</button>
						<span style="clear: both;">&nbsp;</span>
					</form>
					<form action=""	method="post" style="display: inline" >
						<input type="hidden" name="deleteTasktype" value="'.$oneType["id"].'" />
						<button type="submit" class="btn btn-danger" ><i class="fa fa-trash-o"></i></button>
					</form>
					</li>
					';
			}
		}
		echo '<li class="list-group-item" style="padding-bottom: 30px;">
				<form action="" method="post">
					<p style="float: left; margin-right: 30px;">New Tasktype:</p>
					<input type="hidden" name="newType" value="true" /> 
					<input type="text"
					    name="typname" class="form-control" value=""
					    style="width: 250px; float: left;" />
					<button type="submit" class="btn btn-success" style="float: left; margin-left: 10px;">
					<i class="fa fa-plus"></i>
					</button>
					<span style="clear: both;">&nbsp;</span>
				 </form>
			  </li>';
		?>
	</ul>


	<h2>Status</h2>
	<script type="text/javascript" src="../js/jscolor/jscolor.js"></script>
	<ul class="list-group">
	<?php 
	$taskstatus = $taskpropDA->getAllStatus();
	if ($taskstatus != null) {
		while ($oneStatus = $taskstatus->fetch_assoc()) {
			$isDone = "";
			if ($oneStatus["isDone"] == 1) {
				$isDone = "checked=\"checked\"";
			}
			echo '
				<li class="list-group-item" >
					<form action="" method="post" style="display: inline">
						<input type="hidden" name="statusId" value="'.$oneStatus["id"].'" /> 
						<input type="text"
							name="statusname" class="form-control" value="'.$oneStatus["name"].'"
							style="width: 250px; float: left;" /> 
						<input
							class="color form-control" name="color" value="'.$oneStatus["color"].'"
							style="width: 150px; float: left; margin-left: 10px;" />
						<label style="margin-left: 10px; float: left;">
					      <input type="checkbox" name="isDone" style="margin-top: 10px" '.$isDone.'> is Done
					    </label>
						<button type="submit" class="btn btn-default"
							style="float: left; margin-left: 10px;">
							<i class="fa fa-check"></i>
						</button>
						<span style="clear: both;">&nbsp;</span>
					</form>
					<form action=""	method="post" style="display: inline" >
						<input type="hidden" name="deleteStatus" value="'.$oneStatus["id"].'" />
						<button type="submit" class="btn btn-danger" ><i class="fa fa-trash-o"></i></button>
					</form>
				</li>
				';
		}
	}
	echo '
		<li class="list-group-item" style="padding-bottom: 30px;">
			<form action="" method="post">
				<p style="float: left; margin-right: 30px;">New Status:</p>
				<input type="hidden" name="newStatus" value="true" /> 
				<input type="text"
					name="statusname" class="form-control" value=""
					style="width: 250px; float: left;" /> 
				<input
					class="color form-control" name="color" value=""
					style="width: 150px; float: left; margin-left: 10px;" />
				<label style="margin-left: 10px; float: left;">
			      	<input type="checkbox" name="isDone" style="margin-top: 10px"> is Done
			    </label>
				<button type="submit" class="btn btn-success"
					style="float: left; margin-left: 10px;">
					<i class="fa fa-plus"></i>
				</button>
				<span style="clear: both;">&nbsp;</span>
			</form>
		 </li>
			';
	?>
	</ul>
</div>
<?php 
include '../core/footer.php';