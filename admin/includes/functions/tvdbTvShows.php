<?php
function tvdbTvShowsHome($url) {
    GLOBAL $tvdbToken;    
    $page = 1;
    if (preg_match('/page=(\d+)/', $url, $matches)) {
        $page = $matches[1];
    }

    $searchQuery = '';
    if (preg_match('/search=([^&]+)/', $url, $matches)) {
        $searchQuery = urldecode($matches[1]);
    }

    if (!empty($searchQuery)) {
        $apiUrl = "https://api.themoviedb.org/3/search/tv?query=" . urlencode($searchQuery) . "&include_adult=false&language=en&page={$page}";
    } else {
        $apiUrl = "https://api.themoviedb.org/3/discover/tv?include_adult=false&include_null_first_air_dates=false&language=en&page={$page}&sort_by=popularity.desc";
    }
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $apiUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Authorization: Bearer " . $tvdbToken,
        "accept: application/json"
    ]);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    $response = curl_exec($ch);
    curl_close($ch);

    $result = json_decode($response, true);
    $data = ['shows' => []];

    if (isset($result['results'])) {
        foreach ($result['results'] as $item) {
            $data['shows'][] = [
                'href' => $item['id'],
                'image' => "https://image.tmdb.org/t/p/w500" . $item['poster_path'],
                'backdrop' => "https://image.tmdb.org/t/p/original" . $item['backdrop_path'],
                'episode' => $item['vote_average'],
                'category' => 'TV Show',
                'title' => $item['name'] ?: $item['original_name'],
                'description' => $item['first_air_date'] ? substr($item['first_air_date'], 0, 4) : '',
                'genres' => []
            ];
        }
    }

    return json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
}

function tvdbTvShowsListings($id) {
    GLOBAL $tvdbToken;
    // If we're coming from the Home/Search $id is just the Show ID.
    // If we're clicking a Season, $id will be "ShowID/season/N".
    $showId = $id; 
    $requestedSeason = null;

    if (strpos($id, 'season/') !== false) {
        $parts = explode('/', $id);
        $showId = $parts[0];
        $requestedSeason = end($parts);
    }
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Authorization: Bearer " . $tvdbToken,
        "accept: application/json"
    ]);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

    $seasonsData = [];
    $episodesData = [];

    // 1. Always get Show Info to get list of Seasons
    $apiUrl = "https://api.themoviedb.org/3/tv/{$showId}?language=en";
    curl_setopt($ch, CURLOPT_URL, $apiUrl);
    $response = curl_exec($ch);
    $result = json_decode($response, true);

    if (isset($result['seasons'])) {
        foreach ($result['seasons'] as $season) {
            $seasonsData[] = [
                'link' => "{$showId}/season/{$season['season_number']}",
                'title' => $season['name'] ?: "Season " . $season['season_number'],
                'season_number' => $season['season_number'],
                'season_text' => "Season " . $season['season_number'],
                'poster' => $season['poster_path'] ? "https://image.tmdb.org/t/p/w500" . $season['poster_path'] : ""
            ];
        }

        // Determine which season to load episodes for
        $seasonToLoad = $requestedSeason;
        if ($seasonToLoad === null && !empty($result['seasons'])) {
            // Get the last season number from the seasons list
            $lastSeason = end($result['seasons']);
            $seasonToLoad = $lastSeason['season_number'];
        }

        // Fetch episodes for the determined season
        if ($seasonToLoad !== null) {
            $epApiUrl = "https://api.themoviedb.org/3/tv/{$showId}/season/{$seasonToLoad}?language=en";
            curl_setopt($ch, CURLOPT_URL, $epApiUrl);
            $epResponse = curl_exec($ch);
            $epResult = json_decode($epResponse, true);
            
            if (isset($epResult['episodes'])) {
                foreach ($epResult['episodes'] as $ep) {
                    $episodesData[] = [
                        'link' => "{$showId}/{$seasonToLoad}/{$ep['episode_number']}",
                        'title' => ( isset($ep['name'] ) ? $ep['name'] : "الحلقة " . $ep['episode_number']),
                        'episode_number' => $ep['episode_number'],
                        'episode_text' => $ep['episode_number'],
                        'poster' => $ep['still_path'] ? "https://image.tmdb.org/t/p/w500" . $ep['still_path'] : ""
                    ];
                }
            }
        }
    }

    curl_close($ch);

    return [
        'seasons' => $seasonsData,
        'episodes' => $episodesData,
        'backdrop' => isset($result['backdrop_path']) ? "https://image.tmdb.org/t/p/original" . $result['backdrop_path'] : ""
    ];
}

function tvdbTvShowsServers($id_info) {
    GLOBAL $tvdbToken;
    // Expect format: "ID/Season/Episode"
    $parts = explode('/', $id_info);
    if (count($parts) < 3) return [];
    
    $id = $parts[0];
    $s = $parts[1];
    $e = $parts[2];

    $ch = curl_init();
    $apiUrl = "https://api.themoviedb.org/3/tv/{$id}?language=en";
    curl_setopt($ch, CURLOPT_URL, $apiUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Authorization: Bearer " . $tvdbToken,
        "accept: application/json"
    ]);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    $response = curl_exec($ch);
    curl_close($ch);
    $res = json_decode($response, true);
    $backdrop = (isset($res['backdrop_path']) && !empty($res['backdrop_path'])) ? "https://image.tmdb.org/t/p/original" . $res['backdrop_path'] : "";

    return [
        ['name' => 'Server VidKing', 'link' => "https://www.vidking.net/embed/tv/{$id}/{$s}/{$e}", 'backdrop' => $backdrop],
        ['name' => 'Server Vidsrc CC', 'link' => "https://vidsrc.cc/v2/embed/tv/{$id}/{$s}/{$e}", 'backdrop' => $backdrop],
        ['name' => 'Server Vidsrc ME', 'link' => "https://vidsrc.me/embed/tv/{$id}/{$s}/{$e}", 'backdrop' => $backdrop],
        ['name' => 'Server Videasy', 'link' => "https://player.videasy.net/tv/{$id}/{$s}/{$e}", 'backdrop' => $backdrop]
    ];
} 
?>