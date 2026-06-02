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
            $titleAnchor = $item->find('.anime-card-title h3 a', 0);
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

    // Scrape episodes (new structure)
    $episodesList = $htmlDom->find('ul#ULEpisodesList', 0);
    if ($episodesList) {
        foreach ($episodesList->find('li a') as $episodeLink) {
            $onclick = $episodeLink->getAttribute('onclick');
            $link = '';
            if (preg_match("/openEpisode\('([^']+)'\)/", $onclick, $matches)) {
                $link = base64_decode($matches[1]);
            }
            $title = trim($episodeLink->plaintext);
            $episodeNumber = '';
            $episodeNumberDigits = '';
            if (preg_match('/(\d+)/u', $title, $matches)) {
                $episodeNumber = $matches[1];
                $episodeNumberDigits = $episodeNumber;
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

    $data = [
        'seasons' => $seasonsData,
        'episodes' => $episodesData
    ];
    $htmlDom->clear();
	unset($htmlDom);
    return $data;
}

function witanimeServers($url) {
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