<?php
define( 'ACTIVE_MENU', 'proj');
include_once 'core/header.php';
include_once 'core/logic/projectDA.php';
include_once 'core/logic/permissionDA.php';
include_once 'core/logic/taskpropDA.php';
include_once 'core/logic/componentDA.php';
$projDA = new ProjectDA();
$permDA = new PermissionDA();
$taskpropDA = new TaskpropDA();
$componentDA = new ComponentDA();

$selectedProject = null;
$alerts = "";
$edited = false;

if (isset($_GET["p"])) {
	$selectedProject = $projDA->getProject($_GET["p"]);
}

if (!isset($selectedProject)) {
	$permDA->echoPermissionDeniedAndDie();
}

if (!$permDA->isAdminOnProjectAllowed($selectedProject["id"])) {
	$permDA->echoPermissionDeniedAndDie();
}


// General Settings
if (isset($_POST["general"])) {
	$projDA->updateGeneral($selectedProject["id"], $_POST["editProjectname"], $_POST["editDescription"], "1.0");
	$alerts = $alerts . '<div class="alert alert-success alert-dismissable">
			<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
			<strong>Successfull</strong> changed General Settings </div>';
	$edited = true;
}

// New Component
if (isset($_POST["newComponent"])) {
	$componentDA->createComponent($_POST["componentname"],"" , $selectedProject["id"]);
	$alerts = $alerts . '<div class="alert alert-success alert-dismissable">
			<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
			<strong>Successfull</strong> create Component '.$_POST["componentname"].' </div>';
	$edited = true;
}

// Update Componentname
if (isset($_POST["componentID"])) {
	$componentDA->updateName($_POST["componentID"], $_POST["componentname"], $selectedProject["id"]);
	$alerts = $alerts . '<div class="alert alert-success alert-dismissable">
			<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
			<strong>Successfull</strong> update Component '.$_POST["componentname"].' </div>';
	$edited = true;
}

// Delete (just deactivate) Component
if (isset($_POST["deleteComponent"])) {
	$componentDA->deactivateComponent($_POST["deleteComponent"], $selectedProject["id"]);
	$alerts = $alerts . '<div class="alert alert-success alert-dismissable">
			<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
			<strong>Successfull</strong> deactivate Component</div>';
	$edited = true;
}

if ($edited) {
	$selectedProject = $projDA->getProject($_GET["p"]);
}

?>
<div id="main">
	<ol class="breadcrumb">
	  <li><a href="index.php">Home</a></li>
	  <li><a href="project.php?p=<?php echo $selectedProject["id"]; ?>">Project <?php echo $selectedProject["name"]; ?></a></li>
	  <li class="active">Project Settings</li>
	</ol>
	
	<?php echo $alerts; ?>
	<h1><?php echo $selectedProject["name"];?> Projectmanager</h1>
	<p class="text-muted"><em>Created by <?php $user = $userDA->getUser($selectedProject["meta_creatorID"]); echo $user["prename"] . " " . $user["surname"]; ?>
		(<?php echo $selectedProject["meta_createDate"]; ?>)</em></p>
	
	
	
	<h2 class="subtitle"><i class="fa fa-angle-double-right"></i> General</h2>
	<form action="?p=<?php echo $selectedProject["id"]; ?>"	method="post">
		<input type="hidden" name="general" value="true" />
		<table class="table userEditTable">
			<tr>
				<th>Projectname:</th>
				<td><input type="text" class="form-control" name="editProjectname"
					value="<?php echo $selectedProject["name"];?>"></td>
			</tr>
			<tr>
				<th>Description:</th>
				<td><textarea name="editDescription" class="form-control" id="desc"	
					onkeydown="resizeTextarea('desc')"><?php echo $selectedProject["description"] ?></textarea></td>
			</tr>
		</table>
		<script type="text/javascript">
			resizeTextarea('desc');
		</script>
		<button type="submit" class="btn btn-primary">Save Changes</button>
	</form>
	
	
	<h2 class="subtitle"><i class="fa fa-angle-double-right"></i> Components</h2>
	<ul class="list-group">
		<?php 
			$componentsOfTheProject = $componentDA->getComponents($selectedProject["id"]);

			while ($oneComponent = $componentsOfTheProject->fetch_assoc()) {
				echo '
				  <li class="list-group-item">
				  	<form action="?p='.$selectedProject["id"].'"	method="post" style="display: inline;" > 
				  		<input type="text" class="form-control" name="componentname" value="'.$oneComponent["name"].'" style="width: 250px; float: left;" />
				  		<input type="hidden" name="componentID" value="'.$oneComponent["id"].'" />
				  		<button type="submit" class="btn btn-default" style="margin-left: 30px;"><i class="fa fa-check"></i></button>
				  	</form>
					<form action="?p='.$selectedProject["id"].'"	method="post" style="display: inline" >
						<input type="hidden" name="deleteComponent" value="'.$oneComponent["id"].'" />
						<button type="submit" class="btn btn-danger" style="margin-left: 10px;"><i class="fa fa-trash-o"></i></button>
					</form>
				  </li>';
			}
		?>

	  <li class="list-group-item">
	  	<form action="?p=<?php echo $selectedProject["id"]; ?>"	method="post">
	  		<p style="float: left; margin-right: 30px;">New Component:</p>
	  		<input type="hidden" name="newComponent" value="true" />
	  		<input type="text" class="form-control" name="componentname" value="" style="width: 250px; float: left;" />
	  		<button type="submit" class="btn btn-success" style="margin-left: 30px;"><i class="fa fa-plus"></i></button>
	  	</form>
	  </li>
	</ul>
</div>
<?php
include 'core/footer.php';