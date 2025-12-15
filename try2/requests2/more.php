<?php
if( isset($_POST["id"]) && !empty($_POST["id"]) ){
    $html = curlCall($_POST["id"]);
    $htmlDom = str_get_html($html);
    $seasonsData = [];
    $episodesData = [];
    foreach ($htmlDom->find('section.allseasonss .Small--Box.Season') as $seasonBox) {
        $link = $seasonBox->find('a', 0)->href;
        $title = trim($seasonBox->find('.title', 0)->plaintext);
        $seasonNumber = trim($seasonBox->find('.epnum', 0)->plaintext);
        $seasonNumber = preg_replace('/[^0-9]/', '', $seasonNumber); // Extract only the number

        $seasonsData[] = [
            'link' => $link,
            'title' => $title,
            'season_number' => $seasonNumber
        ];
    }

    // Scrape episodes
    foreach ($htmlDom->find('section.allepcont .row a') as $episodeLink) {
        $link = $episodeLink->href;
        $title = trim($episodeLink->find('.ep-info h2', 0)->plaintext);
        $episodeNumber = trim($episodeLink->find('.epnum', 0)->plaintext);
        $episodeNumber = preg_replace('/[^0-9]/', '', $episodeNumber); // Extract only the number

        $episodesData[] = [
            'link' => $link,
            'title' => $title,
            'episode_number' => $episodeNumber
        ];
    }
	if (strpos(strtolower($_POST["id"]), 'season') === false){
		$episodesData = array_reverse($episodesData);
		$seasonsData = array_reverse($seasonsData);
	}
	
    if (strpos(strtolower($_POST["id"]), 'season') === false){
		for( $i = 0; $i < sizeof($seasonsData); $i++){
			echo "<div data-bs-toggle='modal' data-bs-target='#threeDots2'  class='btn btn-warning m-1 threeDots2' id='".str_replace("film","watch",str_replace("post","watch",str_replace("episode","watch",$seasonsData[$i]["link"])))."'>{$seasonsData[$i]["title"]}</div>";
		}
		echo "<hr>";
	}
	
	for( $i = 0; $i < sizeof($episodesData); $i++){
		echo "<div data-bs-toggle='modal' data-bs-target='#playVideo'  class='btn btn-danger m-1 playVideo' id='".str_replace("film","watch",str_replace("post","watch",str_replace("episode","watch",$episodesData[$i]["link"])))."'>{$episodesData[$i]["title"]}</div>";
	}
	
	if( sizeof($episodesData) < 1 &&  sizeof($seasonsData) < 1 ){
		echo "<div>لا يوجد المزيد من الحلقات ... شاهد الفيديو مباشرة</div>";
	}
    
}

?>