<?php
function anime4upHome($url) {
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
var_dump($html);
    $dom = str_get_html($html);
    $data = [
        'shows' => []
    ];
    if ($dom) {
        $items = $dom->find('div.anime-card-container');
        if ($items) {
            foreach ($items as $item) {
                // Link (Episode link)
                $epNumDiv = $item->find('div.ep_num a', 0);
                $href = $epNumDiv ? $epNumDiv->href : '';
                
                // Image
                $img = $item->find('div.anime-card-poster img', 0);
                $image = '';
                if ($img) {
                    if ($img->getAttribute('data-image')) {
                        $image = $img->getAttribute('data-image');
                    } elseif ($img->src) {
                        $image = $img->src;
                    }
                }

                // Title
                $title = '';
                $h3 = $item->find('div.anime-card-title h3 a', 0);
                if ($h3) {
                    $title = trim($h3->plaintext);
                }

                // Category
                $category = '';
                $typeDiv = $item->find('div.anime-card-type a', 0);
                if ($typeDiv) {
                    $category = trim($typeDiv->plaintext);
                }

                // Episode
                $episode = '';
                if ($epNumDiv) {
                    $episode = trim($epNumDiv->plaintext);
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

function anime4upListings($url) {
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

    $data = [
        'seasons' => $seasonsData,
        'episodes' => $episodesData
    ];
    $htmlDom->clear();
	unset($htmlDom);
    return $data;
}

function anime4upServers($url) {
    $html = curlCall("{$url}watch");
    $dom = str_get_html($html);
    $servers = [];
    if ($dom) {
        $lis = $dom->find('div.watch--servers--list ul li.server--item');
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
        $dom->clear();
        unset($dom);
    } else {
        echo 'Error: Invalid DOM object.';
    }
    return $servers;
}
?>