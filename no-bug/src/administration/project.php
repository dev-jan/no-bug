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
		<li class="active"><a href="projects.php">Projects</a></li>
		<li><a href="settings.php">Global Settings</a></li>
	</ul>
	<h1>Edit Project no-bug (NOBUG)...</h1>
	<form action="#" class="userEditForm">
		<h2>> General</h2>
		<table class="table">
			<tr>
				<th>Name:</th>
				<td><input type="text" class="form-control" id="editName" placeholder="no-bug"></td>
			</tr>
			<tr>
				<th>Description:</th>
				<td><textarea class="form-control" rows="3" id="editDescription" placeholder="Bugtracking Platform based on PHP"></textarea></td>
			</tr>
			<tr>
				<th>Version:</th>
				<td><input type="text" class="form-control" id="editVersion" placeholder="0.0.1 PreAlpha"></td>
			</tr>
		</table>
		<button type="submit" class="btn btn-primary">Save Changes</button>
	</form>
	
	<form action="#" class="userEditForm">
		<h2>> Groups</h2>
		<table class="table userEditTable">
			<tr>
				<th>Admin Group: </th>
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
			<tr>
				<th>Write Group: </th>
				<td>
					<select class="form-control">
					  <option id="1">global-admin</option>
					  <option id="2">jquery-admin</option>
					  <option id="3" selected="selected">jquery-dev</option>
					  <option id="4">jquery-tester</option>
					  <option id="5">inf2abm</option>
					</select>
				</td>
			</tr>
			<tr>
				<th>Read Group: </th>
				<td>
					<select class="form-control">
					  <option id="1">global-admin</option>
					  <option id="2">jquery-admin</option>
					  <option id="3">jquery-dev</option>
					  <option id="4">jquery-tester</option>
					  <option id="5" selected="selected">inf2abm</option>
					</select>
				</td>
			</tr>
		</table>
		<button type="submit" class="btn btn-warning">Change Groups</button>
	</form>
</div>
<?php 
	include '../core/footer.php';
?>