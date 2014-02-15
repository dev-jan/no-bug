<?php
if (file_exists(dirname(__FILE__).'/../../nobug-config.php')) {
	include_once dirname(__FILE__).'/../../nobug-config.php';
}
else {
	$rootpath = "//" . $_SERVER['SERVER_NAME'] . substr(dirname(__FILE__). '/../../', strlen($_SERVER['DOCUMENT_ROOT']));
	echo '<!DOCTYPE html><html>
			<head>
				<title>Error: 500</title>
				<link rel="stylesheet" href="'.$rootpath.'style/bootstrap.min.css" />
				<link href="//netdna.bootstrapcdn.com/font-awesome/4.0.3/css/font-awesome.css" rel="stylesheet">
			</head>
			<body><div class="alert alert-danger" style="margin: 50px;">
			  	<strong><i class="fa fa-puzzle-piece"></i> Configuration File not found!</strong> 
			    <p>The configuration File (nobug-config.php) is not found on this Installation!</p>
				<p>Create this file and run the <a href="'.$rootpath.'setup.php">Setup</a></p>
			</p></div></body></html>';
	die();
}
include_once dirname(__FILE__).'/../logger.php';

class DB {
	public $db;
	
	public function connect() {
		$this->db = @mysqli_connect(DATABASE_HOSTNAME, DATABASE_USER, DATABASE_PASSWORD, DATABASE_NAME);
		if (mysqli_connect_errno($this->db)) {
			echo '<div class="alert alert-danger alert-dismissable" style="margin: 50px;">
			  	<strong><i class="fa fa-puzzle-piece"></i> Database Error!</strong> <p>'. mysqli_connect_error($this->db).'</p></div>';
		}
		$this->db->set_charset("utf8");
	}
	
	public function check_connection() {
		$this->db = mysqli_connect(DATABASE_HOSTNAME, DATABASE_USER, DATABASE_PASSWORD, DATABASE_NAME);
		if (mysqli_connect_errno($this->db)) {
			echo '<div class="alert alert-danger alert-dismissable" style="margin: 50px;">
			  	<strong><i class="fa fa-puzzle-piece"></i> Database Error!</strong> <p>'. mysqli_connect_error($this->db).'</p></div>';
			return false;
		}
		else {
			echo '<div class="alert alert-success alert-dismissable" style="margin: 50px;">
			  			<strong><i class="fa fa-puzzle-piece"></i> Database Successfull connected!</strong></div>';
			return true;
		}
	}
	
	public function close_connection() {
		$this->db->close();
	}
	
	public function query($sql) {
		$result = $this->db->query($sql);
				
		if($result !== false ) {
			return $result;
		} else {
			Logger::error("SQL query Failed!", "query= { $sql }");
			return null;
		}
	}
	
	public function multiQuery ($sql) {
		if ($return = $this->db->multi_query($sql)) {
			do {
				$this->db->next_result();
				if ($result = $this->db->store_result()) {
					$result->free();
				}
			} while ($this->db->more_results());
			return true;
		}else{
			Logger::error("SQL multiQuery Failed!", "query= { $sql }");
			return false;
		}
	}
	
	public function esc($par) {
		$htmlEscape = htmlspecialchars($par, ENT_COMPAT | ENT_HTML5 , "UTF-8");
		$sqlEscape = mysqli_real_escape_string($this->db, $htmlEscape);
		return $sqlEscape;
	}
	
	public function mySqlEsc($par) {
		return mysqli_real_escape_string($this->db, $par);
	}
	
	public function fixDoubleSpace ($text) {
		return str_replace("  ", "&nbsp;&nbsp;", $text);
	}
	
	public function createSalt() {
		return $this->random_string(29,false,true);
	}
	
	public function random_string($length,$noCaps = false, $addNumbers = false)    {
		$w_s=array ('a','b','c','d','e','f','g','h','j','k','m','n','p','q','r','s','t','u','v','w','x','y','z',);
		if($noCaps === false) {
			$w_s = array_merge($w_s,array_map('strtoupper',$w_s));
		}
		if($addNumbers === true) {
			$w_s = array_merge($w_s,array(2,3,4,5,6,7,8,9,));
		}
		$max = count($w_s) - 1;
		$returnString = "";
		for($i=0;$i<$length;$i++) {
			srand((double)microtime()*1000000);
			$wg=rand(0,$max);
			$returnString.=$w_s[$wg];
		}
		return $returnString;
	}
	
	public function toDate($unixTimestamp){
		return date("Y-m-d", $unixTimestamp);
	}
}