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
        $apiUrl = "https://api.themoviedb.org/3/search/movie?query=" . urlencode($searchQuery) . "&include_adult=false&language=en&page={$page}";
    } else {
        $apiUrl = "https://api.themoviedb.org/3/discover/movie?include_adult=false&include_video=false&language=en&page={$page}&sort_by=popularity.desc";
        $apiUrl = "https://api.themoviedb.org/3/trending/movie/day?language=en-US&page={$page}";
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
    GLOBAL $tvdbToken;
    $ch = curl_init();
    $apiUrl = "https://api.themoviedb.org/3/movie/{$id}?language=en";
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
    $overview = isset($res['overview']) ? $res['overview'] : "";
    $date = isset($res['release_date']) ? $res['release_date'] : "";

    return [
        ['name' => 'Server VidKing', 'link' => "https://www.vidking.net/embed/movie/{$id}", 'backdrop' => $backdrop, 'overview' => $overview, 'date' => $date],
        ['name' => 'Server Vidsrc CC', 'link' => "https://vidsrc.cc/v2/embed/movie/{$id}", 'backdrop' => $backdrop, 'overview' => $overview, 'date' => $date],
        ['name' => 'Server Vidsrc ME', 'link' => "https://vidsrc.me/embed/movie/{$id}", 'backdrop' => $backdrop, 'overview' => $overview, 'date' => $date],
        ['name' => 'Server Videasy', 'link' => "https://player.videasy.net/movie/{$id}", 'backdrop' => $backdrop, 'overview' => $overview, 'date' => $date],
        ['name' => 'Server Peachify', 'link' => "https://peachify.top/embed/movie/{$id}", 'backdrop' => $backdrop, 'overview' => $overview, 'date' => $date],
        ['name' => 'Server Xpass', 'link' => "https://play.xpass.top/e/movie/{$id}", 'backdrop' => $backdrop, 'overview' => $overview, 'date' => $date]
    ];
}

function tvdbMoviesListings($id) {
    GLOBAL $tvdbToken;
    $ch = curl_init();
    $apiUrl = "https://api.themoviedb.org/3/movie/{$id}?language=en";
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
    $backdrop = (isset($result['backdrop_path']) && !empty($result['backdrop_path'])) ? "https://image.tmdb.org/t/p/original" . $result['backdrop_path'] : "";

    return[
        "seasons" => [],
        "episodes" => [],
        "backdrop" => $backdrop,
        "overview" => isset($result['overview']) ? $result['overview'] : "",
        "release_date" => isset($result['release_date']) ? $result['release_date'] : ""
    ];
}
?>