<?php
include_once ("includes/config.php");
include_once ("includes/checksouthead.php");

//number of posts per page
$PostsPerPage = 24;

//getting languages for dropdown menue
$sql = "SELECT language FROM category WHERE type like '%tv-show%'";
$result = $dbconnect->query($sql);
$NumberOfPosts = $result->num_rows;
$language = array();
$alllanguages = array();
$y = 0;
while ( $row = $result->fetch_assoc() )
{
		$language = explode(",",$row["language"]);
		$numberoflanguages = sizeof($language);

		while ( $y < $numberoflanguages )
		{
			$language[$y] = strtolower(trim($language[$y]));
			if ( !in_array( $language[$y] , $alllanguages) )
			{
				$newlanguage = sizeof($alllanguages) + 1;
				$alllanguages[$newlanguage] = $language[$y];;
			}
			$y = $y + 1;
		}
		$y = 0;
}
sort($alllanguages);
$alllanguages = array_map('ucfirst', $alllanguages);
$alllanguages = array_filter($alllanguages);

//getting genres for dropdown menue
$sql = "SELECT genre FROM category WHERE type like '%tv-show%'";
$result = $dbconnect->query($sql);
$NumberOfPosts = $result->num_rows;
$genre = array();
$allgenres = array();
$i = 0;
$y = 0;
while ( $row = $result->fetch_assoc() )
{
		$genre = explode(",",$row["genre"]);
		$numberofgenres = sizeof($genre);

		while ( $y < $numberofgenres )
		{
			$genre[$y] = strtolower(str_replace(' ', '', $genre[$y]));
			if ( !in_array( $genre[$y] , $allgenres) )
			{
				$newgenre = sizeof($allgenres) + 1;
				$allgenres[$newgenre] = $genre[$y];;
			}
			$y = $y + 1;
		}
		$y = 0;
}
sort($allgenres);
$allgenres = array_map('ucfirst', $allgenres);
$allgenres = array_filter($allgenres);

//couting movies by language
if ( isset( $_GET["sfc"] ) AND !isset( $_GET["sfg"] ) )
{
	$lang = $_GET["sfc"];
	$searchfilter = $_GET["sfc"];
	$sql = "SELECT * FROM category WHERE type LIKE '%tv-show%' AND language LIKE '%$lang%'";
	$result = $dbconnect->query($sql);
	$NumberOfPosts = $result->num_rows;
	$NumberOfPages = ceil($NumberOfPosts / $PostsPerPage);
}
//couting movies by genre
elseif ( !isset($_GET["sfc"]) AND isset($_GET["sfg"]) )
{
	$gen = $_GET["sfg"];
	$searchfilter = $_GET["sfg"];
	$sql = "SELECT * FROM category WHERE type LIKE '%tv-show%' AND genre LIKE '%$gen%'";
	$result = $dbconnect->query($sql);
	$NumberOfPosts = $result->num_rows;
	$NumberOfPages = ceil($NumberOfPosts / $PostsPerPage);
}
elseif( isset($_GET["sfc"]) AND isset($_GET["sfg"]))
{
	$gen = $_GET["sfg"];
	$searchfilter = $_GET["sfg"];
	$lang = $_GET["sfc"];
	$searchfilter = $_GET["sfc"];
	$sql = "SELECT * FROM category WHERE type LIKE '%tv-show%' AND language LIKE '%$lang%' AND genre LIKE '%$gen%' ";
	$result = $dbconnect->query($sql);
	$NumberOfPosts = $result->num_rows;
	$NumberOfPages = ceil($NumberOfPosts / $PostsPerPage);
}
//total number of movies
else
{
	if ( !isset($_GET["sfc"]) )
	{
		$sql = "SELECT * FROM category WHERE type LIKE '%tv-show%'";
		$result = $dbconnect->query($sql);
		$NumberOfPosts = $result->num_rows;
		$NumberOfPages = ceil($NumberOfPosts / $PostsPerPage);
	}
	else
	{
		$lang = $_GET["sfc"];
		$sql = "SELECT * FROM category 
				WHERE type LIKE '%tv-show%'
				AND language LIKE '%$lang%'";
		$result = $dbconnect->query($sql);
		$NumberOfPosts = $result->num_rows;
		$NumberOfPages = ceil($NumberOfPosts / $PostsPerPage);
	}
}
//pagination
if ( !isset ( $_GET["page"] ) )
{
	$page = 1;
	$ppage = 1;
	$currentPage = 0;
}
else
{
	$page = $_GET["page"]+1;
	$ppage = $_GET["page"]-1;
	$currentPage = ($page * $PostsPerPage)-($PostsPerPage);
}


