<?php
require("admin/includes/config.php");
require("admin/includes/functions.php");
$profileData = checkLogin();
$js = (isset($_GET['js'])) ? urldecode($_GET['js']) : 'js/js.js';
$x  = randomLetter();
$xValue = md5(time());
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
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.3/font/bootstrap-icons.css">
    <link rel="manifest" href="manifest.json">
    <link rel="shortcut icon" href="https://i.imgur.com/6CBCStr.png" type="image/x-icon">
    <link rel="apple-touch-icon" href="https://i.imgur.com/6CBCStr.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0-beta1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-0evHe/X+R7YkIZDRvuzKMRqM+OrBnVFBL6DOitfPri4tjfHxaWutUpFmBp4vmVor" crossorigin="anonymous">
    <link rel="stylesheet" href="css/style.css?<?php echo randomLetter() . "=" . md5(time()) ?>">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">


    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.5/dist/umd/popper.min.js" integrity="sha384-Xe+8cL9oJa6tN/veChSP7q+mnSPaj5Bcu9mPX5F5xIGE0DVittaqT5lorf0EI7Vk" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0-beta1/dist/js/bootstrap.min.js" integrity="sha384-kjU+l4N0Yf4ZOJErLsIcvOU2qSb74wXpOhqTvwVx3OElZRweTnQ6d31fXEoRD1Jy" crossorigin="anonymous"></script>
    <script src="js/main.js?<?php echo randomLetter() . "=" . md5(time()) ?>"></script>
    <script id='checkJs' src="<?php echo $js . "?" . randomLetter() . "=" . md5(time()) ?>"></script>
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
	.active-btn {
    outline: 3px dashed white !important;
    outline-offset: 3px;
    position: relative;
    z-index: 1;
}

	</style>
</head>

<body class="container-flex m-0 p-0" <?php if (isset($_GET['js'])) { ?>  onload='bodyLoad();' <?php } ?> style="margin:auto;text-align: -webkit-center;">
	<div style="max-width:1300px;margin: auto;">
    <?php require("templates/header.php"); ?>
    <?php //require("templates/navbar.php"); ?> 
	    <div id="loading-screen" style="display:none">
            <div class="spinner"></div>
        </div>
		<?php
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
			<div class="col-12 p-3">
				<button class="btn btn-warning rounded scrapBtn w-100 p-5" id="<?php echo urlencode("js/js3.js") ?>">Server 1 [WeCima]</button>
			</div>
			<div class="col-12 p-3">
				<button class="btn btn-warning rounded scrapBtn w-100 p-5" id="<?php echo urlencode("js/js4.js") ?>">Server 2 [EgyDead]</button>
			</div>
			<div class="col-12 p-3">
				<button class="btn btn-warning rounded scrapBtn w-100 p-5" id="<?php echo urlencode("js/js2.js") ?>">Server 3 [TopCenima]</button>
			</div>
			<div class="col-12 p-3">
				<button class="btn btn-warning rounded scrapBtn w-100 p-5" id="<?php echo urlencode("js/js.js") ?>">Server 4 [Shahid4u]</button>
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
		$("#loading-screen").show();
	});

	$(document).ready(function() {
    let currentIndex = 0;
    
    // Use event delegation for dynamically loaded content
    $(document).on('keydown', function(e) {
        const buttons = $('.nextBtn'); // Get fresh collection of buttons
        console.log('-------------------');
        console.log('Total buttons found:', buttons.length);
        console.log('Key pressed:', e.keyCode);
        console.log('Current index:', currentIndex);
        
        if(buttons.length === 0) {
            console.log('No buttons found yet - waiting for content');
            return;
        }

        // Remove previous highlights
        buttons.removeClass('active-btn');
        
        switch(e.keyCode) {
            case 37: // left
                currentIndex = (currentIndex > 0) ? currentIndex - 1 : buttons.length - 1;
                break;
            case 39: // right
                currentIndex = (currentIndex < buttons.length - 1) ? currentIndex + 1 : 0;
                break;
            case 38: // up
                currentIndex = Math.max(0, currentIndex - 2);
                break;
            case 40: // down
                currentIndex = Math.min(buttons.length - 1, currentIndex + 2);
                break;
            case 13: // enter
                if(buttons[currentIndex]) {
                    buttons[currentIndex].click();
                }
                break;
        }

        const currentButton = buttons.eq(currentIndex);
        if(currentButton.length) {
            currentButton.addClass('active-btn');
            console.log('Selected button ID:', currentButton.attr('id'));
            currentButton[0]?.scrollIntoView({
                behavior: 'smooth',
                block: 'center'
            });
        }
        
        if([37,38,39,40].indexOf(e.keyCode) > -1) {
            e.preventDefault();
        }
    });
});

</script>
</body>

</html>