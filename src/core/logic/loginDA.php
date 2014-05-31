<?php
include_once 'db.php';

/**
 * DataAccess for all things releated to the login page
 */
class LoginDA {
	/**
	 * Returns the ID of the user that matches with the parameters, NULL if no user matches
	 * @param <String> $username
	 * @param <String> $password
	 * @return <Int>|NULL 
	 */
	public function getUser($username, $password) {
		$db = new DB();
		$db->connect();
		
		$username = $db->esc($username);
		$password = $db->esc($password);
		
		$sqlsalt = "SELECT salt FROM user
						WHERE user.username = '$username' AND user.active != 0";
		
		$saltquery = $db->query($sqlsalt);
		$saltresult = $saltquery->fetch_assoc();
		if ($saltquery->num_rows > 0) {
			$sql = "SELECT * FROM user
					WHERE user.username = '$username' AND user.password = SHA2('".$password . $saltresult["salt"]."', 256);";
			$sqlquery = $db->query($sql);
			$sqlresult = $sqlquery->fetch_assoc();
			
			if ($sqlquery->num_rows > 0) {
				return $sqlresult['id'];
			}
			else {
				sleep(2);
				return null;
			}
		}
		else {
			sleep(2);
			return null;
		}
	}
}