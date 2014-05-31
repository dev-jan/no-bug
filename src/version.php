<?php
define( 'ACTIVE_MENU', 'proj');
include_once 'core/header.php';
include_once 'core/logic/projectDA.php';
include_once 'core/logic/permissionDA.php';

$selectedProject = null;
$projDA = new ProjectDA();
$permDA = new PermissionDA();
$alerts = "";

if (isset($_GET["list"])) {
	include_once 'core/logic/taskDA.php';
	$taskDA = new TaskDA();
	$currentVersion = $projDA->getVersionById($_GET["list"])->fetch_assoc();
	$selectedProject = $projDA->getProject($currentVersion["project_id"]);
	echo '<div id="main">
			<ol class="breadcrumb">
			  <li><a href="index.php">Home</a></li>
			  <li><a href="project.php?p='. $selectedProject["id"] .'">Project '.$selectedProject["name"].'</a></li>
			  <li><a href="version.php?p='. $selectedProject["id"] .'">Versions</a></li>
			  <li class="active">Changelog '.$currentVersion["name"].'</li>
			</ol>
		
			<h1>Changelog '.$selectedProject["name"].' '.$currentVersion["name"].'</h1>
			<div>';
	$tasks = $taskDA->getTasksByVersionID($_GET["list"]);
	if ($tasks != null) {
		$areTaskAvailable = false;
		while ($oneTask = $tasks->fetch_assoc()) {
			$component = $oneTask["componentName"];
			if ($component != "") {
				$component = '<span class="text-success pull-center" style="margin-left: 20px;">Component: '.$oneTask["componentName"].'</span>';
			}
			$assignee = $oneTask["assigneePrename"];
			if ($assignee == "") {
				$assignee = "none";
			}
			echo '<a href="task.php?t='.$oneTask["id"].'" class="list-group-item">
							<div>
				              <b>'.$oneTask["key"].'-'.$oneTask["id"].'</b>: '.
							              $oneTask["summary"]. '' .
							              ' <span class="badge pull-right" style="background-color: '.$oneTask["color"].'">'.$oneTask["name"].'</span>
	  		                </div>
							<div style="margin-left: 10px;"><em>
	  							<span class="text-info">Assignee: '.$assignee.' '.$oneTask["assigneeSurname"].'</span>
	  							'.$component.'
						    </em></div>
						  </a>';
			$areTaskAvailable = true;
		}
		if (!$areTaskAvailable) {
			echo '<span class="list-group-item">No Tasks founded...</span>';
		}
	}
	else {
		$permDA->echoPermissionDeniedAndDie();
	}
	echo '</div></div>';
	include 'core/footer.php';
	die();
}

if (!isset($_GET["p"])) {
	$permDA->echoPermissionDeniedAndDie();
}
$writeAllowed = $permDA->isAdminOnProjectAllowed($_GET["p"]);

if (isset($_GET["p"])) {
	$selectedProject = $projDA->getProject($_GET["p"]);
}

if (!isset($selectedProject)) {
	$permDA->echoPermissionDeniedAndDie();
}

if (!$permDA->isReadOnProjectAllowed($_GET["p"])) {
	$permDA->echoPermissionDeniedAndDie();
}

if (isset($_POST["newVersion"]) && $writeAllowed) {
	$isReleased = 0;
	if (isset($_POST["released"])) {
		$isReleased = 1;
	}
	$date = null;
	if (isset($_POST["releaseDay"]) && $_POST["releaseDay"] != "") {
		$date = $_POST["releaseDay"];
	}
	$projDA->createNewVersionForProject($_POST["p"], $_POST["versionname"], $_POST["description"], $isReleased, $date);
}

if (isset($_POST["editVersion"]) && $writeAllowed) {
	$isReleased = 0;
	if (isset($_POST["released"])) {
		$isReleased = 1;
	}
	$date = null;
	if (isset($_POST["releaseDay"]) && $_POST["releaseDay"] != "") {
		$date = $_POST["releaseDay"];
	}
	$projDA->editVersion($_POST["versionId"], $_POST["versionname"], $_POST["description"], $isReleased, $date);
}

if (isset($_GET["del"]) && $writeAllowed) {
	$projDA->deleteVersion($_GET["versionId"]);
}

?>

