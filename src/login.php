<?php 
session_start(); 
include_once dirname(__FILE__).'/core/logic/loginDA.php';
include_once dirname(__FILE__).'/core/logic/settingsDA.php';
include_once dirname(__FILE__).'/core/logger.php';
define("ROOTPATH", str_replace("core/../", "", substr(dirname(__FILE__). '/', strlen($_SERVER['DOCUMENT_ROOT']))));

if (!defined('ISCONFIGURATED')) {
	echo '<!DOCTYPE html><html>
			<head>
				<title>Error: 500</title>
				<link rel="stylesheet" href="'.ROOTPATH.'style/bootstrap.min.css" />
				<link href="//netdna.bootstrapcdn.com/font-awesome/4.0.3/css/font-awesome.css" rel="stylesheet">
			</head>
			<body><div id=main> <div class="alert alert-danger" style="margin: 50px;"><strong>Setup not finished!</strong> <br />
			          The setup of this platform is not finished. If you want to go to the setup, click
					  <a href="setup.php">here</a>.
			          </div></div>
			</body></html>';
	die();
}

$error = null;
if (isset($_POST['loginusername']) && isset($_POST['loginpassword'])) {
	$loginDA = new LoginDA();
	
	if (($uid = $loginDA->getUser($_POST['loginusername'], $_POST['loginpassword'])) != null) {
		$_SESSION['nobug'.RANDOMKEY.'userId'] = $uid;
		Logger::debug("Successfull Login for user: { ".$_POST['loginusername']." }", null);
		header("Location: index.php");
		die();
	} else {
		$error = "<b>Error:</b> Incorrect username or password code entered. Please try again.";
		Logger::warn("Login with wrong credentials: { ".$_POST['loginusername']." (".$_SERVER['REMOTE_ADDR'].") }", null);
	}
}

?>

<!DOCTYPE html>
<html>
<head>
	<title>no-bug</title>
	<?php 
	include "core/meta.php"; 
	$settingsDA = new SettingsDA();
	echo $settingsDA->getTrackingCode(); 
	?>
	<link rel="stylesheet" href="<?php echo ROOTPATH; ?>js/nivo-slider/nivo-slider.css" type="text/css" />
	<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.10.1/jquery.min.js" type="text/javascript"></script>
	<script src="<?php echo ROOTPATH; ?>js/nivo-slider/jquery.nivo.slider.pack.js" type="text/javascript"></script>
	<link rel="stylesheet" href="<?php echo ROOTPATH; ?>js/nivo-slider/themes/default/default.css" type="text/css" />
	
	<link rel="stylesheet" href="<?php echo ROOTPATH; ?>style/bootstrap.min.css" />
	<link rel="stylesheet" href="<?php echo ROOTPATH; ?>style/gh-fork-ribbon.css" />
</head>

<body style="padding-top: 70px" onload="javascript:document.loginform.loginusername.focus();">
	
	<div id="header" class="navbar navbar-default navbar-fixed-top">
		<div id="header-wrapper" >
			<a class="navbar-brand" href="#"><i class="fa fa-bug"></i> no-bug</a>
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
			<h3 style="color: #428bca; border-bottom: 1px solid; padding-bottom: 2px; margin-bottom: 30px;"><strong><i class="fa fa-lock"></i> Login</strong></h3>
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
		<div id="promo-text" class="promo-box" style="margin-bottom: 10px;">
			<h4>no-bug Bugtracker</h4>
			<p>Welcome to the "no-bug" - Bugtracker. This is a new, innovative, simple and stylish Bugtracking System for
			   your Software Project. And it's no only free, its Open Source, so you can simply implement your own features.
			   Here are some screenshots:
			</p>
		</div>
		<div id="promo-box" class="slider-wrapper theme-default promo-box">
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