<?php
require("admin/includes/config.php");
require("admin/includes/functions.php");
$profileData = checkLogin();
$js = (isset($_GET['js'])) ? urldecode($_GET['js']) : 'js/js3.js?y=' . md5(time());
?>
<!doctype html>
<html lang="en" style="direction:rtl">

<head>

    <title>TRYQ8FLiX 2.0</title>

    <meta charset="utf-8">
    <meta name="theme-color" content="black" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="origin" name="referrer">
    <meta name="description" content="Put your description here.">
    <meta http-equiv="Cache-control" content="public">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.3/font/bootstrap-icons.css">
    <link rel="manifest" href="manifest.json">
    <link rel="shortcut icon" href="https://i.imgur.com/6CBCStr.png" type="image/x-icon">
    <link rel="apple-touch-icon" href="https://i.imgur.com/6CBCStr.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0-beta1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-0evHe/X+R7YkIZDRvuzKMRqM+OrBnVFBL6DOitfPri4tjfHxaWutUpFmBp4vmVor" crossorigin="anonymous">
    <link rel="stylesheet" href="css/style.css?y=<?php echo md5(time()) ?>">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">


    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.5/dist/umd/popper.min.js" integrity="sha384-Xe+8cL9oJa6tN/veChSP7q+mnSPaj5Bcu9mPX5F5xIGE0DVittaqT5lorf0EI7Vk" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0-beta1/dist/js/bootstrap.min.js" integrity="sha384-kjU+l4N0Yf4ZOJErLsIcvOU2qSb74wXpOhqTvwVx3OElZRweTnQ6d31fXEoRD1Jy" crossorigin="anonymous"></script>
    <script src="js/main.js?y=<?php echo md5(time()) ?>"></script>
    <script id='checkJs' src="<?php echo $js . "?y=" . md5(time()) ?>"></script>
	<style>
	body{
		background-color: #101010;
	}
	.card{
		background-color: #211f20 ;
		color: white;
	}
	header{
		background-color: #211f20 !important;
		border: 1px solid black !important;
		color: black;
	}
	.modal-content{
		background-color: #211f20 ;
		color: white;
	}
	</style>
</head>

<body class="container-flex m-0 p-0" style="margin:auto;text-align: -webkit-center;">
	<div style="max-width:1300px;margin: auto;">
    <?php require("templates/header.php"); ?>
    <?php //require("templates/navbar.php"); ?> 
	    <div id="loading-screen">
            <div class="spinner"></div>
        </div>
		<?php
		//onload="bodyLoad();"
		echo "<div class='row m-0 p-0 w-100'> 
				<div class='col p-1'>
				<button id='{$website}' class='btn btn-danger changeIframeSrc w-100'>S4U</button>
				</div>
				<div class='col p-1'>
				<button id='https://www.tuktukcima.com/' class='btn btn-dark changeIframeSrc w-100'>Tuk</button>
				</div>
				<div class='col p-1'>
				<button id='https://e.3sk.media/' class='btn btn-danger changeIframeSrc w-100'>3SQ</button>
				</div>
				<div class='col p-1'>
				<button id='https://bollyrulez.info/' class='btn btn-dark changeIframeSrc w-100'>WWE</button>
				</div>
				<div class='col p-1'>
				<button id='https://anime4up.ch/' class='btn btn-danger changeIframeSrc w-100'>Anime</button>
				</div>
			  </div>
			";
		?> 
    <div id="mainBody">
		<div class="row m-0 p-o w-100">
			<div class="col-md-4">
				<button class="btn btn-warning rounded scrapBtn" id="<?php echo urlencode("js/js.js") ?>">Server 1</button>
			</div>
			<div class="col-md-4">
				<button class="btn btn-warning rounded scrapBtn" id="<?php echo urlencode("js/js2.js") ?>">Server 2</button>
			</div>
			<div class="col-md-4">
				<button class="btn btn-warning rounded scrapBtn" id="<?php echo urlencode("js/js3.js") ?>">Server 3</button>
			</div>
		</div>
        <?php require("templates/content.php"); ?>
    </div>
    <?php //require("templates/footer.php"); ?>

    <?php require("templates/modals.php"); ?>

    <!-- calling js files -->
    </div>
    
<script>
	// use jquery to listen to a click event and then change the #checkJs src depnding on btn id
	$(document).on('click', '.scrapBtn', function() {
		var btnId = $(this).attr('id');
		$('#checkJs').attr('src', btnId);
		// refresh page and send $_GET["js"] = btnId
		window.location.href = "?js=" + btnId;
		
	});
</script>
</body>

</html>