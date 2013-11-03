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
	<h1>Projects</h1>
	<button type="button" class="btn btn-success" style="float: right; margin-bottom: 10px;">New Project</button>
	<table class="table table-hover">
		<tr>
			<th>Key</th>
			<th>Name</th>
			<th>Description</th>
			<th>Groups</th>
			<th>Version</th>
			<th>Actions</th>
		</tr>
		<tr>
			<td>NOBUG</td>
			<td>no-bug</td>
			<td>Bugtracking Plattform based on PHP</td>
			<td>
				Admin: <a href="group.php?g=no-bug-admin">no-bug-admin</a> <br /> 
				Write: <a href="group.php?g=no-bug-dev">no-bug-dev</a> <br /> 
				Read : <a href="group.php?g=no-bug-dev">no-bug-testers</a> <br /> 
			</td>
			<td>0.0.1 Pre Alpha</td>
			<td><form action="project.html?p=nobug"><button type="submit" class="btn btn-default btn-sm" >edit</button></form></td>
		</tr>
		<tr>
			<td>JQ</td>
			<td>jQuery</td>
			<td>Javascript Library</td>
			<td>
				Admin: <a href="group.php?g=global-admin">global-admin</a> <br /> 
				Write: <a href="group.php?g=jq-dev">jq-dev</a> <br /> 
				Read : <a href="group.php?g=inf2abm">inf2abm</a> <br /> 
			</td>
			<td>2.5</td>
			<td><form action="project.html?p=nobug"><button type="submit" class="btn btn-default btn-sm" >edit</button></form></td>
		</tr>
	</table>
</div>
<?php 
	include '../core/footer.php';
?>