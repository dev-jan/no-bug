<?php 
/* Description: Show the log of the platform */

// Include core files
define( 'ACTIVE_MENU', 'administration');
include_once '../core/header.php';
include_once '../core/logic/logDA.php';
include_once '../core/logic/permissionDA.php';
include_once '../core/logic/adminDA.php';

// DataAccess initialisation
$permDA = new PermissionDA();
$adminDA = new AdminDA();

// Check if the user is allowed to access this page
if (!$permDA->isGeneralAdmininstrationAllowed()) {
	$permDA->echoPermissionDeniedAndDie();
}
?>

<div id="main">
	<?php 
	$adminDA->getAdminMenu("log.php");
	?>
	<h1><i class="fa fa-file-text-o"></i> Log</h1>
	
	<?php 
	$logDA = new logDA();
	$count = 1000;
	$dateFrom = date("Y-m-d",strtotime("-1 week"));
	$dateTo = date("Y-m-d");
	// Read configuration
	if (isset($_POST['count'])) {
		$count = $_POST['count'];
	}
	if (isset($_POST['dateFrom'])) {
		$dateFrom = $_POST['dateFrom'];
	}
	if (isset($_POST['dateTo'])) {
		$dateTo = $_POST['dateTo'];
	}
	?>
	
	<form action="#" class="form-inline" method="POST">
		<div class="form-group">
		    <label class="sr-only" for="count">count: </label>
		    <input type="number" class="form-control" name="count" value="<?php echo $count; ?>" />
		</div>
		<div class="form-group">
		    <label class="sr-only" for="count">from: </label>
		    <input type="date" class="form-control" name="dateFrom" value="<?php echo $dateFrom; ?>" />
		</div>
		<div class="form-group">
		    <label class="sr-only" for="count">to: </label>
		    <input type="date" class="form-control" name="dateTo" value="<?php echo $dateTo; ?>" />
		</div>
		<input type="submit" name="submit" class="btn btn-default" value="show" />
	</form>
	
	<table class="table log-table">
		<thead>
			<tr>
				<th class="id">ID</th>
				<th class="date">Date</th>
				<th class="level">Level</th>
				<th class="message">Message</th>
				<th class="exception">Exception</th>
				<th class="user">User</th>
			</tr>
		</thead>
		<tbody id="log-table-content">
			<?php 

			foreach ($logDA->getLog($count, $dateFrom, $dateTo) as $logentry) {
			?>
			<tr>
				<td class="id"><?php echo $logentry["id"] ?></td>
				<td class="date"><?php echo $logentry["date"] ?></td>
				<td class="level"><?php echo $logentry["level"] ?></td>
				<td class="message"><?php echo $logentry["message"] ?></td>
				<td class="exception"><?php echo $logentry["exception"] ?></td>
				<td class="user"><?php echo $logentry["user"]  ?></td>
			</tr>
			<?php 
			}
			?>
		</tbody>
		<script type="text/javascript">
		$(document).ready(function() {
			setHeight();
		});
		
		$(window).resize(function() {
			setHeight();
		});

		function setHeight() {
			var bodyheight = $(document).height() - 330;
			$('#log-table-content').height(bodyheight);
		}
		</script>
	</table>
</div>

<?php
include '../core/footer.php';
?>