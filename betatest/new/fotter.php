	<!-- details -->
    <div class="modal fade bd-example-modal-lg" id="TryQ8FliXModal" tabindex="-1" aria-labelledby="myLargeModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content" >
          <div class="modal-header">
		  <div class="container-fluid">
			<div class="row p-0">
				<div class="col">
					<h5 class="modal-title" id="exampleModalLabel"></h5> <span class="rating"></span> | <span class="duration"></span> | <span class="genre"></span> | <span class="year"></span>
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
		  <?php
	if ( !isset($mobileWidth) ){
		?>
		<a  class="btn btn-warning MovieProfile flixCateHref" href="" id="" ><b>Start watching<b></a>
		<?php
	}else{
		?>
		<a  class="btn btn-warning MovieProfile flixCate" data-toggle="modal" data-target="#TryQ8FliXModalLinks" id="" ><b>Start watching<b></a>
		<?php
	}
	?>
				
				<a  class="btn btn-warning" data-toggle="modal" data-target="#TryQ8FliXModalTrailer" ><b>Trailer<b></a>
				
				<a class="btn btn-warning favoCate" id=""><b>Favourite<b></a>
          </div>
        </div>
      </div>
    </div>
	
	
	<!-- Search -->
	<div class="modal fade bd-example-modal-lg" id="TryQ8FliXModalSearch" tabindex="-1" aria-labelledby="myLargeModalLabel" aria-hidden="true">
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
		background-color: #202020;
		"
		>
          <div class="modal-header" style="border-bottom:0px">
		  <div class="container-fluid">
			<div class="row p-0">
				<div class="col " style="text-align: -webkit-right;">
					<button type="button" class="btn btn-secondary  rounded-circle" data-dismiss="modal" aria-label="Close">
					  <span aria-hidden="true" style="color:black"><b>X</b></span>
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
					<div class="embed-responsive embed-responsive-16by9" style="">
						<iframe class="embed-responsive-item searchMovies" src="search1.php" allowfullscreen style=" width:100%;min-height: 87vh;
    background: #00CC33;
    float: none;
    display: inline-block;
    vertical-align: top;" ></iframe>
					</div>
				</div>
			</div>
          </div>
        </div>
      </div>
    </div>
	
	<style>
	.embed-responsive-16by9::before {
    padding-top: 0px;
}
	</style>
	
	<!-- Notifications -->
	<div class="modal fade " id="notifications" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content" style="min-height: 350px;background-color: #202020;">
		<div class="modal-header" style="border-bottom:0px">
		  <div class="container-fluid">
			<div class="row p-0">
				<div class="col">
					<span>Notifications</span>
				</div>
				<div class="col " style="text-align: -webkit-right;">
					<button type="button" class="btn btn-secondary  rounded-circle" data-dismiss="modal" aria-label="Close">
					  <span aria-hidden="true" style="color:black"><b>X</b></span>
					</button>
				</div>
			</div>
		  </div>
        </div>
          <div class="modal-body m-0 p-0">
		  <div class="embed-responsive embed-responsive-16by9">
			<iframe class="embed-responsive-item " src="live/notifications_update.php" allowfullscreen style="position:relative ;min-height: 350px;width: 100%;"></iframe>
		  </div>
          </div>
		  
		  <div class="modal-footer">

          </div>
		  
        </div>
      </div>
    </div>
	
	<script type="text/javascript">
	$(function(){
		$('.searchButton').click(function(e){
			e.preventDefault();
			catID = $(this).attr('id');
			$('.searchMovies').attr('src',"search1.php?id="+catID);
		});
	});
	
	$('.close').click(function(e) {
    e.preventDefault();
    $('.MovieTrailer').attr('src', '');
	$('.postLink').attr('src', '');
  });

	</script>

<div class="pb-5"></div>
	<?php 

