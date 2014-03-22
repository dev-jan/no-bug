<?php
include_once 'db.php';
include_once dirname(__FILE__).'/permissionDA.php';
include_once dirname(__FILE__).'/loginDA.php';
include_once dirname(__FILE__).'/../logger.php';

class UserDA {	
	public function printAllUsersTable($reallyAll) {
		$db = new DB();
		$db->connect();
		$allUsersSql;
		if ($reallyAll) {
			$allUsersSql = "SELECT * FROM user";
		}
		else {
			$allUsersSql = "SELECT * FROM user
							WHERE user.active = 1";
		}
		
		$usersQuery = $db->query($allUsersSql);		
		while ($oneUser = $usersQuery->fetch_assoc()) {
			if ($oneUser["active"] == 1) {
				echo '<tr>';
			}
			else {
				echo '<tr class="danger">';
			}
			echo '	<td>'.$oneUser['username'].'</td>
					<td>'.$oneUser['prename'].' '.$oneUser['surname'].'</td>
					<td>'.$oneUser['email'].'</td>
					<td><form action="user.php?" method="GET">
							<input type="hidden" name="u" value="'.$oneUser['id'].'" />
							<button type="submit" class="btn btn-default btn-sm"><i class="fa fa-pencil"></i> edit</button></form></td>
				</tr>';
		}
	}
	
	public function getUser ($userID) {
		$db = new DB();
		$db->connect();
		
		$userID = $db->esc($userID);
		
		$userSql = "SELECT * FROM user
							WHERE user.id = '$userID'";
		$userSQLResult = $db->query($userSql);
		if ($userSQLResult->num_rows > 0) {
			return $userSQLResult->fetch_assoc();
		}
		else {
			return null;
		}
	}
	
	public function updateUsername($userId, $newUsername) {
		$db = new DB();
		$db->connect();
		
		$userId = $db->esc($userId);
		$newUsername = $db->esc($newUsername);
		
		
		if (!$this->usernameExists($newUsername)) {
			$updateSql = "UPDATE user SET username='$newUsername'
							WHERE id=$userId";
			$db->query($updateSql);
			return true;
		}
		else {
			return false;
		}
		
	}
	public function updatePrename($userId, $newPrename) {
		$db = new DB();
		$db->connect();
	
		$userId = $db->esc($userId);
		$newValue = $db->esc($newPrename);
	
		$updateSql = "UPDATE user SET prename='$newValue'
						WHERE id=$userId";
		$db->query($updateSql);
	}
	
	public function updatePassword($userId, $newPassword) {
		$db = new DB();
		$db->connect();
	
		$userId = $db->esc($userId);
		$newValue = $db->esc($newPassword);
		$salt = $db->esc($db->createSalt());
		$newValue = $newPassword . $salt;
	
		$updateSql = "UPDATE user SET password=SHA2('$newValue',256), salt='$salt'
						WHERE id=$userId";
		$db->query($updateSql);
		Logger::info("Password updated for User { id = $userId }", null);
	}
	
	public function updateSurname($userId, $newSurname) {
		$db = new DB();
		$db->connect();
	
		$userId = $db->esc($userId);
		$newValue = $db->esc($newSurname);
	
		$updateSql = "UPDATE user SET surname='$newValue'
						WHERE id=$userId";
		$db->query($updateSql);
	}
	
	public function updateEmail($userId, $newEmail) {
		$db = new DB();
		$db->connect();
	
		$userId = $db->esc($userId);
		$newValue = $db->esc($newEmail);
	
		$updateSql = "UPDATE user SET email='$newValue'
						WHERE id=$userId";
		$db->query($updateSql);
	}
	
	private function usernameExists ($username) {
		$db = new DB();
		$db->connect();
		
		$username = $db->esc($username);
		$existsSql = "SELECT id FROM user WHERE user.username = '$username'";
		$existsResult = $db->query($existsSql);
		if ($existsResult->num_rows == 0) {
			return false;
		}
		else {
			return true;
		}
	}
	
