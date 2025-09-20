<?php
function myCimaHome($url) {
    $url = trim($url);
    $url = str_replace(' ', '+', $url);
    $html = curlCall($url);
    $dom = str_get_html($html);
    $data = [
        'shows' => []
    ];
    if ($dom) {
        foreach ($dom->find('.Small--Box') as $show) {
            $link = $show->find('a', 0);
            // Find the poster image (skip logo)
            $images = $show->find('img');
            $poster = '';
            foreach ($images as $img) {
                if ($img->getAttribute('data-src')) {
                    $poster = $img->getAttribute('data-src');
                    break;
                }
            }
            $genre = $show->find('.liList li.category', 0);
            // Title and description inside <inner--title>
            $innerTitle = $show->find('inner--title', 0);
            $title = '';
            $desc = '';
            if ($innerTitle) {
                $h2 = $innerTitle->find('h2', 0);
                $p = $innerTitle->find('p', 0);
                $title = $h2 ? $h2->plaintext : '';
                $desc = $p ? $p->plaintext : '';
            }
            // Episode (if exists)
            $episode = '';
            $numberDiv = $show->find('.number em', 0);
            if ($numberDiv) {
                $episode = $numberDiv->plaintext;
            }
            $jsonData = [
                'href' => $link ? $link->href : '',
                'image' => $poster,
                'episode' => $episode,
                'category' => $genre ? $genre->plaintext : '',
                'title' => $title,
                'description' => $desc,
            ];
            $data['shows'][] = $jsonData;
        }
        $shows = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    } else {
        echo 'Error: Invalid DOM object.';
        $shows = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }
    $shows = ( isset($shows) && !empty($shows) ) ? json_decode($shows,true) : array() ;
    if ($dom) { $dom->clear(); unset($dom); }
    return $shows = $shows["shows"];
}

function myCimaListings($url) {
    $html = curlCall($url);
    $htmlDom = str_get_html($html);
    $seasonsData = [];
    $episodesData = [];

    // Scrape seasons
    foreach ($htmlDom->find('section.allseasonss ul.Blocks--List .Small--Box') as $seasonBox) {
        $a = $seasonBox->find('a', 0);
        $link = $a ? $a->href : '';
        $epnumDiv = $seasonBox->find('.epnum', 0);
        $seasonNumber = $epnumDiv ? trim($epnumDiv->plaintext) : '';
        $seasonNumberDigits = preg_replace('/[^0-9]/', '', $seasonNumber);
        $innerTitle = $seasonBox->find('inner--title h2', 0);
        $title = $innerTitle ? trim($innerTitle->plaintext) : '';
        $poster = '';
        $img = $seasonBox->find('img', 0);
        if ($img && $img->getAttribute('data-src')) {
            $poster = $img->getAttribute('data-src');
        }
        $seasonsData[] = [
            'link' => $link,
            'title' => $title,
            'season_number' => $seasonNumberDigits,
            'season_text' => $seasonNumber,
            'poster' => $poster
        ];
    }

    // Scrape episodes
    foreach ($htmlDom->find('section.allepcont .row a') as $episodeLink) {
        $link = $episodeLink->href;
        $epInfo = $episodeLink->find('.ep-info h2', 0);
        $title = $epInfo ? trim($epInfo->plaintext) : '';
        $epnumDiv = $episodeLink->find('.epnum', 0);
        $episodeNumber = $epnumDiv ? trim($epnumDiv->plaintext) : '';
        $episodeNumberDigits = preg_replace('/[^0-9]/', '', $episodeNumber);
        $poster = '';
        $img = $episodeLink->find('img', 0);
        if ($img && $img->getAttribute('data-src')) {
            $poster = $img->getAttribute('data-src');
        }
        $episodesData[] = [
            'link' => $link,
            'title' => $title,
            'episode_number' => $episodeNumberDigits,
            'episode_text' => $episodeNumber,
            'poster' => $poster
        ];
    }

    $data = [
        'seasons' => $seasonsData,
        'episodes' => $episodesData
    ];
    $htmlDom->clear();
	unset($htmlDom);
    return $data;
}

function myCimaServers($url) {
    GLOBAL $website2;
    $html = curlCall("{$url}watch/");
    $dom = str_get_html($html);
    $data = [
        'shows' => []
    ];
    if ($dom) {
        foreach ($dom->find('.server--item') as $server) {
            $id = $server->getAttribute('data-id');
            $i = $server->getAttribute('data-server');
            $jsonData = [
                'id' => $id,
                'i' => $i,
                'link' => "{$url}watch/",
            ];
            $data['shows'][] = $jsonData;
        }
        $servers = json_encode($data['shows'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    } else {
        echo 'Error: Invalid DOM object.';
        $servers = json_encode([], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }
    $servers = json_decode($servers, true);
    $mainServer = [];
    $ajaxUrl = "https://tryq8flix.com/requests2/index?type=getServer";
    $blackList = [0,3,4,5,6];
    for ($i = 0; $i < sizeof($servers); $i++) {
        if (in_array($i, $blackList)) {
        }else{
            unset($servers[$i]["link"]);
            //$url1 = makeRequest($ajaxUrl, array("data"=>$servers[$i]), "");
            $curl = curl_init();
            curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://web2.myCima.cam/wp-content/themes/movies2023/Ajaxat/Single/Server.php',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => array('id' => "{$servers[$i]['id']}",'i' => "{$servers[$i]['i']}"),
            CURLOPT_HTTPHEADER => array(
                'Referer: https://web2.myCima.cam',
                'X-Requested-With: XMLHttpRequest'
            ),
            ));
            $response = curl_exec($curl);
            $link = extractLink($response);
            curl_close($curl);
            $mainServer[]["link"] = $link;
        }
    }
    return $mainServer;
}
?>