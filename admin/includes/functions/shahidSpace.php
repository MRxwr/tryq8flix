<?php
function searchShahidSpaceListing($url){
	GLOBAL $website, $_GET;
	$collection = ( isset($_GET["collection"]) ) ? "?order={$_GET["collection"]}" : "" ;
	$category = ( isset($_GET["category"]) ) ? "&category={$_GET["category"]}" : "" ;
	$html = scrapePage($url.$collection.$category);
    //var_dump($html); die();
	$dom = str_get_html($html);
	$data = [
		'shows' => []
	];
	if ($dom) {
        // Loop through each show block in the new structure
        foreach ($dom->find('.Small--Box') as $show) {
            $anchor = $show->find('a.recent--block', 0);
            $href = $anchor ? $anchor->href : '';
            $imageTag = $anchor ? $anchor->find('.Poster img', 0) : null;
            $image = $imageTag ? $imageTag->getAttribute('data-src') : '';
            $episodeEm = $anchor ? $anchor->find('.number em', 0) : null;
            $episode = $episodeEm ? $episodeEm->plaintext : '';
            $categoryLi = $anchor ? $anchor->find('ul.liList li.category', 0) : null;
            $category = $categoryLi ? $categoryLi->plaintext : '';
            $titleTag = $anchor ? $anchor->find('inner--title h2', 0) : null;
            $title = $titleTag ? $titleTag->plaintext : '';
            $descTag = $anchor ? $anchor->find('inner--title p', 0) : null;
            $description = $descTag ? $descTag->plaintext : '';

            $jsonData = [
                'href'       => trim($href),
                'image'      => trim($image),
                'episode'    => trim($episode),
                'views'      => '', // No views in new structure
                'title'      => trim($title),
                'category'   => trim($category),
                'description'=> trim($description)
            ];
            $data['shows'][] = $jsonData;
        }
        $shows = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    } else {
        echo 'Error: Invalid DOM object.';
        $shows = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }

	$shows = ( isset($shows) && !empty($shows) ) ? json_decode($shows,true) : array() ;
	return $shows = $shows["shows"];
	$dom->clear();
	unset($dom);
}

function shahidSpaceMore($url){
    $html = scrapePage("{$url}");
    $htmlDom = str_get_html($html);
    $seasonsData = [];
    foreach ($htmlDom->find('div.items a.epss') as $linkNode) {
        $link = $linkNode->href;
        $title = trim($linkNode->find('h3', 0)->plaintext);
                if (stripos($link, 'season') !== false) {
            $seasonsData[] = [
                'link' => $link,
                'title' => $title,
                'season_number' => ''
            ];
        }
    }
    $episodesData = [];
    foreach ($htmlDom->find('div.items a.epss') as $linkNode) {
        $link = $linkNode->href;
        $title = trim($linkNode->find('h3', 0)->plaintext);
        if (stripos($link, 'season') === false) {
            $episodesData[] = [
                'link' => $link,
                'title' => $title,
                'episode_number' => ''
            ];
        }
    }
	if (strpos(strtolower($url), 'season') === false){
		$episodesData = array_reverse($episodesData);
		$seasonsData = array_reverse($seasonsData);
	}
    $data = [
        'seasons' => $seasonsData,
        'episodes' => $episodesData
    ];
    $htmlDom->clear();
	unset($htmlDom);
    return $data;
}

function shahidSpaceServers($url){
    $url = str_replace("film","watch",str_replace("post","watch",str_replace("episode","watch",$url)));
    $mainServer = [];
    $html = scrapePage("{$url}");
    $pattern = '/let servers\s*=\s*JSON\.parse\(\'(.*?)\'\);/s';
    preg_match($pattern, $html, $matches);
    if (isset($matches[1])) {
        $serversData = json_decode($matches[1], true);
        $server = json_encode($serversData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    } else {
        echo 'Error: Server information not found.';
		$server = json_encode(array());
    }
    $servers = json_decode($server,true);
    foreach ($servers as $server) {
        $mainServer[]["link"] = $server["url"];
    }
    return $mainServer;
}

function outputData5($shows){ 
	$user = checkLogin();
	$output = "";
	if( is_array($shows) && !empty($shows) && !empty($user["id"]) ){
		for ($i = 0; $i < sizeof($shows); $i++) {
			$checkVideoType = str_replace("film","watch",str_replace("post","watch",str_replace("episode","watch",$shows[$i]["href"])));
			if( strstr($shows[$i]["href"],"episode") ){
				$catgoryType = "categoryTitleTv";
				$shows[$i]["episode"] = $shows[$i]["episode"];
			}elseif( strstr($shows[$i]["href"],"film") ){
				$catgoryType = "categoryTitleMovie";
				$shows[$i]["episode"] = "تشغيل";
			}else{
				$catgoryType = "categoryTitlePost";
				$shows[$i]["episode"] = "تشغيل";
			}
			$realTitle = explode("الحلقة",$shows[$i]["title"]);
			$output .= "
				<div class='col-xl-4 col-lg-6 col-md-6 col-sm-12 p-1'>
					<div class='card w-100'>
						<div class='card-body'>
							<div class='row w-100 p-0 m-0'>
								<div class='col-4 p-1'>
									<img src='{$shows[$i]["image"]}' style='width:100%;height:170px;border-radius: 10px; box-shadow: 0px 0px 10px 0px black;'>
								</div>
								<div class='col-8 p-1'>
									<div style='height:170px; overflow:auto;text-align: -webkit-right;' class='pt-2'>
										<h6 class='card-title {$catgoryType}' id='".str_replace(' ','-',$shows[$i]["category"])."' style='color:#9f8d5c'><b>{$shows[$i]["category"]}</b></h6>
										<h6 class='card-title postTitle{$i}'>{$realTitle[0]}</h6>
										<p class='card-text'>
											<b>العنوان:</b> {$shows[$i]["episode"]}<br>
											<b>التفاصيل:</b> ".substr($shows[$i]["description"],0,100)."...
										</p>
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
	}else{
		$msg = "<h1 class='text-center mt-5'>No result.<h1>";
		echo $msg;
	}
}
?>