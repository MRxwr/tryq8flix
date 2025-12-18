<?php
function esqHome($url) {
    $url = trim($url);
    $url = str_replace(' ', '+', $url);
    
    // Use a custom curl call with a fixed User-Agent to ensure consistency
    // between Postman and Browser requests.
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    // Use a standard Desktop User-Agent
    curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36");
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
    curl_setopt($ch, CURLOPT_TIMEOUT, 60);
    $html = curl_exec($ch);
    curl_close($ch);

    $dom = str_get_html($html);
    $data = [
        'shows' => []
    ];
    if ($dom) {
        // Loop through each li.video-grid
        foreach ($dom->find('li.video-grid') as $li) {
            // Find the thumb div with anchor
            $thumb = $li->find('div.thumb', 0);
            if ($thumb) {
                $a = $thumb->find('a', 0);
                $href = $a ? $a->href : '';
                
                // Get image from data-src attribute
                $image = '';
                $img = $thumb->find('img', 0);
                if ($img) {
                    $image = $img->getAttribute('data-src');
                    if (!$image) {
                        $image = $img->src;
                    }
                }
                
                // Get category
                $category = '';
                $catDiv = $thumb->find('div.cat', 0);
                if ($catDiv) {
                    $category = trim($catDiv->plaintext);
                }
                
                // Get duration (could be used as extra info)
                $duration = '';
                $durationDiv = $thumb->find('div.duration', 0);
                if ($durationDiv) {
                    $duration = trim($durationDiv->plaintext);
                }
            }
            
            // Get title from data section
            $title = '';
            $titleAttr = '';
            $dataDiv = $li->find('div.data', 0);
            if ($dataDiv) {
                $titleH2 = $dataDiv->find('h2.title a', 0);
                if ($titleH2) {
                    $title = trim($titleH2->plaintext);
                }
            }
            
            // Extract episode number from title if present
            $episode = '';
            if (preg_match('/الحلقة\s+(\d+)/u', $title, $matches)) {
                $episode = $matches[1];
            }
            
            // Use image proxy
            $imageUrl = trim($image);
            if ($imageUrl) {
                $imageUrl = 'https://' . $_SERVER['HTTP_HOST'] . '/image-proxy.php?url=' . urlencode($imageUrl);
            }
            
            $jsonData = [
                'href' => $href,
                'image' => $imageUrl,
                'episode' => $episode,
                'category' => $category,
                'title' => $title,
                'description' => $duration,
                'genres' => []
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

function esqListings($url) {
    // Use a custom curl call with a fixed User-Agent
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36");
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
    curl_setopt($ch, CURLOPT_TIMEOUT, 60);
    $html = curl_exec($ch);
    curl_close($ch);

    $htmlDom = str_get_html($html);
    $seasonsData = [];
    $episodesData = [];

    // Scrape seasons (new structure) 
    $seasonsList = $htmlDom->find('section.allseasonss ul.Blocks--List', 0);
    if ($seasonsList) {
        foreach ($seasonsList->find('div.Block--Item') as $seasonBox) {
            $a = $seasonBox->find('a', 0);
            $link = $a ? $a->href : '';
            $img = $seasonBox->find('img', 0);
            $poster = $img && $img->getAttribute('data-src') ? $img->getAttribute('data-src') : '';
            $title = '';
            $h3 = $seasonBox->find('h3', 0);
            if ($h3) {
                $title = trim($h3->plaintext);
            }
            $seasonNumber = '';
            $seasonNumberDigits = '';
            // Try to extract season number from title if possible
            if (preg_match('/(\d+)/u', $title, $matches)) {
                $seasonNumber = $matches[1];
                $seasonNumberDigits = $seasonNumber;
            }
            $seasonsData[] = [
                'link' => $link,
                'title' => $title,
                'season_number' => $seasonNumberDigits,
                'season_text' => $seasonNumber,
                'poster' => $poster
            ];
        }
    }

    // Scrape episodes (new structure)
    $eplistDiv = $htmlDom->find('div.eplist', 0);
    if ($eplistDiv) {
        foreach ($eplistDiv->find('a.btn.btn-info') as $episodeLink) {
            $link = $episodeLink->href;
            $title = $episodeLink->getAttribute('title');
            $episodeText = trim($episodeLink->plaintext);
            
            // Extract episode number from the link text (e.g., "حلقة 1")
            $episodeNumber = '';
            $episodeNumberDigits = '';
            if (preg_match('/حلقة\s+(\d+)/u', $episodeText, $matches)) {
                $episodeNumber = $matches[1];
                $episodeNumberDigits = $episodeNumber;
            }
            
            $episodesData[] = [
                'link' => $link,
                'title' => $title ? $title : $episodeText,
                'episode_number' => $episodeNumberDigits,
                'episode_text' => $episodeNumber,
                'poster' => ''
            ];
        }
    }

    $data = [
        'seasons' => $seasonsData,
        'episodes' => $episodesData
    ];
    $htmlDom->clear();
	unset($htmlDom);
    return $data;
}

function esqServers($url) {
    $url = trim($url);
    
    // Use curl to fetch the page
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36");
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
    curl_setopt($ch, CURLOPT_TIMEOUT, 60);
    $html = curl_exec($ch);
    curl_close($ch);
    
    $dom = str_get_html($html);
    $servers = [];
    
    if ($dom) {
        // Find the anchor tag with the watch link containing base64 post parameter
        $watchLink = $dom->find('a[href*="watch?post="]', 0);
        
        if ($watchLink) {
            $href = $watchLink->href;
            
            // Add main server with the full encoded link
            $servers[] = [
                'name' => 'main',
                'link' => $href
            ];
            
            // Extract the base64 encoded post parameter for ok.ru server
            if (preg_match('/post=([^&"]+)/', $href, $matches)) {
                $base64Post = $matches[1];
                
                // Decode the base64 string
                $decodedJson = base64_decode($base64Post);
                $postData = json_decode($decodedJson, true);
                
                if ($postData && isset($postData['servers'])) {
                    foreach ($postData['servers'] as $server) {
                        $name = isset($server['name']) ? $server['name'] : '';
                        $id = isset($server['id']) ? $server['id'] : '';
                        $nameLower = strtolower($name);
                        
                        // Only add ok.ru server
                        if (strpos($nameLower, 'ok') !== false) {
                            $servers[] = [
                                'name' => 'ok',
                                'link' => "https://ok.ru/videoembed/{$id}"
                            ];
                            break;
                        }
                    }
                }
            }
        }
        
        $dom->clear();
        unset($dom);
    }
    
    return $servers;
}
?>