<?php
        define( 'ACTIVE_MENU', 'administration');	
        include_once '../core/header.php';
        include_once '../core/logic/projectDA.php';
        include_once '../core/logic/permissionDA.php';
        
        $permDA = new PermissionDA();
        if (!$permDA->isGeneralAdmininstrationAllowed()) {
        	$permDA->echoPermissionDeniedAndDie();
        }
        
        $projDA = new ProjectDA();
        $alerts = "";
        
        if (isset($_POST["createProject"])) {
                if ($projDA->checkProjectKey($_POST["newKey"])) {
                        $projDA->createProject($_POST["newKey"], $_POST["newName"], $_POST["newDescription"], $_POST["newVersion"], $_POST["adminselect"], $_POST["writeselect"], $_POST["readselect"]);
                        $alerts = $alerts . '<div class="alert alert-success alert-dismissable">
					  				<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
					  				<strong>Successfull</strong> created Project "'.$_POST["newName"].'"</div>';
                }
                else {
                        $alerts = $alerts . '<div class="alert alert-danger alert-dismissable">
					  				<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
					  				<strong>Failed</strong> to create Project "'.$_POST["newUsername"].'", because KEY already exists.</div>';
                }
        }
        
?>
        <div id="main">
        	<?php echo $alerts; ?>
                <ul class="nav nav-tabs">
                        <li><a href="users.php">Users</a></li>
                        <li><a href="groups.php">Groups</a></li>
                        <li class="active"><a href="projects.php">Projects</a></li>
                        <li><a href="settings.php">Global Settings</a></li>
                </ul>
                <h1>New Project...</h1>
                <form action="" class="userEditForm" method="post">
                        <input type="hidden" name="createProject" value="true" />
                        <h2>> General</h2>
                        <table class="table userEditTable">
                                <tr>
                                        <th>KEY: </th>
                                        <td><input type="text" class="form-control" name="newKey" placeholder="Enter Project KEY"><div class="alert alert-danger">The Key cannot be changed later!</div></td>
                                </tr>
                                <tr>
                                        <th>Name:</th>
                                        <td><input type="text" class="form-control" name="newName" placeholder="Enter Project Name"></td>
                                </tr>
                                <tr>
                                        <th>Description:</th>
                                        <td><input type="text" class="form-control" name="newDescription" placeholder="Enter a Description"></td>
                                </tr>
                                <tr>
                                        <th>Version:</th>
                                        <td><input type="text" class="form-control" name="newVersion" placeholder="Enter the Project Version (optional)"></td>
                                </tr>
                                <tr>
                                        <th>Admin Group: </th>
                                        <td>
                                                <select class="form-control" name="adminselect">
                                                 <?php $projDA->printGroupSelect("");?>
                                                </select>
                                        </td>
                                </tr>
                                <tr>
                                        <th>Write Group: </th>
                                        <td>
                                                <select class="form-control" name="writeselect">
                                                 <?php $projDA->printGroupSelect("");?>
                                                </select>
                                        </td>
                                </tr>
                                <tr>
                                        <th>Read Group: </th>
                                        <td>
                                                <select class="form-control" name="readselect">
                                                 <?php $projDA->printGroupSelect("");?>
                                                </select>
                                        </td>
                                </tr>
                        </table>
                        <button type="submit" class="btn btn-primary">Create Project!</button>
                </form>
        </div>
<?php
        include '../core/footer.php';
?>