	public function createUser ($username, $prename, $surname, $email, $password) {
		$db = new DB();
		$db->connect();
		
		$username = $db->esc($username);
		$prename = $db->esc($prename);
		$surname = $db->esc($surname);
		$email = $db->esc($email);
		$password = $db->esc($password);
		$salt = $db->esc($db->createSalt());
		$password = $password . $salt;
		
		if (!$this->usernameExists($username)) {
			$insertSql = "INSERT INTO `user`
						(`username`, `email`, `prename`, `surname`, `password`, `salt`, `active`, `meta_creatorID`, `meta_createDate`,
						`meta_changeUserID`, `meta_changeDate`)
						VALUES ('$username', '$email', '$prename', '$surname', SHA2('$password', 256), '$salt', 1, ".$_SESSION['nobug'.RANDOMKEY.'userId'].", '".$db->toDate(time())."', '".$_SESSION['nobug'.RANDOMKEY.'userId']."', '".$db->toDate(time())."');";
			$db->query($insertSql);
		}
		Logger::info("New user { $username } created", null);
	}
	
	public function deactivateUser($userId) {
		$db = new DB();
		$db->connect();
		
		$userId = $db->esc($userId);
		$db->query("UPDATE user SET active=0 WHERE user.id=$userId");
		Logger::info("User { id = $userId } deactivated", null);
	}
	
	public function activateUser($userId) {
		$db = new DB();
		$db->connect();
	
		$userId = $db->esc($userId);
		$db->query("UPDATE user SET active=1 WHERE user.id=$userId");
		Logger::info("User { id = $userId } activated", null);
	}
	
	public function isUserActive($userId) {
		$db = new DB();
		$db->connect();
		
		$userId = $db->esc($userId);
		$query = $db->query("SELECT active FROM user WHERE user.id=$userId");
		$result = $query->fetch_assoc();
		if ($result["active"] == 1) {
			return true;
		}
		return false;
	}
	
	public function printPermissionTable ($userId) {
		$db = new DB();
		$db->connect();
		
		$userId = $db->esc($userId);
		$permDA = new PermissionDA();
		
		$query = $permDA->getAllAllowedProjects($userId);
		$usersGroups = $permDA->getAllGroups($userId);
		$textYes = "<i class=\"fa fa-check\"></i>";
		$textNO = "<i class=\"fa fa-times\"></i>";
		
		echo '<table class="table">
				<tr>
					<th>Project</th>
					<th>Admin</th>
					<th>Write</th>
					<th>Read</th>
				</tr>';		
		if ($query != null && $query->num_rows > 0) {
			while ($oneProject = $query->fetch_assoc()) {
				$adminText = $textNO;
				$writeText = $textNO;
				$readText = $textNO;
				if ($permDA->isGroupInList($oneProject["group_admin"], $usersGroups)) {
					$adminText = $textYes;
					$writeText = $textYes;
					$readText = $textYes;
				}
				else if ($permDA->isGroupInList($oneProject["group_write"], $usersGroups)) {
					$writeText = $textYes;
					$readText = $textYes;
				}
				else if ($permDA->isGroupInList($oneProject["group_read"], $usersGroups)) {
					$readText = $textYes;
				}
				
				echo '<tr>
						<td>'.$oneProject["name"].' ('.$oneProject["key"].')</td>
						<td>'.$adminText.'</td>
						<td>'.$writeText.'</td>
						<td>'.$readText.'</td>
					  </tr>';
			}
		}
		else {
			echo '<tr>
						<td>No Projects for this user...</td>
						<td></td>
						<td></td>
						<td></td>
					  </tr>';
		}
		echo '</table>';
	}
	
	public function checkPassword ($userId, $password) {
		$loginDA = new LoginDA();
		$username = $this->getUser($userId)["username"];
		if ($loginDA->getUser($username, $password) != null) {
			return true;
		}
		else {
			return false;
		}
	}
}