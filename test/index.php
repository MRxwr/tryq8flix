<?php
require('includes/config.php');
require('includes/checkLogin.php');
require('includes/functions.php');
?>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />

    <!-- Material Icons -->

    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet" />
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">

    <!-- CSS File -->
    <link rel="stylesheet" href="css/styles.css" />
    <title>TRYQ8FiLX - Just Click Play</title>
  </head>
  <body>
    <!-- Header Starts -->
    <?php require('templates/header.php'); ?>
    <!-- Main Body Starts -->
    <div class="mainBody">
      <!-- Sidebar Starts -->
      <?php require('templates/sidebar.php'); ?>
      <!-- Sidebar Ends -->
      <!-- Videos Section -->
      <div class="videos">
        <?php
		if( isset($_GET["video"]) && !empty($_GET["video"]) ){
			require('templates/player.php');
		}else{
			require('templates/videos.php');
		}
		
		?>
      </div>
    </div>
    <script src="javascript/script.js"></script>
    <!-- Main Body Ends -->
  </body>
</html>