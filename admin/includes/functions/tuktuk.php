<?php
function tuktukHome($url) {
    $url = trim($url);
    $url = str_replace(' ', '+', $url);
    $html = curlCall($url);
    $dom = str_get_html($html);
    $data = [
        'shows' => []
    ];
    if ($dom) {
        $list = $dom->find('ul.Blocks--List', 0);
        if ($list) {
            foreach ($list->find('div.Block--Item') as $item) {
                $a = $item->find('a', 0);
                $href = $a ? $a->href : '';
                $img = $item->find('img', 0);
                $image = $img && $img->getAttribute('data-src') ? $img->getAttribute('data-src') : '';
                $genres = [];
                $genresList = $item->find('ul.Genres', 0);
                if ($genresList) {
                    foreach ($genresList->find('li') as $li) {
                        $genres[] = trim($li->plaintext);
                    }
                }
                $title = '';
                $h3 = $item->find('h3', 0);
                if ($h3) {
                    $title = trim($h3->plaintext);
                }
                // Keep same array keys, fill missing with empty string/array
                $jsonData = [
                    'href' => $href,
                    'image' => 'https://' . $_SERVER['HTTP_HOST'] . '/image-proxy.php?url=' . urlencode(trim($image)),
                    'episode' => '',
                    'category' => '',
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

function tuktukListings($url) {
    $html = curlCall($url);
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

function tuktukServers($url) {
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