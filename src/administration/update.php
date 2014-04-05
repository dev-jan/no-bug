<?php
/* Description: Updates to platform "no-bug" */

// Include core files
define( 'ACTIVE_MENU', 'administration');
include_once '../core/header.php';
include_once '../core/logic/logDA.php';
include_once '../core/logic/permissionDA.php';
include_once '../core/logic/adminDA.php';

// DataAccess initialisation
$permDA = new PermissionDA();
$adminDA = new AdminDA();
$error = "";
$successfullMessage = "";

// Check if the user is allowed to access this page
if (!$permDA->isGeneralAdmininstrationAllowed()) {
	$permDA->echoPermissionDeniedAndDie();
}

// Delete existing ZIP file
if (isset($_GET["delzip"])) {
	unlink("../core/update.zip");
	echo '<META HTTP-EQUIV="refresh" content="0;URL=update.php">';
	die();
}

// Update via Zip
if (isset($_POST["makeUpdateWithZip"])) {
	if ($_FILES["file"]["error"] > 0) {
		$error = "Error: " . $_FILES["updatefile"]["error"];
	}
	else {
		if ($_FILES["file"]["type"] == "application/zip" 
				|| $_FILES["file"]["type"] == "application/octet-stream") {
			if (!file_exists("../core/update.zip")) {
				@fopen("../core/update.zip", 'w');
			}
			if (is_writable("../core/update.zip")) {
				move_uploaded_file($_FILES["file"]["tmp_name"], "../core/update.zip");
			}
			else {
				$error = "Permission denied on serveral files. Please give full write 
				access to the no-bug user. After that, reload this page. <br style=\"margin-bottom: 15px;\" />
				via shell: <pre style=\"display: inline;\">chmod 777 -R " . str_replace("administration/update.php", "", __FILE__) ."</pre><br style=\"margin-bottom: 15px;\" />
				via FTP: Grant write access to all users to ".str_replace("administration/update.php", "", __FILE__)." (include all subfolders)
				";
			}
		}
		else {
			$error = "Error: The File is not a zip file... (it was ".$_FILES["file"]["type"].")";
		}
	}
}

if (isset($_GET["update"])) {
	// Check if the no-bug is fully writeable...
	if (!(is_writable("../administration") && 
		is_writable("../core") &&
		is_writable("../style"))) {
		$error = "Permission denied on serveral files. Please give full write 
				access to the no-bug user. After that, reload this page. <br style=\"margin-bottom: 15px;\" />
				via shell: <pre style=\"display: inline;\">chmod 777 -R " . str_replace("administration/update.php", "", __FILE__) ."</pre><br style=\"margin-bottom: 15px;\" />
				via FTP: Grant write access to all users (include all subfolders)
				";
	}
	else {
		include "../core/version.php";
		$currentVersion = $internalVersion;
		// Backup the configuration file
		copy("../nobug-config.php", "../nobug-config.php_bak");
		$zip = new ZipArchive;
		if ($zip->open('../core/update.zip') === true) {
			// Unzip and override the new files
			$zip->extractTo('../'); 
			$zip->close();
			// Put the configuration file back on his old position
			copy("../nobug-config.php_bak", "../nobug-config.php");
			include '../core/updater.php';
			update($currentVersion);
			//Hack to get the new version
			copy("../core/version.php", "../core/newversion.php");
			include '../core/newversion.php';
			// Show the new Version
			$successfullMessage = "Update successfull to ".$versionname." <br />";
			// Remove temporary files
			unlink("../nobug-config.php_bak");
			unlink("../core/newversion.php");
			unlink("../core/update.zip");
		} else {
			$error = 'Unzip failed';
		}
	}
}

// Interface...
?>
<div id="main">
	<h1>Update no-bug</h1>	
	
	<a href="update.php" class="btn btn-success" style="margin-bottom:20px" >Reload...</a>
	
	<?php 
	if ($error != "") {
		echo '<div class="alert alert-danger">'.$error.'</div>';
	}
	?>
	
	<?php 
	if ($successfullMessage != "") {
		echo '<div class="alert alert-success">'.$successfullMessage.'</div>';
	}
	?>
	
	<?php 
	if (file_exists("../core/update.zip")) {
		echo '<div class="alert alert-success">Uploaded Zip found. Click here to continue: <a href="?update=true">Update!</a> <a href="?delzip=true" class="close">&times;</a></div>';
	}
	include "../core/version.php";
	?>
	
	<h2>Informations</h2>
	<table class="table">
		<tr>
			<th>Current Version:</th>
			<td><?php echo $versionname . " (" . $compileDate . ")"; ?></td>
		</tr>
		<tr>
			<th>Latest Version:</th>
			<td>?</td>
		</tr>
	</table>
	
	<h2>Start Update</h2>
	<form action="" method="post" enctype="multipart/form-data">
		<input type="hidden" name="makeUpdateWithZip" value="true" /> 
		<h4>To start the update, upload the newest version of no-bug (as zip):</h4>
		<input type="file" name="file" id="uploadForm" style="float: left; margin-top: 5px;"/>
		<input type="submit" value="upload" class="btn btn-primary" />
	</form>
</div>
<?php 
include '../core/footer.php';