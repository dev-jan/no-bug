<?php 
session_start(); 
include 'core/logic/loginDA.php';

if (isset($_POST['login-username']) && isset($_POST['login-password'])) {
	$loginDA = new LoginDA();
	
	if (($uid = $loginDA->getUser($_POST['login-username'], $_POST['login-password'])) != null) {
		$_SESSION['userId'] = $uid;
		header("Location: index.php");
		die();
	}
}

?>

<!DOCTYPE html>
<html>
<head>
	<title>no-bug | pre alpha 0.0.1</title>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<meta name="author" content="Benj Fassbind & Jan Bucher" />
	<meta http-equiv="content-type" content="text/html; charset=utf-8" />
	<script type="text/javascript" src="js/bootstrap.min.js"></script>
	<script type="text/javascript" src="js/less.js" ></script>
	<link rel="stylesheet" href="style/bootstrap.min.css" />
	<link rel="stylesheet/less" type="text/css" href="style/global.less" />
	<script type="text/javascript" src="js/bootstrap.min.js"></script>
	<script type="text/javascript" src="js/less.js" ></script>
</head>

<body style="padding-top: 70px">
	
	<div id="header" class="navbar navbar-default navbar-fixed-top">
		<a class="navbar-brand" href="#">no-bug</a>
	</div>
	
	<div id=main>
		<div id="login-box" style="margin-bottom: 200px;">
			<form method="POST" action="">
				<div class="form-group">
					<label for="login-username">Username:</label>
					<input type="text" name="login-username" class="form-control" id="login-username" placeholder="Username..." />
				</div>
				<div class="form-group">
					<label for="login-password">Password:</label>
					<input type="password" name="login-password" class="form-control" id="login-password" placeholder="Password..." />
				</div>
				<button type="submit" class="btn btn-primary">Login</button>
			</form>
		</div>
	</div>
	
<?php


include 'core/footer.php';

