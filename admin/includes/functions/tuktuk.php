<?php
function tuktukHome($url) {
    $url = trim($url);
    $url = str_replace(' ', '+', $url);
    echo $url;
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
                    'image' => $image,
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

    // Scrape seasons
    foreach ($htmlDom->find('section.allseasonss ul.Blocks--List .Small--Box') as $seasonBox) {
        $a = $seasonBox->find('a', 0);
        $link = $a ? $a->href : '';
        $epnumDiv = $seasonBox->find('.epnum', 0);
        $seasonNumber = $epnumDiv ? trim($epnumDiv->plaintext) : '';
        $seasonNumberDigits = preg_replace('/[^0-9]/', '', $seasonNumber);
        $innerTitle = $seasonBox->find('inner--title h2', 0);
        $title = $innerTitle ? trim($innerTitle->plaintext) : '';
        $poster = '';
        $img = $seasonBox->find('img', 0);
        if ($img && $img->getAttribute('data-src')) {
            $poster = $img->getAttribute('data-src');
        }
        $seasonsData[] = [
            'link' => $link,
            'title' => $title,
            'season_number' => $seasonNumberDigits,
            'season_text' => $seasonNumber,
            'poster' => $poster
        ];
    }

    // Scrape episodes
    foreach ($htmlDom->find('section.allepcont .row a') as $episodeLink) {
        $link = $episodeLink->href;
        $epInfo = $episodeLink->find('.ep-info h2', 0);
        $title = $epInfo ? trim($epInfo->plaintext) : '';
        $epnumDiv = $episodeLink->find('.epnum', 0);
        $episodeNumber = $epnumDiv ? trim($epnumDiv->plaintext) : '';
        $episodeNumberDigits = preg_replace('/[^0-9]/', '', $episodeNumber);
        $poster = '';
        $img = $episodeLink->find('img', 0);
        if ($img && $img->getAttribute('data-src')) {
            $poster = $img->getAttribute('data-src');
        }
        $episodesData[] = [
            'link' => $link,
            'title' => $title,
            'episode_number' => $episodeNumberDigits,
            'episode_text' => $episodeNumber,
            'poster' => $poster
        ];
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
        $lis = $dom->find('.ServersList ul#watch li');
        $count = 0;
        foreach ($lis as $li) {
            if ($count >= 5) break;
            $link = $li->getAttribute('data-watch');
            $name = trim($li->plaintext);
            $iframeSrc = '';
            if ($link) {
                $serverHtml = curlCall($link);
                $serverDom = str_get_html($serverHtml);
                if ($serverDom) {
                    $iframe = $serverDom->find('iframe', 0);
                    if ($iframe && $iframe->getAttribute('src')) {
                        $iframeSrc = $iframe->getAttribute('src');
                    }
                    $serverDom->clear();
                    unset($serverDom);
                }
            }
            $servers[] = [
                'name' => $name,
                'link' => $iframeSrc ? $iframeSrc : $link
            ];
            $count++;
        }
        $dom->clear();
        unset($dom);
    } else {
        echo 'Error: Invalid DOM object.';
    }
    return $servers;
}
?>