<?php
	define( 'ACTIVE_MENU', 'administration');
	include_once '../core/header.php';
	include_once '../core/logic/userDA.php'	;
?>	
<div id="main">
	<div class="alert alert-warning">THIS SITE IS JUST A DEMO!!!</div>	
	<ul class="nav nav-tabs">
		<li><a href="users.php">Users</a></li>
		<li><a href="groups.php">Groups</a></li>
		<li><a href="projects.php">Projects</a></li>
		<li class="active"><a href="settings.php">Global Settings</a></li>
	</ul>
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
<?php 
	include '../core/footer.php';
?>