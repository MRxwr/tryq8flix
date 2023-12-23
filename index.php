<?php
require("admin/includes/config.php");
require("admin/includes/functions.php");
$profileData = checkLogin();
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

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.5/dist/umd/popper.min.js" integrity="sha384-Xe+8cL9oJa6tN/veChSP7q+mnSPaj5Bcu9mPX5F5xIGE0DVittaqT5lorf0EI7Vk" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0-beta1/dist/js/bootstrap.min.js" integrity="sha384-kjU+l4N0Yf4ZOJErLsIcvOU2qSb74wXpOhqTvwVx3OElZRweTnQ6d31fXEoRD1Jy" crossorigin="anonymous"></script>
    <script src="js/main.js?y=<?php echo md5(time()) ?>"></script>
    <script src="js/js.js?y=<?php echo md5(time()) ?>"></script>
</head>

<body class="container-flex m-0 p-0" onload="bodyLoad();">

    <?php require("templates/header.php"); ?>
    <?php //require("templates/navbar.php"); ?>
	<div id="loading-screen">
	  <div class="spinner"></div>
	</div>
    <div id="mainBody">
        <?php require("templates/content.php"); ?>
    </div>
    <?php require("templates/footer.php"); ?>

    <?php require("templates/modals.php"); ?>

    <!-- calling js files -->
    
    

</body>

</html>