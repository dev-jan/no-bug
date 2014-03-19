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

$groupDA = new GroupDA();
$alerts = "";

if (isset($_POST["createGroup"])) {
	$groupDA->addGroup($_POST["newGroupname"]);
	$alerts = $alerts . '<div class="alert alert-success alert-dismissable">
			<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
			<strong>Successfull</strong> created Group "'.$_POST["newGroupname"].'"</div>';
}

?>
<div id="main">
	<?php echo $alerts; ?>
	<?php 
	$adminDA = new AdminDA();
	$adminDA->getAdminMenu("groups.php");
	?>
	<h1><i class="fa fa-users"></i> New Group...</h1>
	<form action="" class="userEditForm" method="post">
		<input type="hidden" name="createGroup" value="true" />
		<table class="table userEditTable">
			<tr>
				<th>Name:</th>
				<td><input type="text" class="form-control" name="newGroupname"
					placeholder="Enter Groupname here..."></td>
			</tr>
		</table>
		<button type="submit" class="btn btn-primary">Create Group!</button>
	</form>
</div>
<?php 
include '../core/footer.php';
?>