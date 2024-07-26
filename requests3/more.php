<?php
if( isset($_POST["id"]) && !empty($_POST["id"]) ){
    $html = file_get_contents($_POST["id"]);
    $htmlDom = str_get_html($html);
    $seasonsData = [];
    $episodesData = [];
    foreach ($htmlDom->find('.List--Seasons--Episodes a') as $seasonLink) {
        $link = $seasonLink->href;
        $title = trim($seasonLink->plaintext);
        $seasonNumber = preg_replace('/[^0-9]/', '', $title);
        $seasonsData[] = [
            'link' => $link,
            'title' => $title,
            'season_number' => $seasonNumber
        ];
    }
    // Scrape episodes
    foreach ($htmlDom->find('.Episodes--Seasons--Episodes a') as $episodeLink) {
        $link = $episodeLink->href;
        $title = trim($episodeLink->find('episodetitle', 0)->plaintext);
        $episodeNumber = preg_replace('/[^0-9]/', '', $title);

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