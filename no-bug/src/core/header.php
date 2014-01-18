<?php 
session_start();
include_once dirname(__FILE__).'/logic/userDA.php';
include_once dirname(__FILE__).'/logic/permissionDA.php';
include_once dirname(__FILE__).'/logic/settingsDA.php';
define("ROOTPATH", "//" . $_SERVER['SERVER_NAME'] . substr(dirname(__FILE__). '/../', strlen($_SERVER['DOCUMENT_ROOT'])));

if (!isset($_SESSION['nobug'.RANDOMKEY.'userId'])) {
	header("Location: " . ROOTPATH . "login.php");
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
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
		<meta http-equiv="content-type" content="text/html; charset=utf-8" />
		<meta name="author" content="Benj Fassbind & Jan Bucher" />

		<link rel="stylesheet" href="<?php echo ROOTPATH; ?>style/bootstrap.min.css" />
		<link href="//netdna.bootstrapcdn.com/font-awesome/4.0.3/css/font-awesome.css" rel="stylesheet">
		<link rel="stylesheet/less" type="text/css" href="<?php echo ROOTPATH; ?>style/global.less" />
		<link rel="stylesheet/less" type="text/css" href="<?php echo ROOTPATH; ?>style/administration.less" />
		<script type="text/javascript" src="<?php echo ROOTPATH; ?>js/jquery-1.10.2.min.js" ></script>
		<script type="text/javascript" src="<?php echo ROOTPATH; ?>js/bootstrap.min.js"></script>
		<script type="text/javascript" src="<?php echo ROOTPATH; ?>js/less.js" ></script>
		<script type="text/javascript" src="<?php echo ROOTPATH; ?>js/global.js" ></script>
		<link rel="shortcut icon" href="<?php echo ROOTPATH; ?>icon.ico" type="image/x-icon" />
		<link rel="icon" href="<?php echo ROOTPATH; ?>icon.ico" type="image/x-icon" /> 
		<?php echo $settingsDA->getTrackingCode(); ?>
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
			        	<li class="divider"></li>
			        	<li><a href="<?php echo ROOTPATH; ?>administration/settings.php"><i class="fa fa-globe"></i> Global Settings</a></li>
			        </ul>
		      	</li>
		      	<?php 
		      	}
		      	?>
	      	</ul>
			
			 <ul class="nav navbar-nav navbar-right">
		      	<li class="dropdown">
		        	<a class="dropdown-toggle" data-toggle="dropdown" href="#"><i class="fa fa-user"></i> <?php echo $logedInUser["prename"] . " " . $logedInUser["surname"]?> <b class="caret"></b></a>
			        <ul class="dropdown-menu" id="menuUserDropdown" role="menu" aria-labelledby="dLabel">
			        	<li><a href="<?php echo ROOTPATH; ?>profil.php"><i class="fa fa-user"></i> Profil</a></li>
			        	<li class="divider"></li>
			        	<li><a href="<?php echo ROOTPATH; ?>core/logout.php"><i class="fa fa-sign-out"></i> Logout</a></li>
			        </ul>
		      	</li>
		    </ul>
	    </div>
	</div>
		