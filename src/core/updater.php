<?php
/* Description: This file contains specific update instruction for each released version */

// Include some core files
include_once dirname(__FILE__).'/logic/db.php';
include_once dirname(__FILE__).'/logger.php';

/**
 * Main function that will be called after each update
 * @param INT $currentVersion The current installed version (internal version)
 */
function update($currentVersion) {
	// Link to the newest version...
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
	$sql = "CREATE TABLE test (id int);
			ALTER TABLE `project` DROP COLUMN `version`;";
	$db->multiQuery($sql);
	
	// Add update to log
	Logger::info("Updated platform successfull to 0.6", null);
}