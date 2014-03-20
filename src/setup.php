<?php 
session_start();

define("ROOTPATH", "//" . $_SERVER['SERVER_NAME'] . substr(dirname(__FILE__). '/', strlen($_SERVER['DOCUMENT_ROOT'])));
function random_string($length,$noCaps = false, $addNumbers = false)    {
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
?>

<!DOCTYPE html>
<html>
<head>
	<title>no-bug SETUP</title>
	<?php include 'core/meta.php'; ?>
</head>

<body style="padding-top: 70px">
	
	<div id="header" class="navbar navbar-default navbar-fixed-top">
		<div id="header-wrapper" >
			<a class="navbar-brand" href="<?php echo ROOTPATH; ?>"><i class="fa fa-bug"></i> no-bug</a>
		</div>
	</div>
	
	<?php 
		// Check if setup is allowed
		if (file_exists('nobug-config.php')) {include_once 'nobug-config.php';}
		if (defined('ISCONFIGURATED')) {
			echo '<div id=main> <div class="alert alert-danger"><strong>Failed to load Setup!</strong> <br />
			          You are not allowed to start the setup, because its forbidden in the "nobug-config.php" file!<br />
					  If you want to access the setup, remove this line from the nobug-config.php file: <br style="margin-bottom: 20px;" />
						<pre>define( \'ISCONFIGURATED\',    \'TRUE\');</pre>
			          </div></div>';
			die();
		}	
	
		// Check entered settings...
		$everythingCorrect = true;
		if (!file_exists('nobug-config.php')) {
			@fopen('nobug-config.php', 'w');
		}
		
		if (!file_exists('nobug-config.php')) {
			echo '<div id=main> <div class="alert alert-danger"><strong>Failed to create Config File!</strong> <br />
			          Please create the following File: <strong>&lt;no-bug&gt;/nobug-config.php</strong> <br style="margin-bottom: 20px;" />
					  via Shell:   <pre style="display: inline;">touch ' . str_replace("setup.php", "nobug-config.php", __FILE__) . '</pre><br style="margin-bottom: 20px;" />
					  via FTP: <pre style="display: inline;">Go the the no-bug directory and create an empty file named "nobug-config.php" and set the Permissions to 666</pre>
			          </div></div>';
			die();
		}
		if (isset($_POST["check"])) {
			if (file_exists('nobug-config.php') && is_writable('nobug-config.php')) {
				
				$configcontent = "<?php // Do not remove this Line!!\n".
						"// DATABASE CONFIGURATION: -------------------------------------\n".
						"  define( 'DATABASE_HOSTNAME', '".$_POST["db_host"]."' );\n".
						"  define( 'DATABASE_USER',     '".$_POST["db_user"]."' );\n".
						"  define( 'DATABASE_PASSWORD', '".$_POST["db_pw"]."' );\n".
						"  define( 'DATABASE_NAME',     '".$_POST["db_dbname"]."' );\n".
						"  define( 'RANDOMKEY',         '".random_string(6)."');\n".
						"// LOG CONFIGURATION: -------------------------------------\n".
						"  define( 'LOG_LEVEL',		 'DEBUG'); \n".
						"// SETUP CONFIGURATION: -------------------------------------\n".
						"  define( 'ISCONFIGURATED',    'TRUE');\n";
				if ($handle = fopen('nobug-config.php', "w")) {
					fwrite($handle, $configcontent);
				}
				else {
					echo '<div class="alert alert-danger alert-dismissable" style="margin: 50px;">
							<strong><i class="fa fa-lock"></i> Config File Error!</strong> The configuration File is not correct! Please click <a href="?">here</a> </div>';
					die();
				}
				include 'nobug-config.php';
				include_once dirname(__FILE__).'/core/logic/db.php';
				include_once dirname(__FILE__).'/core/logger.php';
				$db = new DB();
				// Check connection
				if (!@$db->check_connection()) {
					$everythingCorrect = false;
				}
				// Check Admin Password
				if ($_POST["admin_pw1"] != $_POST["admin_pw2"]) {
					$everythingCorrect = false;
					echo '<div class="alert alert-danger alert-dismissable" style="margin: 50px;">
						    <strong><i class="fa fa-lock"></i> Admin Passwords doesn\'t match!</strong></div>';
				}
				
				if ($everythingCorrect) {
					include dirname(__FILE__).'/core/logic/databaseScript.php';
					$db->connect();
					$adminSQL = createAdminSql($db->esc($_POST["admin_username"]), $db->esc($_POST["admin_name"]), $db->esc($_POST["admin_email"]), $db->esc($_POST["admin_pw1"]));
					$db->multiQuery($sqlToCreateDb . $adminSQL);
					echo '<div class="alert alert-success alert-dismissable" style="margin: 50px;">
							<strong><i class="fa fa-check-square-o"></i> Setup successfull!</strong> 
						    You can now login with your Admin account <a href="index.php">here</a> 
						</div>';
				}
				else {
					if ($handle = fopen('nobug-config.php', "w")) {
					$configcontent = "<?php // Do not remove this Line!!\n".
							"define( 'DATABASE_HOSTNAME', '".$_POST["db_host"]."' );\n".
							"define( 'DATABASE_USER',     '".$_POST["db_user"]."' );\n".
							"define( 'DATABASE_PASSWORD', '".$_POST["db_pw"]."' );\n".
							"define( 'DATABASE_NAME',     '".$_POST["db_dbname"]."' );\n".
							"define( 'RANDOMKEY',         '".random_string(6)."');\n";
						fwrite($handle, $configcontent);
					}
				}
			}
		}
	
	?>
	
	<div id=main>
		<h1>Setup</h1>
		<div>
			<p>Welcome to the setup of "no-bug". It only takes 5 minutes to set up your platform!</p>
			
			<?php 
			// *** Check Prerequirements ***
			$successfullPrerequireds = "";
			$everythingCorrect = true;
			
			// 1. Check if nobug-config.php is OK
			if (file_exists('nobug-config.php')) {
				if (is_writable('nobug-config.php')) {
					$successfullPrerequireds = $successfullPrerequireds.'<i class="fa fa-check-square-o"></i> Settings File (nobug-config.php) is writeable';
				}
				else {
					echo '<div class="alert alert-danger"><strong>Config File not Writeable!</strong> <br />
			          Please add Write Permission to the following File: <strong>&lt;no-bug&gt;/nobug-config.php</strong> <br style="margin-bottom: 20px;" />
					  via Shell:   <pre style="display: inline;">chmod a+w ' . str_replace("setup.php", "nobug-config.php", __FILE__) . '</pre><br style="margin-bottom: 20px;" />
					  via FTP: <pre style="display: inline;">Set the Permission of the Configuration (&lt;no-bug&gt;/nobug-config.php) to 666</pre>
			          </div>';
					$everythingCorrect = false;
				}
			}
			else {
			  	echo '<div class="alert alert-danger" style="font-size: 70%" ><strong>Config File not Found!</strong> <br />
			          Please add this File and add write Permission: <strong>&lt;no-bug&gt;/nobug-config.php</strong> <br />
					  via Shell: <br />
						<pre>touch '.str_replace("setup.php", "nobug-config.php", __FILE__).'</pre>
						<pre>chmod a+w ' . str_replace("setup.php", "nobug-config.php", __FILE__) . '</pre>
					  via FTP: <pre style="display: inline;">create the File nobug-config.php and set the Permission of the File to 666</pre>
			          </div>';
			  	$everythingCorrect = false;
			}
			
			// 2. Check if the mySQLi Module is installed 
			if (extension_loaded('mysqli')) {
			  	$successfullPrerequireds = $successfullPrerequireds.'<br/ ><i class="fa fa-check-square-o"></i> mySQLi is installed';
			}
			else {
			  	echo '<div class="alert alert-danger"><strong>mySQLi is not installed!</strong> <br />
			          Please install this extension, because no-bug use this to communicate with the database. <br style="margin-bottom: 20px;" />
					  via Shell:   <pre style="display: inline;">sudo apt-get install php5-mysql</pre><br style="margin-bottom: 20px;" />
					  if you don\'t have root access to this server, please contact your administrator and ask him to install "mysqli"
			          </div>';
			  	$everythingCorrect = false;
			}
			
			// ** Show successfull messages or die if there is an error
			if ($successfullPrerequireds != "") {
				echo '<div class="alert alert-success">'.$successfullPrerequireds.'</div><p></p>';
			}
			if (!$everythingCorrect) {
				echo '<p>Please fix the errors. After that, please reload this page!</p>';
			  	include 'core/footer.php';
			  	die();
			}
			?>
		</div>
		
		<form action="" method="post">
			  <input type="hidden" name="check" value="true" />
			  <h2>Database Settings...</h2>
			  <table class="table">
					<tr>
						<td style="width:400px" width="400px"><b>Host:</b> <br /><em><i class="fa fa-angle-double-right"></i> e.g. "localhost"</em></td>
						<td><input type="text" name="db_host" class="form-control" 
							value="<?php if (isset($_POST["db_host"])) {echo $_POST["db_host"];} ?>"/>
						</td>
					</tr>
					<tr>
						<td><b>Username:</b><br><em><i class="fa fa-angle-double-right"></i> The username for the database user (for security reasons
						 																please don't take the root account!)</em></td>
						<td><input type="text" name="db_user" class="form-control" 
							value="<?php if (isset($_POST["db_user"])) {echo $_POST["db_user"];} ?>" />
						</td>
					</tr>
					<tr>
						<td><b>Password:</b><br /><em><i class="fa fa-angle-double-right"></i> The password for your database user</em></td>
						<td><input type="password" name="db_pw" class="form-control" 
							value="<?php if (isset($_POST["db_pw"])) {echo $_POST["db_pw"];} ?>" />
						</td>
					</tr>
					<tr>
						<td><b>Databasename:</b><br /><em><i class="fa fa-angle-double-right"></i> e.g. "nobug"</em></td>
						<td><input type="text" name="db_dbname" class="form-control" 
							value="<?php if (isset($_POST["db_dbname"])) {echo $_POST["db_dbname"];} ?>" />
						</td>
					</tr>
				</table>
				
				<h2>Admin Account...</h2>
				<table class="table">
					<tr>
						<td style="width:400px" width="400px"><b>Name:</b><br /><em><i class="fa fa-angle-double-right"></i> e.g. "Administrator"</em></td>
						<td><input type="text" name="admin_name" class="form-control" 
							value="<?php if (isset($_POST["admin_name"])) {echo $_POST["admin_name"];} ?>" />
						</td>
					</tr>
					<tr>
						<td><b>Username:</b><br /><em><i class="fa fa-angle-double-right"></i> e.g. "admin"</em></td>
						<td><input type="text" name="admin_username" class="form-control" 
							value="<?php if (isset($_POST["admin_username"])) {echo $_POST["admin_username"];} ?>" />
						</td>
					</tr>
					<tr>
						<td><b>Email:</b><br /><em><i class="fa fa-angle-double-right"></i> e.g. "info@yourcompany.com"</em></td>
						<td><input type="text" name="admin_email" class="form-control" 
							value="<?php if (isset($_POST["admin_email"])) {echo $_POST["admin_email"];} ?>" />
						</td>
					</tr>
					<tr>
						<td><b>Password:</b><br /><em><i class="fa fa-angle-double-right"></i> Chose a long and complex password!</em></td>
						<td><input type="password" name="admin_pw1" class="form-control" 
							value="<?php if (isset($_POST["admin_pw1"])) {echo $_POST["admin_pw1"];} ?>" />
						</td>
					</tr>
					<tr>
						<td><b>Password:</b> <br /><em><i class="fa fa-angle-double-right"></i> Repeat your password</em></td>
						<td><input type="password" name="admin_pw2" class="form-control" 
							value="<?php if (isset($_POST["admin_pw2"])) {echo $_POST["admin_pw2"];}?>" />
						</td>
					</tr>
				</table>
			    <div style="float: right;">
			  		<input type="submit" class="btn btn-primary btn-lg" value="Next..." />
			    </div>
			  </form>
			  <div style="clear: both; margin-bottom: 20px;"></div>
	</div>
<?php
include 'core/footer.php';