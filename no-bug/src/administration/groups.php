<?php
define( 'ACTIVE_MENU', 'administration');
include_once '../core/header.php';
include_once '../core/logic/groupDA.php';
include_once '../core/logic/permissionDA.php';
include_once '../core/logic/adminDA.php';

$permDA = new PermissionDA();
if (!$permDA->isGeneralAdmininstrationAllowed()) {
	$permDA->echoPermissionDeniedAndDie();
}
?>
<div id="main">
	<?php 
	$adminDA = new AdminDA();
	$adminDA->getAdminMenu("groups.php");
	?>
	<h1><i class="fa fa-users"></i> Groups</h1>
	<?php 
	if (isset($_GET["showDeactivated"])) {
		echo '<form action="#" method="get" style="float: left;"><button type="submit" class="btn btn-default"><i class="fa fa-eye-slash"></i> Show activated Groups</button></form>';
	}
	else {
		echo '<form action="groups.php" method="get" style="float: left;"><input type="hidden" name="showDeactivated" value="true" /><button type="submit" class="btn btn-default" ><i class="fa fa-eye"></i> Show all Groups</button></form>';
	}
	?>
	<form action="newGroup.php">
		<button type="submit" class="btn btn-success"
			style="float: right; margin-bottom: 10px;"><i class="fa fa-plus-square"></i> New Group</button>
	</form>
	<table class="table table-hover">
		<tr>
			<th>Name</th>
			<th>Member of</th>
			<th>Members</th>
			<th>actions</th>
		</tr>
		<?php 
		$groupDA = new GroupDA();
		$allGroups = false;
		if (isset($_GET["showDeactivated"])) {
			$allGroups = true;
		}
		$groupDA->printAllGroupsTable($allGroups);
		?>
	</table>
</div>
<?php 
include '../core/footer.php';
?>