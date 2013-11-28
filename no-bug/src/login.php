<?php 
session_start(); 
include_once dirname(__FILE__).'/core/logic/loginDA.php';
define("ROOTPATH", "//" . $_SERVER['SERVER_NAME'] . substr(dirname(__FILE__). '/', strlen($_SERVER['DOCUMENT_ROOT'])));


if (isset($_POST['loginusername']) && isset($_POST['loginpassword'])) {
	$loginDA = new LoginDA();
	
	if (($uid = $loginDA->getUser($_POST['loginusername'], $_POST['loginpassword'])) != null) {
		$_SESSION['userId'] = $uid;
		header("Location: index.php");
		die();
	}
}

?>

<!DOCTYPE html>
<html>
<head>
	<title>no-bug</title>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<meta name="author" content="Benj Fassbind & Jan Bucher" />
	<meta http-equiv="content-type" content="text/html; charset=utf-8" />
	<script type="text/javascript" src="<?php echo ROOTPATH; ?>js/bootstrap.min.js"></script>
	<script type="text/javascript" src="<?php echo ROOTPATH; ?>js/less.js" ></script>
	<link rel="stylesheet" href="<?php echo ROOTPATH; ?>style/bootstrap.min.css" />
	<link rel="stylesheet/less" type="text/css" href="<?php echo ROOTPATH; ?>style/global.less" />
	<script type="text/javascript" src="<?php echo ROOTPATH; ?>js/bootstrap.min.js"></script>
	<script type="text/javascript" src="<?php echo ROOTPATH; ?>js/less.js" ></script>
</head>

<body style="padding-top: 70px" onload="javascript:document.loginform.loginusername.focus();">
	
	<div id="header" class="navbar navbar-default navbar-fixed-top">
		<div id="header-wrapper" >
			<a class="navbar-brand" href="#">no-bug</a>
		</div>
	</div>
	
	<div id=main>
		<div id="login-box" style="margin-bottom: 200px;">
			<form method="POST" action="" name="loginform">
				<div class="form-group">
					<label for="login-username">Username:</label>
					<input type="text" name="loginusername" class="form-control" id="login-username" placeholder="Username..." />
				</div>
				<div class="form-group">
					<label for="login-password">Password:</label>
					<input type="password" name="loginpassword" class="form-control" id="login-password" placeholder="Password..." />
				</div>
				<button type="submit" class="btn btn-primary">Login</button>
			</form>
		</div>
	</div>
<?php
include 'core/footer.php';

