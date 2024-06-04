<!DOCTYPE html>
<html lang="en">
<?php 
require("template/header.php");
require('includes/config.php');
if ( isset($_POST["switch"]) )
{
$sql = "UPDATE `payment_method` 
        SET
        `status` = '".$_POST["switch"]."'
        WHERE `title` LIKE 'cash'
        ";
$result = $dbconnect->query($sql);
} 
$sql = "SELECT `status` 
        FROM `payment_method`
        WHERE `title` LIKE 'cash'
        ";
$result = $dbconnect->query($sql);
$row = $result->fetch_assoc();
$status = $row["status"];

?>

	<body>
		<!--Preloader-->
		<div class="preloader-it">
			<div class="la-anim-1"></div>
		</div>
		<!--/Preloader-->
		<div class="wrapper  theme-1-active pimary-color-green">
			
			<!-- Top Menu Items -->
		<?php require ("template/navbar.php") ?>
		<!-- /Top Menu Items -->
		
		<!-- Left Sidebar Menu -->
		<?php require("template/leftSideBar.php") ?>
		<!-- /Left Sidebar Menu -->
			
			<!-- Right Sidebar Backdrop -->
			<div class="right-sidebar-backdrop"></div>
			<!-- /Right Sidebar Backdrop -->
			
			<!-- Main Content -->
			<div class="page-wrapper">
				<div class="container-fluid">
					<!-- Title -->
					<!-- /Title -->
					
					<!-- Row -->
					<div class="row" style="padding:16px">
				<div class="col-md-12">
					<div class="panel panel-default card-view">
						<div class="panel-heading">
							<div class="pull-left">
								<h6 class="panel-title txt-dark">Pay with cash:</h6>
							</div>
							<div class="clearfix"></div>
						</div>
						<div class="panel-wrapper collapse in">
							<div class="panel-body">
								<form method="POST" action="">
								Turn on/off pay with cash method: 
								<div class="radio">
									<input type="radio" name="switch" id="radio11" value="1" <?php if ( $status == 1 ) {echo "checked";} ?>>
									<label for="radio11"> On </label>
								</div>
								<div class="radio">
									<input type="radio" name="switch" id="radio11" value="0" <?php if ( $status == 0 ) {echo "checked";} ?>>
									<label for="radio11"> Off </label>
								</div>
								<input type="submit" value="submit">
								</form>
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
		
		<!-- Slimscroll JavaScript -->
		<script src="dist/js/jquery.slimscroll.js"></script>
	
		<!-- Fancy Dropdown JS -->
		<script src="dist/js/dropdown-bootstrap-extended.js"></script>
		
		<!-- Owl JavaScript -->
		<script src="../vendors/bower_components/owl.carousel/dist/owl.carousel.min.js"></script>
	
		<!-- Switchery JavaScript -->
		<script src="../vendors/bower_components/switchery/dist/switchery.min.js"></script>
	
		<!-- Init JavaScript -->
		<script src="dist/js/init.js"></script>
		
	</body>
</html>