<?php
include_once 'db.php';
include_once dirname(__FILE__).'/permissionDA.php';
include_once dirname(__FILE__).'/loginDA.php';
include_once dirname(__FILE__).'/../logger.php';

/**
 * DataAccess for all user releated stuff
 */
class UserDA {	
	/**
	 * Print out the table content of all users (for the administration)
	 * @param <Boolean> $reallyAll If TRUE it prints also the deactivated users
	 */
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
	
	/**
	 * Returns all values of a user
	 * @param <Int> $userID ID of the selected user
	 * @return <dbResult> the user
	 */
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
	
	/**
	 * Change the username of a user
	 * @param <Int> $userId ID of the user
	 * @param <String> $newUsername new username
	 * @return boolean FALSE if the username exists
	 */
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
	
	/**
	 * Change the prename of a user
	 * @param <Int> $userId ID of the user to change
	 * @param <String> $newPrename new prename
	 */
	public function updatePrename($userId, $newPrename) {
		$db = new DB();
		$db->connect();
	
		$userId = $db->esc($userId);
		$newValue = $db->esc($newPrename);
	
		$updateSql = "UPDATE user SET prename='$newValue'
						WHERE id=$userId";
		$db->query($updateSql);
	}
	
	/**
	 * Change the password of a user
	 * @param <Int> $userId ID of the user to change
	 * @param <String> $newPassword new password
	 */
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
	
	/**
	 * Change the surname of a user
	 * @param <Int> $userId ID of the user to change
	 * @param <String> $newSurname new surname
	 */
	public function updateSurname($userId, $newSurname) {
		$db = new DB();
		$db->connect();
	
		$userId = $db->esc($userId);
		$newValue = $db->esc($newSurname);
	
		$updateSql = "UPDATE user SET surname='$newValue'
						WHERE id=$userId";
		$db->query($updateSql);
	}
	
	/**
	 * change the email address of a user
	 * @param <Int> $userId ID of the user to change
	 * @param <String> $newEmail new email
	 */
	public function updateEmail($userId, $newEmail) {
		$db = new DB();
		$db->connect();
	
		$userId = $db->esc($userId);
		$newValue = $db->esc($newEmail);
	
		$updateSql = "UPDATE user SET email='$newValue'
						WHERE id=$userId";
		$db->query($updateSql);
	}
	
	/**
	 * Checks if a username currently exists
	 * @param <String> $username username to check
	 * @return boolean TRUE if the username exists
	 */
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
	
	/**
	 * Create a new user
	 * @param <String> $username username of the new user
	 * @param <String> $prename prename of the new user
	 * @param <String> $surname surname of the new user
	 * @param <String> $email email of the new user
	 * @param <String> $password password (pain text) of the new user
	 */
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
	
	/**
	 * Deactivate an existing user
	 * @param <Int> $userId ID of the user to deactivate
	 */
	public function deactivateUser($userId) {
		$db = new DB();
		$db->connect();
		
		$userId = $db->esc($userId);
		$db->query("UPDATE user SET active=0 WHERE user.id=$userId");
		Logger::info("User { id = $userId } deactivated", null);
	}
	
	/**
	 * Activate an existing user
	 * @param <Int> $userId ID of the user to activate
	 */
	public function activateUser($userId) {
		$db = new DB();
		$db->connect();
	
		$userId = $db->esc($userId);
		$db->query("UPDATE user SET active=1 WHERE user.id=$userId");
		Logger::info("User { id = $userId } activated", null);
	}
	
	/**
	 * Checks if a user is active or not
	 * @param <Int> $userId ID of the user to check
	 * @return <Boolean> TRUE if the user is active
	 */
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
	
	/**
	 * Print out the table of permissions of a user on each project
	 * @param <Int> $userId ID of the user
	 */
	public function printPermissionTable ($userId) {
		$db = new DB();
		$db->connect();
		
		$userId = $db->esc($userId);
		$permDA = new PermissionDA();
		
		$query = $permDA->getAllAllowedProjects($userId);
		$usersGroups = $permDA->getAllGroups($userId);
		$textYes = "<i class=\"fa fa-check\" style=\"color: #60CF3E\"></i>";
		$textNO = "<i class=\"fa fa-times\" style=\"color: #C21616\"></i>";
		
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
	
	/**
	 * Checks if the given password of a user is correct (same in the database)
	 * @param <Int> $userId ID of the user to check the password
	 * @param <String> $password The password to check (plain text)
	 * @return boolean TRUE if the password from the parameter is correct
	 */
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