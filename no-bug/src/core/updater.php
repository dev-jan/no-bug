<?php
include_once dirname(__FILE__).'/logic/db.php';
include_once dirname(__FILE__).'/logger.php';

function update($currentVersion) {
	v2($currentVersion);
}

/**
 * Update to Pre-BETA 0.0-RC1 (not official)
 * Internal Version: 2
 */
function v2 ($currentVersion) {
	// Database changes...
	$db = new DB();
	$db->connect();
	$sql = "CREATE TABLE test (id int);"; // Just for testing...
	$db->query($sql);
	
	// Add update to log
	Logger::info("Updated platform successfull to 0.6", null);
}