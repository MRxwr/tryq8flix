<?php
function animeSlayerHome($url) {
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
        $items = $dom->find('div.bsx');
        if ($items) {
            foreach ($items as $item) {
                $a = $item->find('a', 0);
                $href = $a ? $a->href : '';
                
                $img = $item->find('img', 0);
                $image = '';
                if ($img) {
                    if ($img->src && strpos($img->src, 'data:image') === false) {
                        $image = $img->src;
                    } elseif ($img->getAttribute('data-src')) {
                        $image = $img->getAttribute('data-src');
                    }
                }

                $title = '';
                $h2 = $item->find('h2', 0);
                if ($h2) {
                    $title = trim($h2->plaintext);
                }

                $category = '';
                $typez = $item->find('div.typez', 0);
                if ($typez) {
                    $category = trim($typez->plaintext);
                }

                $episode = '';
                $epx = $item->find('span.epx', 0);
                if ($epx) {
                    $episode = trim($epx->plaintext);
                }

                $genres = [];

                $jsonData = [
                    'href' => $href,
                    'image' => trim($image),
                    'episode' => $episode,
                    'category' => $category,
                    'title' => $title,
                    'description' => '',
                    'genres' => $genres
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

function animeSlayerListings($url) {
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

    // Check for the noscript tag content (New Structure)
    $noscript = $htmlDom->find('noscript#diplayer', 0);
    if ($noscript) {
        $innerHtml = html_entity_decode($noscript->innertext);
        $innerDom = str_get_html($innerHtml);
        
        // Episodes
        $epList = $innerDom->find('#EpList1', 0);
        if ($epList) {
            $index = 0;
            foreach($epList->find('div.CSB') as $epDiv) {
                $title = trim($epDiv->plaintext);
                // Extract number
                $epNum = filter_var($title, FILTER_SANITIZE_NUMBER_INT);
                
                // Construct link with index. 
                $separator = (parse_url($url, PHP_URL_QUERY) == NULL) ? '?' : '&';
                $episodeLink = $url . $separator . 'ep_index=' . $index;

                $episodesData[] = [
                    'link' => $episodeLink,
                    'title' => $title,
                    'episode_number' => $epNum,
                    'episode_text' => $epNum,
                    'poster' => ''
                ];
                $index++;
            }
        }
        $innerDom->clear();
        unset($innerDom);
    } else {
        // Fallback to old structure
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
        $episodesRow = $htmlDom->find('section.allepcont .row', 0);
        if ($episodesRow) {
            foreach ($episodesRow->find('a') as $episodeLink) {
                $link = $episodeLink->href;
                $img = $episodeLink->find('img', 0);
                $poster = $img && $img->getAttribute('data-src') ? $img->getAttribute('data-src') : '';
                $epInfo = $episodeLink->find('.ep-info h2', 0);
                $title = $epInfo ? trim($epInfo->plaintext) : '';
                $epnumDiv = $episodeLink->find('.epnum', 0);
                $episodeNumber = '';
                $episodeNumberDigits = '';
                if ($epnumDiv) {
                    // Extract number from text
                    if (preg_match('/(\d+)/u', $epnumDiv->plaintext, $matches)) {
                        $episodeNumber = $matches[1];
                        $episodeNumberDigits = $episodeNumber;
                    }
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
    }

    $data = [
        'seasons' => $seasonsData,
        'episodes' => $episodesData
    ];
    $htmlDom->clear();
	unset($htmlDom);
    return $data;
}

function animeSlayerServers($url) {
    // Parse URL to get index
    $parts = parse_url($url);
    parse_str($parts['query'] ?? '', $query);
    $epIndex = isset($query['ep_index']) ? intval($query['ep_index']) : 0;

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
    $servers = [];
    
    $noscript = $htmlDom->find('noscript#diplayer', 0);
    if ($noscript) {
        $innerHtml = html_entity_decode($noscript->innertext);
        $innerDom = str_get_html($innerHtml);
        
        $serverList = $innerDom->find('#ServerList1', 0);
        if ($serverList) {
            // Find the specific div for this episode index
            $serverDivs = $serverList->find('div.divv11');
            if (isset($serverDivs[$epIndex])) {
                $targetDiv = $serverDivs[$epIndex];
                foreach($targetDiv->find('li') as $li) {
                    $type = $li->getAttribute('type');
                    $data = $li->getAttribute('data');
                    $quality = $li->getAttribute('quality-data');
                    
                    $link = '';
                    switch(strtolower($type)) {
                        case 'videa':
                            $link = "https://videa.hu/player?v={$data}";
                            break;
                        case 'dailymotion':
                            $link = "https://dailymotion.com/embed/video/{$data}";
                            break;
                        case 'ok':
                             $link = "https://www.ok.ru/videoembed/{$data}";
                             break;
                        case 'mega':
                            $link = "https://mega.nz/embed/{$data}";
                            break;
                        case 'mp4upload':
                            $link = "https://www.mp4upload.com/embed-{$data}.html";
                            break;
                        case 'highload':
                            $link = "https://highload.to/v/{$data}";
                            break;
                        case 'gdrive':
                        case 'google drive':
                        case 'drive':
                            $link = "https://drive.google.com/file/d/{$data}/preview";
                            break;
                    }
                    
                    if ($link) {
                        $servers[] = [
                            'name' => strtoupper($type) . ($quality ? " - $quality" : ""),
                            'link' => $link
                        ];
                    }
                }
            }
        }
        $innerDom->clear();
        unset($innerDom);
    } else {
        // Fallback to old method
        if ($htmlDom) {
            $lis = $htmlDom->find('div.watch--servers--list ul li.server--item');
            foreach ($lis as $li) {
                $dataLink = $li->getAttribute('data-link');
                $nameSpan = $li->find('span', 0);
                $name = $nameSpan ? trim($nameSpan->plaintext) : '';
                $decodedLink = '';
                if ($dataLink) {
                    // PHP equivalent of decodeLink JS function
                    $split = explode('0REL0Y&', $dataLink);
                    $part = $split[0];
                    $reversed = strrev($part);
                    $decodedLink = base64_decode($reversed);
                }
                $servers[] = [
                    'name' => $name,
                    'link' => $decodedLink
                ];
            }
        }
    }
    
    if ($htmlDom) { $htmlDom->clear(); unset($htmlDom); }
    return $servers;
}
?>