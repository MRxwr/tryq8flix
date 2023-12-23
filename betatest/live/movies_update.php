<?php
include ("../includes/config.php");
include ("../includes/checksouthead.php");



if ( isset($_GET["q"]) )
{
	//number of posts per page
	$PostsPerPage = 24;
	
	$page = 1 + $_GET["q"];
	$currentPage = ($page * $PostsPerPage)-($PostsPerPage);
}
else
{
		
//number of posts per page
	$PostsPerPage = 24;
	
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
}

if ( isset($_GET["sfc"]) )
{
	$lang = $_GET["sfc"];
	if ( in_array($lang,$alllanguages) )
	{
		$sql = "
		SELECT DISTINCT c.id, c.poster, c.title, c.imdbrating, c.releasedate 
		FROM posts AS p
		JOIN category AS c 
		ON c.id = p.catid 
		WHERE c.language LIKE '%$lang%' AND c.type LIKE '%movie%' 
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
}
elseif ( isset($_GET["sfg"]) )
{
	$gen = $_GET["sfg"];
	if ( in_array($gen,$allgenres) )
	{
		$sql = "
		SELECT DISTINCT c.id, c.poster, c.title, c.imdbrating, c.releasedate 
		FROM category AS c
		JOIN posts AS p 
		ON c.id = p.catid 
		WHERE c.genre LIKE '%$gen%' AND c.type LIKE '%movie%' 
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
}
elseif( isset ( $_GET["sfm"] ) AND $_GET["sfm"] == "name" ) 
{
	$sfm = $_GET["sfm"];
	$searchfilter = $sfm;
	$sql = "
	SELECT DISTINCT c.id, c.poster, c.title, c.imdbrating, c.releasedate
	FROM category AS c 
	WHERE c.type LIKE '%movie%'
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
elseif( isset ( $_GET["sfm"]) AND $_GET["sfm"] == "mwatched" ) 
{
	$sfm = $_GET["sfm"];
	$searchfilter = $sfm;
	$sql = "
	SELECT DISTINCT c.id, c.poster, c.imdbrating, c.releasedate, p.views, c.title
	FROM posts AS p
	JOIN category AS c 
	ON c.id = p.catid 
	WHERE c.type LIKE '%movie%' 
	ORDER BY cast(p.views as int) DESC 
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
    <b id="fontindex"><?php echo $row["views"] ?><b style="color: yellow"> Views</b></b>
    </div>
    </a>
    </div>
	<?php		 
	}
}
elseif( isset ( $_GET["sfm"])  AND $_GET["sfm"] == "year" ) 
{
	$sfm = $_GET["sfm"];
	$searchfilter = $sfm;
	$sql = "
	SELECT DISTINCT c.id, c.poster, c.title, c.imdbrating, c.releasedate
	FROM category AS c 
	WHERE c.type LIKE '%movie%'
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
elseif( isset ( $_GET["sfm"]) AND $_GET["sfm"] == "imdb" ) 
{
	$sfm = $_GET["sfm"];
	$searchfilter = $sfm;
	$sql = "
	SELECT DISTINCT  c.id, c.poster, c.title, c.imdbrating, c.releasedate
	FROM category AS c 
	WHERE c.type LIKE '%movie%'
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
	WHERE p.type LIKE '%movie%' 
	AND p.id IN 
	(
		SELECT MAX(pp.id) 
		FROM posts AS pp
		GROUP BY pp.category
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
	echo "</div></div>";
//couting movies by language
if ( isset( $_GET["sfc"] ) )
{
	$lang = $_GET["sfc"];
	$searchfilter = $_GET["sfc"];
	$sql = "SELECT * FROM category WHERE type LIKE '%movie%' AND language LIKE '%$lang%'";
	$result = $dbconnect->query($sql);
	$NumberOfPosts = $result->num_rows;
	$NumberOfPages = ceil($NumberOfPosts / $PostsPerPage);
}
//couting movies by genre
elseif ( isset( $_GET["sfg"] ) )
{
	$gen = $_GET["sfg"];
	$searchfilter = $_GET["sfg"];
	$sql = "SELECT * FROM category WHERE type LIKE '%movie%' AND genre LIKE '%$gen%'";
	$result = $dbconnect->query($sql);
	$NumberOfPosts = $result->num_rows;
	$NumberOfPages = ceil($NumberOfPosts / $PostsPerPage);
}
//total number of movies
else
{
	$sql = "SELECT * FROM category WHERE type LIKE '%movie%'";
	$result = $dbconnect->query($sql);
	$NumberOfPosts = $result->num_rows;
	$NumberOfPages = ceil($NumberOfPosts / $PostsPerPage);
}
		 
if ( $NumberOfPages != 0 )
{
	if ( $page == 1 && $page < $NumberOfPages )
	{
		if ( isset ($lang) )
		{ 
			$link = 'newmovies.php?page='.$page.'&sfc='.$searchfilter; 
		} 
		elseif ( isset ($gen) )
		{ 
			$link = 'newmovies.php?page='.$page.'&sfg='.$searchfilter; 
		} 
		elseif ( isset( $sfm ) )
		{
			$link = 'newmovies.php?page='.$page.'&sfm='.$searchfilter;
		}
		else
		{
			$link = $page;
		}
		echo "<center><a onclick='showUser(".$link.")'><img src='images/next1.png' width='50' height='50'></a></center>";
	}
	if ( $page < $NumberOfPages && $page > 1)
	{
		$link = $page - 1;
		echo "<center><table><tr><td><a onclick='showUser(".$link.")'><img src='images/prev1.png' width='50' height='50'></a></td>";
		
		if ( isset ($lang) )
		{
			$link =  'newmovies.php?page='.$page.'&sfc='.$searchfilter; 
		} 
		elseif ( isset ($gen) )
		{ 
			$link =   'newmovies.php?page='.$page.'&sfg='.$searchfilter; 
		} 
		elseif ( isset( $sfm ) )
		{
			$link =   'newmovies.php?page='.$page.'&sfm='.$searchfilter;
		}
		else
		{
			$link = $page;
		}

		echo "<td><a onclick='showUser(".$link.")'><img src='images/next1.png' width='50' height='50'></a></td></tr></table></center>";
		
	}
	if ( $page == $NumberOfPages && $page > 1 )
	{
		echo "<center><a onclick='goBack()'><img src='images/prev1.png' width='50' height='50'></a></a>";
	}
} 
?>