<!DOCTYPE html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8" />
    <meta
      name="viewport"
      content="width=device-width, initial-scale=1, shrink-to-fit=no"
    />

    <!-- Bootstrap CSS -->
    <link
      rel="stylesheet"
      href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/css/bootstrap.min.css"
      integrity="sha384-TX8t27EcRE3e/ihU7zmQxVncDAy5uIKz4rEkgIXeMed4M0jlfIDPvg6uqKI2xXr2"
      crossorigin="anonymous"
    />

    <title>:: TRYQ8FLiX ::</title>

    <style>
	body{
		background-color: #1a1a1a;
		max-width: 1280px;
		margin: auto;
	}
      .div-container {
        position: relative;
        text-align: center;
        color: white;
      }
      .img-top-right {
        position: absolute;
        top: 8px;
        right: 16px;
        width: 30%;
      }
      .shadow-right {
        position: absolute;
        bottom: 0px;
        right: 16px;
        min-height: 95%;
        background-color: black;
        opacity: 0.7;
        width: 30%;
      }
      .header-img {
        width: 100%;
        height: 500px;
      }
	  .featured-img{
		  transition: box-shadow .3s;
		  width:100%;
		  height: 200px;
		  border-radius: 10px;
		  float: left;
	  }
	  .featured-img:hover {
		  box-shadow: 0 0 11px rgba(250,227,91,.5); 
	  }
    </style>
  </head>
  <body>
    <?php
	
	//main banner
	require ('templates/mainHeader.php');
	
	//featured
	require ('templates/featured.php');
	
	?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/js/bootstrap.bundle.min.js" integrity="sha384-ygbV9kiqUc6oa4msXn9868pTtWMgiQaeYH7/t7LECLbyPA2x65Kgf80OJFdroafW" crossorigin="anonymous"></script>
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
  </body>
</html>
