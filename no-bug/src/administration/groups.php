<?php
	define( 'ACTIVE_MENU', 'administration');
	include_once '../core/header.php';
	include_once '../core/logic/userDA.php'	;
?>
<div id="main">
	<div class="alert alert-warning">THIS SITE IS JUST A DEMO!!!</div>	
	<ul class="nav nav-tabs">
		<li><a href="users.php">Users</a></li>
		<li class="active"><a href="groups.php">Groups</a></li>
		<li><a href="projects.php">Projects</a></li>
		<li><a href="settings.php">Global Settings</a></li>
	</ul>
	<h1>Groups</h1>
	<button type="button" class="btn btn-success" style="float: right; margin-bottom: 10px;">New Group</button>
	<table class="table table-hover">
		<tr>
			<th>Name</th>
			<th>Member of</th>
			<th>Members</th>
			<th>actions</th>
		</tr>
		<tr>
			<td>global-admin</td>
			<td>-</td>
			<td>jan (User)<br /> benj (User)</td>
			<td><form action="group.html?g=global-admin"><button type="submit" class="btn btn-default btn-sm" >edit</button></form></td>
		</tr>
		<tr>
			<td>jquery-admin</td>
			<td>-</td>
			<td>billgates (User) <br /> steve (User)</td>
			<td><button type="button" class="btn btn-default btn-sm">edit</button></td>
		</tr>
		<tr>
			<td>jquery-dev</td>
			<td>-</td>
			<td>jquery-admin (Group) <br /> inf2abm (Group)</td>
			<td><button type="button" class="btn btn-default btn-sm">edit</button></td>
		</tr>
		<tr>
			<td>jquery-tester</td>
			<td>-</td>
			<td>julia (User) <br /> manuel (User)</td>
			<td><button type="button" class="btn btn-default btn-sm">edit</button></td>
		</tr>
		<tr>
			<td>inf2abm</td>
			<td>jquery-dev</td>
			<td>benj (User) <br /> manuel (User) <br /> jan (User) <br /> boss (User) <br /> alex (User) <br /> pesc (User)</td>
			<td><button type="button" class="btn btn-default btn-sm">edit</button></td>
		</tr>
	</table>
</div>
<?php 
	include '../core/footer.php';
?>