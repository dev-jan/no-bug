<?php
define( 'ACTIVE_MENU', 'main');
include_once 'core/header.php';
include_once 'core/logic/projectDA.php';
?>
<div id="main">
	<div class="panel panel-default">
		<div class="panel-body">Achtung: no-bug ist zurzeit noch in der Entwicklung! Bei Fehler: Mail an <a href="mailto:jan@endlesscoderz.ch">jan@endlesscoderz.ch</a></div>
	</div>

	<div class="panel panel-primary">
		<div class="panel-heading">
			<h3 class="panel-title">Your Projects</h3>
		</div>
		<div class="">
			<?php 
			$projDA = new ProjectDA();
			$projDA->printProjectsOnMainPage();
			?>
		</div>
	</div>

	<div class="panel panel-success">
		<div class="panel-heading">
			<h3 class="panel-title">Assigned to Me</h3>
		</div>
		<div class="">
			<a href="#" class="list-group-item"><b>NOBUG-512</b>: Can not delete
				Projects</a> <a href="#" class="list-group-item"><b>JQ-14493</b>:
				Fail to load library in Firefox 25</a>
		</div>
	</div>
</div>

<?php 
include 'core/footer.php';
?>
