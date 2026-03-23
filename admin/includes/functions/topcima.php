<?php
function domTopCinema($url) {
    $html = curlCall($url);
	$dom = str_get_html($html);
	$data = [
		'shows' => []
	];
	if ($dom) {
		foreach ($dom->find('.Posts--List .Small--Box') as $show) {
			$link = $show->find('a', 0);
			$image = $show->find('img', 0);
			$genre = $show->find('.liList li', 0);
			$title = $show->find('h3', 0);
			$jsonData = [
				'href' => $link->href,
				'image' => $image->getAttribute('data-src'),
				'episode' => '', // Not present in the provided HTML
				'category' => $genre ? $genre->plaintext : '',
				'title' => $title ? $title->plaintext : '',
				'description' => '', // Not present in the provided HTML
			];
			$data['shows'][] = $jsonData;
		}
		$shows = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
	} else {
		echo 'Error: Invalid DOM object.';
		$shows = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
	}
	$shows = ( isset($shows) && !empty($shows) ) ? json_decode($shows,true) : array() ;
	$dom->clear();
	unset($dom);
	return $shows = $shows["shows"];
}

function TopCenimaListings($url) {
    $html = curlCall($url);
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

    $data = [
        'seasons' => $seasonsData,
        'episodes' => $episodesData
    ];
    $htmlDom->clear();
	unset($htmlDom);
    return $data;
}

function topCinemaServers($url) {
    GLOBAL $website2;
    $html = curlCall("{$url}watch/");
    $dom = str_get_html($html);
    $mainServer = [];
    
    // Extract base domain for headers
    $parsed = parse_url($url);
    $baseHost = isset($parsed['host']) ? $parsed['host'] : 'web7.topcinema.cloud';
    $origin = "{$parsed['scheme']}://{$baseHost}";

    if ($dom) {
        // Extract the main iframe link first if it exists
        $iframe = $dom->find('.player--iframe iframe', 0);
        if ($iframe && $iframe->src) {
            $src = $iframe->src;
            // Handle protocol-relative URLs
            if (strpos($src, '//') === 0) {
                $src = 'https:' . $src;
            }
            $mainServer[] = ['link' => $src];
        }

        $servers = [];
        foreach ($dom->find('.server--item') as $server) {
            $id = $server->getAttribute('data-id');
            $i = $server->getAttribute('data-server');
            $servers[] = [
                'id' => $id,
                'i' => $i,
            ];
        }
    } else {
        echo 'Error: Invalid DOM object.';
    }
    
    $blackList = []; //[0,3,4,5,6];
    for ($i = 0; $i < sizeof($servers); $i++) {
        if (in_array($i, $blackList)) {
        }else{
            $curl = curl_init();
            curl_setopt_array($curl, array(
            CURLOPT_URL => "{$origin}/wp-content/themes/movies2023/Ajaxat/Single/Server.php",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => "id={$servers[$i]['id']}&i={$servers[$i]['i']}",
            CURLOPT_HTTPHEADER => array(
                "Origin: {$origin}",
                "Referer: {$url}watch/",
                'X-Requested-With: XMLHttpRequest',
                'Content-Type: application/x-www-form-urlencoded; charset=UTF-8',
                'User-Agent: Mozilla/5.0 (iPhone; CPU iPhone OS 18_5 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/18.5 Mobile/15E148 Safari/604.1'
            ),
            ));
            $response = curl_exec($curl);
            $link = extractLink($response);
            curl_close($curl);
            if ($link) {
                $mainServer[] = ["link" => $link];
            }
        }
    }
    return $mainServer;
}
?>