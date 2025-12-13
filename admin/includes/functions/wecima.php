<?php
function wecimaListing($url) {
    $html = curlCall($url);
    $htmlDom = str_get_html($html);
    $seasonsData = [];
    $episodesData = [];
    
    // Scrape seasons from List--Seasons--Episodes
    foreach ($htmlDom->find('.List--Seasons--Episodes a') as $seasonLink) {
        $dataId = $seasonLink->getAttribute('data-id');
        $dataSeason = $seasonLink->getAttribute('data-season');
        $title = trim($seasonLink->plaintext);
        $seasonNumber = preg_replace('/[^0-9]/', '', $title);
        
        // Make POST request to get episodes for this season
        $postData = [
            'id' => $dataId,
            'season' => $dataSeason
        ];
        
        $episodesHtml = curlPost('https://wecima.click/ajax/Episode', $postData);
        echo $episodesDom = str_get_html($episodesHtml);
        
        // Get the first episode link as the season link
        $firstEpisode = $episodesDom ? $episodesDom->find('a', 0) : null;
        $seasonLinkUrl = $firstEpisode ? $firstEpisode->href : '';
        
        $seasonsData[] = [
            'link' => $seasonLinkUrl,
            'title' => $title,
            'season_number' => $seasonNumber
        ];
        
        if ($episodesDom) {
            $episodesDom->clear();
            unset($episodesDom);
        }
    }
    
    // Scrape episodes from current page EpisodesList
    foreach ($htmlDom->find('.EpisodesList a') as $episodeLink) {
        $link = $episodeLink->href;
        $title = '';
        $episodeTitleTag = $episodeLink->find('episodetitle', 0);
        if ($episodeTitleTag) {
            $title = trim($episodeTitleTag->plaintext);
        }
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
    $dom = str_get_html($html);
    $data = [ 'shows' => [] ];
    $i = 1;
    if ($dom) {
        foreach ($dom->find('.WatchServersList li btn') as $btn) {
            $encoded = $btn->getAttribute('data-url');
            if ($encoded) {
                // Remove + characters used for obfuscation
                $cleaned = str_replace('+', '', $encoded);
                // The encoded string starts with 'HM6Ly' which is 'https://' in base64
                // Prepend 'ht' to complete 'https://'
                $decoded = base64_decode('aHR0cHM6Ly' . substr($cleaned, 5));
                $data['shows'][] = [ 'name' => "Server {$i}", 'link' => $decoded ];
                $i++;
            }
        }
        $dom->clear();
        unset($dom);
    }
    return $data['shows'];
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
            $link = $thumbDiv ? $thumbDiv->find('a', 0) : null;
            $bgSpan = $thumbDiv ? $thumbDiv->find('.BG--GridItem', 0) : null;
            $h2 = $link ? $link->find('h2.hasyear[itemprop=name]', 0) : null;

            // Extract image URL from data-src or style attribute
            $imageUrl = '';
            if ($bgSpan) {
                if ($bgSpan->hasAttribute('data-src')) {
                    $imageUrl = $bgSpan->getAttribute('data-src');
                } elseif ($bgSpan->hasAttribute('style')) {
                    preg_match('/background-image:\s*url\(["\']?(.*?)["\']?\)/', $bgSpan->getAttribute('style'), $matches);
                    $imageUrl = isset($matches[1]) ? $matches[1] : '';
                }
            }

            // Extract title and year from h2
            $title = '';
            $year = '';
            if ($h2) {
                $titleText = $h2->plaintext;
                preg_match('/\((\d{4})\)/', $titleText, $matches);
                $year = isset($matches[1]) ? $matches[1] : '';
                $title = trim(preg_replace('/\(\d{4}\)/', '', $titleText));
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