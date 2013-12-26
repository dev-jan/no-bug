<?php
include_once 'db.php';

class AdminDA {
	public function getAdminMenu ($active) {
		$usersActive = '';
		$groupsActive = '';
		$projectsActive = '';
		$taskpropActive = '';
		$settingsActive = '';
		
		$activeCode = '  class="active"  ';
		
		if ($active == "users.php") {
			$usersActive = $activeCode;
		}
		if ($active == "groups.php") {
			$groupsActive = $activeCode;
		}
		if ($active == "projects.php") {
			$projectsActive = $activeCode;
		}
		if ($active == "taskproperties.php") {
			$taskpropActive = $activeCode;
		}
		if ($active == "settings.php") {
			$settingsActive = $activeCode;
		}
		
		
		echo '<ul class="nav nav-tabs">
		<li '.$usersActive.'><a href="users.php"><i class="fa fa-user"></i> Users</a></li>
		<li '.$groupsActive.'><a href="groups.php"><i class="fa fa-users"></i> Groups</a></li>
		<li '.$projectsActive.'><a href="projects.php"><i class="fa fa-folder-open"></i> Projects</a></li>
		<li '.$taskpropActive.'><a href="taskproperties.php"><i class="fa fa-tasks"></i> Taskproperties</a></li>
		<li '.$settingsActive.'><a href="settings.php"><i class="fa fa-globe"></i> Global Settings</a></li>
		</ul>';
	}
	
}