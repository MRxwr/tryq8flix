<?php
if( isset($_POST["id"]) && !empty($_POST["id"]) ){
    $html = scrapePage("{$_POST["id"]}");
    $htmlDom = str_get_html($html);
    $episodesData = [];
    $seasonsData = [];
    // Episodes
    foreach ($htmlDom->find('div.EpisodesArea h3:contains(جميع الحلقات) + div.EpisodesList a') as $linkNode) {
        $link = $linkNode->href;
        $epNum = $linkNode->find('em', 0);
        $title = $epNum ? 'الحلقة ' . trim($epNum->plaintext) : '';
        $episodesData[] = [
            'link' => $link,
            'title' => $title,
        ];
    }
    // Seasons
    foreach ($htmlDom->find('div.EpisodesArea h3:contains(جميع المواسم) + div.EpisodesList a') as $linkNode) {
        $link = $linkNode->href;
        $seasonNum = $linkNode->find('em', 0);
        $title = $seasonNum ? 'الموسم ' . trim($seasonNum->plaintext) : '';
        $seasonsData[] = [
            'link' => $link,
            'title' => $title,
        ];
    }
    if (strpos(strtolower($_POST["id"]), 'season') === false){
        $episodesData = array_reverse($episodesData);
        // Sort seasons numerically by extracting the number from the title
        usort($seasonsData, function($a, $b) {
            preg_match('/(\d+)/', $a['title'], $matchA);
            preg_match('/(\d+)/', $b['title'], $matchB);
            $numA = isset($matchA[1]) ? intval($matchA[1]) : 0;
            $numB = isset($matchB[1]) ? intval($matchB[1]) : 0;
            return $numA - $numB;
        });
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