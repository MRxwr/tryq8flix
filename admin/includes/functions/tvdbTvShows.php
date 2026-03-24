<?php
function tvdbTvShowsHome($url) {
    $token = 'eyJhbGciOiJIUzI1NiJ9.eyJhdWQiOiI1MGJjZWE4ZDI5YmNlOTkzZjBiZDJjNzVjOGE0OGVjMiIsIm5iZiI6MTcyMDEzMjA4My4yOSwic3ViIjoiNjY4NzIxZjNhMTM1MjQyZWQ5MjY1ZmZhIiwic2NvcGVzIjpbImFwaV9yZWFkIl0sInZlcnNpb24iOjF9.l66VL660inwA8lNw9mzA7RBOvcWuVi_0f0fmw9FfhIM';
    
    $page = 1;
    if (preg_match('/page=(\d+)/', $url, $matches)) {
        $page = $matches[1];
    }

    $searchQuery = '';
    if (preg_match('/search=([^&]+)/', $url, $matches)) {
        $searchQuery = urldecode($matches[1]);
    }

    if (!empty($searchQuery)) {
        $apiUrl = "https://api.themoviedb.org/3/search/tv?query=" . urlencode($searchQuery) . "&include_adult=false&language=ar&page={$page}";
    } else {
        $apiUrl = "https://api.themoviedb.org/3/discover/tv?include_adult=false&include_null_first_air_dates=false&language=ar&page={$page}&sort_by=popularity.desc";
    }
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $apiUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Authorization: Bearer " . $token,
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
    if (!is_numeric($id)) {
        // Handle if full URL was passed
        if (preg_match('/\/tv\/(\d+)/', $id, $matches)) {
            $id = $matches[1];
        } else {
            return ['seasons' => [], 'episodes' => []];
        }
    }

    $token = 'eyJhbGciOiJIUzI1NiJ9.eyJhdWQiOiI1MGJjZWE4ZDI5YmNlOTkzZjBiZDJjNzVjOGE0OGVjMiIsIm5iZiI6MTcyMDEzMjA4My4yOSwic3ViIjoiNjY4NzIxZjNhMTM1MjQyZWQ5MjY1ZmZhIiwic2NvcGVzIjpbImFwaV9yZWFkIl0sInZlcnNpb24iOjF9.l66VL660inwA8lNw9mzA7RBOvcWuVi_0f0fmw9FfhIM';
    $apiUrl = "https://api.themoviedb.org/3/tv/{$id}?language=ar";
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $apiUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Authorization: Bearer " . $token,
        "accept: application/json"
    ]);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    $response = curl_exec($ch);
    curl_close($ch);

    $result = json_decode($response, true);
    $seasonsData = [];
    $episodesData = [];

    if (isset($result['seasons'])) {
        foreach ($result['seasons'] as $season) {
            $seasonsData[] = [
                'link' => "{$id}/season/{$season['season_number']}",
                'title' => $season['name'],
                'season_number' => $season['season_number'],
                'season_text' => "Season " . $season['season_number'],
                'poster' => "https://image.tmdb.org/t/p/w500" . $season['poster_path']
            ];
        }
    }
    
    // If season is requested via more_link format "ID/season/N"
    if (strpos($_GET['href'], 'season/') !== false) {
        $parts = explode('/', $_GET['href']);
        $seasonNum = end($parts);
        $id = $parts[0];
        
        $epApiUrl = "https://api.themoviedb.org/3/tv/{$id}/season/{$seasonNum}?language=ar";
        curl_setopt($ch, CURLOPT_URL, $epApiUrl);
        $epResponse = curl_exec($ch);
        $epResult = json_decode($epResponse, true);
        
        if (isset($epResult['episodes'])) {
            foreach ($epResult['episodes'] as $ep) {
                $episodesData[] = [
                    'link' => "{$id}/{$seasonNum}/{$ep['episode_number']}",
                    'title' => "الحلقة " . $ep['episode_number'] . " - " . $ep['name'],
                    'episode_number' => $ep['episode_number'],
                    'episode_text' => $ep['episode_number'],
                    'poster' => "https://image.tmdb.org/t/p/w500" . $ep['still_path']
                ];
            }
        }
    }

    return [
        'seasons' => $seasonsData,
        'episodes' => $episodesData
    ];
}

function tvdbTvShowsServers($id_info) {
    // Expect format: "ID/Season/Episode"
    $parts = explode('/', $id_info);
    if (count($parts) < 3) return [];
    
    $id = $parts[0];
    $s = $parts[1];
    $e = $parts[2];

    return [
        ['name' => 'Server VidKing', 'link' => "https://www.vidking.net/embed/tv/{$id}/{$s}/{$e}"],
        ['name' => 'Server Vidsrc CC', 'link' => "https://vidsrc.cc/v2/embed/tv/{$id}/{$s}/{$e}"],
        ['name' => 'Server Vidsrc ME', 'link' => "https://vidsrc.me/embed/tv/{$id}/{$s}/{$e}"],
        ['name' => 'Server Videasy', 'link' => "https://player.videasy.net/tv/{$id}/{$s}/{$e}"]
    ];
}
?>