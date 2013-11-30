<?php
define( 'ACTIVE_MENU', 'proj');
include_once 'core/header.php';
include_once 'core/logic/userDA.php';
$userDA = new UserDA();
$alerts = "";
if (isset($_POST["newpw"]) && $_POST["newpw"] == $_POST["newpw2"]) {
	$userDA->updatePassword($_SESSION["userId"], $_POST["newpw"]);
	$alerts = '<div class="alert alert-success alert-dismissable">
		  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
		  <strong>Password changed!</strong> Your Password was successful changed </div>';
}

?>

<div id="main">
	<?php echo $alerts; ?>
	<h1>My Profil</h1>
	<p>On this site you can change your Profile Settings. </p>
	<h3 class="text-primary">Change my Password</h3>
	<form action="" method="post">
		<table class="table" border="0">
			<tr>
				<td>New Password: </td>
				<td><input type="password" name="newpw" placeholder="password" class="form-control" /></td>
			</tr>		
			<tr>
				<td>New Password: </td>
				<td><input type="password" name="newpw2" placeholder="password" class="form-control" /></td>
			</tr>	
		</table>
		<input type="submit" value="Change Password" class="btn btn-primary" />
	</form>
</div>

<?php 
	include 'core/footer.php';
?>