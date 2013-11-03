<?php
	define( 'ACTIVE_MENU', 'administration');
	include_once '../core/header.php';
	include_once '../core/logic/userDA.php'	;
?>
<div id="main">
	<div class="alert alert-warning">THIS SITE IS JUST A DEMO!!!</div>	
	<ul class="nav nav-tabs">
		<li><a href="users.html">Users</a></li>
		<li class="active"><a href="groups.php">Groups</a></li>
		<li><a href="projects.php">Projects</a></li>
		<li><a href="settings.php">Global Settings</a></li>
	</ul>
	<h1>Edit Global-Admin...</h1>
	<form action="#" class="userEditForm">
		<table class="table">
			<tr>
				<th>Groupname:</th>
				<td><input type="text" class="form-control" id="editGroupname" placeholder="Global-Admin"></td>
			</tr>
		</table>
		<button type="submit" class="btn btn-primary">Save</button>
	</form>
	
	<form action="#" class="userEditForm">
		<h2>> Group Members...</h2>
		<table class="table">
			<tr>
				<th>Name</th>
				<th>Action</th>
			</tr>
			<tr>
				<td>jan (User)</td>
				<td><button type="button" class="btn btn-danger" >Remove</button></td>
			</tr>
		</table>
	</form>
	<form action="#">
	<table class="table">
			<tr>
				<td>Add User: 
					<select class="form-control">
					  <option id="1">global-admin</option>
					  <option id="2">jquery-admin</option>
					  <option id="3">jquery-dev</option>
					  <option id="4">jquery-tester</option>
					  <option id="5">inf2abm</option>
					</select></td>
				<td><button type="button" class="btn btn-success" style="margin-top: 20px;">Add</button></td>
			</tr>
	</table>
	</form>
</div>
<?php 
	include '../core/footer.php';
?>