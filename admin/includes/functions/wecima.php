<?php
function wecimaListing($url) {
    $html = curlCall($url);
    $htmlDom = str_get_html($html);
    $seasonsData = [];
    $episodesData = [];
    
    // Check if there are seasons
    $seasonsList = $htmlDom->find('.List--Seasons--Episodes a');
    
    if (count($seasonsList) > 0) {
        // Scrape all seasons and get episodes for each
        foreach ($seasonsList as $seasonLink) {
            $dataId = $seasonLink->getAttribute('data-id');
            $dataSeason = $seasonLink->getAttribute('data-season');
            $title = trim($seasonLink->plaintext);
            $seasonNumber = preg_replace('/[^0-9]/', '', $title);
            $seasonNumber = str_pad($seasonNumber, 2, '0', STR_PAD_LEFT);
            
            // Make POST request to get episodes for this season
            $postData = [
                'post_id' => $dataId,
                'season' => $dataSeason
            ];
            
            $episodesHtml = curlPost('https://wecima.click/ajax/Episode', $postData);
            $episodesDom = str_get_html($episodesHtml);
            
            if ($episodesDom) {
                // Get all episodes for this season
                foreach ($episodesDom->find('a') as $episodeLink) {
                    $link = $episodeLink->href;
                    $episodeTitleTag = $episodeLink->find('episodetitle', 0);
                    if ($episodeTitleTag) {
                        $episodeTitle = trim($episodeTitleTag->plaintext);
                        $episodeNumber = preg_replace('/[^0-9]/', '', $episodeTitle);
                        $episodeNumber = str_pad($episodeNumber, 2, '0', STR_PAD_LEFT);
                        
                        $episodesData[] = [
                            'link' => $link,
                            'title' => "S{$seasonNumber}E{$episodeNumber}",
                            'episode_number' => $episodeNumber
                        ];
                    }
                }
                $episodesDom->clear();
                unset($episodesDom);
            }
        }
    } else {
        // No seasons, get episodes directly from EpisodesList
        foreach ($htmlDom->find('.EpisodesList a') as $episodeLink) {
            $link = $episodeLink->href;
            $episodeTitleTag = $episodeLink->find('episodetitle', 0);
            if ($episodeTitleTag) {
                $episodeTitle = trim($episodeTitleTag->plaintext);
                $episodeNumber = preg_replace('/[^0-9]/', '', $episodeTitle);
                $episodeNumber = str_pad($episodeNumber, 2, '0', STR_PAD_LEFT);
                
                $episodesData[] = [
                    'link' => $link,
                    'title' => "E{$episodeNumber}",
                    'episode_number' => $episodeNumber
                ];
            }
        }
    }

    if (strpos(strtolower($url), 'season') === false){
        $episodesData = array_reverse($episodesData);
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
function scrapeWecimaSearch($query) {
    // Use http_build_query to ensure proper encoding (application/x-www-form-urlencoded)
    // This handles Arabic characters correctly by percent-encoding them.
    $postData = http_build_query(['q' => $query]);
    $response = curlPost('https://wecima.click/search', $postData);
    $result = json_decode($response, true);
    
    if (!$result || !isset($result['output'])) {
        return json_encode(['shows' => []], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }
    
    $data = ['shows' => []];
    
    // Handle if output is a string (HTML containing all items)
    if (is_string($result['output'])) {
        $dom = str_get_html($result['output']);
        if ($dom) {
            foreach ($dom->find('.GridItem') as $item) {
                $thumbDiv = $item->find('.Thumb--GridItem', 0);
                $link = $thumbDiv ? $thumbDiv->find('a', 0) : null;
                $bgSpan = $thumbDiv ? $thumbDiv->find('.BG--GridItem', 0) : null;
                $h2 = $link ? $link->find('h2.hasyear', 0) : null;
                
                $imageUrl = '';
                if ($bgSpan && $bgSpan->hasAttribute('style')) {
                    preg_match('/--image:url\(([^)]+)\)/', $bgSpan->getAttribute('style'), $matches);
                    $imageUrl = isset($matches[1]) ? $matches[1] : '';
                }
                
                $title = '';
                $year = '';
                if ($h2) {
                    $titleText = $h2->plaintext;
                    preg_match('/\((\d{4})\)/', $titleText, $matches);
                    $year = isset($matches[1]) ? $matches[1] : '';
                    $title = trim(preg_replace('/\(\d{4}\)/', '', $titleText));
                }
                
                $proxyImageUrl = 'https://' . $_SERVER['HTTP_HOST'] . '/image-proxy.php?url=' . urlencode(trim($imageUrl));
                $data['shows'][] = [
                    'href' => $link ? $link->href : '',
                    'image' => $proxyImageUrl,
                    'episode' => '',
                    'category' => '',
                    'title' => $title,
                    'description' => $year,
                ];
            }
            $dom->clear();
            unset($dom);
        }
    } 
    // Handle if output is an array of HTML strings (legacy/fallback)
    elseif (is_array($result['output'])) {
        foreach ($result['output'] as $htmlString) {
            $dom = str_get_html($htmlString);
            if (!$dom) continue;
            
            $item = $dom->find('.GridItem', 0);
            if (!$item) continue;
            
            $thumbDiv = $item->find('.Thumb--GridItem', 0);
            $link = $thumbDiv ? $thumbDiv->find('a', 0) : null;
            $bgSpan = $thumbDiv ? $thumbDiv->find('.BG--GridItem', 0) : null;
            $h2 = $link ? $link->find('h2.hasyear', 0) : null;
            
            $imageUrl = '';
            if ($bgSpan && $bgSpan->hasAttribute('style')) {
                preg_match('/--image:url\(([^)]+)\)/', $bgSpan->getAttribute('style'), $matches);
                $imageUrl = isset($matches[1]) ? $matches[1] : '';
            }
            
            $title = '';
            $year = '';
            if ($h2) {
                $titleText = $h2->plaintext;
                preg_match('/\((\d{4})\)/', $titleText, $matches);
                $year = isset($matches[1]) ? $matches[1] : '';
                $title = trim(preg_replace('/\(\d{4}\)/', '', $titleText));
            }
            
            $proxyImageUrl = 'https://' . $_SERVER['HTTP_HOST'] . '/image-proxy.php?url=' . urlencode(trim($imageUrl));
            $data['shows'][] = [
                'href' => $link ? $link->href : '',
                'image' => $proxyImageUrl,
                'episode' => '',
                'category' => '',
                'title' => $title,
                'description' => $year,
            ];
            
            $dom->clear();
            unset($dom);
        }
    }
    
    return json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
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
        foreach ($dom->find('.GridItem') as $item) {
            $thumbDiv = $item->find('.Thumb--GridItem', 0);
            $link = $thumbDiv ? $thumbDiv->find('a', 0) : null;
            $bgSpan = $thumbDiv ? $thumbDiv->find('.BG--GridItem', 0) : null;
            $h2 = $link ? $link->find('.hasyear', 0) : null;

            // Extract image URL from data-src or style attribute
            $imageUrl = '';
            if ($bgSpan) {
                if ($bgSpan->hasAttribute('data-src')) {
                    $imageUrl = $bgSpan->getAttribute('data-src');
                } elseif ($bgSpan->hasAttribute('style')) {
                    // Match --image: url(URL) or background-image: url(URL)
                    if (preg_match('/(?:--image|background-image):\s*url\(\s*[\'"]?(.*?)[\'"]?\s*\)/', $bgSpan->getAttribute('style'), $matches)) {
                        $imageUrl = $matches[1];
                    }else{
                        $imageUrl = $bgSpan;
                    }
                }
            }

            // Extract title and year from hasyear element
            $title = '';
            $year = '';
            if ($h2) {
                $titleText = trim($h2->plaintext);
                // Improved regex to handle spaces and formatting within the year parenthesis
                if (preg_match('/\(?\s*(\d{4})\s*\)?/', $titleText, $matches)) {
                    $year = $matches[1];
                    $title = trim(preg_replace('/\(\s*(\d{4})\s*\)/', '', $titleText));
                } else {
                    $title = $titleText;
                }
            }

            $proxyImageUrl = 'https://' . $_SERVER['HTTP_HOST'] . '/image-proxy.php?url=' . urlencode(trim($imageUrl));
            $jsonData = [
                'href' => $link ? $link->href : '',
                'image' => trim($imageUrl),//$proxyImageUrl,
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