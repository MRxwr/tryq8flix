<?php
function animePecHome($url) {
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
        // Try new structure first (Small--Box)
        $items = $dom->find('div.Small--Box');
        if ($items && count($items) > 0) {
            foreach ($items as $item) {
                // Link
                $a = $item->find('a.recent--block', 0);
                $href = $a ? $a->href : '';
                
                // Image
                $img = $item->find('div.Poster img', 0);
                $image = '';
                if ($img) {
                    if ($img->getAttribute('data-src')) {
                        $image = $img->getAttribute('data-src');
                    } elseif ($img->src) {
                        $image = $img->src;
                    }
                }

                // Title
                $title = '';
                $h3 = $item->find('h3.title', 0);
                if ($h3) {
                    $title = trim($h3->plaintext);
                } elseif ($a && $a->title) {
                    $title = trim($a->title);
                }

                // Episode number
                $episode = '';
                $numberDiv = $item->find('div.number em', 0);
                if ($numberDiv) {
                    $episode = trim($numberDiv->plaintext);
                }

                // Genres
                $genres = [];
                $genresList = $item->find('ul.liList li.genre');
                foreach ($genresList as $genreItem) {
                    $genres[] = trim($genreItem->plaintext);
                }

                // Category/Season
                $category = '';
                $seasonLi = $item->find('ul.liList li.anime_season', 0);
                if ($seasonLi) {
                    $category = trim($seasonLi->plaintext);
                }

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
        } else {
            // Fallback to old structure (anime-card-container)
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

function animePecListings($url) {
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

    // Try new structure first (Small--Box Season)
    $seasonsList = $htmlDom->find('section.allseasonss ul.Blocks--List', 0);
    if ($seasonsList) {
        $seasonBoxes = $seasonsList->find('div.Small--Box.Season');
        if ($seasonBoxes && count($seasonBoxes) > 0) {
            // New structure
            foreach ($seasonBoxes as $seasonBox) {
                $a = $seasonBox->find('a', 0);
                $link = $a ? $a->href : '';
                
                // Get title from anchor's title attribute (contains unique season info)
                $title = $a && $a->title ? trim($a->title) : '';
                
                $img = $seasonBox->find('div.Poster img', 0);
                $poster = '';
                if ($img) {
                    if ($img->getAttribute('data-src')) {
                        $poster = $img->getAttribute('data-src');
                    } elseif ($img->src) {
                        $poster = $img->src;
                    }
                }
                
                // Extract season number from div.epnum
                $epnumDiv = $seasonBox->find('div.epnum', 0);
                $seasonNumber = '';
                $seasonNumberDigits = '';
                if ($epnumDiv) {
                    // Look for number after "الموسم"
                    if (preg_match('/(\d+)/u', $epnumDiv->plaintext, $matches)) {
                        $seasonNumber = $matches[1];
                        $seasonNumberDigits = $seasonNumber;
                    }
                }
                
                $seasonsData[] = [
                    'link' => $link,
                    'title' => $title,
                    'season_number' => $seasonNumberDigits,
                    'season_text' => $seasonNumber,
                    'poster' => $poster
                ];
            }
        } else {
            // Fallback to old Block--Item structure
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
    }

    // Try new structure for episodes first (section.allepcont > div.row > a)
    $episodesSection = $htmlDom->find('section.allepcont', 0);
    if ($episodesSection) {
        $episodeLinks = $episodesSection->find('div.row > a');
        if ($episodeLinks && count($episodeLinks) > 0) {
            // New structure
            foreach ($episodeLinks as $episodeLink) {
                $link = $episodeLink->href;
                
                // Get title from anchor's title attribute (contains unique episode info)
                $title = $episodeLink->title ? trim($episodeLink->title) : '';
                
                $img = $episodeLink->find('div.image img', 0);
                $poster = '';
                if ($img) {
                    if ($img->getAttribute('data-src')) {
                        $poster = $img->getAttribute('data-src');
                    } elseif ($img->src) {
                        $poster = $img->src;
                    }
                }
                
                // Extract episode number from div.epnum
                $epnumDiv = $episodeLink->find('div.epnum', 0);
                $episodeNumber = '';
                $episodeNumberDigits = '';
                if ($epnumDiv) {
                    // Look for number after "الحلقة"
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
    
    // Fallback: Try ul#ULEpisodesList structure
    if (empty($episodesData)) {
        $episodesList = $htmlDom->find('ul#ULEpisodesList', 0);
        if ($episodesList) {
            foreach ($episodesList->find('li') as $li) {
                $a = $li->find('a', 0);
                $link = $a ? $a->href : '';
                $title = $a ? trim($a->plaintext) : '';
                
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
    }

    $data = [
        'seasons' => $seasonsData,
        'episodes' => $episodesData
    ];
    $htmlDom->clear();
	unset($htmlDom);
    return $data;
}

function animePecServers($url) {
    // Append /watch to the URL if not already present
    if (strpos($url, '/watch') === false) {
        $url = rtrim($url, '/') . '/watch';
    }
    
    $html = curlCall("{$url}");
    $dom = str_get_html($html);
    $servers = [];
    
    if ($dom) {
        // Try new structure first: ul#watch
        $watchList = $dom->find('ul#watch', 0);
        if ($watchList) {
            // Extract post_id from the li elements
            $firstLi = $watchList->find('li', 0);
            $postId = $firstLi ? $firstLi->getAttribute('data-id') : '';
            
            if ($postId) {
                // API endpoint for fetching server links
                $apiUrl = 'https://y0vx70khe8u.animepec.online/wp-content/themes/animepec%203.1.1/Ajaxt/Single/GetServer.php';
                
                foreach ($watchList->find('li') as $li) {
                    $index = $li->getAttribute('data-index');
                    $type = $li->getAttribute('data-type');
                    $name = '';
                    $nameSpan = $li->find('span#serverName', 0);
                    if ($nameSpan) {
                        $name = trim($nameSpan->plaintext);
                    }
                    
                    if ($index !== null && $type) {
                        // Make POST request to get the actual server link
                        $ch = curl_init();
                        curl_setopt($ch, CURLOPT_URL, $apiUrl);
                        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                        curl_setopt($ch, CURLOPT_POST, true);
                        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
                            'post_id' => $postId,
                            'index' => $index,
                            'type' => $type
                        ]));
                        curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36");
                        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
                        curl_setopt($ch, CURLOPT_TIMEOUT, 60);
                        $response = curl_exec($ch);
                        curl_close($ch);
                        
                        // Parse the response to extract iframe src
                        $responseDom = str_get_html($response);
                        if ($responseDom) {
                            $iframe = $responseDom->find('iframe', 0);
                            if ($iframe) {
                                $iframeSrc = $iframe->src;
                                
                                // Remove the embed6.blogspot.com wrapper and decode URL
                                if (strpos($iframeSrc, 'embed6.blogspot.com/?url=') !== false) {
                                    $iframeSrc = str_replace('https://embed6.blogspot.com/?url=', '', $iframeSrc);
                                    // URL decode the encoded URL
                                    $iframeSrc = urldecode($iframeSrc);
                                }
                                
                                $servers[] = [
                                    'name' => $name,
                                    'link' => $iframeSrc
                                ];
                            }
                            $responseDom->clear();
                            unset($responseDom);
                        }
                    }
                }
            }
        }
        
        // Fallback: Try ul#episode-servers structure
        if (empty($servers)) {
            $serverList = $dom->find('ul#episode-servers', 0);
            if ($serverList) {
                foreach ($serverList->find('li') as $li) {
                    $link = $li->getAttribute('data-watch');
                    $name = '';
                    $a = $li->find('a', 0);
                    if ($a) {
                        $name = trim($a->plaintext);
                        $name = str_replace('noscript', '', $name);
                        $name = trim($name);
                    }
                    
                    if ($link) {
                        $servers[] = [
                            'name' => $name,
                            'link' => $link
                        ];
                    }
                }
            }
        }
        
        // Fallback to oldest structure
        if (empty($servers)) {
            // Fallback to old structure
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
        }
        $dom->clear();
        unset($dom);
    } else {
        echo 'Error: Invalid DOM object.';
    }
    return $servers;
}
?>