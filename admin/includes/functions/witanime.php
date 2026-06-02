<?php
function witanimeHome($url) {
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
    curl_setopt($ch, CURLOPT_ENCODING, "");
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.7",
        "Accept-Language: en-US,en;q=0.9,ar;q=0.8",
        "Cache-Control: no-cache",
        "Pragma: no-cache",
        "Upgrade-Insecure-Requests: 1"
    ]);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
    curl_setopt($ch, CURLOPT_TIMEOUT, 60);
    $html = curl_exec($ch);
    curl_close($ch);

    $dom = str_get_html($html);
    $data = [
        'shows' => []
    ];
    if ($dom) {
        foreach ($dom->find('div.anime-card-container') as $item) {
            $titleAnchor = $item->find('.episodes-card-title h3 a', 0);
            $href = $titleAnchor ? $titleAnchor->href : '';
            $title = $titleAnchor ? trim($titleAnchor->plaintext) : '';

            $img = $item->find('.anime-card-poster img', 0);
            $image = $img ? $img->src : '';

            $epAnchor = $item->find('.episodes-card-title h3 a', 0);
            $episode = $epAnchor ? trim($epAnchor->plaintext) : '';

            $statusAnchor = $item->find('.anime-card-status a', 0);
            $category = $statusAnchor ? trim($statusAnchor->plaintext) : '';

            $descriptionDiv = $item->find('.anime-card-title', 0);
            $description = $descriptionDiv ? trim($descriptionDiv->getAttribute('data-content')) : '';

            $genres = [];
            
            // Keep same array keys, fill missing with empty string/array
            $posterUrl = 'https://' . $_SERVER['HTTP_HOST'] . '/image-proxy.php?url=' . urlencode(trim($image));
            $jsonData = [
                'href' => $href,
                'image' => trim($posterUrl),
                'episode' => $episode,
                'category' => $category,
                'title' => $title,
                'description' => $description,
                'genres' => $genres
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

function witanimeListings($url) {
    $url = trim($url);
    // Properly encode Arabic/special characters in the URL
    $url_parts = parse_url($url);
    if (isset($url_parts['path'])) {
        $path_segments = explode('/', $url_parts['path']);
        foreach ($path_segments as &$segment) {
            $segment = rawurlencode(rawurldecode($segment));
        }
        $url_parts['path'] = implode('/', $path_segments);
        
        $url = (isset($url_parts['scheme']) ? $url_parts['scheme'] . '://' : '') .
               (isset($url_parts['host']) ? $url_parts['host'] : '') .
               $url_parts['path'] .
               (isset($url_parts['query']) ? '?' . $url_parts['query'] : '');
    }

    // Use a custom curl call with a fixed User-Agent to ensure consistency
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    // Use the same standard Desktop User-Agent as in witanimeHome
    curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36");
    curl_setopt($ch, CURLOPT_ENCODING, "");
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.7",
        "Accept-Language: en-US,en;q=0.9,ar;q=0.8",
        "Cache-Control: no-cache",
        "Pragma: no-cache",
        "Upgrade-Insecure-Requests: 1"
    ]);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
    curl_setopt($ch, CURLOPT_TIMEOUT, 60);
    $html = curl_exec($ch);
    curl_close($ch);

    $htmlDom = str_get_html($html);
    $seasonsData = [];
    $episodesData = [];

    if ($htmlDom) {
        // Scrape seasons (new structure)
        $seasonsList = $htmlDom->find('section.allseasonss ul.Blocks--List', 0);
        if ($seasonsList) {
            foreach ($seasonsList->find('div.Block--Item') as $seasonBox) {
                $a = $seasonBox->find('a', 0);
                $link = $a ? $a->href : '';
                $img = $seasonBox->find('img', 0);
                $poster = $img && $img->getAttribute('data-src') ? $img->getAttribute('data-src') : ($img ? $img->src : '');
                $posterUrl = 'https://' . $_SERVER['HTTP_HOST'] . '/image-proxy.php?url=' . urlencode(trim($poster));
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
                    'poster' => $posterUrl
                ];
            }
        }

        // Scrape episodes (new structure based on user snippet)
        $episodesList = $htmlDom->find('ul#ULEpisodesList', 0);
        if (!$episodesList) {
            $episodesList = $htmlDom->find('ul.all-episodes-list', 0);
        }
        if (!$episodesList) {
            $episodesList = $htmlDom->find('div.all-episodes ul', 0);
        }

        if ($episodesList) {
            foreach ($episodesList->find('li a') as $episodeLink) {
                $title = trim($episodeLink->plaintext);
                $episodeNumber = '';
                $episodeNumberDigits = '';
                
                // Extract number from title (e.g., "الحلقة 1")
                if (preg_match('/(\d+)/u', $title, $matches)) {
                    $episodeNumber = $matches[1];
                    $episodeNumberDigits = $episodeNumber;
                }
                
                // Construct link by replacing the number in the current URL
                // e.g., .../liar-game-الحلقة-8/ -> .../liar-game-الحلقة-1/
                $link = $url;
                if ($episodeNumberDigits !== '') {
                    // This pattern looks for the last sequence of digits in the slug
                    $link = preg_replace('/(\d+)(\/)?$/', $episodeNumberDigits . '$2', rtrim($url, '/'));
                    if (strpos($link, 'episode') === false) {
                        // If current URL is anime page, ensure it points to episode
                        $link = str_replace('/anime/', '/episode/', $link);
                    }
                }

                $episodesData[] = [
                    'link' => $link,
                    'title' => $title,
                    'episode_number' => $episodeNumberDigits,
                    'episode_text' => $episodeNumber,
                    'poster' => ''
                ];
            }
        }
        $htmlDom->clear();
        unset($htmlDom);
    }

    return [
        'seasons' => $seasonsData,
        'episodes' => $episodesData
    ];
}

