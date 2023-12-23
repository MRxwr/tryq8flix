<?php
require ("includes/config.php");
require ("includes/checksouthead.php");
date_default_timezone_set('Asia/Kuwait');
?>
<!DOCTYPE html>
<html lang="en">
<?php require ("template/header.php"); ?>
<body>
	<!-- Preloader -->
	<div class="preloader-it">
		<div class="la-anim-1"></div>
	</div>
	<!-- /Preloader -->
    <div class="wrapper  theme-6-active pimary-color-green">
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
            <div class="container-fluid mt-5">
				<!-- Row -->
				
				<div class="row">
	<div class="col-sm-12">
	<div class="panel panel-default card-view">
	<div class="panel-heading">
		<div class="pull-left">
			<h6 class="panel-title txt-dark">List Of Reports</h6>
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
								<th>Date</th>
								<th>Username</th>
								<th style="width:200px">Title</th>
								<th>Issue</th>
								<th style="width:200px">Description</th>
								<th>Actions</th>
							</tr>
						</thead>
						<tbody>
						<?php 
						$i = 1;
						$sql = "SELECT r.*, u.username, p.category, p.title, p.id as postId
								FROM `reports` as r
								JOIN `users` as u
								ON r.userid = u.id
								JOIN `posts` as p
								ON r.postid = p.id
								WHERE r.status != 'Done'
								ORDER BY `date` DESC
								";
						$result = $dbconnect->query($sql);
						while ( $row = $result->fetch_Assoc() ){
						?>
							<tr>
								<td><?php echo $row["date"] ?></td>
								<td><?php echo $row["username"] ?></td>
								<td><?php echo $row["category"] . " " .$row["title"] ?></td>
								<td><?php echo $row["issue"] ?></td>
								<td><?php echo $row["description"] ?></td>
								<td>
								<?php 
								if ($row["status"] != "Done" ){
									
								?>
								<a href="tryeditpost?id=<?php echo $row["postId"] ?>"><i class="ti-pencil-alt" style="padding: 10px;"></i></a>
								<?php 
								}
								?>
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
	
	<!-- Tinymce JavaScript -->
	<script src="../vendors/bower_components/tinymce/tinymce.min.js"></script>
		
	<!-- Tinymce Wysuhtml5 Init JavaScript -->
	<script src="dist/js/tinymce-data.js"></script>
	
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
