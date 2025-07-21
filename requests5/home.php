<?php
function getWebsite(){
    GLOBAL $website5, $_GET;
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
    return $website5.$collection.$category;
}

function searchShahid()
{
    GLOBAL $_GET, $website5;
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
    $html = scrapePage($website5 . $collection . $category);
    // Parse HTML
    $dom = str_get_html($html);
    $data = [
        'shows' => []
    ];

    if ($dom) {
        // Loop through each show block in the new structure
        foreach ($dom->find('.Small--Box') as $show) {
            $anchor = $show->find('a.recent--block', 0);
            $href = $anchor ? $anchor->href : '';
            $imageTag = $anchor ? $anchor->find('.Poster img', 0) : null;
            $image = $imageTag ? $imageTag->getAttribute('data-src') : '';
            $episodeEm = $anchor ? $anchor->find('.number em', 0) : null;
            $episode = $episodeEm ? $episodeEm->plaintext : '';
            $categoryLi = $anchor ? $anchor->find('ul.liList li.category', 0) : null;
            $category = $categoryLi ? $categoryLi->plaintext : '';
            $titleTag = $anchor ? $anchor->find('inner--title h2', 0) : null;
            $title = $titleTag ? $titleTag->plaintext : '';
            $descTag = $anchor ? $anchor->find('inner--title p', 0) : null;
            $description = $descTag ? $descTag->plaintext : '';

            $jsonData = [
                'href'       => trim($href),
                'image'      => trim($image),
                'episode'    => trim($episode),
                'views'      => '', // No views in new structure
                'title'      => trim($title),
                'category'   => trim($category),
                'description'=> trim($description)
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