<?php 
session_start();

define("ROOTPATH", "//" . $_SERVER['SERVER_NAME'] . substr(dirname(__FILE__). '/../', strlen($_SERVER['DOCUMENT_ROOT'])));

if (!isset($_SESSION['userId'])) {
	header("Location: " . ROOTPATH . "login.php");
}

?>

<html>
	<head>
		<title>no-bug | pre alpha 0.0.1</title>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
		<meta name="author" content="Benj Fassbind & Jan Bucher" />
		<meta http-equiv="content-type" content="text/html; charset=utf-8" />
		<link rel="stylesheet" href="style/bootstrap.min.css" />
		<link rel="stylesheet/less" type="text/css" href="style/global.less" />
		<script type="text/javascript" src="js/bootstrap.min.js"></script>
		<script type="text/javascript" src="js/less.js" ></script>
	</head>
	<body>
	
	<!-- The Main navigation Head. -->
	<div id="header" class="navbar navbar-default navbar-fixed-top">
		<a class="navbar-brand" href="#">no-bug</a>
		<ul class="nav navbar-nav">
      		<li class="active"><a href="#">Main</a></li>
      		<li class=""><a href="#">Projects</a></li>
      		<li class=""><a href="#">Administration</a></li>
      	</ul>
		
		 <ul class="nav navbar-nav navbar-right">
	      	<li class="dropdown">
	        	<a href="#" class="dropdown-toggle" data-toggle="dropdown">Hans Vader <b class="caret"></b></a>
		        <ul class="dropdown-menu">
		        	<li><a href="#">Action</a></li>
		        	<li><a href="#">Another action</a></li>
		        	<li><a href="#">Something else here</a></li>
		        	<li class="divider"></li>
		        	<li><a href="#">Separated link</a></li>
		        </ul>
	      	</li>
	    </ul>
	</div>
		