<?php
function getWebsite(){
	GLOBAL $website, $_GET;
	$collection = ( isset($_GET["collection"]) ) ? "?order={$_GET["collection"]}" : "" ;
	$category = ( isset($_GET["category"]) ) ? "&category={$_GET["category"]}" : "" ;
	if( isset($_GET["collection"]) ){
		$collection = "?order={$_GET["collection"]}";
		if( isset($_GET["category"]) ){
			$category = "&category={$_GET["category"]}";
		}
	}elseif( !isset($_GET["collection"]) && isset($_GET["category"])){
		$collection = "";
		$category = "?category={$_GET["category"]}";
	}else{
		$collection = "";
		$category = "";
	}
	return $website.$collection.$category;
}

function searchSite()
{
    GLOBAL $website, $_GET;

    // Handle collection/category from query string (same as your original logic)
    $collection = (isset($_GET["collection"])) ? "?order={$_GET["collection"]}" : "";
    $category   = (isset($_GET["category"]))   ? "&category={$_GET["category"]}" : "";

    if (isset($_GET["collection"])) {
        $collection = "?order={$_GET["collection"]}";
        if (isset($_GET["category"])) {
            $category = "&category={$_GET["category"]}";
        }
    } elseif (!isset($_GET["collection"]) && isset($_GET["category"])) {
        $collection = "";
        $category   = "?category={$_GET["category"]}";
    } else {
        $collection = "";
        $category   = "";
    }

    // Scrape the final URL
    $html = scrapePage($website . $collection . $category);
var_dump($html);
    // Parse HTML
    $dom = str_get_html($html);
    $data = [
        'shows' => []
    ];

    if ($dom) {
        // Loop through each show block
        foreach ($dom->find('.MediaGrid .media-block') as $show) {
            
            // The anchor that holds the main href & data-src for the image
            $anchor = $show->find('.content-box a.image', 0);
            $href   = $anchor ? $anchor->href : '';
            $image  = $anchor ? $anchor->getAttribute('data-src') : '';
            
            // Episode number (inside <span class="episode-number"><em>42</em></span>)
            $episodeSpan = $show->find('.episode-number em', 0);
            $episode     = $episodeSpan ? $episodeSpan->plaintext : '';
            
            // Views (inside <span class="views ti-eye">2</span>)
            $viewsSpan = $show->find('.views', 0);
            $views     = $viewsSpan ? $viewsSpan->plaintext : '';
            
            // Title (inside <h3> ... </h3>)
            $titleTag = $show->find('.hvr h3', 0);
            $title    = $titleTag ? $titleTag->plaintext : '';

            // Prepare data for each show
            $jsonData = [
                'href'       => trim($href),
                'image'      => trim($image),
                'episode'    => trim($episode),
                'views'      => trim($views),
                'title'      => trim($title),
                'description'=> '' // This site doesn't appear to include a show description
            ];

            $data['shows'][] = $jsonData;
        }

        $shows = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

    } else {
        echo 'Error: Invalid DOM object.';
        $shows = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }

    // Decode and return only the "shows" array
    $shows = (isset($shows) && !empty($shows)) ? json_decode($shows, true) : [];
    $dom->clear();
    unset($dom);

    return $shows['shows'] ?? [];
}


if( isset($_POST["type"]) && !empty($_POST["type"]) ){ 
	$user = checkLogin();
	if ( !empty($user["id"]) ){
		
		if( $_POST["type"] == "get" ){
			$collection = ( isset($_GET["collection"]) ) ? "{$_GET["collection"]}" : "" ;
			$category = ( isset($_GET["category"]) ) ? "&category={$_GET["category"]}" : "" ;
			$user = checkLogin();
			$shows = searchShahid();
			echo "<div class='row m-0 w-100' id='content'>";
			outputData($shows); 
			echo "<div class='col-md-12 loadMoreBtn mb-3' style='text-align-last: center;' id='1'><div class='btn btn-secondary w-75' >تابع</div></div><div style='display:none' class='getCollection' id='{$collection}{$category}'></div>";
			echo "</div>";
		}else{
			echo "<iframe id='frame' src='".getWebsite()."' style='width:100%;height:100vh;' sandbox='allow-same-origin allow-scripts' allowFullScreen></iframe>
			<script>
			$(document).ready(function() {
				$('.changeIframeSrc').on('click', function() {
					var link = $(this).attr('id');
					$('#frame').attr('src', link);
				});
			});
			</script>";
		}
		
	}else{
		$msg = "Please login first.";
		echo $msg;
	}
}else{
    $msg = "something wrong happened, Please try again.";
    echo $msg;
}
?>