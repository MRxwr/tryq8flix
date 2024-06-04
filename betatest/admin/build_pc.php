<!DOCTYPE html>
<html lang="en">

<?php 
require ("template/header.php");
include_once ("includes/config.php");

if ( isset($_GET["p"]) AND isset($_GET["m"]) )
{
	$proccessor = $_GET["p"];
	$motherboard = $_GET["m"];
	$sql= "INSERT INTO `groups`(`id`, `proccessor`, `motherboard`) VALUES (NULL,'$proccessor','$motherboard')";
	$result = $dbconnect->query($sql);
}
if ( isset($_GET["d"]) )
{
	$delete = $_GET["d"];
	$sql= "DELETE FROM `groups` WHERE `groups`.`id` = $delete ";
	$result = $dbconnect->query($sql);
}
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
				<div class="row">
				
				<!-- Bordered Table -->
					<div class="col-md-6" style="width:100%">
							<div class="panel panel-default card-view">
								<div class="panel-heading">
									<div class="pull-left">
										<h6 class="panel-title txt-dark">Create a group</h6>
									</div>
									<div class="clearfix"></div>
								</div>
								<div class="panel-wrapper collapse in">
									<div class="panel-body">
										<form>
											<div class="form-group">
												<label class="control-label mb-10">Select Proccessor:</label>
												<select class="form-control select2" name="p">
													<option>Select</option>
													<?php 
													$sql = "SELECT *
															FROM `products`
															WHERE `categoryId` LIKE '5'
															";
													$result = $dbconnect->query($sql);
													while ( $row = $result->fetch_assoc() )
													{
													?>
													<option value="<?php echo $row["id"] ?>"><?php echo $row["enTitle"] ?></option>
													<?php
													}
													?>
												</select>
											</div>
										<div class="form-group">
												<label class="control-label mb-10">Select Motherboard:</label>
												<select class="form-control select2" name="m">
													<option>Select</option>
													<?php 
													$sql = "SELECT *
															FROM `products`
															WHERE `categoryId` LIKE '6'
															";
													$result = $dbconnect->query($sql);
													while ( $row = $result->fetch_assoc() )
													{
													?>
													<option value="<?php echo $row["id"] ?>"><?php echo $row["enTitle"] ?></option>
													<?php
													}
													?>
												</select>
											</div>
											<button class="btn btn-primary btn-rounded">Add Group</button>
										</form>	
									</div>
								</div>
							</div>
						</div>
				<!-- /Bordered Table -->
				
				<div class="col-sm-12">
						<div class="panel panel-default card-view">
							<div class="panel-heading">
								<div class="pull-left">
									<h6 class="panel-title txt-dark">Groups</h6>
								</div>
								<div class="clearfix"></div>
							</div>
							<div class="panel-wrapper collapse in">
								<div class="panel-body">
									<div class="table-wrap">
										<div class="table-responsive">
											<table id="datable_1" class="table table-hover display  pb-30" >
												<thead>
													<tr>
														<th>Group Id</th>
														<th>Proccessor</th>
														<th>Motherboard</th>
														<th>Actions</th>
													</tr>
												</thead>
												<tbody>
													
						<?php 
						$sql = "SELECT * FROM `groups`";
						$result = $dbconnect->query($sql);
						while ( $row = $result->fetch_assoc() )
						{
							$pId[] = $row["proccessor"];
							$mId[] = $row["motherboard"];
							$gId[] = $row["id"];
						}
						$i = 0;
						while ( $i < sizeof($mId) )
						{
							$sql = "SELECT enTitle FROM `products` WHERE id = $pId[$i]";
							$result = $dbconnect->query($sql);
							$row = $result->fetch_assoc();
							
								?>
															<tr>
															<td><?php echo $i+1 ?></td>
															<td><?php echo $row["enTitle"] ?></td>
								<?php
								$sql = "SELECT enTitle FROM `products` WHERE id = $mId[$i]";
								$result = $dbconnect->query($sql);
								$row = $result->fetch_assoc();
								?>
															<td><?php echo $row["enTitle"] ?></td>
															<td><a href="?d=<?php echo $gId[$i] ?>"><button class="btn btn-danger btn-icon-anim btn-circle"><i class="icon-trash"></i></button><a/></td>
															</tr>
								<?php
							$i++;
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
		
		<!-- Moment JavaScript -->
		<script type="text/javascript" src="../vendors/bower_components/moment/min/moment-with-locales.min.js"></script>
		
		<!-- Bootstrap Colorpicker JavaScript -->
		<script src="../vendors/bower_components/mjolnic-bootstrap-colorpicker/dist/js/bootstrap-colorpicker.min.js"></script>
		
		<!-- Switchery JavaScript -->
		<script src="../vendors/bower_components/switchery/dist/switchery.min.js"></script>
		
		<!-- Select2 JavaScript -->
		<script src="../vendors/bower_components/select2/dist/js/select2.full.min.js"></script>
		
		<!-- Bootstrap Select JavaScript -->
		<script src="../vendors/bower_components/bootstrap-select/dist/js/bootstrap-select.min.js"></script>
		
		<!-- Bootstrap Tagsinput JavaScript -->
		<script src="../vendors/bower_components/bootstrap-tagsinput/dist/bootstrap-tagsinput.min.js"></script>
		
		<!-- Bootstrap Touchspin JavaScript -->
		<script src="../vendors/bower_components/bootstrap-touchspin/dist/jquery.bootstrap-touchspin.min.js"></script>
		
		<!-- Multiselect JavaScript -->
		<script src="../vendors/bower_components/multiselect/js/jquery.multi-select.js"></script>
		
		 
		<!-- Bootstrap Switch JavaScript -->
		<script src="../vendors/bower_components/bootstrap-switch/dist/js/bootstrap-switch.min.js"></script>
		
		<!-- Bootstrap Datetimepicker JavaScript -->
		<script type="text/javascript" src="../vendors/bower_components/eonasdan-bootstrap-datetimepicker/build/js/bootstrap-datetimepicker.min.js"></script>
		
		<!-- Form Advance Init JavaScript -->
		<script src="dist/js/form-advance-data.js"></script>
		
		<!-- Slimscroll JavaScript -->
		<script src="dist/js/jquery.slimscroll.js"></script>
	
		<!-- Fancy Dropdown JS -->
		<script src="dist/js/dropdown-bootstrap-extended.js"></script>
		
		<!-- Owl JavaScript -->
		<script src="../vendors/bower_components/owl.carousel/dist/owl.carousel.min.js"></script>
	
		<!-- Init JavaScript -->
		<script src="dist/js/init.js"></script>
</body>

</html>
