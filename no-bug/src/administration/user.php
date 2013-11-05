<?php
	define( 'ACTIVE_MENU', 'administration');
	include_once '../core/header.php';
	include_once '../core/logic/userDA.php';
	$userDA = new UserDA();
	
	if(!isset($_GET["u"])) {
		//header("Location: users.php");
		die();
	}
	
	// Event General->Save Changes
	if (isset($_POST["general"])) {
		if (isset($_POST["editUsername"])) {
			$userDA->updateUsername($_GET["u"], $_POST["editUsername"]);
		}
		if (isset($_POST["editPrename"])) {
			$userDA->updatePrename($_GET["u"], $_POST["editPrename"]);
		}
		if (isset($_POST["editSurname"])) {
			$userDA->updateSurname($_GET["u"], $_POST["editSurname"]);
		}
		if (isset($_POST["editEmail"])) {
			$userDA->updateEmail($_GET["u"], $_POST["editEmail"]);
		}
	}
	
	// Event PwReset
	if (isset($_POST["pwReset"])) {
		if ($_POST["pwreset1"] == $_POST["pwreset2"]) {
			$userDA->updatePassword($_GET["u"], $_POST["pwreset1"]);
		}
	}
	
	// Event Activate/Deactivate User
	if (isset($_POST["activate"])) {
		$userDA->activateUser($_GET["u"]);
	}
	if (isset($_POST["deactivate"])) {
		$userDA->deactivateUser($_GET["u"]);
	}
	
	
	$selectedUser = $userDA->getUser($_GET["u"]);

	if ($selectedUser == null) {
		//header("Location: users.php");
		die();
	}
	
	
	
	
?>
	<div id="main">
		<ul class="nav nav-tabs">
			<li class="active"><a href="users.php">Users</a></li>
			<li><a href="groups.php">Groups</a></li>
			<li><a href="projects.php">Projects</a></li>
			<li><a href="settings.php">Global Settings</a></li>
		</ul>
		<h1>Edit <?php echo $selectedUser["prename"].' '.$selectedUser["surname"]; ?>...</h1>
		<form action="?u=<?php echo $selectedUser["id"]; ?>" class="userEditForm" method="post">
			<input type="hidden" name="general" value="true" />
			<h2>> General</h2>
			<table class="table userEditTable">
				<tr>
					<th>Username:</th>
					<td><input type="text" class="form-control" name="editUsername" value="<?php echo $selectedUser["username"];?>"></td>
				</tr>
				<tr>
					<th>Prename:</th>
					<td><input type="text" class="form-control" name="editPrename" value="<?php echo $selectedUser["prename"];?>"></td>
				</tr>
				<tr>
					<th>Surname:</th>
					<td><input type="text" class="form-control" name="editSurname" value="<?php echo $selectedUser["surname"];?>"></td>
				</tr>
				<tr>
					<th>Email:</th>
					<td><input type="text" class="form-control" name="editEmail" value="<?php echo $selectedUser["email"];?>"></td>
				</tr>
			</table>
			<button type="submit" class="btn btn-primary">Save Changes</button>
		</form>
		
		
		<form action="#" class="userEditForm" method="post">
			<input type="hidden" name="pwReset" value="true" />
			<h2>> Password Reset...</h2>
			<table class="table userEditTable">
				<tr>
					<th>New Password:</th>
					<td><input type="password" class="form-control" name="pwreset1" placeholder="Password"></td>
				</tr>
				<tr>
					<th>New Password:</th>
					<td><input type="password" class="form-control" name="pwreset2" placeholder="Retype"></td>
				</tr>
			</table>
			<button type="submit" class="btn btn-warning">Change Password</button>
		</form>
		
		<form action="#" class="userEditForm">
			<h2>> Member of...</h2>
			<table class="table">
				<tr>
					<th>Member of this Groups:</th>
					<th>Add new Group...</th>
				</tr>
				<tr>
					<td>
						<button type="button" class="btn btn-default btn-sm closebtn" >&times; global-admin </button>   <br />
						<button type="button" class="btn btn-default btn-sm closebtn" >&times; jquery-dev </button>   <br />
						<button type="button" class="btn btn-default btn-sm closebtn" >&times; inf2abm </button>   <br />
					</td>
					<td>
						Add Group: 
						<select class="form-control">
						  <option id="1">global-admin</option>
						  <option id="2">jquery-admin</option>
						  <option id="3">jquery-dev</option>
						  <option id="4">jquery-tester</option>
						  <option id="5">inf2abm</option>
						</select>
						<button type="button" class="btn btn-success" style="margin-top: 10px;">Add</button>
					</td>
				</tr>
			</table>
		</form>
		
		<form action="#" method="post">
			<?php 
				if ($userDA->isUserActive($selectedUser["id"])) {
					echo '<input type="hidden" name="deactivate" value="true" />';
					echo '<button type="submit" class="btn btn-danger">Deactivate User</button>';
				}
				else {
					echo '<input type="hidden" name="activate" value="true" />';
					echo '<button type="submit" class="btn btn-success">Activate User</button>';
				}
			?>
		</form>
	</div>
<?php 
	include '../core/footer.php';
?>