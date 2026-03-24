<?php
function tvdbMoviesHome($url) {
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
        $apiUrl = "https://api.themoviedb.org/3/search/movie?query=" . urlencode($searchQuery) . "&include_adult=false&language=ar&page={$page}";
    } else {
        $apiUrl = "https://api.themoviedb.org/3/discover/movie?include_adult=false&include_video=false&language=ar&page={$page}&sort_by=popularity.desc";
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
                'episode' => $item['vote_average'],
                'category' => 'Movie',
                'title' => $item['title'] ?: $item['original_title'],
                'description' => $item['release_date'] ? substr($item['release_date'], 0, 4) : '',
                'genres' => []
            ];
        }
    }

    return json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
}

function tvdbMoviesServers($id) {
    return [
        ['name' => 'Server VidKing', 'link' => "https://www.vidking.net/embed/movie/{$id}"],
        ['name' => 'Server Vidsrc CC', 'link' => "https://vidsrc.cc/v2/embed/movie/{$id}"],
        ['name' => 'Server Vidsrc ME', 'link' => "https://vidsrc.me/embed/movie/{$id}"],
        ['name' => 'Server Videasy', 'link' => "https://player.videasy.net/movie/{$id}"]
    ];
}

function tvdbMoviesListings($id) {
    return[
        "seasons" => [],
        "episodes" => []
    ];
}
?>