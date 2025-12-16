<?php
function curlCallEgyDead($url) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_ENCODING, '');
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36');
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.7',
        'Accept-Language: en-US,en;q=0.9,ar;q=0.8',
        'Upgrade-Insecure-Requests: 1',
        'Referer: https://a.a5s0d.sbs/'
    ]);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
    curl_setopt($ch, CURLOPT_TIMEOUT, 60);
    
    $response = curl_exec($ch);
    curl_close($ch);
    return $response;
}

function scrapEgyDead($url) {
	$html = curlCallEgyDead($url);
	$dom = str_get_html($html);
	
    $data = [
		'shows' => []
	];

	if ($dom) {
        $items = [];
        
        // Try to find items in specific containers first
        if (strpos($url, '?s=') !== false) {
            // Search page
            $container = $dom->find('.posts-list', 0);
            if (!$container) $container = $dom->find('.search-page', 0);
            if (!$container) $container = $dom->find('.Block--Item', 0);
            
            if ($container) {
                $items = $container->find('.movieItem');
            }
        } elseif (strpos($url, 'category') !== false) {
            // Category page
            $container = $dom->find('.cat-page', 0);
            if ($container) {
                $items = $container->find('.movieItem');
            }
        } else {
            // Home/Main page
            $container = $dom->find('.main-section', 0);
            if ($container) {
                $items = $container->find('.movieItem');
            }
        }
        
        // Fallback: if no items found yet, search globally for .movieItem
        if (empty($items)) {
            $items = $dom->find('.movieItem');
        }

        foreach ($items as $movie) {
            $link = $movie->find('a', 0);
            $image = $movie->find('img', 0);
            $title = $movie->find('.BottomTitle', 0);
            $category = $movie->find('.cat_name', 0);
            $episode = $movie->find('.number_episode em', 0);
            $label = $movie->find('.label', 0);

            $movieData = [
                'href' => $link ? $link->href : '',
                'image' => $image ? $image->src : '',
                'title' => $title ? $title->plaintext : '',
                'category' => $category ? $category->plaintext : '',
                'episode' => $episode ? $episode->plaintext : '',
                'description' => $label ? $label->plaintext : '',
            ];
            $data['shows'][] = $movieData;
        }
		$shows = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
	} else {
		$shows = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
	}
    echo $url;
	$shows = ( isset($shows) && !empty($shows) ) ? json_decode($shows,true) : array() ;
	$dom->clear();
	unset($dom);
	return $shows = $shows["shows"];
}

function extractSeasonUrlEgyDead($html) {
    if (preg_match('/<a itemprop="url" href="(https:\/\/[^"]*\/season\/[^"]*)"/', $html, $matches)) {
        return $matches[1];
    }
    return null;
}

function egyDeadListing($url) {
	$_POST["id"] = $url;
	$html = $_POST["id"];
    if (strpos(strtolower($_POST["id"]), 'season') === false && strpos(strtolower($_POST["id"]), 'episode') === false) {
        echo "<div>لا يوجد المزيد من الحلقات ... شاهد الفيديو مباشرة</div>"; die();
    }
    if (strpos(strtolower($_POST["id"]), 'season') === false) {
        $html = curlCallEgyDead($_POST["id"]);
        $html = extractSeasonUrlEgyDead($html);
    }
    $html = curlCallEgyDead($html);
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
	$data = [
		'seasons' => $seasonsData,
		'episodes' => $episodesData
	];
	$htmlDom->clear();
	unset($htmlDom);
	return $data;
}

function egyDeadServers($url) {
    $_POST["id"] = $url;
    $curl = curl_init();
    curl_setopt_array($curl, array(
    CURLOPT_URL => "{$_POST["id"]}",
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => '',
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 0,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => 'POST',
    CURLOPT_POSTFIELDS => array('View' => '1'),
    CURLOPT_USERAGENT => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
    CURLOPT_HTTPHEADER => [
        'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.7',
        'Accept-Language: en-US,en;q=0.9,ar;q=0.8',
        'Upgrade-Insecure-Requests: 1'
    ],
    CURLOPT_SSL_VERIFYPEER => false,
    CURLOPT_SSL_VERIFYHOST => false,
    ));
    $html = curl_exec($curl);
    curl_close($curl);
    $dom = str_get_html($html);
    $data = [
        'shows' => []
    ];
    if ($dom) {
        $serversList = $dom->find('.watchAreaMaster .serversList', 0);
        if ($serversList) {
            foreach ($serversList->find('li') as $server) {
                $link = $server->getAttribute('data-link');
                $jsonData = [
                    'link' => str_replace(" ", "", $link)
                ];
                $data['shows'][] = $jsonData;
            }
            $servers = json_encode($data['shows'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        } else {
            $servers = json_encode([], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        }
    } else {
        $servers = json_encode([], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }
    $servers = json_decode($servers, true);
	$dom->clear();
	unset($dom);
	return $servers;
}

function outputData3($shows) { 
    $user = checkLogin();
    $output = "";
    
    if (is_array($shows) && !empty($shows) && !empty($user["id"])) {
        for ($i = 0; $i < sizeof($shows); $i++) {
            $checkVideoType = str_replace("film", "watch", str_replace("post", "watch", str_replace("episode", "watch", $shows[$i]["href"])));

            if (strstr($shows[$i]["href"], "episode")) {
                $catgoryType = "categoryTitleTv";
                $shows[$i]["episode"] = $shows[$i]["episode"];
            } elseif (strstr($shows[$i]["href"], "film")) {
                $catgoryType = "categoryTitleMovie";
                $shows[$i]["episode"] = "تشغيل";
            } else {
                $catgoryType = "categoryTitlePost";
                $shows[$i]["episode"] = "تشغيل";
            }

            $realTitle = explode("الحلقة", $shows[$i]["title"]);

            $output .= "
                <div class='col-xl-4 col-lg-6 col-md-6 col-sm-12 p-1'>
                    <div class='card w-100'>
                        <div class='card-body'>
                            <div class='row w-100 p-0 m-0'>
                                <div class='col-4 p-1'>
                                    <img src='{$shows[$i]["image"]}' style='width:100%;height:170px;border-radius: 10px; box-shadow: 0px 0px 10px 0px black;'>
                                </div>
                                <div class='col-8 p-1'>
                                    <div style='height:170px; overflow:auto; text-align: -webkit-right;' class='pt-2'>
                                        <h6 class='card-title {$catgoryType}' id='" . str_replace(' ', '-', $shows[$i]["category"]) . "' style='color:#9f8d5c'><b>{$shows[$i]["category"]}</b></h6>
                                        <h6 class='card-title postTitle{$i}'>{$realTitle[0]}</h6>
                                    </div>
                                </div>
                                <div class='col-6 p-1'>
                                    <div data-bs-toggle='modal' data-bs-target='#playVideo' class='btn btn-danger w-100 playVideo nextBtn' id='{$checkVideoType}'><i class='bi bi-play-fill'></i> {$shows[$i]["episode"]}</div>
                                </div>
                                <div class='col-6 p-1'>
                                    <div data-bs-toggle='modal' data-bs-target='#threeDots' class='btn btn-warning w-100 threeDots nextBtn' id='{$shows[$i]["href"]}'><i class='bi bi-three-dots'></i></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            ";
        }
        echo $output;
    } else {
        echo "<h1 class='text-center mt-5'>No result.</h1>";
    }
}
?>