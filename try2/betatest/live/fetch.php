<?php
include_once ("../includes/config.php");
include_once("../includes/checksouthead.php");
?>
    
<!-- Required meta tags -->
    
	
<?php
$output = '';
if(isset($_POST["query"]))
{
	$input = trim(preg_replace('/\s+/', ' ', $_POST["query"]));
	$search = mysqli_real_escape_string($dbconnect, $input);
	$query = "
	SELECT *
	FROM `category` 
	WHERE
	`title` LIKE '%".$search."%'
	OR
	`notes` LIKE '%".$search."%'
	ORDER BY `id` DESC
	limit 100
	";
}
else
{
	exit;
}
$result = mysqli_query($dbconnect, $query);
if(mysqli_num_rows($result) > 0)
{
	$output .= '<div class="container-fluid d-flex m-0 p-0">
      <div class="row justify-content-center w-100 m-0 mt-1">';
	while($row = mysqli_fetch_array($result))
	{
		if ($row["type"] == "AniMov" )
		{ $row["type"] = "Anime Movie";}
		elseif ( $row["type"] == "Anime" )
		{ $row["type"] = "Anime Series";}
		$output .= '<div class="col-6 col-sm-4 col-md-3 col-lg-2 text-center m-0 p-2">
          <a data-toggle="modal" data-target="#TryQ8FliXModal" class="flixMovie1" id="';
		  if ( isset($row["catid"]) ){
			  $output .= $row["catid"];
		  }else{
			  $output .= $row["id"];
		  }
		  $output .= '"><img src="' . $row["poster"]. '" class="img-fluid img-hovering rounded"></a>
		  ' . $row["title"]. '</div>';
	}
	$output .= '</div>
	</div>';
	
	$output .= "<script>

$(function(){
		$('.flixMovie1').click(function(e){
			e.preventDefault();
			flixMovieID = $(this).attr('id');
			console.log(flixMovieID);
			$.ajax({
				type:'POST',
				url: 'api/functions.php',
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
	});</script>";
	
	echo $output;
}
else
{
	echo 'Data Not Found';
}
?>