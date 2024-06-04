<!DOCTYPE html>
<html lang="en">
<?php 
require ("template/header.php");
require ("includes/config.php");
require ("includes/checksouthead.php");
require ("includes/translate.php");
?>
<body>
<!-- Preloader -->
<div class="preloader-it">
<div class="la-anim-1"></div>
</div>
<!-- /Preloader -->
<div class="wrapper  theme-1-active pimary-color-green">
<!-- Top Menu Items -->
<?php require ("template/navbar.php") ?>
<!-- /Top Menu Items -->

<!-- Left Sidebar Menu -->
<?php require("template/leftSideBar.php") ?>
<!-- /Left Sidebar Menu -->

<!-- Right Sidebar Menu -->
<div class="fixed-sidebar-right">
</div>
<!-- /Right Sidebar Menu -->



<!-- Right Sidebar Backdrop -->
<div class="right-sidebar-backdrop"></div>
<!-- /Right Sidebar Backdrop -->

<!-- Main Content -->
<div class="page-wrapper">
<div class="container-fluid pt-25">
<!-- Row -->
<?php 
$sql = "SELECT * 
		FROM `employees` 
		WHERE `hidden` = 0
		";
$result = $dbconnect->query($sql);
$numberOfEmployees = $result->num_rows;
if ( $numberOfEmployees < 4 )
{
?>
<div class="row">
<div class="col-md-12">
<div class="panel panel-default card-view">
<div class="panel-heading">
<div class="pull-left">
<h6 class="panel-title txt-dark"></h6>
</div>
<div class="clearfix"></div>
</div>
<div class="panel-wrapper collapse in">
<div class="panel-body">
<div class="row">
<div class="col-sm-12 col-xs-12">
<div class="form-wrap">
<form action="includes/clients/add.php" method="POST">
<div class="form-body">
<h6 class="txt-dark capitalize-font">
<i class="zmdi zmdi-account mr-10"></i><?php echo $employeeInfo ?>
</h6>
<hr class="light-grey-hr"/>
<div class="row">
<div class="col-md-6">
<div class="form-group">
<label class="control-label mb-10"><?php echo $fullNameText; ?></label>
<input type="text" name="fullName" class="form-control" required >
</div>
</div>
<!--/span-->
<div class="col-md-6">
<div class="form-group">
<label class="control-label mb-10"><?php echo $passwordText ?></label>
<input type="password" name="password" class="form-control" required >
</div>
</div>
<!--/span-->
</div>
<!-- -->
<div class="row">
<div class="col-md-4">
<div class="form-group">
<label class="control-label mb-10"><?php echo $Mobile ?></label><br>
<input type="number" name="phone" class="form-control" required >
</div>
</div>

<div class="col-md-4">
<div class="form-group">
<label class="control-label mb-10"><?php echo $emailText ?></label><br>
<input type="email" name="email" class="form-control" required >
</div>
</div>


<div class="col-md-4">
<div class="form-group">
<label class="control-label mb-10"><?php echo $empTypeText ?></label><br>
<select name="empType" class="form-control" required>
<option value="0">Admin</option>
<option value="1">Employee</option>
</select></div>
</div>

<!--/span-->
</div>
<!-- -->
<!-- /Row -->
</div>
<div class="form-actions mt-10">
<button type="submit" class="btn btn-success  mr-10"><?php echo $save ?></button>
</div>
</form>
</div>
</div>
</div>
</div>
</div>
</div>		
</div>
</div>

<?php
}
?>
<div class="row">
<div class="col-sm-12">
<div class="panel panel-default card-view">
<div class="panel-heading">
<div class="pull-left">
<h6 class="panel-title txt-dark"><?php echo $listOfEmployees ?></h6>
</div>
<div class="clearfix"></div>
</div>
<div class="panel-wrapper collapse in">
<div class="panel-body">
<div class="table-wrap">
<div class="table-responsive">
<table id="myTable" class="table table-hover display  pb-30" >
<thead>
<tr>
<th><?php echo $fullNameText ?></th>
<th><?php echo $emailText ?></th>
<th><?php echo $Mobile ?></th>
<th><?php echo $empTypeText ?></th>
<th><?php echo $joingingDate ?></th>
<th><?php echo $Action ?></th>
</tr>
</thead>
<tbody>
<?php
$sql = "SELECT * 
		FROM `employees` 
		WHERE `hidden` = 0
		";
$result = $dbconnect->query($sql);
while ( $row = $result->fetch_assoc() )
{

?>
<tr>
<td><?php echo $row["fullName"] ?></td>
<td><?php echo $row["email"] ?></td>
<td><?php echo $row["phone"] ?></td>
<td><?php if ( $row["empType"] == 0 ){echo "Admin";}else{echo "Employee";} ?></td>
<td><?php echo $row["date"] ?></td>
<td>
<a href="includes/clients/delete.php?userid=<?php echo $row["id"]?>" data-toggle="tooltip" data-original-title="Delete"><span class="ml-5 fa fa-trash-o" aria-hidden="true"></span></a>
</td>
</tr>
<?php
}
?>
</tbody>
</table>
</div>
</div>
</div>
</div>
</div>	
</div>
</div>
<!-- /Row -->
</div>

<!-- Footer -->
<?php require("template/footer.php") ?>
<!-- /Footer -->

</div>
<!-- /Main Content -->

</div>
<!-- /#wrapper -->

<!-- JavaScript -->

<!-- jQuery -->
<script src="../vendors/bower_components/jquery/dist/jquery.min.js"></script>

<!-- Bootstrap Core JavaScript -->
<script src="../vendors/bower_components/bootstrap/dist/js/bootstrap.min.js"></script>

<!-- Data table JavaScript -->
<script src="../vendors/bower_components/datatables/media/js/jquery.dataTables.min.js"></script>
<script src="dist/js/dataTables-data.js"></script>

<!-- Slimscroll JavaScript -->
<script src="dist/js/jquery.slimscroll.js"></script>

<!-- Owl JavaScript -->
<script src="../vendors/bower_components/owl.carousel/dist/owl.carousel.min.js"></script>

<!-- Switchery JavaScript -->
<script src="../vendors/bower_components/switchery/dist/switchery.min.js"></script>

<!-- Fancy Dropdown JS -->
<script src="dist/js/dropdown-bootstrap-extended.js"></script>

<!-- Init JavaScript -->
<script src="dist/js/init.js"></script>
</body>

</html>
