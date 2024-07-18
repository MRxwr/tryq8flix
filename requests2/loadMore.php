<?php
function searchShahid($more){
	GLOBAL $website2, $_GET;
	$collection = ( isset($_GET["collection"]) ) ? "order={$_GET["collection"]}" : "" ;
	$category = ( isset($_GET["category"]) ) ? "&category={$_GET["category"]}" : "" ;
	var_dump("{$website2}/recent/page/{$more}&{$collection}{$category}");
	$html = file_get_contents("{$website2}/page/{$more}&{$collection}{$category}");
	$dom = str_get_html($html);
	$data = [
		'shows' => []
	];
	if ($dom) {
		foreach ($dom->find('.Block--Item') as $show) {
			$link = $show->find('a', 0);
			$image = $show->find('img', 0);
			$genre = $show->find('.Genres li', 0);
			$title = $show->find('h3', 0);
			$jsonData = [
				'href' => $link->href,
				'image' => $image->getAttribute('data-src'),
				'episode' => '', // Not present in the provided HTML
				'category' => $genre ? $genre->plaintext : '',
				'title' => $title ? $title->plaintext : '',
				'description' => '', // Not present in the provided HTML
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

if( isset($_POST["type"]) && !empty($_POST["type"]) ){
    if( $_POST["type"] == "get" ){
		$collection = ( isset($_GET["collection"]) ) ? "{$_GET["collection"]}" : "" ;
		$category = ( isset($_GET["category"]) ) ? "&category={$_GET["category"]}" : "" ;
        $user = checkLogin();
		$shows = searchShahid($_POST["more"]);
        $output = "<div class='row m-0 w-100'>";
        if( is_array($shows) && !empty($shows) ){
            for ( $i = 0 ; $i < sizeof($shows) ; $i++){
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
				$output .= "
					<div class='col-xl-3 col-lg-4 col-md-4 col-sm-12 p-3'>
					<div class='card w-100'>
						<div class='card-body'>
							<img src='{$shows[$i]["image"]}' style='width:100%;height:300px;border-radius: 10px; box-shadow: 0px 0px 10px 0px black;'>
							<div style='height:250px; overflow:auto;text-align: -webkit-right;' class='pt-2'>
								<h4 class='card-title {$catgoryType}' id='".str_replace(' ','-',$shows[$i]["category"])."' style='color:#9f8d5c'><b>{$shows[$i]["category"]}</b></h2>
								<h5 class='card-title postTitle{$i}'>{$shows[$i]["title"]}</h3>
							</div>
							<div class='row w-100 p-0 m-0'>
								<div class='col-6 p-1'>
									<div data-bs-toggle='modal' data-bs-target='#playVideo' class='btn btn-danger w-100 playVideo' id='{$checkVideoType}'><i class='bi bi-play-fill'></i> {$shows[$i]["episode"]}</div>
								</div>
								<div class='col-6 p-1'>
									<div data-bs-toggle='modal' data-bs-target='#threeDots' class='btn btn-warning w-100 threeDots' id='{$shows[$i]["href"]}'><i class='bi bi-three-dots'></i></div>
								</div>
							</div>
						</div>
					</div>
					</div>
					";
			}
            echo "</div>".$output;
			echo '<div class="col-md-12 loadMoreBtn mb-3" style="text-align-last: center;" id="'.$_POST["more"].'"><div class="btn btn-primary w-75" >تابع</div></div><div style="display:none" class="getCollection" id="'.$collection.$category.'"></div>';
        }else{
            $msg = "<h1 class='text-center mt-5'>No result.<h1>";
            echo $msg;
        }
    }
}else{
    $msg = "something wrong happened, Please try again.";
    echo $msg;
}
?>