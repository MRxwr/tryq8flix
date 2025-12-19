<?php

// ============================================================================
// CACHING CONFIGURATION FOR LARGE ANIME SERIES (1000+ episodes like One Piece)
// ============================================================================
define('ANIME_CACHE_DIR', __DIR__ . '/cache/');
define('ANIME_CACHE_EXPIRY', 3600); // Cache expires in 1 hour (3600 seconds)

/**
 * Initialize cache directory if it doesn't exist
 */
function initAnimeCacheDir() {
    if (!file_exists(ANIME_CACHE_DIR)) {
        mkdir(ANIME_CACHE_DIR, 0755, true);
    }
}

/**
 * Get cached data if valid
 * @param string $cacheKey Unique cache identifier
 * @return array|null Returns cached data or null if expired/missing
 */
function getAnimeCache($cacheKey) {
    initAnimeCacheDir();
    $cacheFile = ANIME_CACHE_DIR . md5($cacheKey) . '.json';
    
    if (file_exists($cacheFile)) {
        $cacheData = json_decode(file_get_contents($cacheFile), true);
        if ($cacheData && isset($cacheData['timestamp'])) {
            // Check if cache is still valid
            if (time() - $cacheData['timestamp'] < ANIME_CACHE_EXPIRY) {
                return $cacheData['data'];
            }
        }
    }
    return null;
}

/**
 * Save data to cache
 * @param string $cacheKey Unique cache identifier
 * @param mixed $data Data to cache
 */
function setAnimeCache($cacheKey, $data) {
    initAnimeCacheDir();
    $cacheFile = ANIME_CACHE_DIR . md5($cacheKey) . '.json';
    $cacheData = [
        'timestamp' => time(),
        'data' => $data
    ];
    file_put_contents($cacheFile, json_encode($cacheData, JSON_UNESCAPED_UNICODE));
}

/**
 * Clear cache for a specific key or all cache
 * @param string|null $cacheKey Specific key to clear, or null to clear all
 */
function clearAnimeCache($cacheKey = null) {
    initAnimeCacheDir();
    if ($cacheKey) {
        $cacheFile = ANIME_CACHE_DIR . md5($cacheKey) . '.json';
        if (file_exists($cacheFile)) {
            unlink($cacheFile);
        }
    } else {
        // Clear all cache files
        $files = glob(ANIME_CACHE_DIR . '*.json');
        foreach ($files as $file) {
            unlink($file);
        }
    }
}

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

/**
 * Get anime listings with caching and optional pagination
 * @param string $url The anime page URL
 * @param int|null $page Page number (null for all episodes, or 1-based page number)
 * @param int $perPage Episodes per page (default 50 for faster loading)
 * @param bool $forceRefresh Force refresh cache
 * @return array Contains seasons, episodes, and pagination info
 */
function animePecListings($url, $page = null, $perPage = 50, $forceRefresh = false) {
    $cacheKey = 'listings_' . $url;
    
    // Try to get from cache first (unless force refresh)
    if (!$forceRefresh) {
        $cachedData = getAnimeCache($cacheKey);
        if ($cachedData) {
            // Return paginated data if page is specified
            if ($page !== null) {
                return paginateEpisodes($cachedData, $page, $perPage);
            }
            return $cachedData;
        }
    }
    
    // Use a custom curl call with a fixed User-Agent
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36");
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
    curl_setopt($ch, CURLOPT_TIMEOUT, 120); // Increased timeout for large pages
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
        'episodes' => $episodesData,
        'total_episodes' => count($episodesData)
    ];
    
    $htmlDom->clear();
	unset($htmlDom);
    
    // Cache the full data for future requests
    setAnimeCache($cacheKey, $data);
    
    // Return paginated data if page is specified
    if ($page !== null) {
        return paginateEpisodes($data, $page, $perPage);
    }
    
    return $data;
}

/**
 * Paginate episodes for faster loading
 * @param array $data Full data with episodes
 * @param int $page Current page (1-based)
 * @param int $perPage Episodes per page
 * @return array Data with paginated episodes and pagination info
 */
function paginateEpisodes($data, $page, $perPage) {
    $totalEpisodes = count($data['episodes']);
    $totalPages = ceil($totalEpisodes / $perPage);
    $page = max(1, min($page, $totalPages)); // Clamp page to valid range
    
    $offset = ($page - 1) * $perPage;
    $paginatedEpisodes = array_slice($data['episodes'], $offset, $perPage);
    
    return [
        'seasons' => $data['seasons'],
        'episodes' => $paginatedEpisodes,
        'pagination' => [
            'current_page' => $page,
            'per_page' => $perPage,
            'total_episodes' => $totalEpisodes,
            'total_pages' => $totalPages,
            'has_next' => $page < $totalPages,
            'has_prev' => $page > 1
        ]
    ];
}

/**
 * Get episodes count without loading all episode details
 * Useful for showing total count before loading paginated data
 * @param string $url The anime page URL
 * @return int Total episode count
 */
function animePecGetEpisodeCount($url) {
    $cacheKey = 'listings_' . $url;
    $cachedData = getAnimeCache($cacheKey);
    
    if ($cachedData && isset($cachedData['total_episodes'])) {
        return $cachedData['total_episodes'];
    }
    
    // If not cached, we need to load it (will be cached for next time)
    $data = animePecListings($url);
    return count($data['episodes']);
}

/**
 * Get specific episode range by episode number (not pagination)
 * Useful for jumping to a specific episode range like 900-950
 * @param string $url The anime page URL  
 * @param int $startEp Starting episode number
 * @param int $endEp Ending episode number
 * @return array Filtered episodes in the range
 */
function animePecGetEpisodeRange($url, $startEp, $endEp) {
    $data = animePecListings($url); // Uses cache if available
    
    $filteredEpisodes = array_filter($data['episodes'], function($ep) use ($startEp, $endEp) {
        $epNum = intval($ep['episode_number']);
        return $epNum >= $startEp && $epNum <= $endEp;
    });
    
    return [
        'seasons' => $data['seasons'],
        'episodes' => array_values($filteredEpisodes),
        'range' => [
            'start' => $startEp,
            'end' => $endEp,
            'total_in_range' => count($filteredEpisodes),
            'total_episodes' => count($data['episodes'])
        ]
    ];
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
            foreach ($watchList->find('li') as $li) {
                $link = $li->getAttribute('data-watch');
                $name = '';
                $nameSpan = $li->find('span#serverName', 0);
                if ($nameSpan) {
                    $name = trim($nameSpan->plaintext);
                }
                
                if ($link) {
                    // Remove the embed6.blogspot.com wrapper
                    $link = str_replace('https://embed6.blogspot.com/?url=', '', $link);
                    
                    $servers[] = [
                        'name' => $name,
                        'link' => $link
                    ];
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