<?php
include dirname(__FILE__).'/../nobug-config.php';

class DB {
	public $db;
	
	public function connect() {
		$this->db = mysqli_connect(DATABASE_HOSTNAME, DATABASE_USER, DATABASE_PASSWORD, DATABASE_NAME);
		if (mysqli_connect_errno($this->db)) {
			echo "Connection lost! ^^";
		}
	}
	
	public function query($sql) {
		return $this->db->query($sql);
	}
	
	public function esc($par) {
		return mysqli_real_escape_string($this->db, $par);
	}
}