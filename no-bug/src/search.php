<?php
	define( 'ACTIVE_MENU', 'proj');
	include_once 'core/header.php';
	include_once 'core/logic/projectDA.php';
	include_once 'core/logic/permissionDA.php';
	include_once 'core/logic/taskpropDA.php';
	$projDA = new ProjectDA();
	$permDA = new PermissionDA();
	$taskpropDA = new TaskpropDA();
?>
<div id="main">
	<div class="alert alert-info"><i class="fa fa-cogs"></i> We are currently working on this feature...</div>
	

</div>
<?php
include 'core/footer.php';
?>