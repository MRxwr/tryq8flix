<?php
function wecimaListing($url) {
    $html = curlCall($url);
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

    if (strpos(strtolower($url), 'season') === false){
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
function scrapeWecimaServers($url) {
    $html = curlCall("{$url}");
    var_dump($html);
    $dom = str_get_html($html);
    $data = [
        'shows' => []
    ];
    if ($dom) {
        foreach ($dom->find('.WatchServersList ul li') as $server) {
            $btn = $server->find('btn', 0);
            if ($btn) {
                $title = $btn->find('strong', 0)->plaintext;
                $dataUrl = $btn->getAttribute('data-url');
                $jsonData = [
                    'link' => str_replace(" ", "", $dataUrl)
                ];
                $data['shows'][] = $jsonData;
            }
        }
        $servers = json_encode($data['shows'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    } else {
        $servers = json_encode([], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }
    $servers = json_decode($servers, true);
    $dom->clear();
    unset($dom);
    return $servers;
}
function scrapeWecima($url) {
    GLOBAL $website3;
    $url = ( !isset($url) || empty($url) ) ? $website3 : $url;
    $html = file_get_contents($url);
    $dom = str_get_html($html);
    if ($dom) {
        $data = [
            'shows' => []
        ];
        foreach ($dom->find('.Grid--WecimaPosts .GridItem') as $item) {
            $thumbDiv = $item->find('.Thumb--GridItem', 0);
            $link = $thumbDiv->find('a', 0);
            $bgSpan = $thumbDiv->find('.BG--GridItem', 0);
            $titleStrong = $thumbDiv->find('strong', 0);

            // Extract image URL from data-lazy-style attribute
            $imageUrl = '';
            if ($bgSpan) {
                preg_match('/url\((.*?)\)/', $bgSpan->getAttribute('data-lazy-style'), $matches);
                $imageUrl = isset($matches[1]) ? $matches[1] : '';
            }

            // Extract year from the title
            $year = '';
            $title = '';
            if ($titleStrong) {
                $title = $titleStrong->plaintext;
                preg_match('/\((\d{4})\)/', $title, $matches);
                $year = isset($matches[1]) ? $matches[1] : '';
                $title = trim(preg_replace('/\(\d{4}\)/', '', $title));
            }

            $proxyImageUrl = 'https://' . $_SERVER['HTTP_HOST'] . '/image-proxy.php?url=' . urlencode(trim($imageUrl));
            $jsonData = [
                'href' => $link ? $link->href : '',
                'image' => $proxyImageUrl,
                'episode' => '',
                'category' => '',
                'title' => $title,
                'description' => $year,
            ];
            $data['shows'][] = $jsonData;
        }
        return json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    } else {
        return 0;
    }
}

function outputData2($shows) { 
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