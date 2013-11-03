<?php 
session_start();

define("ROOTPATH", "//" . $_SERVER['SERVER_NAME'] . substr(dirname(__FILE__). '/../', strlen($_SERVER['DOCUMENT_ROOT'])));

if (!isset($_SESSION['userId'])) {
	header("Location: " . ROOTPATH . "login.php");
}

include_once dirname(__FILE__).'/logic/userDA.php';
$userDA = new UserDA();

$logedInUser = $userDA->getUser($_SESSION["userId"]);

?>
<!DOCTYPE html>
<html>
	<head>
		<title>no-bug | pre alpha 0.0.1</title>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
		<meta name="author" content="Benj Fassbind & Jan Bucher" />
		<meta http-equiv="content-type" content="text/html; charset=utf-8" />

		<link rel="stylesheet" href="<?php echo ROOTPATH; ?>style/bootstrap.min.css" />
		<link rel="stylesheet/less" type="text/css" href="<?php echo ROOTPATH; ?>style/global.less" />
		<link rel="stylesheet/less" type="text/css" href="<?php echo ROOTPATH; ?>style/administration.less" />
		<script type="text/javascript" src="<?php echo ROOTPATH; ?>js/jquery-1.10.2.min.js" ></script>
		<script type="text/javascript" src="<?php echo ROOTPATH; ?>js/bootstrap.min.js"></script>
		<script type="text/javascript" src="<?php echo ROOTPATH; ?>js/less.js" ></script>
		<script type="text/javascript" src="<?php echo ROOTPATH; ?>js/global.js" ></script>
	</head>
	<body>
	
	<!-- The Main navigation Head. -->
	<div id="header" class="navbar navbar-default navbar-fixed-top">
		<div id="header-wrapper">
			<a class="navbar-brand" href="#">no-bug</a>
			<ul class="nav navbar-nav">
	      		<li class="<?php if (ACTIVE_MENU == "main") {echo 'active';}?>"><a href="<?php echo ROOTPATH; ?>">Main</a></li>
	      		<li class="<?php if (ACTIVE_MENU == "proj") {echo 'active';}?>"><a href="#">Projects</a></li>
	      		<li class="<?php if (ACTIVE_MENU == "administration") {echo 'active';}?>"><a href="<?php echo ROOTPATH; ?>administration">Administration</a></li>
	      	</ul>
			
			 <ul class="nav navbar-nav navbar-right">
		      	<li class="dropdown">
		        	<a class="dropdown-toggle" data-toggle="dropdown" href="#"><?php echo $logedInUser["prename"] . " " . $logedInUser["surname"]?> <b class="caret"></b></a>
			        <ul class="dropdown-menu" id="menuUserDropdown" role="menu" aria-labelledby="dLabel">
			        	<li><a href="#">Profil</a></li>
			        	<li class="divider"></li>
			        	<li><a href="<?php echo ROOTPATH; ?>core/logout.php">Logout</a></li>
			        </ul>
		      	</li>
		    </ul>
	    </div>
	</div>
		