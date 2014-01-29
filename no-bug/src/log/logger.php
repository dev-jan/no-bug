<?php
include_once dirname(__FILE__).'/../core/logic/db.php';

class Logger {
	
	/**
	 * Logs Debug level
	 * @param <String> $message
	 *   The message
	 * @param <Exception> $ex
	 *   The exception
	 */
	public static function debug($message, $ex) {
		self::logOnLevel($message, $ex, "DEBUG");
	}
	
	/**
	 * Logs an error
	 * @param unknown $message
	 *   The message
	 * @param unknown $ex
	 *   The Exception
	 * @param unknown $loglevel
	 *   The loglevel
	 */
	private static function logOnLevel($message, $ex, $loglevel) {
		
		$ex = var_dump($ex);
		
		$db = new DB();
		
		$db->connect();
		$ex = $db->esc($ex);
		$message = $db->esc($message);
		$loglevel = $db->esc($loglevel);
		
		$user = "";
		
		if (!isset($_SESSION['nobug'.RANDOMKEY.'userId'])) {
			$user = "Anonymus";
		} else {
			$user = $_SESSION['nobug'.RANDOMKEY.'userId'];
		}
		
		$user = $db->esc($user);
		
		$sql = "INSERT INTO log (message, exception, `date`, `level`, user) VALUE ('$message', '$ex', NOW(), '$loglevel', '$user')";
		$db->query($sql);
	}
}