<div id="main">
	<ol class="breadcrumb">
	  <li><a href="index.php">Home</a></li>
	  <li><a href="project.php?p=<?php echo $selectedProject["id"]; ?>">Project <?php echo $selectedProject["name"]; ?></a></li>
	  <li class="active">Version Overview</li>
	</ol>
	
	<h1>Versions Overview</h1>
	<div>
		<?php if ($writeAllowed) {?>
		<button class="btn btn-default" data-toggle="modal" data-target="#newVersionModal">
		  <i class="fa fa-plus-square"></i> New Version...
		</button>
		
		<div class="modal fade" id="newVersionModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		  <div class="modal-dialog">
		    <div class="modal-content">
		      <form action="" method="post">
			      <div class="modal-header">
			        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
			        <h4 class="modal-title" id="myModalLabel">New Version...</h4>
			      </div>
			      <div class="modal-body">
		      		<input type="hidden" name="newVersion" value="true" />
		      		<input type="hidden" name="p" value="<?php echo $selectedProject["id"];?>" />
			        <table width="100%">
			        	<tr>
			        		<td>Name:</td>
			        		<td><input type="text" class="form-control" name="versionname" value="" /></td>
			        	</tr>
			        	<tr>
			        		<td>Description:</td>
			        		<td><input type="text" class="form-control" name="description" value="" /></td>
			        	</tr>
			        	<tr>
			        		<td>Release Day:</td>
			        		<td><input type="date" class="form-control" name="releaseDay" value="" /></td>
			        	</tr>
			        	<tr>
			        		<td>Released?</td>
			        		<td><input type="checkbox" class="form-control" name="released"/></td>
			        	</tr>
			        </table>
			      </div>
			      <div class="modal-footer">
			        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
			        <button type="submit" class="btn btn-primary">Create!</button>
			      </div>
		      </form>
		    </div>
		  </div>
		</div>
		<?php }?>
	</div>
	<h2><i class="fa fa-suitcase"></i> Unreleased Versions</h2>
	<div class="list-group">
	<?php 
	$allUnreleasedVersions = $projDA->getVersionsOfProject($_GET["p"], false);
	$existsVersions = false;
	while ($oneVersion = $allUnreleasedVersions->fetch_assoc()) {
		$dateColor = "#2FBD47";
		$existsVersions = true;
		$versionDate = "";
		if ($oneVersion["doneDate"] != "") {
			$versionDate = explode("-", $oneVersion["doneDate"]);
			$realDate = mktime("0", "0", "0", $versionDate[1],$versionDate[2], $versionDate[0]);
			if ($realDate <= time()) {
				$dateColor = "#FF0000";
			}
		}
		
		$releaseDateString = "";
		if ($versionDate != "") {
			$releaseDateString = 'Release Day: '.$oneVersion["doneDate"];
		}
		if ($writeAllowed) {
			echo '<a href="#" class="list-group-item" data-toggle="modal" data-target="#version'.$oneVersion["id"].'Modal">
				  	<i class="fa fa-tag"></i> <span style="font-weight: bold; width: 200px; display: inline-block;">
						'.$oneVersion["name"].'</span> '.$oneVersion["description"].' <span style="float:right; color: '.$dateColor.'; 
						font-style:italic;">'.$releaseDateString.'</span>
				  </a>';
			echo '<div class="modal fade" id="version'.$oneVersion["id"].'Modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
					  <div class="modal-dialog">
					    <div class="modal-content">
					      <form action="" method="post">
		  			          <input type="hidden" name="editVersion" value="true" />
							  <input type="hidden" name="versionId" value="'.$oneVersion["id"].'" />
						      <div class="modal-header">
						        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
						        <h4 class="modal-title" id="myModalLabel">Version '.$oneVersion["name"].'</h4>
						      </div>
						      <div class="modal-body">
					      		<input type="hidden" name="versionId" value="'.$oneVersion["id"].'" />
						        <table width="100%">
						        	<tr>
						        		<td>Name:</td>
						        		<td><input type="text" class="form-control" name="versionname" value="'.$oneVersion["name"].'" /></td>
						        	</tr>
						        	<tr>
						        		<td>Description:</td>
						        		<td><input type="text" class="form-control" name="description" value="'.$oneVersion["description"].'" /></td>
						        	</tr>
						        	<tr>
						        		<td>Release Day:</td>
						        		<td><input type="date" class="form-control" name="releaseDay" value="'.$oneVersion["doneDate"].'" /></td>
						        	</tr>
						        	<tr>
						        		<td>Released?</td>
						        		<td><input type="checkbox" class="form-control" name="released" /></td>
						        	</tr>
						        </table>
						      </div>
						      <div class="modal-footer">
								<a href="?list='.$oneVersion["id"].'" class="btn btn-default">View Changelog</a>
				        		<a href="?p='.$_GET["p"].'&versionId='.$oneVersion["id"].'&del=true" class="btn btn-danger" onclick="return askBeforSending(\'Delete this Version forever?\')"><i class="fa fa-trash-o"></i> Delete</a>
						        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
						        <button type="submit" class="btn btn-primary">Save changes</button>
						      </div>
					      </form>
					    </div>
					  </div>
					</div>';
		}
		else {
			echo '<a href="?list='.$oneVersion["id"].'" class="list-group-item">
				  	<i class="fa fa-tag"></i> <span style="font-weight: bold; width: 200px; display: inline-block;">
						'.$oneVersion["name"].'</span> '.$oneVersion["description"].' <span style="float:right; color: '.$dateColor.';
						font-style:italic;">'.$releaseDateString.'</span>
				  </a>';
		}
	} 
	if (!$existsVersions) {
		echo '<span class="list-group-item"><i class="fa fa-tag"></i> Nothing to display... </span>';
	}
	?>
	</div>
		
	<h2 style="margin-top: 50px;"><i class="fa fa-truck"></i> Released Versions</h2>
	<div class="list-group">
		<?php 
		$allReleasedVersions = $projDA->getVersionsOfProject($_GET["p"], true);
		$existsVersions = false;
		while ($oneVersion = $allReleasedVersions->fetch_assoc()) {
			$existsVersions = true;
			$checkedText = "";
			if ($oneVersion == 1) {
				$checkedText = 'checked="checked"';
			}
			$releaseDateString = "";
			if ($oneVersion["doneDate"] != "") {
				$releaseDateString = 'Release Day: '.$oneVersion["doneDate"];
			}
			if ($writeAllowed) {
				echo '<a href="#" class="list-group-item" data-toggle="modal" data-target="#version'.$oneVersion["id"].'Modal">
					  	<i class="fa fa-tag"></i> <span style="font-weight: bold; width: 200px; display: inline-block;">
							'.$oneVersion["name"].'</span> '.$oneVersion["description"].' <span style="float:right; color: #2FBD47; 
							font-style:italic;">'.$releaseDateString.'</span>
					  </a>';
				echo '<div class="modal fade" id="version'.$oneVersion["id"].'Modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
					  <div class="modal-dialog">
					    <div class="modal-content">
					      <form action="" method="post">
							  <input type="hidden" name="editVersion" value="true" />
							  <input type="hidden" name="versionId" value="'.$oneVersion["id"].'" />
						      <div class="modal-header">
						        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
						        <h4 class="modal-title" id="myModalLabel">Version '.$oneVersion["name"].'</h4>
						      </div>
						      <div class="modal-body">
					      		<input type="hidden" name="versionId" value="'.$oneVersion["id"].'" />
						        <table width="100%">
						        	<tr>
						        		<td>Name:</td>
						        		<td><input type="text" class="form-control" name="versionname" value="'.$oneVersion["name"].'" /></td>
						        	</tr>
						        	<tr>
						        		<td>Description:</td>
						        		<td><input type="text" class="form-control" name="description" value="'.$oneVersion["description"].'" /></td>
						        	</tr>
						        	<tr>
						        		<td>Release Day:</td>
						        		<td><input type="date" class="form-control" name="releaseDay" value="'.$oneVersion["doneDate"].'" /></td>
						        	</tr>
						        	<tr>
						        		<td>Released?</td>
						        		<td><input type="checkbox" class="form-control" name="released" checked="checked" /></td>
						        	</tr>
						        </table>
						      </div>
						      <div class="modal-footer">
								<a href="?list='.$oneVersion["id"].'" class="btn btn-default">View Changelog</a>
			      				<a href="?p='.$_GET["p"].'&versionId='.$oneVersion["id"].'&del=true" class="btn btn-danger" onclick="return askBeforSending(\'Delete this Version forever?\')"><i class="fa fa-trash-o"></i> Delete</a>
						        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
						        <button type="submit" class="btn btn-primary">Save changes</button>
						      </div>
					      </form>
					    </div>
					  </div>
					</div>';
			}
			else {
				echo '<a href="?list='.$oneVersion["id"].'" class="list-group-item">
				  	<i class="fa fa-tag"></i> <span style="font-weight: bold; width: 200px; display: inline-block;">
						'.$oneVersion["name"].'</span> '.$oneVersion["description"].' <span style="float:right; color: #2FBD47;
						font-style:italic;">'.$releaseDateString.'</span>
				  </a>';
			}
		}
		
		if (!$existsVersions) {
			echo '<span class="list-group-item"><i class="fa fa-tag"></i> Nothing to display... </span>';
		}
		
		?>
	</div>
	
</div>

<?php 
include 'core/footer.php';