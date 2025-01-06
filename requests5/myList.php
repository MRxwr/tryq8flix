<?php
if( isset($_POST["type"]) && !empty($_POST["type"]) ){
    if( $_POST["type"] == "add" ){
        if( isset($_POST["id"]) && !empty($_POST["id"]) ){
            $user = checkLogin();
            $data = array(
                "userId" => $user["id"],
                "categoryId" => $_POST["id"]
            );
            if ( selectDB("mylist","`userId` = {$user["id"]} AND `categoryId` = {$_POST["id"]}") ){
                deleteDB("mylist","`userId` = {$user["id"]} AND `categoryId` = {$_POST["id"]}");
                $msg = "Video has been removed from liked list";
                echo $msg; 
            }else{
                insertDB("mylist",$data);
                echo 0;
            }
        }else{
            $msg = "Please send id as postId.";
            echo $msg; 
        }
    }elseif( $_POST["type"] == "get" ){
        $user = checkLogin();
        $output = "<div class='row m-0 w-100'>";
        if( !empty($user["id"]) && $myList = selectDB("mylist","`userId` = {$user["id"]}") ){
            for ( $i = 0 ; $i < sizeof($myList) ; $i++){
                if ( $categories = selectDB("category","`id` = '{$myList[$i]["categoryId"]}' AND `status` = '0'") ){
					$output .= "
					<div class='col-xl-3 col-lg-4 col-md-6 col-sm-12 p-3'>
						<div class='card w-100'>
							<img src='{$categories[0]["poster"]}' class='card-img-top mainImage' alt='{$categories[0]["title"]}'>
							<div class='card-body'>
							<div style='height:200px; overflow:auto' >
								<h2 class='card-title categoryTitle{$categories[0]["id"]}'><b>{$categories[0]["title"]}</b></h2>
								<p class='card-text'>
									<b>IMDb:</b> {$categories[0]["imdbrating"]}<br>
									<b>Duration:</b> {$categories[0]["duration"]}<br>
									<b>Year:</b> {$categories[0]["releasedate"]}<br>
									<b>Cast:</b> {$categories[0]["channel"]}<br>
							</div>
								<div class='row w-100 p-0 m-0'>
									<div class='col-6 p-1'>
										<div class='btn btn-info w-100 favoStar addToMyList' id='{$categories[0]["id"]}'><i class='bi bi-star-fill'></i></div>
									</div>
									<div class='col-6 p-1'>
										<div data-bs-toggle='modal' data-bs-target='#threeDots' class='btn btn-warning w-100 threeDots' id='{$categories[0]["id"]}'><i class='bi bi-three-dots'></i></div>
									</div>
								</div>
							</div>
						</div>
					</div>";
				}
			}
            echo "</div>".$output;
        }else{
            $msg = "<h1 class='text-center mt-5'>You list is empty.<h1>";
            echo $msg;
        }
    }
}else{
    $msg = "something wrong happened, Please try again.";
    echo $msg;
}
?>