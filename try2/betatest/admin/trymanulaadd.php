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
				<!-- manual adding -->
				<div class="row">
				<div class="col-sm-12">
					<div class="panel panel-default card-view">
						<div class="panel-heading">
							<div class="pull-left">
								<h6 class="panel-title txt-dark">Manual Adding</h6>
							</div>
							<div class="clearfix"></div>
						</div>
						<div class="panel-wrapper collapse in">
							<div class="panel-body">
								<div class="form-wrap">
									<form action="includes/newcategorydb.php" method="post" >
									<div class="row">
									<div class="col-md-4">
										<div class="form-group">
											<label class="control-label mb-10">Type</label>
											<select class="form-control" name="type" >
												<option>Select</option>
												
												<?php 
												$sql = "SELECT * FROM `categorieslist`";
												$result = $dbconnect->query($sql);
												while ( $row = $result->fetch_Assoc() ){
													?>
													
													<option value="<?php echo $row["title"] ?>"><?php if ( $row["title"] == "AniMov" ){ echo "Anime Movie";}else{
													echo $row["title"];}?></option>
													
													<?php
												}
												?>
												
											</select>
										</div>
										</div>
										<div class="col-md-4">
										<div class="form-group">
											<label class="control-label mb-10" for="email_de">Title</label>
											<input type="text" name="title" class="form-control" id="">
										</div>
										</div>
										<div class="col-md-4">
										<div class="form-group">
											<label class="control-label mb-10" for="email_de">Rating</label>
											<input type="text" name="rating" class="form-control" id="">
										</div>
										</div>
										<div class="col-md-4">
										<div class="form-group">
											<label class="control-label mb-10" for="email_de">IMDb</label>
											<input type="text" name="imdbrating" class="form-control" id="">
										</div>
										</div>
										<div class="col-md-4">
										<div class="form-group">
											<label class="control-label mb-10" for="email_de">Duration</label>
											<input type="text" name="duration" class="form-control" id="">
										</div>
										</div>
										<div class="col-md-4">
										<div class="form-group">
											<label class="control-label mb-10" for="email_de">Genre</label>
											<input type="text" name="genre" class="form-control" id="">
										</div>
										</div>
										<div class="col-md-4">
										<div class="form-group">
											<label class="control-label mb-10" for="email_de">Year</label>
											<input type="text" name="releasedate" class="form-control" id="">
										</div>
										</div>
										<div class="col-md-4">
										<div class="form-group">
											<label class="control-label mb-10" for="email_de">Language</label>
											<input type="text" name="language" class="form-control" id="">
										</div>
										</div>
										<div class="col-md-4">
										<div class="form-group">
											<label class="control-label mb-10" for="email_de">Notes</label>
											<input type="text" name="notes" class="form-control" id="">
										</div>
										</div>
										<div class="col-md-4">
										<div class="form-group">
											<label class="control-label mb-10" for="email_de">Country</label>
											<input type="text" name="country" class="form-control" id="">
										</div>
										</div>
										<div class="col-md-4">
										<div class="form-group">
											<label class="control-label mb-10" for="email_de">Cast</label>
											<input type="text" name="channel" class="form-control" id="">
										</div>
										</div>
										<div class="col-md-4">
										<div class="form-group">
											<label class="control-label mb-10" for="email_de">Poster</label>
											<input type="text" name="poster" class="form-control" id="">
										</div>
										</div>
										<div class="col-md-4">
										<div class="form-group">
											<label class="control-label mb-10" for="email_de">Header</label>
											<input type="text" name="header" class="form-control" id="">
										</div>
										</div>
										<div class="col-md-4">
										<div class="form-group">
											<label class="control-label mb-10" for="pwd_de">Trailer:</label>
											<input type="text" name="trailer" class="form-control" id="">
											<input type="hidden" name="posttime" class="form-control" value="<?php echo date("g:i A") ?>">
											<input type="hidden" name="postdate" class="form-control" value="<?php echo date("Y/m/d") ?>">
										</div>
										</div>
										</div>
										<div class="row">
										<div class="col-md-12">
										<div class="form-group">
											<label class="control-label mb-10" for="email_de">Description</label>
											<textarea class="tinymce" name="description" ></textarea>
										</div>
										</div>
										</div>
										<div class="col-md-12 text-center" style="margin-top: 30px !important;">
										<div class="form-group mb-0">
											<button type="submit" class="btn btn-success btn-anim"><i class="icon-rocket"></i><span class="btn-text">submit</span></button>
										</div>	
									</form>
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
