<?php 
session_start();
include_once dirname(__FILE__).'/logic/userDA.php';
include_once dirname(__FILE__).'/logic/permissionDA.php';
define("ROOTPATH", "//" . $_SERVER['SERVER_NAME'] . substr(dirname(__FILE__). '/../', strlen($_SERVER['DOCUMENT_ROOT'])));

if (!isset($_SESSION['userId'])) {
	header("Location: " . ROOTPATH . "login.php");
}

$permDA = new PermissionDA();
$userDA = new UserDA();
$logedInUser = $userDA->getUser($_SESSION["userId"]);

?>
<!DOCTYPE html>
<html>
	<head>
		<title>no-bug</title>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
		<meta http-equiv="content-type" content="text/html; charset=utf-8" />
		<meta name="author" content="Benj Fassbind & Jan Bucher" />

		<link rel="stylesheet" href="<?php echo ROOTPATH; ?>style/bootstrap.min.css" />
		<link rel="stylesheet/less" type="text/css" href="<?php echo ROOTPATH; ?>style/global.less" />
		<link rel="stylesheet/less" type="text/css" href="<?php echo ROOTPATH; ?>style/administration.less" />
		<script type="text/javascript" src="<?php echo ROOTPATH; ?>js/jquery-1.10.2.min.js" ></script>
		<script type="text/javascript" src="<?php echo ROOTPATH; ?>js/bootstrap.min.js"></script>
		<script type="text/javascript" src="<?php echo ROOTPATH; ?>js/less.js" ></script>
		<script type="text/javascript" src="<?php echo ROOTPATH; ?>js/global.js" ></script>
		<link rel="shortcut icon" href="<?php echo ROOTPATH; ?>icon.ico" type="image/x-icon" />
		<link rel="icon" href="<?php echo ROOTPATH; ?>icon.ico" type="image/x-icon" /> 
	</head>
	<body>
	
	<div id="header" class="navbar navbar-default navbar-fixed-top">
		<div id="header-wrapper">
			<a class="navbar-brand" href="<?php echo ROOTPATH; ?>">no-bug</a>
			<ul class="nav navbar-nav">
	      		<li class="<?php if (ACTIVE_MENU == "main") {echo 'active';}?>"><a href="<?php echo ROOTPATH; ?>">Main</a></li>
	      		<li class="<?php if (ACTIVE_MENU == "proj") {echo 'active';}?>"><a href="<?php echo ROOTPATH; ?>project.php">Projects</a></li>
	      		<?php 
	      		if ($permDA->isGeneralAdmininstrationAllowed()) {
	      		?>
	      		<li class="dropdown <?php if (ACTIVE_MENU == "administration") {echo 'active';}?>">
		        	<a class="dropdown-toggle" data-toggle="dropdown" href="#">Administration <b class="caret"></b></a>
			        <ul class="dropdown-menu" id="menuAdminDropdown" role="menu" aria-labelledby="dLabel">
			        	<li><a href="<?php echo ROOTPATH; ?>administration/users.php">Users</a></li>
			        	<li><a href="<?php echo ROOTPATH; ?>administration/groups.php">Groups</a></li>
			        	<li><a href="<?php echo ROOTPATH; ?>administration/projects.php">Projects</a></li>
			        	<li class="divider"></li>
			        	<li><a href="<?php echo ROOTPATH; ?>administration/settings.php">Global Settings</a></li>
			        </ul>
		      	</li>
		      	<?php 
		      	}
		      	?>
	      	</ul>
			
			 <ul class="nav navbar-nav navbar-right">
		      	<li class="dropdown">
		        	<a class="dropdown-toggle" data-toggle="dropdown" href="#"><?php echo $logedInUser["prename"] . " " . $logedInUser["surname"]?> <b class="caret"></b></a>
			        <ul class="dropdown-menu" id="menuUserDropdown" role="menu" aria-labelledby="dLabel">
			        	<li><a href="<?php echo ROOTPATH; ?>profil.php">Profil</a></li>
			        	<li class="divider"></li>
			        	<li><a href="<?php echo ROOTPATH; ?>core/logout.php">Logout</a></li>
			        </ul>
		      	</li>
		    </ul>
	    </div>
	</div>
		