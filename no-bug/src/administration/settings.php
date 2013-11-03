<!DOCTYPE html>
<html>
<head>
		<title>no-bug | pre alpha 0.0.1</title>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
		<meta name="author" content="Benj Fassbind & Jan Bucher" />
		<meta http-equiv="content-type" content="text/html; charset=utf-8" />
		<link rel="stylesheet" href="../style/bootstrap.min.css" />
		<link rel="stylesheet/less" type="text/css" href="../style/global.less" />
		<link rel="stylesheet/less" type="text/css" href="../style/administration.less" />
		<script type="text/javascript" src="../js/bootstrap.min.js"></script>
		<script type="text/javascript" src="//raw.github.com/less/less.js/master/dist/less-1.5.0.min.js" ></script>
		<script type="text/javascript" src="../js/global.js" ></script>
	</head>
<body style="padding-top: 70px" onload="javascript:document.loginform.loginUsername.focus();">
	<div id="header" class="navbar navbar-default navbar-fixed-top">
		<a class="navbar-brand" href="#">no-bug</a>
		<ul class="nav navbar-nav">
      		<li class=""><a href="#">Main</a></li>
      		<li class=""><a href="#">Projects</a></li>
      		<li class="active"><a href="#">Administration</a></li>
      	</ul>
		 <ul class="nav navbar-nav navbar-right">
	      	<li class="dropdown">
	        	<a href="#" class="dropdown-toggle" data-toggle="dropdown" onclick="hideshow(document.getElementById('menuUserDropdown'));" onblur="hideshow(document.getElementById('menuUserDropdown'));">Hans Vader <b class="caret"></b></a>
		        <ul class="dropdown-menu" id="menuUserDropdown" >
		        	<li><a href="#">Profil</a></li>
		        	<li class="divider"></li>
		        	<li><a href="#">Logout</a></li>
		        </ul>
	      	</li>
	    </ul>
	</div>
	
	<ul class="nav nav-tabs">
		<li><a href="users.html">Users</a></li>
		<li><a href="groups.html">Groups</a></li>
		<li><a href="projects.html">Projects</a></li>
		<li class="active"><a href="settings.html">Global Settings</a></li>
	</ul>
	
	
	<div id="main">
		<h1>Global Settings</h1>
		<form action="#" class="userEditForm">
			<h2>> General</h2>
			<table class="table">
				<tr>
					<th>Global Admin Group:</th>
					<td>
						<select class="form-control">
						  <option id="1" selected="selected">global-admin</option>
						  <option id="2">jquery-admin</option>
						  <option id="3">jquery-dev</option>
						  <option id="4">jquery-tester</option>
						  <option id="5">inf2abm</option>
						</select>
					</td>
				</tr>
			</table>
			<button type="submit" class="btn btn-primary">Save Changes</button>
		</form>
		
		<form action="#" class="userEditForm">
			<h2>> Infos</h2>
			<table class="table userEditTable">
				<tr>
					<th>Servername: </th>
					<td>Linux raspberrypi 3.2.27+ #250 PREEMPT Thu Oct 18 19:03:02 BST 2012 armv6l</td>
				</tr>
				<tr>
					<th>Total Tasks: </th>
					<td>20'432</td>
				</tr>
			</table>
		</form>
	</div>
	<div id="footer" >Copyright 2013 | Benj Fassbind & Jan Bucher</div>
</body>
</html>