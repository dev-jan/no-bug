<?php 
session_start();
include_once dirname(__FILE__).'/logic/userDA.php';
include_once dirname(__FILE__).'/logic/permissionDA.php';
include_once dirname(__FILE__).'/logic/settingsDA.php';
include_once dirname(__FILE__).'/version.php';
if (!defined("ROOTPATH")) {
	define("ROOTPATH", "//" . $_SERVER['SERVER_NAME'] . substr(dirname(__FILE__). '/../', strlen($_SERVER['DOCUMENT_ROOT'])));
}

if (!isset($_SESSION['nobug'.RANDOMKEY.'userId'])) {
	header("Location: " . ROOTPATH . "login.php");
	die();
}

$permDA = new PermissionDA();
$settingsDA = new SettingsDA();
$userDA = new UserDA();
$logedInUser = $userDA->getUser($_SESSION['nobug'.RANDOMKEY.'userId']);

?>
<!DOCTYPE html>
<html>
<head>
	<title>no-bug | <?php echo $settingsDA->getPlatformName(); ?></title>
	<?php 
	include dirname(__FILE__).'/meta.php';
	echo $settingsDA->getTrackingCode(); 
	?>
</head>
<body>

<div id="header" class="navbar navbar-default navbar-fixed-top">
	<div id="header-wrapper">
		<a class="navbar-brand" href="<?php echo ROOTPATH; ?>"><i class="fa fa-bug"></i> no-bug</a>
		<ul class="nav navbar-nav">
      		<li class="<?php if (ACTIVE_MENU == "main") {echo 'active';}?>"><a href="<?php echo ROOTPATH; ?>"><i class="fa fa-home"></i> Main</a></li>
      		<li class="dropdown <?php if (ACTIVE_MENU == "proj") {echo 'active';}?>">
      			<a class="dropdown-toggle" data-toggle="dropdown" href="#"><i class="fa fa-folder-open"></i> Projects <b class="caret"></b></a>
      			<ul class="dropdown-menu" id="menuProjDropdown" role="menu" aria-labelledby="dLabel">
      				<?php 
      				$allAllowedProjects = $permDA->getAllAllowedProjects($_SESSION['nobug'.RANDOMKEY.'userId']);
      				if ($allAllowedProjects != null) {
	      				while ($oneProject = $allAllowedProjects->fetch_assoc()) {
	      					echo '<li><a href="'.ROOTPATH.'project.php?p='.$oneProject["id"].'">'.$oneProject["name"].'</a></li>';
	      				}
      				}
      				?>
		        	<li class="divider"></li>
		        	<li><a href="<?php echo ROOTPATH; ?>project.php"><i class="fa fa-sitemap"></i> All Projects</a></li>
		        </ul>
      		</li>
      		<?php 
      		if ($permDA->isGeneralAdmininstrationAllowed()) {
      		?>
      		<li class="dropdown <?php if (ACTIVE_MENU == "administration") {echo 'active';}?>">
	        	<a class="dropdown-toggle" data-toggle="dropdown" href="#"><i class="fa fa-cog"></i> Administration <b class="caret"></b></a>
		        <ul class="dropdown-menu" id="menuAdminDropdown" role="menu" aria-labelledby="dLabel">
		        	<li><a href="<?php echo ROOTPATH; ?>administration/users.php">Users</a></li>
		        	<li><a href="<?php echo ROOTPATH; ?>administration/groups.php">Groups</a></li>
		        	<li><a href="<?php echo ROOTPATH; ?>administration/projects.php">Projects</a></li>
		        	<li><a href="<?php echo ROOTPATH; ?>administration/taskproperties.php">Taskproperties</a></li>
		        	<li><a href="<?php echo ROOTPATH; ?>administration/log.php">Log</a></li>
		        	<li class="divider"></li>
		        	<li><a href="<?php echo ROOTPATH; ?>administration/settings.php"><i class="fa fa-globe"></i> Global Settings</a></li>
		        </ul>
	      	</li>
	      	<?php 
	      	}
	      	?>
      	</ul>
		
		 <ul class="nav navbar-nav navbar-right">
			<form class="navbar-form navbar-left" role="search" action="<?php echo ROOTPATH; ?>search.php" method="get">
	        	<div class="form-group">
	          		<input type="text" class="form-control" name="s" placeholder="Search">
	        	</div>
	        	<button type="submit" class="btn btn-default"><i class="fa fa-search"></i></button>
	        </form>
	      	<li class="dropdown">
	        	<a class="dropdown-toggle" data-toggle="dropdown" href="#"><i class="fa fa-user"></i> <?php echo $logedInUser["prename"] . " " . $logedInUser["surname"]?> <b class="caret"></b></a>
		        <ul class="dropdown-menu" id="menuUserDropdown" role="menu" aria-labelledby="dLabel">
		        	<li><a href="<?php echo ROOTPATH; ?>profil.php"><i class="fa fa-user"></i> Profile</a></li>
		        	<li class="divider"></li>
		        	<li><a href="<?php echo ROOTPATH; ?>core/logout.php"><i class="fa fa-sign-out"></i> Logout</a></li>
		        </ul>
	      	</li>
	    </ul>
    </div>
</div>