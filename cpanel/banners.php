<!DOCTYPE html>
<html lang="en">

<?php 
require ("template/header.php");
include_once ("includes/config.php");
require ("includes/checksouthead.php");
if ( isset($_GET["d"]) )
{
	$sql = "DELETE 
			FROM `banner` 
			WHERE id = '".$_GET["d"]."'";
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
					<div class="col-sm-12">
						<div class="panel panel-default card-view">
							<div class="panel-heading">
								<div class="pull-left">
									<h6 class="panel-title txt-dark"><?php echo $List_of_Banners ?></h6>
								</div>
								<div class="clearfix"></div>
							</div>
							<div class="panel-wrapper collapse in">
								<div class="panel-body">
									<a href="add-banners.php?act=add"><button class="btn  btn-primary btn-rounded"><?php echo $Add_Banner ?></button></a>	  
									<div class="table-wrap mt-40">
										<div class="table-responsive">
										  <table class="table table-hover table-bordered mb-0">
											<thead>
											  <tr>
												<th>#</th>
												<th><?php echo $Title ?></th>
												<th><?php echo $Image ?></th>
												<th class="text-nowrap"><?php echo $Action ?></th>
											  </tr>
											</thead>
											<tbody>
											<?php 
											$sql= "SELECT * 
											FROM banner";
											$result = $dbconnect->query($sql);
											$i = 1;
											while ( $row = $result->fetch_assoc() )
											{
												?>
												<tr>
												<td><?php echo $i ?></td>
												<td><?php echo $row["title"] ?></td>
												<td><img src="../logos/<?php echo $row["image"] ?>" style="width:250px; height:250px"></td>
												<td class="text-nowrap">
												<a href="?d=<?php echo $row["id"] ?>" data-toggle="tooltip" data-original-title="Delete"><i class="fa fa-close text-danger"></i></a> 
												<a href="add-banners?act=edit&id=<?php echo $row["id"] ?>" data-toggle="tooltip" data-original-title="Edit"><i class="fa fa-pencil text-secondary"></i></a></td>
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
					<!-- /Bordered Table -->
				
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
	
	<!-- Owl JavaScript -->
	<script src="../vendors/bower_components/owl.carousel/dist/owl.carousel.min.js"></script>
	
	<!-- Sweet-Alert  -->
	<script src="../vendors/bower_components/sweetalert/dist/sweetalert.min.js"></script>
	<script src="dist/js/sweetalert-data.js"></script>
		
	<!-- Switchery JavaScript -->
	<script src="../vendors/bower_components/switchery/dist/switchery.min.js"></script>
	
	<!-- Fancy Dropdown JS -->
	<script src="dist/js/dropdown-bootstrap-extended.js"></script>
		
	<!-- Init JavaScript -->
	<script src="dist/js/init.js"></script>
</body>

</html>
