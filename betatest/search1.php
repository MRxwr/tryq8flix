<?php
include_once ("includes/config.php");
include_once("includes/checksouthead.php");
?>
<!DOCTYPE html>
<html>
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/css/bootstrap.min.css" integrity="sha384-TX8t27EcRE3e/ihU7zmQxVncDAy5uIKz4rEkgIXeMed4M0jlfIDPvg6uqKI2xXr2" crossorigin="anonymous">

    <title>TRYQ8FLiX</title>
	
	<style>
	body{
		background-color:#181818;
		color:white;
		font-size:22px;
	}
	@media only screen and (max-width: 1280px ) {
		body{
			font-size:18px;
		}
	}
	.img-hovering:hover {
		box-shadow: 0 .5rem 1rem rgba(219,198,150,.3)!important;
		border-radius: .25rem!important;
		background-color: #fff!important;
	}
	.img-fluid{
		height: 400px;
		width: 100%;
	}
	@media only screen and (max-width: 1280px ) {
			.img-fluid{
				height: 250px;
				width: 100%;
			}
		}
	.img-fluid:hover {
		border: 1px;
		border-color: grey;
		border-style: solid;
	}
	.modal-content{
		background-color:#212121;
		color:white;
	}
	.page-link{
	color: #bdbdbd;
    background-color: #2e2e2e;
    border: 1px solid #000000;
	}
	.page-link:hover{
		color: orange;
		background-color: #515151;
		border: 1px solid #000000;
	}
	.page-item.disabled .page-link {
		background-color: #2e2e2e;
		border-color: #00060c;
	}

	</style>
	
	<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ho+j7jyWK8fNQe+A12Hb8AhRq26LrZ/JpcUGGOn+Y7RsweNrtN/tE3MoK7ZeZDyx" crossorigin="anonymous"></script>
	<script src="https://code.jquery.com/jquery-3.2.1.min.js"></script>

	
  </head>

<body>

    <!-- Right Column -->
 <input name="search_text" id="search_text" type="text" placeholder="Type a title or genre or actors name ..." value="" class="form-control" autoFocus="autoFocus" >  
<p></p>
<div id="result"></div>
<div style="clear:both"></div>

