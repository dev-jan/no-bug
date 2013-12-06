<?php 
$error = null;
session_start(); 
include_once dirname(__FILE__).'/core/logic/loginDA.php';
define("ROOTPATH", "//" . $_SERVER['SERVER_NAME'] . substr(dirname(__FILE__). '/', strlen($_SERVER['DOCUMENT_ROOT'])));


if (isset($_POST['loginusername']) && isset($_POST['loginpassword'])) {
	$loginDA = new LoginDA();
	
	if (($uid = $loginDA->getUser($_POST['loginusername'], $_POST['loginpassword'])) != null) {
		$_SESSION['userId'] = $uid;
		header("Location: index.php");
		die();
	} else {
		$error = "<b>Error:</b> Incorrect username or password code entered. Please try again.";
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
	<link rel="stylesheet" href="<?php echo ROOTPATH; ?>style/gh-fork-ribbon.css" />
	<link rel="stylesheet/less" type="text/css" href="<?php echo ROOTPATH; ?>style/global.less" />
	<script type="text/javascript" src="<?php echo ROOTPATH; ?>js/bootstrap.min.js"></script>
	<script type="text/javascript" src="<?php echo ROOTPATH; ?>js/less.js" ></script>
	<link rel="stylesheet" href="<?php echo ROOTPATH; ?>js/nivo-slider/nivo-slider.css" type="text/css" />
	<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.10.1/jquery.min.js" type="text/javascript"></script>
	<script src="<?php echo ROOTPATH; ?>js/nivo-slider/jquery.nivo.slider.pack.js" type="text/javascript"></script>
	<link rel="stylesheet" href="<?php echo ROOTPATH; ?>js/nivo-slider/themes/default/default.css" type="text/css" />
</head>

<body style="padding-top: 70px" onload="javascript:document.loginform.loginusername.focus();">
	
	<div id="header" class="navbar navbar-default navbar-fixed-top">
		<div id="header-wrapper" >
			<a class="navbar-brand" href="#">no-bug</a>
		</div>
	</div>
	
	<div id=main>
		<?php 
		if ($error != null):
		?>
		<div class="alert alert-danger">
			<?php echo $error ?>
		</div>
		<?php
		endif
		?>
		
		<div class="github-fork-ribbon-wrapper right">
	        <div class="github-fork-ribbon">
	            <a href="https://github.com/dev-jan/no-bug">Fork me on GitHub</a>
	        </div>
	    </div>
		
		<div id="login-box">
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
		<div id="promo-box" class="slider-wrapper theme-default">
			<div class="ribbon"></div>
			<div id="slider" class="nivoSlider">
			    <img src="images/promotion/promotion.PNG" alt="" />
			    <img src="images/promotion/promotion1.PNG" alt=""/>
			    <img src="images/promotion/promotion2.PNG" alt="" />
			    <img src="images/promotion/promotion3.PNG" alt="" />
			    <img src="images/promotion/promotion4.PNG" alt="" />
			</div>
			
			<script type="text/javascript">
			$(window).load(function() {
			    $('#slider').nivoSlider();
			});
			</script>
		</div>
		<div style="clear:both;"></div>
	</div>
<?php
include 'core/footer.php';

