<?php
function extractSeasonUrl($htmlDom) {
    // First, try to find the season URL in the breadcrumbs
    $breadcrumbs = $htmlDom->find('.breadcrumbs-single ul', 0);
    if ($breadcrumbs) {
        $seasonLink = $breadcrumbs->find('a[href*="/season/"]', 0);
        if ($seasonLink) {
            return $seasonLink->href;
        }
    }
    
    // If not found in breadcrumbs, try to find it in the seasons list
    $seasonsList = $htmlDom->find('.seasons-list', 0);
    if ($seasonsList) {
        $seasonLink = $seasonsList->find('.movieItem a', 0);
        if ($seasonLink) {
            return $seasonLink->href;
        }
    }
    
    // If still not found, return null
    return null;
}

if( isset($_POST["id"]) && !empty($_POST["id"]) ){
    echo $_POST["id"];
    echo $html = extractSeasonUrl($_POST["id"]);
    echo $html = curlCall($html);die();
    $htmlDom = str_get_html($html);
    $seasonsData = [];
    $episodesData = [];

    // Scrape seasons
    foreach ($htmlDom->find('.seasons-list .movieItem') as $seasonItem) {
        $seasonLink = $seasonItem->find('a', 0);
        $link = $seasonLink->href;
        $title = $seasonLink->title;
        $seasonNumber = preg_replace('/[^0-9]/', '', $title);
        $seasonsData[] = [
            'link' => $link,
            'title' => $title,
            'season_number' => $seasonNumber
        ];
    }

    // Scrape episodes
    foreach ($htmlDom->find('.episodes-list .EpsList li') as $episodeItem) {
        $episodeLink = $episodeItem->find('a', 0);
        $link = $episodeLink->href;
        $title = $episodeLink->title;
        $episodeNumber = preg_replace('/[^0-9]/', '', $episodeLink->plaintext);
        $episodesData[] = [
            'link' => $link,
            'title' => $title,
            'episode_number' => $episodeNumber
        ];
    }

    // Reverse arrays if not a season page
    if (strpos(strtolower($_POST["id"] ?? ''), 'season') === false) {
        $episodesData = array_reverse($episodesData);
        $seasonsData = array_reverse($seasonsData);
    }

	
    if (strpos(strtolower($_POST["id"]), 'season') === false){
		for( $i = 0; $i < sizeof($seasonsData); $i++){
			echo "<div data-bs-toggle='modal' data-bs-target='#threeDots2'  class='btn btn-warning m-1 threeDots2' id='".$seasonsData[$i]["link"]."'>{$seasonsData[$i]["title"]}</div>";
		}
		echo "<hr>";
	}
	
	for( $i = 0; $i < sizeof($episodesData); $i++){
		echo "<div data-bs-toggle='modal' data-bs-target='#playVideo'  class='btn btn-danger m-1 playVideo' id='".$episodesData[$i]["link"]."'>{$episodesData[$i]["title"]}</div>";
	}
	
	if( sizeof($episodesData) < 1 &&  sizeof($seasonsData) < 1 ){
		echo "<div>لا يوجد المزيد من الحلقات ... شاهد الفيديو مباشرة</div>";
	}
    
}

?>