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
		$groupDA->printAllGroupsTable(false);
		?>
	</table>
</div>
<?php 
include '../core/footer.php';
?>