<?php
include_once 'db.php';

class logDA {
	/**
	 * Gets the Log entrys in a Timespan
	 * @param <int> $count
	 *   Count of the returned rows
	 * @param <String> $dateFrom
	 *   Format: Y-m-d
	 * @param <String> $dateTo
	 *   Format Y-m-d
	 * @return multitype:
	 *   Array of log Entrys
	 */
	public function getLog($count, $dateFrom, $dateTo) {
		// Connect do Database
		$db = new DB();
		$db->connect();
		// Escape parameters
		$count = $db->esc($count);
		$dateFrom = $db->esc($dateFrom);
		$dateTo = $db->esc($dateTo);
		
		$sql = "SELECT * FROM log where `date` BETWEEN '$dateFrom' AND '$dateTo' ORDER BY `date` DESC LIMIT $count";
		$querry = $db->query($sql);
		$logEntrys = Array();
		while ($logEntry = $querry->fetch_assoc()) {
			$arrayEntry = [
				"id" => $logEntry['Id'],
				"date" => $logEntry['date'],
				"message" => $logEntry['message'],
				"exception" => $logEntry['exception'],
				"level" => $logEntry['level'],
				"user" => $logEntry['user']
			];
			array_push($logEntrys, $arrayEntry);
		}
		
		return $logEntrys;
	}
}