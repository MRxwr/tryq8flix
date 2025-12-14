<?php
function shahidwBsListing($url) {
    $html = curlCall($url);
    $htmlDom = str_get_html($html);
    $seasonsData = [];
    $episodesData = [];
    // Scrape seasons from new HTML structure
    $seasonTitles = [];
    foreach ($htmlDom->find('.SeasonsBoxUL .Tab button') as $seasonBtn) {
        $title = trim($seasonBtn->plaintext);
        $seasonNumber = preg_replace('/[^0-9]/', '', $title);
        $seasonsData[] = [
            'link' => '',
            'title' => $title,
            'season_number' => $seasonNumber
        ];
        $seasonTitles[] = $title;
    }
    // Scrape episodes from new HTML structure
    $seasonIdx = 0;
    foreach ($htmlDom->find('.SeasonsEpisodesMain .Tab .tabcontent') as $seasonTab) {
        $seasonTitle = isset($seasonTitles[$seasonIdx]) ? $seasonTitles[$seasonIdx] : '';
        foreach ($seasonTab->find('ul a') as $episodeLink) {
            $link = $episodeLink->href;
            $title = trim($episodeLink->plaintext);
            $episodeNumber = preg_replace('/[^0-9]/', '', $title);
            $episodesData[] = [
                'link' => $link,
                'title' => $seasonTitle . ' - ' . $title,
                'episode_number' => $episodeNumber
            ];
        }
        $seasonIdx++;
    }
    $data = [
        'seasons' => $seasonsData,
        'episodes' => $episodesData
    ];
    $htmlDom->clear();
    unset($htmlDom);
    return $data;
}
function scrapeShahidwBsServers($url) {
    $url = str_replace("watch", "play", $url);
    $html = curlCall("{$url}");
    $dom = str_get_html($html);
    $data = [
        'shows' => []
    ];
    if ($dom) {
        foreach ($dom->find('ul.list_servers li') as $server) {
            $title = '';
            $link = '';
            $strong = $server->find('strong', 0);
            if ($strong) {
                $title = trim($strong->plaintext);
            }
            $dataEmbed = $server->getAttribute('data-embed');
            if ($dataEmbed && preg_match("/src='([^']+)'/", $dataEmbed, $matches)) {
                $link = $matches[1];
            }
            if ($title && $link) {
                $data['shows'][] = [
                    'link' => $link,
                    'title' => $title
                ];
            }
        }
        $dom->clear();
        unset($dom);
    }
    return $data['shows'];
}
function scrapeShahidwBs($url) {
    $html = file_get_contents($url);
    echo $html;
    $dom = str_get_html($html);
    if ($dom) {
        $data = [
            'shows' => []
        ];
        // Find all video list items in the new theme
        foreach ($dom->find('ul#pm-grid li') as $item) {
            $thumbDiv = $item->find('.thumbnail', 0);
            if (!$thumbDiv) continue;
            $videoThumb = $thumbDiv->find('.pm-video-thumb', 0);
            $durationSpan = $videoThumb ? $videoThumb->find('.pm-label-duration', 0) : null;
            $duration = $durationSpan ? trim($durationSpan->plaintext) : '';
            $aTag = $videoThumb ? $videoThumb->find('a', 1) : null;
            $href = $aTag ? $aTag->href : '';
            $title = $aTag ? $aTag->title : '';
            $imgTag = $aTag ? $aTag->find('img', 0) : null;
            $image = $imgTag ? $imgTag->src : '';
            $episodeSpan = $aTag ? $aTag->find('.pm-video-labels .ep', 0) : null;
            $episode = $episodeSpan ? trim($episodeSpan->plaintext) : '';
            $captionDiv = $thumbDiv->find('.caption', 0);
            $captionTitle = $captionDiv ? $captionDiv->find('h3 a', 0) : null;
            $caption = $captionTitle ? $captionTitle->plaintext : '';

            $proxyImageUrl = 'https://' . $_SERVER['HTTP_HOST'] . '/image-proxy.php?url=' . urlencode(trim($image));
            $jsonData = [
                'href' => $href,
                'image' => $proxyImageUrl,
                'episode' => $episode,
                'category' => '',
                'title' => $caption,
                'description' => $duration,
            ];
            $data['shows'][] = $jsonData;
        }
        return json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    } else {
        return 0;
    }
}

function outputData6($shows) { 
    $user = checkLogin();
    $output = "";
    
    if (is_array($shows) && !empty($shows) && !empty($user["id"])) {
        for ($i = 0; $i < sizeof($shows); $i++) {
            $checkVideoType = str_replace("film", "watch", str_replace("post", "watch", str_replace("episode", "watch", $shows[$i]["href"])));

            if (strstr($shows[$i]["href"], "episode")) {
                $catgoryType = "categoryTitleTv";
                $shows[$i]["episode"] = $shows[$i]["episode"];
            } elseif (strstr($shows[$i]["href"], "film")) {
                $catgoryType = "categoryTitleMovie";
                $shows[$i]["episode"] = "تشغيل";
            } else {
                $catgoryType = "categoryTitlePost";
                $shows[$i]["episode"] = "تشغيل";
            }

            $realTitle = explode("الحلقة", $shows[$i]["title"]);

            $output .= "
                <div class='col-xl-4 col-lg-6 col-md-6 col-sm-12 p-1'>
                    <div class='card w-100'>
                        <div class='card-body'>
                            <div class='row w-100 p-0 m-0'>
                                <div class='col-4 p-1'>
                                    <img src='{$shows[$i]["image"]}' style='width:100%;height:170px;border-radius: 10px; box-shadow: 0px 0px 10px 0px black;'>
                                </div>
                                <div class='col-8 p-1'>
                                    <div style='height:170px; overflow:auto; text-align: -webkit-right;' class='pt-2'>
                                        <h6 class='card-title {$catgoryType}' id='" . str_replace(' ', '-', $shows[$i]["category"]) . "' style='color:#9f8d5c'><b>{$shows[$i]["category"]}</b></h6>
                                        <h6 class='card-title postTitle{$i}'>{$realTitle[0]}</h6>
                                    </div>
                                </div>
                                <div class='col-6 p-1'>
                                    <div data-bs-toggle='modal' data-bs-target='#playVideo' class='btn btn-danger w-100 playVideo nextBtn' id='{$checkVideoType}'><i class='bi bi-play-fill'></i> {$shows[$i]["episode"]}</div>
                                </div>
                                <div class='col-6 p-1'>
                                    <div data-bs-toggle='modal' data-bs-target='#threeDots' class='btn btn-warning w-100 threeDots nextBtn' id='{$shows[$i]["href"]}'><i class='bi bi-three-dots'></i></div>
                                </div> 
                            </div>
                        </div>
                    </div>
                </div>
            ";
        }
        echo $output;
    } else {
        echo "<h1 class='text-center mt-5'>No result.</h1>";
    }
}
?>