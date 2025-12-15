<?php
include_once ("../includes/config.php");
include_once("../includes/checksouthead.php");

$output = '';
if(isset($_POST["query"]))
{
	$input = trim(preg_replace('/\s+/', ' ', str_replace("."," ",$_POST["query"])));
	$search = mysqli_real_escape_string($dbconnect, $input);
	$query = "
	SELECT * FROM category 
	WHERE
	`title` LIKE '%".$search."%'
	OR
	`notes` LIKE '%".$search."%'
	ORDER BY id DESC
	LIMIT 100
	";
}
else
{
	exit;
}
$result = mysqli_query($dbconnect, $query);
if(mysqli_num_rows($result) > 0)
{
	while($row = mysqli_fetch_array($result))
	{
		if ($row["type"] == "AniMov" )
		{ $row["type"] = "Anime Movie";}
		elseif ( $row["type"] == "Anime" )
		{ $row["type"] = "Anime Series";}
		$output .= '<a target="" href="category.php?id='.$row["id"].'">
		<div class="w3-quarterindex" style="padding: 3px;  position: relative;text-align: center;color: white;">
		<img src="'.$row["poster"].'" alt="" id="imageindex">
		<div style="position: absolute;bottom: 1.5%;right: 1.5%;left: 1%;background: rgba(0, 0, 0, .8);">
		<b style="color:yellow">'.$row["type"].'</b>
		</div>
		</div>
		</a>
		';
	}
	echo $output;
}
else
{
	echo 'Data Not Found';
}
?>