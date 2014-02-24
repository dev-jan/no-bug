<?php
include_once 'db.php';
include_once dirname(__FILE__).'/groupDA.php';
include_once dirname(__FILE__).'/../logger.php';

class SettingsDA { 
	public function printGlobalAdminGroupSelect () {
		$db = new DB();
		$db->connect();
		
		$sql = "SELECT value FROM setting WHERE `key` = 'global.admingroup'";
		$query = $db->query($sql);
		$adminGroupId = $query->fetch_assoc()["value"];
		
		$groupDA = new GroupDA();
		$groupDA->printGroupSelection($adminGroupId);
	}
	
	public function getPlatformName () {
		$db = new DB();
		$db->connect();
		
		$sql = "SELECT value FROM setting WHERE `key` = 'global.name'";
		return $db->query($sql)->fetch_assoc()["value"];
	}
	
	public function getMotd () {
		$db = new DB();
		$db->connect();
		
		$sql = "SELECT value FROM setting WHERE `key` = 'main.motd'";
		return $db->query($sql)->fetch_assoc()["value"];
	}
	
	public function getTrackingCode() {
		$db = new DB();
		$db->connect();
		
		$sql = "SELECT value FROM setting WHERE `key` = 'global.tracker'";
		return $db->query($sql)->fetch_assoc()["value"];
	}
	
	public function setValues ($globalAdminGroupId, $globalName, $motd, $tracker) {
		$db = new DB();
		$db->connect();
		
		$globalAdminGroupId = $db->esc($globalAdminGroupId);
		$globalName = $db->esc($globalName);
		$motd = $db->mySqlEsc($motd);
		$tracker = $db->mySqlEsc($tracker);
		
		$sql1 = "UPDATE `setting` SET `value`='$globalAdminGroupId' WHERE `key`='global.admingroup'";
		$sql2 = "UPDATE `setting` SET `value`='$globalName' WHERE `key`='global.name'";
		$sql3 = "UPDATE `setting` SET `value`='$motd' WHERE `key`='main.motd';";
		$sql4 = "UPDATE `setting` SET `value`='$tracker' WHERE `key`='global.tracker';";
		$db->query($sql1);
		$db->query($sql2);
		$db->query($sql3);
		$db->query($sql4);
		Logger::info("General settings updated", null);
	}
	
	public function printServerInfos () {
		// Fetch Informations...
		$informations = array();
		
		$informations[0] = array();
		$informations[0]['name'] = 'Version';
		$informations[0]['value'] = $this->getVersionString();
		
		$informations[1] = array();
		$informations[1]['name'] = 'Serverinformations';
		$informations[1]['value'] = php_uname();
		
		$informations[2] = array();
		$informations[2]['name'] = 'PHP Version';
		$informations[2]['value'] = phpversion();
		
		$informations[3] = array();
		$informations[3]['name'] = 'mySQL Version';
		$informations[3]['value'] = mysqli_get_client_info();
		
		$informations[4] = array();
		$informations[4]['name'] = 'Database Size';
		$informations[4]['value'] = $this->getBytesWithPrefix($this->getDatabasesize());

		$informations[5] = array();
		$informations[5]['name'] = 'PHP user';
		$informations[5]['value'] = exec('whoami');
		
		$informations[6] = array();
		$informations[6]['name'] = 'Absolute Path';
		$informations[6]['value'] = getcwd();
		
		$numberOfInformations = count($informations) - 1;
		
		$x = 0;
		while ($x <= $numberOfInformations) {
			echo '<tr>
					<th>'.$informations[$x]['name'].': </th>
					<td>'.$informations[$x]['value'].'</td>
				  </tr>';
			$x++;
		}
	}
	
	private function getVersionString() {
		include dirname(__FILE__).'/../version.php';
		return $versionname . " (" . $compileDate .")";
	}
	
	private function getDatabasesize () {
		$db = new DB();
		$db->connect();
		
		$sql = "SHOW TABLE STATUS";
		$query = $db->query($sql);
		$dbsize = 0;
		
		while ($oneTable = $query->fetch_assoc()) {
			$dbsize += $oneTable["Data_length"] + $oneTable["Index_length"];
		}
		return $dbsize;
	}
	
	private function getBytesWithPrefix ($bytes) {
		$prefixList = array('B', 'kB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB');
		
		$listpointer = 0;
		$max = count($prefixList) - 1;
		while ($bytes >= 1000 && $listpointer < $max) {
			$bytes = $bytes / 1000;
			$listpointer++; 
		}
		return sprintf("%0.2f", $bytes) . " " . $prefixList[$listpointer];
	}
}