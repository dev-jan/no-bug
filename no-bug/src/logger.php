<?php
include_once dirname(__FILE__).'/../core/logic/db.php';		// For Database Connection
include_once dirname(__FILE__).'/../core/logic/userDA.php'; // To get The Username


/**
 * To Log the application
 */
class Logger {
	
	private static $db;
	/**
	 * Gets the DB Connection
	 * @return DB
	 *   The Database
	 */
	private static function getDB() {
		// return the Database
		if (!isset(self::$db)) {
			self::$db = new DB();
		}
		return self::$db;
	}
	
	
	/**
	 * Logs Debug level
	 * @param <String> $message
	 *   The message
	 * @param <Exception> $ex
	 *   The exception
	 */
	public static function debug($message, $ex) {
		if (self::getLogLevel() <= LogLevel::DEBUG) {
			self::logOnLevel($message, $ex, "DEBUG");
		}
	}
	
	/**
	 * Logs on Info level
	 * @param <String> $message
	 *   The message
	 * @param <Exception> $ex
	 *   The exception
	 */
	public static function info($message, $ex) {
		if (self::getLogLevel() <= LogLevel::INFO) {
			self::logOnLevel($message, $ex, "INFO");
		}
	}
	
	/**
	 * Logs on Warn level
	 * @param <String> $message
	 *   The message
	 * @param <Exception> $ex
	 *   The exception
	 */
	public static function warn($message, $ex) {
		if (self::getLogLevel() <= LogLevel::WARN) {
			self::logOnLevel($message, $ex, "WARN");
		}
	}
	
	/**
	 * Logs on Error level
	 * @param <String> $message
	 *   The message
	 * @param <Exception> $ex
	 *   The exception
	 */
	public static function error($message, $ex) {
		if (self::getLogLevel() <= LogLevel::ERROR) {
			self::logOnLevel($message, $ex, "ERROR");
		}
	}
	
	/**
	 * Logs on Fatal level
	 * @param <String> $message
	 *   The message
	 * @param <Exception> $ex
	 *   The exception
	 */
	public static function fatal($message, $ex) {
		if (self::getLogLevel() <= LogLevel::FATAL) {
			self::logOnLevel($message, $ex, "FATAL");
		}
	}
	
	/**
	 * Makes a Log entry in the Database
	 * @param unknown $message
	 *   The message
	 * @param unknown $ex
	 *   The Exception
	 * @param unknown $loglevel
	 *   The loglevel
	 */
	private static function logOnLevel($message, $ex, $loglevel) {
		
		// Connect to Database
		$db = self::getDB();
		$db->connect();
		
		// Get Username
		$user = "ANONYMOUS";
		if (isset($_SESSION['nobug'.RANDOMKEY.'userId'])) {
				
			$userDA = new UserDA();
			$user = $userDA->getUser($_SESSION['nobug'.RANDOMKEY.'userId'])['username'];
		}
		
		// Save Vardump to variable (dont echo it)
		ob_start();
		var_dump($ex);
		$ex = ob_get_clean();
		
		// Escape Parameters
		$user = $db->esc($user);
		$ex = $db->esc($ex);
		$message = $db->esc($message);
		$loglevel = $db->esc($loglevel);
		
		// Save Log to Database
		$sql = "INSERT INTO log (message, exception, `date`, `level`, user) VALUE ('$message', '$ex', NOW(), '$loglevel', '$user')";
		$db->query($sql);
	}
	
	/**
	 * Returns the log Level
	 * @return LogLevel
	 */
	private static function getLogLevel() {
		
		// When not defined use: DEBUG
		$logLevel = LogLevel::DEBUG;
		
		// Get the Database Configuration
		// Connect to Database
		$db = self::getDB();
		$db->connect();
		
		$sql = "SELECT `value` FROM setting WHERE `key` = 'global.loglevel'";
		$querry = $db->query($sql);
		$result = $querry->fetch_assoc();

		// When The Log Level is defined 
		// return the Log Level (standard : DEBUG)
		if (isset($result["value"])) {
			
			switch ($result["value"]) {
				case 'DEBUG':
					$logLevel = LogLevel::DEBUG;
					break;
				case 'INFO':
					$logLevel = LogLevel::INFO;
					break;
				case 'WARN':
					$logLevel = LogLevel::WARN;
					break;
				case 'ERROR':
					$logLevel = LogLevel::ERROR;
					break;
				case 'FATAL':
					$logLevel = LogLevel::FATAL;
					break;
				default:
					$logLevel = LogLevel::DEBUG;
					break;
			}
		}
		return $logLevel;
	}
}



/**
 * LogLevel
 */
abstract class LogLevel {
	/**
	 * Log Level DEBUG
	 * @var DEBUG
	 */
	const DEBUG = 1;
	/**
	 * Log Level INFO
	 * @var INFO
	 */
	const INFO  = 2;
	/**
	 * Log Level WARN
	 * @var WARN
	 */
	const WARN  = 3;
	/**
	 * Log Level ERROR
	 * @var ERROR
	 */
	const ERROR = 4;
	/**
	 * Log Level FATAL
	 * @var FATAL
	 */
	const FATAL = 5;
}


