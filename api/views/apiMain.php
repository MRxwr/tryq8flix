<?php
$response = array(
    "servers" => array(
        array( "id" => 1, "name" => "Wecima"),
        array( "id" => 10, "name" => "Esq" ),
        array( "id" => 7, "name" => "Top Cinema Zone" ),
        array( "id" => 11, "name" => "Anime Slayer" ),
        array( "id" => 9, "name" => "Qesset" ),
        array( "id" => 8, "name" => "TukTuk" ),
        array( "id" => 5, "name" => "Shahid Space" ),
        array( "id" => 4, "name" => "Shahid" ),
        array( "id" => 12, "name" => "Anime Pec" ),
        //array( "id" => 2, "name" => "EgyDead" ),
        //array( "id" => 3, "name" => "TopCinema" ),
        //array( "id" => 6, "name" => "ShahidwBs" ),
    )
);
if ($banners = selectDB2("`id`, `title`, `endpoint`, `server`, `url`, `imageurl`","banners","`status` = '0' AND `hidden` = '0'")) {
    $response["banners"] = $banners;
} else {
    $response["banners"] = array(['error' => 'No banners found']);
}

echo dataOutput($response);
?>