if ( !isset($username) AND !in_array($username,$usernames))
{
	?>
<footer style="background-color: #2E2E2E;
    color: white;
    min-height: 50px;
    box-shadow: 0px 0px 5px #4f4f4f;
    min-width: 100%;
	position: sticky;
    bottom: 0;
    left: 0;
    z-index: 1020;">
  
  <div class="row" style="width: 100%; padding-top: 3px;margin-left: 1px!important;">

	  <div class="col-2 col-sm-2 col-md-2 p-1 text-center" >
	  <a href="index.php"><img src="images/home1.png" style="width: 25px;"></a><div style="font-size: 10px">Home</div>
	  </div>
	  
	  <div class="col-2 col-sm-2 col-md-2 p-1 text-center" >
	  <a href="cate?s=0&type=movie"><img src="images/movies.png" style="width: 25px;"></a><div style="font-size: 10px">Movies</div>
	  </div>
	  
	  <div class="col-2 col-sm-2 col-md-2 p-1 text-center">
	  <a href="cate?s=0&type=tv-show"><img src="images/tvshows.png" style="width: 25px;"></a><div style="font-size: 10px">TVShows</div>
	  </div>
	  
	  <div class="col-2 col-sm-2 col-md-2 p-1 text-center">
	  <a href="cate?s=0&type=anime"><img src="images/animes.png" style="width: 25px;"></a><div style="font-size: 10px">Animes</div>
	  </div>
	  
	  <div class="col-2 col-sm-2 col-md-2 p-1 text-center" style="">
	  <a href="cate?s=0&type=wrestling"><img src="images/wrestling.png" style="width: 25px;"></a><div style="font-size: 10px">Wrestling</div>
	  </div>
	  
	  <div class="col-2 col-sm-2 col-md-2 p-1 text-center">
	  <a href="profile.php"><img src="images/profile1.png" style="width: 30px;"></a><div style="font-size: 10px">Profile</div>
	  </div>
	  
	</div>
</footer>

<?php 
}
elseif (isset($username) AND in_array($username,$usernames))
{
	?>
<footer style="background-color: #2E2E2E;
    color: white;
    min-height: 50px;
    box-shadow: 0px 0px 5px #4f4f4f;
    min-width: 100%;
	position: sticky;
    bottom: 0;
    left: 0;
    z-index: 1020;">
  <div class="row">
	  
	  <div class="col-2 col-sm-2 col-md-2 p-1 text-center">
	  <a href="addcategory1.php"><img src="images/imdb21.png" style="width: 45px;"></a><div style="font-size: 10px">Add IMDB</div>
	  </div>
	  
	  <div class="col-2 col-sm-2 col-md-2 p-1 text-center">
	  <a href="addcategory.php"><img src="images/addcat.png" style="width: 25px;"></a><div style="font-size: 10px">Category</div>
	  </div>
	  
	  <div class="col-2 col-sm-2 col-md-2 p-1 text-center">
	  <a href="index.php"><img src="images/home1.png" style="width: 25px;"></a><div style="font-size: 10px">Home</div>
	  </div>
	  
	  <div class="col-2 col-sm-2 col-md-2 p-1 text-center" >
	  <a href="profile.php"><img src="images/profile1.png" style="width: 25px;"></a><div style="font-size: 10px">Profile</div>
	  </div>
	  
	  <div class="col-2 col-sm-2 col-md-2 p-1 text-center">
	  <a href="https://tryq8flix.com/getlinks.php"><img src="https://i.imgur.com/3qawygU.png" style="width: 25px;"></a><div style="font-size: 10px">Grabber</div>
	  </div>
	  
	  <div class="col-2 col-sm-2 col-md-2 p-1 text-center" >
	  <a href="logout.php"><img src="images/logout1.png" style="width: 25px;"></a><div style="font-size: 10px">Exit</div>
	  </div>
	  
	</div>
</footer>

<?php
}
?>

</div>

<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.7/dist/umd/popper.min.js" integrity="sha384-zYPOMqeu1DAVkHiLqWBUTcbYfZ8osu1Nd6Z89ify25QV9guujx43ITvfi12/QExE" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.min.js" integrity="sha384-Y4oOpwW3duJdCWv5ly8SCFYWqFDsfob/3GkgExXKV4idmbt98QcxXYs9UoXAB7BZ" crossorigin="anonymous"></script>

<script>
$(function(){
	$('.headerPoster').click(function(e){
		e.preventDefault();
		catID = $(this).attr('id');	
		$.ajax({
			type:"POST",
			url: "api/header.php",
			data: {
				headerData: catID,
			},
			success:function(result){
				console.log(result);
				var MovieDetails = result.split('^');
				$('.movieTitle').html(MovieDetails[0]);
				$('.header01').attr('style','height: 480px;background: linear-gradient(transparent 40%, #151515), url('+MovieDetails[15]+') no-repeat center; background-size: 100% 100%;overflow:hidden;');
				$('.year').html(MovieDetails[5]);
				$('.description').html(MovieDetails[10]);
				$('.seasons').html(MovieDetails[14]);
				$('.watchVideo').attr('href',MovieDetails[11]);
				$('.rating').html("IMDb: " + MovieDetails[2]);
				$('.visitCategory').attr('href','category.php?id='+MovieDetails[12]);
			}
		});
	});
});
</script>

  </body>
</html>