function witanimeServers($url) {
    $url = trim($url);
    // Properly encode Arabic/special characters in the URL
    $url_parts = parse_url($url);
    if (isset($url_parts['path'])) {
        $path_segments = explode('/', $url_parts['path']);
        foreach ($path_segments as &$segment) {
            $segment = rawurlencode(rawurldecode($segment));
        }
        $url_parts['path'] = implode('/', $path_segments);
        
        $url = (isset($url_parts['scheme']) ? $url_parts['scheme'] . '://' : '') .
               (isset($url_parts['host']) ? $url_parts['host'] : '') .
               $url_parts['path'] .
               (isset($url_parts['query']) ? '?' . $url_parts['query'] : '');
    }

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36");
    curl_setopt($ch, CURLOPT_ENCODING, "");
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.7",
        "Accept-Language: en-US,en;q=0.9,ar;q=0.8",
        "Cache-Control: no-cache",
        "Pragma: no-cache",
        "Upgrade-Insecure-Requests: 1"
    ]);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
    curl_setopt($ch, CURLOPT_TIMEOUT, 60);
    $html = curl_exec($ch);
    curl_close($ch);

    $servers = [];
    if (!$html) return $servers;

    // Extract _zG and _zH from scripts
    $resourceRegistry = [];
    $configRegistry = [];

    if (preg_match('/var\s+_zG\s*=\s*"([^"]+)"/', $html, $matches)) {
        $resourceRegistry = json_decode(base64_decode($matches[1]), true);
    }
    if (preg_match('/var\s+_zH\s*=\s*"([^"]+)"/', $html, $matches)) {
        $configRegistry = json_decode(base64_decode($matches[1]), true);
    }

    if (!empty($resourceRegistry) && !empty($configRegistry)) {
        $FRAMEWORK_HASH = "23a97133-caf3-4eb4-9466-93d0a4ff8198";

        // Logic to decode each server
        $decodedLinks = [];
        foreach ($resourceRegistry as $i => $resourceData) {
            if (!isset($configRegistry[$i])) continue;
            
            $configSettings = $configRegistry[$i];
            
            // reverse string
            $resourceData = strrev($resourceData);

            // keep only base64 chars
            $resourceData = preg_replace('/[^A-Za-z0-9+\/=]/', '', $resourceData);

            // decode base64
            $decoded = base64_decode($resourceData);
            if (!$decoded) continue;

            // offset logic
            $indexKey = (int)base64_decode($configSettings['k']);
            $offset = (int)$configSettings['d'][$indexKey];

            if ($offset > 0) {
                $decoded = substr($decoded, 0, -$offset);
            }

            if (preg_match('/^https:\/\/yonaplay\.net\/embed\.php\?id=\d+$/', $decoded)) {
                $decoded .= '&apiKey=' . $FRAMEWORK_HASH;
            }

            $decodedLinks[] = $decoded;
        }

        // Map server names from the HTML structure
        $dom = str_get_html($html);
        if ($dom) {
            $lis = $dom->find('div.watch--servers--list ul li.server--item');
            foreach ($lis as $index => $li) {
                $nameSpan = $li->find('span', 0);
                $name = $nameSpan ? trim($nameSpan->plaintext) : 'Server ' . ($index + 1);
                
                if (isset($decodedLinks[$index])) {
                    $servers[] = [
                        'name' => $name,
                        'link' => $decodedLinks[$index]
                    ];
                }
            }
            $dom->clear();
            unset($dom);
        }
    }

    return $servers;
}
?>