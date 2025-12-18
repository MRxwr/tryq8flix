<?php
function qessetHome($url) {
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
        // Loop through each article.post
        foreach ($dom->find('article.post') as $article) {
            // Find the block-post div
            $blockPost = $article->find('div.block-post', 0);
            if ($blockPost) {
                    $a = $blockPost->find('a', 0);
                    $href = $a ? $a->href : '';
                    $titleAttr = $a ? $a->getAttribute('title') : '';
                    
                    // Get episode number
                    $episode = '';
                    $episodeNumDiv = $blockPost->find('div.episodeNum', 0);
                    if ($episodeNumDiv) {
                        $episodeSpans = $episodeNumDiv->find('span');
                        if (count($episodeSpans) >= 2) {
                            $episode = trim($episodeSpans[1]->plaintext);
                        }
                    }
                    
                    // Get image from background-image style
                    $image = '';
                    $imgBg = $blockPost->find('div.imgBg', 0);
                    if ($imgBg) {
                        $style = $imgBg->getAttribute('style');
                        if (preg_match('/background-image:\s*url\((.*?)\)/i', $style, $matches)) {
                            $image = trim($matches[1]);
                        }
                    }
                    
                    // Get title
                    $title = '';
                    $titleDiv = $blockPost->find('div.title', 0);
                    if ($titleDiv) {
                        $title = trim($titleDiv->plaintext);
                    }
                    
                    $jsonData = [
                        'href' => $href,
                        'image' => trim($image),
                        'episode' => $episode,
                        'category' => '',
                        'title' => $title,
                        'description' => $titleAttr,
                        'genres' => []
                    ];
                    $data['shows'][] = $jsonData;
            }
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

function qessetListings($url) {
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
    foreach ($htmlDom->find('article.postEp') as $article) {
        $blockPost = $article->find('div.block-post', 0);
        if ($blockPost) {
            $a = $blockPost->find('a', 0);
            $link = $a ? $a->href : '';
            $titleAttr = $a ? $a->getAttribute('title') : '';
            
            // Get episode number
            $episodeNumber = '';
            $episodeNumberDigits = '';
            $episodeNumDiv = $blockPost->find('div.episodeNum', 0);
            if ($episodeNumDiv) {
                $episodeSpans = $episodeNumDiv->find('span');
                if (count($episodeSpans) >= 2) {
                    $episodeNumber = trim($episodeSpans[1]->plaintext);
                    $episodeNumberDigits = $episodeNumber;
                }
            }
            
            // Get image from background-image style
            $poster = '';
            $imgSer = $blockPost->find('div.imgSer', 0);
            if ($imgSer) {
                $style = $imgSer->getAttribute('style');
                if (preg_match('/background-image:\s*url\((.*?)\)/i', $style, $matches)) {
                    $poster = trim($matches[1]);
                }
            }
            
            // Get title
            $title = '';
            $titleDiv = $blockPost->find('div.title', 0);
            if ($titleDiv) {
                $title = trim($titleDiv->plaintext);
            }
            
            $episodesData[] = [
                'link' => $link,
                'title' => $title,
                'episode_number' => $episodeNumberDigits,
                'episode_text' => $episodeNumber,
                'poster' => $poster
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

function qessetServers($url) {
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