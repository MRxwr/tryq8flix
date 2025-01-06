<?php
if( isset($_POST["type"]) && !empty($_POST["type"]) ){
    if( $_POST["type"] == "get" ){
        $user = checkLogin();
        $output = "<div class='row m-0 w-100'>";
        if( $user["id"] != '' && $history = selectDB("history","`userId` = {$user["id"]} ORDER BY `date` DESC LIMIT 120") ){
            for ( $i = 0 ; $i < sizeof($history) ; $i++){
                if ( $post = selectDB("posts","`id` = '{$history[$i]["postId"]}' AND `status` = '0'") ){
					$categories = selectDB("category","`id` = '{$post[0]["catid"]}'");
					$output .= "
					<div class='col-md-4 col-12 p-3'>
						<div class='card w-100'>
							<img src='{$categories[0]["poster"]}' class='card-img-top mainImage' alt='the Avengers'>
							<div class='card-body'>
							<div style='height:200px; overflow:auto' >
								<h2 class='card-title categoryTitle{$post[0]["id"]}'><b>{$categories[0]["title"]}</b></h2>
								<h3 class='card-title postTitle{$post[0]["id"]}'>{$post[0]["title"]}</h3>
								<p class='card-text'>
									<b>IMDb:</b> {$categories[0]["imdbrating"]}<br>
									<b>Views:</b> {$post[0]["views"]}<br>
									<b>Duration:</b> {$categories[0]["duration"]}<br>
									<b>Year:</b> {$categories[0]["releasedate"]}<br>
									<b>Cast:</b> {$categories[0]["channel"]}<br>
									<b>Details:</b> {$categories[0]["description"]}</p>
							</div>
								<div class='row w-100 p-0 m-0'>
									<div class='col-6 p-1'>
										<div data-bs-toggle='modal' data-bs-target='#playVideo' class='btn btn-danger w-100 playVideo' id='{$post[0]["id"]}'><i class='bi bi-play-fill'></i></div>
									</div>
									<div class='col-6 p-1'>
										<div data-bs-toggle='modal' data-bs-target='#threeDots' class='btn btn-warning w-100 threeDots' id='{$post[0]["catid"]}'><i class='bi bi-three-dots'></i></div>
									</div>
								</div>
							</div>
						</div>
					</div>";
				}
			}
            echo "</div>" . $output;
        }
    }
}else{
    $msg = "something wrong happened, Please try again.";
    echo $msg;
}
?>