?>
<!DOCTYPE html>
<html>
<title>Q8Flix - TV-Shows</title>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
<link rel="icon" href="images/logo.png">
<link rel="stylesheet" href="css/style2.css?x=dasd">
<link rel='stylesheet' href='https://fonts.googleapis.com/css?family=Roboto'>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
<style>
html,body,h1,h2,h3,h4,h5,h6 
{
    font-family: "Roboto", sans-serif;
}
.boxsizingBorder 
{
    -webkit-box-sizing: border-box;
       -moz-box-sizing: border-box;
            box-sizing: border-box;
}
.myButton 
{
	-moz-box-shadow:inset 0px 1px 0px 0px #ffffff;
	-webkit-box-shadow:inset 0px 1px 0px 0px #ffffff;
	box-shadow:inset 0px 1px 0px 0px #ffffff;
	background:-webkit-gradient(linear, left top, left bottom, color-stop(0.05, #ededed), color-stop(1, #dfdfdf));
	background:-moz-linear-gradient(top, #ededed 5%, #dfdfdf 100%);
	background:-webkit-linear-gradient(top, #ededed 5%, #dfdfdf 100%);
	background:-o-linear-gradient(top, #ededed 5%, #dfdfdf 100%);
	background:-ms-linear-gradient(top, #ededed 5%, #dfdfdf 100%);
	background:linear-gradient(to bottom, #ededed 5%, #dfdfdf 100%);
	filter:progid:DXImageTransform.Microsoft.gradient(startColorstr='#ededed', endColorstr='#dfdfdf',GradientType=0);
	background-color:#ededed;
	-moz-border-radius:6px;
	-webkit-border-radius:6px;
	border-radius:6px;
	border:1px solid #dcdcdc;
	display:inline-block;
	cursor:pointer;
	color:#777777;
	font-family:Arial;
	font-size:15px;
	font-weight:bold;
	padding:6px 24px;
	text-decoration:none;
	text-shadow:0px 1px 0px #ffffff;
	width: 100%;
}
.myButton:hover 
{
	background:-webkit-gradient(linear, left top, left bottom, color-stop(0.05, #dfdfdf), color-stop(1, #ededed));
	background:-moz-linear-gradient(top, #dfdfdf 5%, #ededed 100%);
	background:-webkit-linear-gradient(top, #dfdfdf 5%, #ededed 100%);
	background:-o-linear-gradient(top, #dfdfdf 5%, #ededed 100%);
	background:-ms-linear-gradient(top, #dfdfdf 5%, #ededed 100%);
	background:linear-gradient(to bottom, #dfdfdf 5%, #ededed 100%);
	filter:progid:DXImageTransform.Microsoft.gradient(startColorstr='#dfdfdf', endColorstr='#ededed',GradientType=0);
	background-color:#dfdfdf;
}
.myButton:active 
{
	position:relative;
	top:1px;
}
.tags 
{
  display: inline;
  position: relative;
}
.tags:hover:after 
{
  background: #333;
  background: rgba(0, 0, 0, 0.8);
  border-radius: 5px;
  bottom: 125%;
  color: yellow;
  content: attr(glose);
  font-size: 18px;
  left: 0;
  padding: 5px 5px;
  position: absolute;
  z-index: 98;
  width: 100%;
}	
</style>

<body>
<!-- Page Container -->
	
<div class="w3-content" style="max-width:1300px;">
<?php
include_once ("template/header.php");
?>
<!-- The Grid -->
<div class="">
	  

	  
<!-- Right Column -->
<div class="w3-text-white" style="padding-top: 40px" >
<div class="w3-row-padding w3-padding-16 w3-center">

	<table style="width: 100%">
	<?php
	if ( isset($_GET["sfm"]) OR isset($_GET["sfg"]) OR isset($_GET["sfc"]))
	{
		?>
		<tr>
		<td colspan="2">
		<a href="tvshow.php"><div class="myButtonred w3-center" style="width:250px">Cancel Filters</div></a>
		</td>
		</tr>
		<?php
	}
	?>
	<tr>
		<td style="width: 50%">
		<select class="select-css" style="width: 100%" onchange="location = this.options[this.selectedIndex].value;">
        <option value="" >Sort By:</option>	
		<option value="tvshow.php?sfm=mwatched<?php
		if ( isset($lang) )
		{
			echo "&sfc=".$lang;
		}
		?>" >Most Watched</option>
        <option value="tvshow.php?sfm=imdb<?php
		if ( isset($lang) )
		{
			echo "&sfc=".$lang;
		}
		?>" >IMDb Rating</option>
		<option value="tvshow.php?sfm=year<?php
		if ( isset($lang) )
		{
			echo "&sfc=".$lang;
		}
		?>" >Release Year</option>
		<option value="tvshow.php?sfm=name<?php
		if ( isset($lang) )
		{
			echo "&sfc=".$lang;
		}
		?>" >Alphabetical order</option>
		<?php
			$i = 1;
			while ( $i < $newgenre )
			{
        ?>
		<option value="tvshow.php?sfg=<?php 
		echo $allgenres[$i];
		if (isset($lang))
		{
			echo "&sfc=".$lang;
		}
		?>" ><?php echo $allgenres[$i] ?></option>
		<?php
				$i = $i + 1;
			}
		?>
	</select>
		</td>
		<td style="width: 50%">
		<select class="select-css" style="width: 100%" onchange="location = this.options[this.selectedIndex].value;">
		<option value="" >Filter By:</option>	
		<?php
			$i = 1;
			while ( $i < $newlanguage )
			{
		?>
		<option value="tvshow.php?sfc=<?php 
		echo $alllanguages[$i];
		if (isset($gen))
		{
			echo "&sfg=".$gen;
		}elseif(isset($_GET["sfm"]))
		{
			echo "&sfm=".$_GET["sfm"];
		}
		?>" ><?php echo $alllanguages[$i]. " Movies" ?></option>
		<?php
				$i = $i + 1;
			}
		?>
	</select>
		</td>
	</tr>
	</table>
	
<?php
	
if ( isset($_GET["sfc"]) AND in_array($lang,$alllanguages) AND !isset($_GET["sfg"]) AND !isset($_GET["sfm"]))
{
	$sql = "
	SELECT DISTINCT c.id, c.poster, c.title, c.imdbrating, c.releasedate 
	FROM posts AS p
	JOIN category AS c 
	ON c.id = p.catid 
	WHERE c.language LIKE '%$lang%' AND c.type LIKE '%tv-show%' 
	ORDER BY p.id DESC
	LIMIT $currentPage,$PostsPerPage
	";
	$result = $dbconnect->query($sql);
	$lastid = 0;
	while ( $row = $result->fetch_assoc() )
	{
		$glose = $row["title"] . " (" . $row["releasedate"] .")";
    ?>
    <a target="" class="tags" glose="<?php echo $glose; ?>" href="category.php?id=<?php echo $row["id"] ?>">
    <div class="w3-quarterindex" style="padding: 3px;  position: relative;text-align: center;color: white;">
    <img src="<?php echo $row["poster"] ?>" alt="" id="imageindex">
    <div style="position: absolute;bottom: 1.5%;right: 1.5%;left: 1%;background: rgba(0, 0, 0, .8);">
    <b id="fontindex"><b style="color: yellow">IMDb: </b><?php echo $row["imdbrating"] ?></b>
    </div>
    </a>
    </div>
	<?php		 
	}
}

elseif ( isset($_GET["sfg"]) AND in_array($gen,$allgenres) AND !isset($_GET["sfc"]))
{
	$sql = "
			SELECT p.title, p.catid, p.poster, p.category
			FROM posts AS p
			WHERE
			p.type LIKE '%tv-show%' 
			AND
			p.id IN 
			(
				SELECT MAX(pp.id) 
				FROM posts AS pp
				GROUP BY pp.category
			)
			AND
			p.catid IN
			(
				SELECT `id` FROM `category` WHERE `genre` LIKE '%$gen%'
			)
			ORDER BY p.id DESC
			LIMIT $currentPage,$PostsPerPage
			";
	/*$sql = "
	SELECT DISTINCT c.id, c.poster, c.title, c.imdbrating, c.releasedate, p.title as realTitle
	FROM category AS c
	JOIN posts AS p 
	ON c.id = p.catid 
	WHERE c.genre LIKE '%$gen%' AND c.type LIKE '%tv-show%' 
	GROUP BY p.catid
	ORDER BY p.id DESC 
	LIMIT $currentPage,$PostsPerPage
	";*/
	$result = $dbconnect->query($sql);
	$lastid = 0;
	while ( $row = $result->fetch_assoc() )
	{
		$glose = $row["category"] ;//. " (" . $row["releasedate"] .")";
    ?>
    <a target="" class="tags" glose="<?php echo $glose; ?>" href="category.php?id=<?php echo $row["catid"] ?>">
    <div class="w3-quarterindex" style="padding: 3px;  position: relative;text-align: center;color: white;">
    <img src="<?php echo $row["poster"] ?>" alt="" id="imageindex">
    <div style="position: absolute;bottom: 1.5%;right: 1.5%;left: 1%;background: rgba(0, 0, 0, .8);">
    <b id="fontindex"><b style="color: yellow"></b><?php echo $row["title"] ?></b>
    </div>
    </a>
    </div>
	<?php		 
	}
}
elseif (isset($_GET["sfc"]) AND in_array($lang,$alllanguages) AND isset($_GET["sfg"]) AND in_array($gen,$allgenres))
{
	$sql = "
	SELECT DISTINCT c.id, c.poster, c.title, c.imdbrating, c.releasedate 
	FROM category AS c
	JOIN posts AS p 
	ON c.id = p.catid 
	WHERE c.genre LIKE '%$gen%' AND c.type LIKE '%tv-show%' AND c.language LIKE '%$lang%'
	ORDER BY p.id DESC 
	LIMIT $currentPage,$PostsPerPage
	";
	$result = $dbconnect->query($sql);
	$lastid = 0;
	while ( $row = $result->fetch_assoc() )
	{
		$glose = $row["title"] . " (" . $row["releasedate"] .")";
    ?>
    <a target="" class="tags" glose="<?php echo $glose; ?>" href="category.php?id=<?php echo $row["id"] ?>">
    <div class="w3-quarterindex" style="padding: 3px;  position: relative;text-align: center;color: white;">
    <img src="<?php echo $row["poster"] ?>" alt="" id="imageindex">
    <div style="position: absolute;bottom: 1.5%;right: 1.5%;left: 1%;background: rgba(0, 0, 0, .8);">
    <b id="fontindex"><b style="color: yellow">IMDb: </b><?php echo $row["imdbrating"] ?></b>
    </div>
    </a>
    </div>
	<?php		 
	}
}
elseif( isset ( $_GET["sfm"] ) AND $_GET["sfm"] == "name" AND isset($lang)) 
{
	$sfm = $_GET["sfm"];
	$searchfilter = $sfm;
	$sql = "
	SELECT DISTINCT c.id, c.poster, c.title, c.imdbrating, c.releasedate
	FROM category AS c 
	WHERE c.type LIKE '%tv-show%'
	AND c.language LIKE '%$lang%'
	ORDER BY c.title ASC 
	LIMIT $currentPage,$PostsPerPage
	";
	$result = $dbconnect->query($sql);
	$lastid = 0;
	while ( $row = $result->fetch_assoc() )
	{
		$glose = $row["title"] . " (" . $row["releasedate"] .")";
    ?>
    <a target="" class="tags" glose="<?php echo $glose; ?>" href="category.php?id=<?php echo $row["id"] ?>">
    <div class="w3-quarterindex" style="padding: 3px;  position: relative;text-align: center;color: white;">
    <img src="<?php echo $row["poster"] ?>" alt="" id="imageindex">
    <div style="position: absolute;bottom: 1.5%;right: 1.5%;left: 1%;background: rgba(0, 0, 0, .8);">
    <b id="fontindex"><b style="color: yellow">IMDb: </b><?php echo $row["imdbrating"] ?></b>
    </div>
    </a>
    </div>
	<?php		 
	}
}
elseif( isset ( $_GET["sfm"] ) AND $_GET["sfm"] == "name" AND !isset($lang)) 
{
	$sfm = $_GET["sfm"];
	$searchfilter = $sfm;
	$sql = "
	SELECT DISTINCT c.id, c.poster, c.title, c.imdbrating, c.releasedate
	FROM category AS c 
	WHERE c.type LIKE '%tv-show%'
	ORDER BY c.title ASC 
	LIMIT $currentPage,$PostsPerPage
	";
	$result = $dbconnect->query($sql);
	$lastid = 0;
	while ( $row = $result->fetch_assoc() )
	{
		$glose = $row["title"] . " (" . $row["releasedate"] .")";
    ?>
    <a target="" class="tags" glose="<?php echo $glose; ?>" href="category.php?id=<?php echo $row["id"] ?>">
    <div class="w3-quarterindex" style="padding: 3px;  position: relative;text-align: center;color: white;">
    <img src="<?php echo $row["poster"] ?>" alt="" id="imageindex">
    <div style="position: absolute;bottom: 1.5%;right: 1.5%;left: 1%;background: rgba(0, 0, 0, .8);">
    <b id="fontindex"><b style="color: yellow">IMDb: </b><?php echo $row["imdbrating"] ?></b>
    </div>
    </a>
    </div>
	<?php		 
	}
}
elseif( isset ( $_GET["sfm"]) AND $_GET["sfm"] == "mwatched" AND isset($lang)) 
{
	$sfm = $_GET["sfm"];
	$searchfilter = $sfm;
	$sql = "SELECT * , SUM(views) as tview
			FROM `posts` 
			WHERE 
			`catid` IN ( SELECT `id` FROM `category` WHERE `type` LIKE '%tv-show%') 
			AND
			`catid` IN ( SELECT `id` FROM `category` WHERE `language` LIKE '%$lang%') 
			GROUP BY 
			`catid`
			ORDER BY 
			CAST( SUM(views) as int ) DESC
			LIMIT 
			$currentPage,$PostsPerPage
			";
	$result = $dbconnect->query($sql);
	$lastid = 0;
	while ( $row = $result->fetch_assoc() )
	{
		$glose = $row["category"];
    ?>
    <a target="" class="tags" glose="<?php echo $glose; ?>" href="category.php?id=<?php echo $row["catid"] ?>">
    <div class="w3-quarterindex" style="padding: 3px;  position: relative;text-align: center;color: white;">
    <img src="<?php echo $row["poster"] ?>" alt="" id="imageindex">
    <div style="position: absolute;bottom: 1.5%;right: 1.5%;left: 1%;background: rgba(0, 0, 0, .8);">
    <b id="fontindex"><?php echo $row["tview"] ?><b style="color: yellow"> Views</b></b>
    </div>
    </a>
    </div>
	<?php		 
	}
}
elseif( isset ( $_GET["sfm"]) AND $_GET["sfm"] == "mwatched" AND !isset($lang) ) 
{
	$sfm = $_GET["sfm"];
	$searchfilter = $sfm;
	$sql = "SELECT * , SUM(views) as tview
			FROM `posts`
			WHERE 
			`catid` IN ( SELECT `id` FROM `category` WHERE `type` LIKE '%tv-show%') 
			GROUP BY 
			`catid`
			ORDER BY 
			CAST( SUM(views) as int ) DESC
			LIMIT 
			$currentPage,$PostsPerPage
			";
	$result = $dbconnect->query($sql);
	$lastid = 0;
	while ( $row = $result->fetch_assoc() )
	{
		$glose = $row["category"] ;
    ?>
    <a target="" class="tags" glose="<?php echo $glose; ?>" href="category.php?id=<?php echo $row["catid"] ?>">
    <div class="w3-quarterindex" style="padding: 3px;  position: relative;text-align: center;color: white;">
    <img src="<?php echo $row["poster"] ?>" alt="" id="imageindex">
    <div style="position: absolute;bottom: 1.5%;right: 1.5%;left: 1%;background: rgba(0, 0, 0, .8);">
    <b id="fontindex"><?php echo $row["tview"] ?><b style="color: yellow"> Views</b></b>
    </div>
    </a>
    </div>
	<?php		 
	}
}
elseif( isset ( $_GET["sfm"])  AND $_GET["sfm"] == "year" AND isset($lang)) 
{
	$sfm = $_GET["sfm"];
	$searchfilter = $sfm;
	$sql = "
	SELECT DISTINCT c.id, c.poster, c.title, c.imdbrating, c.releasedate
	FROM category AS c 
	WHERE c.type LIKE '%tv-show%'
	AND c.language LIKE '%$lang%'
	ORDER BY c.releasedate DESC
	LIMIT $currentPage,$PostsPerPage
	";
	$result = $dbconnect->query($sql);
	$lastid = 0;
	while ( $row = $result->fetch_assoc() )
	{
		$glose = $row["title"] . " (" . $row["releasedate"] .")";
    ?>
    <a target="" class="tags" glose="<?php echo $glose; ?>" href="category.php?id=<?php echo $row["id"] ?>">
    <div class="w3-quarterindex" style="padding: 3px;  position: relative;text-align: center;color: white;">
    <img src="<?php echo $row["poster"] ?>" alt="" id="imageindex">
    <div style="position: absolute;bottom: 1.5%;right: 1.5%;left: 1%;background: rgba(0, 0, 0, .8);">
    <b id="fontindex"><b style="color: yellow">IMDb: </b><?php echo $row["imdbrating"] ?></b>
    </div>
    </a>
    </div>
	<?php		 
	}
}
elseif( isset ( $_GET["sfm"])  AND $_GET["sfm"] == "year" AND !isset($lang)) 
{
	$sfm = $_GET["sfm"];
	$searchfilter = $sfm;
	$sql = "
	SELECT DISTINCT c.id, c.poster, c.title, c.imdbrating, c.releasedate
	FROM category AS c 
	WHERE c.type LIKE '%tv-show%'
	ORDER BY c.releasedate DESC, c.id DESC 
	LIMIT $currentPage,$PostsPerPage
	";
	$result = $dbconnect->query($sql);
	$lastid = 0;
	while ( $row = $result->fetch_assoc() )
	{
		$glose = $row["title"] . " (" . $row["releasedate"] .")";
    ?>
    <a target="" class="tags" glose="<?php echo $glose; ?>" href="category.php?id=<?php echo $row["id"] ?>">
    <div class="w3-quarterindex" style="padding: 3px;  position: relative;text-align: center;color: white;">
    <img src="<?php echo $row["poster"] ?>" alt="" id="imageindex">
    <div style="position: absolute;bottom: 1.5%;right: 1.5%;left: 1%;background: rgba(0, 0, 0, .8);">
    <b id="fontindex"><b style="color: yellow">IMDb: </b><?php echo $row["imdbrating"] ?></b>
    </div>
    </a>
    </div>
	<?php		 
	}
}
elseif( isset($_GET["sfm"]) AND $_GET["sfm"] == "imdb" AND isset($lang) ) 
{
	$sfm = $_GET["sfm"];
	$searchfilter = $sfm;
	$sql = "
	SELECT DISTINCT  c.id, c.poster, c.title, c.imdbrating, c.releasedate
	FROM category AS c 
	WHERE c.type LIKE '%tv-show%'
	AND c.language LIKE '%$lang%'
	ORDER BY c.imdbrating DESC 
	LIMIT $currentPage,$PostsPerPage
	";
	$result = $dbconnect->query($sql);
	$lastid = 0;
	while ( $row = $result->fetch_assoc() )
	{
		$glose = $row["title"] . " (" . $row["releasedate"] .")";
    ?>
    <a target="" class="tags" glose="<?php echo $glose; ?>" href="category.php?id=<?php echo $row["id"] ?>">
    <div class="w3-quarterindex" style="padding: 3px;  position: relative;text-align: center;color: white;">
    <img src="<?php echo $row["poster"] ?>" alt="" id="imageindex">
    <div style="position: absolute;bottom: 1.5%;right: 1.5%;left: 1%;background: rgba(0, 0, 0, .8);">
    <b id="fontindex"><b style="color: yellow">IMDb: </b><?php echo $row["imdbrating"] ?></b>
    </div>
    </a>
    </div>
	<?php		 
	}
}
elseif( isset ($_GET["sfm"]) AND $_GET["sfm"] == "imdb" AND !isset($lang) ) 
{
	$sfm = $_GET["sfm"];
	$searchfilter = $sfm;
	$sql = "
	SELECT DISTINCT  c.id, c.poster, c.title, c.imdbrating, c.releasedate
	FROM category AS c 
	WHERE c.type LIKE '%tv-show%'
	ORDER BY c.imdbrating DESC 
	LIMIT $currentPage,$PostsPerPage
	";
	$result = $dbconnect->query($sql);
	$lastid = 0;
	while ( $row = $result->fetch_assoc() )
	{
		$glose = $row["title"] . " (" . $row["releasedate"] .")";
    ?>
    <a target="" class="tags" glose="<?php echo $glose; ?>" href="category.php?id=<?php echo $row["id"] ?>">
    <div class="w3-quarterindex" style="padding: 3px;  position: relative;text-align: center;color: white;">
    <img src="<?php echo $row["poster"] ?>" alt="" id="imageindex">
    <div style="position: absolute;bottom: 1.5%;right: 1.5%;left: 1%;background: rgba(0, 0, 0, .8);">
    <b id="fontindex"><b style="color: yellow">IMDb: </b><?php echo $row["imdbrating"] ?></b>
    </div>
    </a>
    </div>
	<?php		 
	}
}
// getting data saved into arrays ( no filters )
else
{
	$sql = "
	SELECT p.title, p.catid, p.poster, p.category 
	FROM posts AS p
	WHERE
	p.type LIKE '%tv-show%' 
	AND
	p.id IN 
	(
		SELECT MAX(pp.id) 
		FROM posts AS pp
		GROUP BY pp.category
	)
	AND
	p.catid NOT IN
	(
		SELECT `id` FROM `category` WHERE `genre` LIKE '%ramadan%'
	)
	ORDER BY p.id DESC
	LIMIT $currentPage,$PostsPerPage
	";
	$result = $dbconnect->query($sql);
	$lastid = 0;
	while ( $row = $result->fetch_assoc() )
	{
		$glose = $row["category"];
    ?>
    <a target="" class="tags" glose="<?php echo $glose; ?>" href="category.php?id=<?php echo $row["catid"] ?>">
    <div class="w3-quarterindex" style="padding: 3px;  position: relative;text-align: center;color: white;">
    <img src="<?php echo $row["poster"] ?>" alt="" id="imageindex">
    <div style="position: absolute;bottom: 1.5%;right: 1.5%;left: 1%;background: rgba(0, 0, 0, .8);">
    <b id="fontindex"><?php echo $row["title"] ?></b>
    </div>
    </a>
    </div>
	<?php		 
	}
}
?>
	</div>
    </div>
	<?php
		 
		 if ( $NumberOfPages != 0 )
		 {
		 if ( $page == 1 && $page < $NumberOfPages )
		 {
			 ?><center><a href='
			 <?php 
			 
			 echo 'tvshow.php?page='.$page; 
			 
			 if ( isset ($lang) AND !isset ($gen) AND !isset( $sfm ))
			 { 
				 echo '&sfc='.$searchfilter; 
			 } 
			 elseif ( !isset ($lang) AND isset ($gen) )
			 { 
				 echo '&sfg='.$searchfilter; 
			 }
			 elseif( isset ($lang) AND isset ($gen))
			 {
				echo '&sfg='.$gen."&sfc=".$lang; 
			 }
			 elseif ( isset( $sfm ) )
			 {
				echo '&sfm='.$searchfilter;
				if ( isset($lang) )
				{
					echo "&sfc=".$lang;
				}
			 }

		  ?>'><img src='images/next1.png' width='50' height='50'></a></center>
		 <?php
		 }
		 if ( $page < $NumberOfPages && $page > 1)
		 {
			 echo "<center><table><tr><td><a onclick='goBack()'><img src='images/prev1.png' width='50' height='50'></a></td>";
			 ?><td><a href='
			 <?php 
			 
				echo 'tvshow.php?page='.$page; 

				if ( isset ($lang) AND !isset ($gen) AND !isset( $sfm ))
				{ 
					echo '&sfc='.$searchfilter; 
				} 
				elseif ( !isset ($lang) AND isset ($gen) )
				{ 
					echo '&sfg='.$searchfilter; 
				}
				elseif( isset ($lang) AND isset ($gen))
				{
					echo '&sfg='.$gen."&sfc=".$lang; 
				}
				elseif ( isset( $sfm ) )
				{
					echo '&sfm='.$searchfilter;
					if ( isset($lang) )
					{
						echo "&sfc=".$lang;
					}
				}

			 ?>'><img src='images/next1.png' width='50' height='50'></a></td></tr></table></center>
		 <?php
		 ;
		 }
		 if ( $page == $NumberOfPages && $page > 1 )
		 {
			 echo "<center><a onclick='goBack()'><img src='images/prev1.png' width='50' height='50'></a></a>";
		 }
		 }
		 
		 ?>
			 </div>	
<?php
include_once ("template/footer.php");
?>
    <!-- End Right Column -->

     </div>
    </div>   

<script>
function goBack() {
  window.history.back();
}
</script>

</body>
</html>