<!-- details -->
    <div class="modal fade bd-example-modal-lg" id="TryQ8FliXModal" tabindex="-1" aria-labelledby="myLargeModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable" style="
	  position: fixed;
	  margin: 0;
	  min-width: 100%;
	  min-height: 100%;
	  padding: 0;
	  "
	  >
        <div class="modal-content" 
		style="
		position: fixed;
		top: 0;
		right: 0;
		bottom: 0;
		left: 0;
		overflow: hidden;
		"
		>
          <div class="modal-header">
		  <div class="container-fluid">
			<div class="row p-0">
				<div class="col">
					<h5 class="modal-title" id="exampleModalLabel">Modal title</h5> <span class="rating"></span> | <span class="duration"></span> | <span class="genre"></span> | <span class="year"></span>
					<button type="button" class="close p-0" data-dismiss="modal" aria-label="Close">
					  <span aria-hidden="true" style="color:white">&times;</span>
					</button>
				</div>
			</div>
			<div class="row">
				<div class="col pt-0 mt-0">
				
				</div>
			</div>
		  </div>
            
          </div>
          <div class="modal-body">
			<div class="row mb-3">
				<div class="col-6 col-sm-6 col-md-4">
					<img src="http://www.google.com" class="img-fluid poster rounded" style="/*height:350px*/">
				</div>
				<div class="col-6 col-sm-6 col-md-8">
				<b style="color:orange">Notes:</b> <span class="notes"></span><br>
				<b style="color:yellow">IMDb:</b> <span class="imdb"></span><br>
				<b>Country:</b> <span class="country"></span><br>
				<b>Language:</b> <span class="language"></span><br>
				<b>Cast:</b> <span class="channel"></span><br>
				</div>
			</div>
			<div class="row">
				  <div class="col" style="text-align: justify;">
					<span class="description"></span>
				  </div>
			</div>
          </div>
          <div class="modal-footer">
				<a  class="btn btn-warning flixCate" href="" target="_blank"><b>Start watching<b></a>
				<a  class="btn btn-warning" data-toggle="modal" data-target="#TryQ8FliXModalTrailer" ><b>Trailer<b></a>
          </div>
        </div>
      </div>
    </div>
	
	<!-- Links -->
	<div class="modal fade bd-example-modal-lg" id="TryQ8FliXModalLinks" tabindex="-1" aria-labelledby="myLargeModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable" style="
	  position: fixed;
	  margin: 0;
	  min-width: 100%;
	  min-height: 100%;
	  padding: 0;
	  "
	  >
        <div class="modal-content" 
		style="
		position: fixed;
		top: 0;
		right: 0;
		bottom: 0;
		left: 0;
		overflow: hidden;
		"
		>
          <div class="modal-header p-0 " style="border-bottom:0px">
		  <div class="container-fluid">
			<div class="row p-0">
				<div class="col text-right">
					<button type="button" class="close1 btn p-0" data-dismiss="modal" aria-label="Close">
					  <span aria-hidden="true" style="color:#909090;     font-size: 1.5rem;
    font-weight: 700;
    line-height: 1;
    color: #909090;
    text-shadow: 0 1px 0 #fff;">&times;</span>
					</button>
				</div>
			</div>
			<div class="row">
				<div class="col pt-0 mt-0">
				
				</div>
			</div>
		  </div>
            
          </div>
          <div class="modal-body">
			<div class="row">
				<div class="col">
					<div class="embed-responsive embed-responsive-16by9" style="min-height:95vh">
						<iframe class="embed-responsive-item postLink" src="" allowfullscreen style="" ></iframe>
					</div>
				</div>
			</div>
          </div>
        </div>
      </div>
    </div>
	
	<!-- trailer -->
	<div class="modal fade " id="TryQ8FliXModalTrailer" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
          <div class="modal-body m-0 p-0">
		  <div class="embed-responsive embed-responsive-16by9" style="height:350px;width:100%">
			<iframe class="embed-responsive-item MovieTrailer" style="height:350px;width:100%" src="" allowfullscreen></iframe>
		  </div>
          </div>
        </div>
      </div>
    </div>
	
<script>

$(function(){
		$('.flixMovie1').click(function(e){
			e.preventDefault();
			flixMovieID = $(this).attr('id');
			console.log(flixMovieID);
			$.ajax({
				type:"POST",
				url: "api/functions.php",
				data: {
					bringData: flixMovieID,
				},
				success:function(result){
					var MovieDetails = result.split('^');
					$('.modal-title').html(MovieDetails[0]);
					$('.poster').attr('src',MovieDetails[9]);
					$('.rating').html(MovieDetails[1]);
					$('.imdb').html(MovieDetails[2]);
					$('.year').html(MovieDetails[5]);
					$('.genre').html(MovieDetails[4]);
					$('.country').html(MovieDetails[7]);
					$('.channel').html(MovieDetails[8]);
					$('.notes').html(MovieDetails[13]);
					$('.description').html(MovieDetails[10]);
					$('.duration').html(MovieDetails[3]);
					$('.language').html(MovieDetails[6]);
					$('.flixCate').attr('href','category?id='+MovieDetails[12]);
					$('.MovieTrailer').attr('src',MovieDetails[11]);
				}
			});
		});
	});
	
$(document).ready(function(){
	load_data();
	function load_data(query)
	{
		$.ajax({
			url:"live/fetch.php",
			method:"post",
			data:{query:query},
			success:function(data)
			{
				$('#result').html(data);
			}
		});
	}
	
	$('#search_text').keyup(function(){
		var search = $(this).val();
		if(search != '')
		{
			load_data(search);
		}
		else
		{
			load_data();			
		}
	});
});

$('.close').click(function(e) {
    e.preventDefault();
    $('.MovieTrailer').attr('src', '');
	$('.postLink').attr('src', '');
  });
  
  $('.close1').click(function(e) {
    e.preventDefault();
	$('.postLink').attr('src', '');
  });
  
  $(function(){
		$('.flixCate').click(function(e){
			e.preventDefault();
			catID = $(this).attr('id');
			$('.postLink').attr('src',"showPosts.php?id="+catID);
		});
	});
</script>

</body>
</html>