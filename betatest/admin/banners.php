<!DOCTYPE html>
<html lang="en">

<?php 
require ("template/header.php");
include_once ("includes/config.php");

if ( isset($_GET["b"]) )
{
	if ( $_GET["b"] == "new" )
	{
		$bannerType = "new";
		$bannerAdd = "add-banners.php?act=add&b=new";
		$bannerEdit = "add-banners.php?act=edit&b=new&id=";
		$bannerDelete = "includes/banners/delete.php?b=new&id=";
	}
	elseif ( $_GET["b"] == "best" )
	{
		$bannerType = "best";
		$bannerAdd = "add-banners.php?act=add&b=best";
		$bannerEdit = "add-banners.php?act=edit&b=best&id=";
		$bannerDelete = "includes/banners/delete.php?b=best&id=";
	}
	elseif ( $_GET["b"] == "build" )
	{
		$bannerType = "build";
		$bannerAdd = "add-banners.php?act=add&b=build";
		$bannerEdit = "add-banners.php?act=edit&b=build&id=";
		$bannerDelete = "includes/banners/delete.php?b=build&id=";
	}
	else
	{
		$bannerType = "main";
		$bannerAdd = "add-banners.php?act=add";
		$bannerEdit = "add-banners.php?act=edit&id=";
		$bannerDelete = "includes/banners/delete.php?b=main&id=";
	}
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
									<h6 class="panel-title txt-dark">List of Banners</h6>
								</div>
								<div class="clearfix"></div>
							</div>
							<div class="panel-wrapper collapse in">
								<div class="panel-body">
									<a href="<?php echo $bannerAdd ?>"><button class="btn  btn-primary btn-rounded">Add Banner</button></a>	  
									<div class="table-wrap mt-40">
										<div class="table-responsive">
										  <table class="table table-hover table-bordered mb-0">
											<thead>
											  <tr>
												<th>#</th>
												<th>Title</th>
												<th>Image</th>
												<th class="text-nowrap">Action</th>
											  </tr>
											</thead>
											<tbody>
											<?php 
											$sql= "SELECT * FROM banners WHERE type LIKE '".$bannerType."'";
											$result = $dbconnect->query($sql);
											$i = 1;
											while ( $row = $result->fetch_assoc() )
											{
												?>
												<tr>
												<td><?php echo $i ?></td>
												<td><?php echo $row["title"] ?></td>
												<td><img src="../logos/<?php echo $row["imageurl"] ?>" style="width:250px; height:250px"></td>
												<td class="text-nowrap">
												<a href="<?php echo $bannerEdit . $row["id"] ?>" class="mr-25" data-toggle="tooltip" data-original-title="Edit"> <i class="fa fa-pencil text-inverse m-r-10"></i></a>
												<a href="<?php echo $bannerDelete . $row["id"] ?>" data-toggle="tooltip" data-original-title="Delete"><i class="fa fa-close text-danger"></i></a></td>
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
