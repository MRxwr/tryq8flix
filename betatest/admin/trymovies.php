<?php
require ("includes/config.php");
require ("includes/checksouthead.php");
date_default_timezone_set('Asia/Kuwait');
?>
<!DOCTYPE html>
<html lang="en">
<?php require ("template/header.php"); ?>
<body>
	<!-- Preloader
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
				
<!-- list of categories -->				
<div class="row">
	<div class="col-sm-12">
	<div class="panel panel-default card-view">
	<div class="panel-heading">
		<div class="pull-left">
			<h6 class="panel-title txt-dark">List Of Titles</h6>
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
								<th>#</th>
								<th>Poster</th>
								<th>Title</th>
								<th>Type</th>
								<th>Actions</th>
							</tr>
						</thead>
						<tbody>
						<?php 
						$i = 1;
						$sql = "SELECT *
								FROM `category`
								WHERE
								`type` LIKE '%movie%'
								ORDER BY `id` DESC
								";
						$result = $dbconnect->query($sql);
						while ( $row = $result->fetch_Assoc() ){
						?>
							<tr>
								<td><?php echo $i++ ?></td>
								<td><img src="<?php echo $row["poster"] ?>" style="width:100px; height:150px" ></td>
								<td style="width:250px"><?php echo $row["title"] ?></td>
								<td><?php echo $row["type"] ?></td>
								<td>
								<a class="getDetailsCat" data-toggle="modal" data-target="#edit-cat-modal" id="<?php echo $row["id"] ?>"><i class="ti-pencil-alt" style="    padding: 10px;"></i></a>
								
								<a class="getDetails" id="<?php echo $row["id"] ?>" data-toggle="modal" data-target="#add-post-modal" ><i class="ti-plus" style="padding: 10px;"></i>
								</a>
								
								<a data-toggle="modal" data-target="#List-modal" id="<?php echo $row["id"] ?>"><i class="ti-list" style="padding: 10px;"></i>
								</a>
								
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

<!-- add successfully notification -->
<div class="jq-toast-wrap top-right">
	<div class="jq-toast-single addPostNot jq-has-icon jq-icon-success" style="text-align: left; display: none;">
		<span class="jq-toast-loader jq-toast-loaded" style="-webkit-transition: width 3.1s ease-in;-o-transition: width 3.1s ease-in;transition: width 3.1s ease-in;background-color: #fec107;"></span>
		<span class="close-jq-toast-single">×</span>
		<h2 class="jq-toast-heading">Post has been added successfully.</h2>
	</div>
</div>

<!-- edit successfully notification modal -->
<div class="jq-toast-wrap top-right">
	<div class="jq-toast-single editCatnot jq-has-icon jq-icon-success" style="text-align: left; display: none;">
		<span class="jq-toast-loader jq-toast-loaded" style="-webkit-transition: width 3.1s ease-in;-o-transition: width 3.1s ease-in;transition: width 3.1s ease-in;background-color: #fec107;"></span>
		<span class="close-jq-toast-single">×</span>
		<h2 class="jq-toast-heading">Editing has been added successfully.</h2>
	</div>
</div>

<!-- add post modal -->
<div id="add-post-modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
				<h5 class="modal-title catTitle">
				
				</h5>
			</div>
			<div class="modal-body">
				<form>
					<div class="form-group">
						<label for="recipient-name" class="control-label mb-10">Title:</label>
						<input type="text" class="form-control EpNo" id="recipient-name">
					</div>
					
					<div class="form-group">
						<label for="recipient-name" class="control-label mb-10">Uptobox:</label>
						<input type="text" class="form-control POSTlink" id="recipient-name">
					</div>
				</form>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
				<button id="" type="button" class="btn btn-danger sendPost">Add Post</button>
			</div>
		</div>
	</div>
</div>

<!-- Edit Category modal -->
<div id="edit-cat-modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
				<h5 class="modal-title editTitle">Edit Category</h5>
			</div>
			<div class="modal-body">
				
				
				<div class="row">
				<div class="col-sm-12">
					<div class="panel panel-default card-view">
						<div class="panel-wrapper collapse in">
							<div class="panel-body">
								<div class="form-wrap">
									<form action="includes/newcategorydb.php" method="post" >
									<div class="row">
									<div class="col-md-4">
										<div class="form-group">
											<label class="control-label mb-10">Type</label>
											<select class="form-control" name="type" >
											<option name="type" value=""></option>
												
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
											<input type="hidden" name="posttime" class="form-control" value="<?php echo date("h:i:s") ?>">
											<input type="hidden" name="postdate" class="form-control" value="<?php echo date("Y-m-d") ?>">
											<input type="hidden" name="catID" class="form-control" value="">
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
								</form>
							</div>
						</div>
					</div>
				</div>
				
				</div>
				<!-- /Row -->
			</div>
				
				
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
				<button type="button" class="btn btn-danger editDetailsCat">Save changes</button>
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
	
	<script>
	<!-- get post details -->
	$('.getDetails').click(function() {
		  var catId = $(this).attr('id');
		$.ajax({
				type:"POST",
				url: "../api/functions.php",
				data: {
				bringPostTitle: catId,
				},
				success:function(result){
					var newData = result.split("^");
					$(".catTitle").html(newData[0]);
					$(".EpNo").val(newData[1]);
					$(".sendPost").attr('id',catId);
				}
			});
		});
	<!-- get category details -->
	$('.getDetailsCat').click(function() {
		  var catId = $(this).attr('id');
		$.ajax({
				type:"POST",
				url: "../api/functions.php",
				data: {
				bringData: catId,
				},
				success:function(result){
					var MovieDetails = result.split("^");
					$('input[name="title"]').val(MovieDetails[0]);
					$('input[name="poster"]').val(MovieDetails[9]);
					$('input[name="rating"]').val(MovieDetails[1]);
					$('input[name="imdbrating"]').val(MovieDetails[2]);
					$('input[name="releasedate"]').val(MovieDetails[5]);
					$('input[name="genre"]').val(MovieDetails[4]);
					$('input[name="country"]').val(MovieDetails[7]);
					$('input[name="channel"]').val(MovieDetails[8]);
					$('input[name="notes"]').val(MovieDetails[13]);
					$('input[name="duration"]').val(MovieDetails[3]);
					$('input[name="language"]').val(MovieDetails[6]);
					$('input[name="carID"]').val(MovieDetails[12]);
					$('input[name="trailer"]').val(MovieDetails[11]);
					$('option[name="type"]').html(MovieDetails[14]);
					$('option[name="type"]').val(MovieDetails[14]);
					$('input[name="header"]').val(MovieDetails[15]);
					$('input[name="catID"]').val(catId);
					tinyMCE.activeEditor.setContent(MovieDetails[10]);
				}
			});
		});
		
	<!-- edit category -->
	$('.editDetailsCat').click(function() {
		  var titlee = $('input[name="title"]').val();
		  var postere = $('input[name="poster"]').val();
		  var ratinge = $('input[name="rating"]').val();
		  var imdbratinge = $('input[name="imdbrating"]').val();
		  var genree = $('input[name="genre"]').val();
		  var countrye = $('input[name="country"]').val();
		  var channele = $('input[name="channel"]').val();
		  var notese = $('input[name="notes"]').val();
		  var duratione = $('input[name="duration"]').val();
		  var languagee = $('input[name="language"]').val();
		  var releasedatee = $('input[name="releasedate"]').val();
		  var trailere = $('input[name="trailer"]').val();
		  var typee = $('select[name="type"]').val();
		  var headere = $('input[name="header"]').val();
		  var descriptione = tinyMCE.activeEditor.getContent();
		  
		  var catId = $('input[name="catID"]').val();
		$.ajax({
				type:"POST",
				url: "../api/functions.php",
				data: {
					cateId: catId,
					cateTitle: titlee,
					catePoster: postere,
					cateRating: ratinge,
					cateIMDb: imdbratinge,
					cateGenre: genree,
					cateCountry: countrye,
					cateChannel: channele,
					cateNotes: notese,
					cateDuration: duratione,
					cateLanguage: languagee,
					cateTrailer: trailere,
					cateType: typee,
					cateHeader: headere,
					cateDescription: descriptione,
					cateYear : releasedatee,
				},
				success:function(result){
					console.log(result);
					$('#edit-cat-modal').modal('toggle');
					$('.editCatnot').attr('style','display:block');
					setTimeout(function (){
					$('.editCatnot').attr('style','display:none');
					}, 2000);
				}
			});
		});
	
	<!-- add new post -->
	$('.sendPost').click(function() {
		  var catId = $(this).attr('id');
		  var postTitle = $(".EpNo").val();
		  var postLink = $(".POSTlink").val();
		$.ajax({
				type:"POST",
				url: "../api/functions.php",
				data: {
				insertCatId: catId,
				insertTitle: postTitle,
				insertLink: postLink,
				},
				success:function(result){
					$('#add-post-modal').modal('toggle');
					$('.addPostNot').attr('style','display:block');
					setTimeout(function (){
					$('.addPostNot').attr('style','display:none');
					}, 2000);
				}
			});
		});

	</script>
	
</body>

</html>
