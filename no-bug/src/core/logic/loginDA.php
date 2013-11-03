<?php
include 'db.php';

class LoginDA {
	
	public function getUser($username, $password) {
		
		$db = new DB();
		
		$db->connect();
		
		$username = $db->esc($username);
		$password = $db->esc($password);
		
		
		$sqlsalt = "SELECT salt FROM user
						WHERE user.username = '$username'";
		
		$saltquery = $db->query($sqlsalt);
		$saltresult = $saltquery->fetch_assoc();
		
		
		if ($saltquery->num_rows > 0) {
			
			$sql = "SELECT * FROM user
					WHERE user.username = '$username' AND user.password = SHA2('$password', 256);";
			$sqlquery = $db->query($sql);
			$sqlresult = $sqlquery->fetch_assoc();
			
			
			if ($sqlquery->num_rows > 0) {
				return $sqlresult['id'];
			}
			else {
				return null;
			}
		}
		else {
			return null;
		}
	}
}