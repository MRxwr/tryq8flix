<?php
require ("includes/config.php");
require ("includes/checksouthead.php");
date_default_timezone_set('Asia/Kuwait');

if ( isset($_POST["category"]) ){
	$id = $_POST["id"];
	$catId = $_POST["category"];
	$videoLink = $_POST["videoLink"];
	$title = $_POST["title"];
	
	$sql = "SELECT `title` FROM `category` WHERE `id` = '".$catId."'";
	$result = $dbconnect->query($sql);
	$row = $result->fetch_Assoc();
	$category = $row["title"];

	$sql = "SELECT * FROM posts WHERE id='$id'";
	$result = $dbconnect->query($sql);
	$row = $result->fetch_assoc();
	
	if( isset($_FILES['subtitle']) AND is_uploaded_file($_FILES['subtitle']['tmp_name']) ){
		//uploading the file
		$directory = "../subs/";
		$originalfile = $directory . round(microtime(true));
		move_uploaded_file($_FILES["subtitle"]["tmp_name"], $originalfile);
		$fileoldname = round(microtime(true));
		rename($originalfile,$fileoldname);

		//converting srt to vtt
		$content = file_get_contents($fileoldname);
		$content = str_replace(",",".",$content);
		$content = "WEBVTT \n\n" . $content;
		file_put_contents($fileoldname, $content);

		//saving the file into vtt extension
		$filenewname = $directory . date("d-m-y") . time() .  round(microtime(true)). ".vtt";
		rename($fileoldname,$filenewname);	
	}else{
		$filenewname = $row["subtitle"];
	}
	
	if ( $title != $row["title"] )
	{
		$sql = "UPDATE posts SET title='$title' WHERE id='$id'";
		$results = $dbconnect->query($sql);
	}
	if ( $category != $row["category"] )
	{
		$sql = "UPDATE posts SET category='$category' WHERE id='$id'";
		$results = $dbconnect->query($sql);
	}
	if ( $filenewname != $row["subtitle"] )
	{
		$sql = "UPDATE posts SET subtitle='$filenewname' WHERE id='$id'";
		$results = $dbconnect->query($sql);
	}
	if ( $catId != $row["catid"] )
	{
		$sql = "UPDATE posts SET catid='$catId' WHERE id='$id'";
		$results = $dbconnect->query($sql);
	}
	
	// ** updating video links ** \\
	$sql = "SELECT *
			FROM `postlinks`
			WHERE
			`id` = '".$id."'
			";
	$result = $dbconnect->query($sql);
	$row = $result->fetch_assoc();
	if ( $videoLink != $row["uptobox"] )
	{
		$sql = "UPDATE `postlinks`
				SET
				`uptobox` ='".$videoLink."'
				WHERE
				`id` = '".$id."'
				";
		$results = $dbconnect->query($sql);
	}
}
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
<div class="col-md-12">
<div class="panel panel-default card-view">
<div class="panel-heading">
<div class="pull-left">
<h6 class="panel-title txt-dark">Edit Post</h6>
</div>
<div class="clearfix"></div>
</div>
<div class="panel-wrapper collapse in">
<div class="panel-body">

<div class="row">
<div class="col-md-12">
<div class="form-wrap">
<form action="" method="POST" enctype="multipart/form-data">
<?php
if ( !isset($_GET["id"]) ){
	header('LOCATION: index');
}else{
	$sql = "SELECT
			p.*, l.uptobox
			FROM `posts` AS p
			JOIN `postlinks` AS l
			ON l.id = p.id
			WHERE p.id = '".$_GET["id"]."'
			";
	$result = $dbconnect->query($sql);
	$row = $result->fetch_Assoc();
	$title = $row["title"];
	$category = $row["category"];
	$catId = $row["catid"];
	$uptobox = $row["uptobox"];
	$uptoboxLink = $uptobox;
	$subtitle = $row["subtitle"];
	
	$uptobox = str_replace("http://uptobox.com/","",str_replace("https://uptobox.com/","",$uptobox));
	$string = file_get_contents("https://uptobox.com/api/link?token=c7592f3d7e8a2c6682fb51ebd2e9d96f725fl&file_code=".$uptobox);
		if ( $string == NULL )
		{
			header("Location: maintenance.php");
		}
			$json_a = json_decode($string, true);
			$jsonIterator = new RecursiveIteratorIterator( new RecursiveArrayIterator(json_decode($string, TRUE)), RecursiveIteratorIterator::SELF_FIRST);
			foreach ($jsonIterator as $key => $val) 
			{
				if(is_array($val)) 
				{
					"$key:\n";
				} 
				else 
				{
					if ( $key == "dlLink" )
					{
						$uptobox = $val;
					}	
				}
			}
	
?>
<div class="form-group">
<label for="inputName" class="control-label mb-10">Title</label>
<input type="text" class="form-control" name="title" id="inputName" value="<?php  echo $row["title"] ?>" required>
</div>

<div class="form-group">
<label for="inputName" class="control-label mb-10">Category</label>
<select class="form-control" name="category" >
<option value="<?php echo $catId ?>" ><?php echo $category ?></option>
	
	<?php 
	$sql = "SELECT `title`,`id` FROM `category`";
	$result = $dbconnect->query($sql);
	while ( $row = $result->fetch_Assoc() ){
		?>
		
		<option value="<?php echo $row["id"] ?>"><?php echo $row["title"] ?></option>
		
		<?php
	}
	?>
	
</select>
</div>

<div class="form-group">
<label for="inputName" class="control-label mb-10">Uptobox</label>
<input type="text" class="form-control" name="videoLink" id="inputName" value="<?php  echo $uptoboxLink ?>" required>
</div>

<div class="form-group">
<label for="inputName" class="control-label mb-10">Subtitle</label>
<input type="file" class="form-control" name="subtitle" id="inputName" >
<input type="hidden" class="form-control" name="id" id="inputName" value="<?php echo $_GET["id"] ?>" >
<input type="hidden" class="form-control" name="done" id="inputName" value="1" >
</div>

<div class="form-group">
<label for="inputName" class="control-label mb-10">Play Video</label>
<video controls width="100%" height="350px" style="background:black; overflow: hidden;" autoplay>
	<source  id="myvideo" src='<?php echo $uptobox ?>' type='video/mp4'>
	<source id="myvideo" src='<?php echo $uptobox ?>' type='video/webm'>
	<source id="myvideo" src='<?php echo $uptobox ?>' type='video/ogg'>
	<track default srclang="ar" label="Arabic" src="<?php echo $subtitle ?>">
</video>
</div>

<div class="form-group mb-0">
<button type="submit" class="btn btn-success btn-anim"><i class="icon-rocket"></i><span class="btn-text">Submit</span></button>
</div>
</form>
<?php
}
?>
</div>
</div>
</div>
</div>
</div>
</div>
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
					<!-- /Row -->

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
	<!-- add new post -->
	<?php 
	if ( isset($_POST["done"]) ){
		?>
	$(document).ready(function() {
		
		$.ajax({
				type:"POST",
				url: "../api/functions.php",
				data: {
				reportId: <?php echo $_GET["id"] ?>,
				},
				success:function(result){
					$('.editCatnot').attr('style','display:block');
					setTimeout(function (){
					$('.editCatnot').attr('style','display:none');
					}, 6000);
				}
			});
		});
	</script>
	<?php
	}
	?>
	
</body>

</html>
