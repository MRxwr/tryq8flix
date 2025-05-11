<?php
$response = array(
    "servers" => array(
        array(
            "id" => 1,
            "name" => "Wecima",
        ),
        array(
            "id" => 2,
            "name" => "EgyDead",
        ),
        array(
            "id" => 3,
            "name" => "TopCinema",
        ),
        array(
            "id" => 4,
            "name" => "Shahid",
        ),
    )
);
if ($banners = selectDB2("`id`, `endpoint`, `server`, `url`, `imageurl`","banners","`status` = '0' AND `hidden` = '0'")) {
    $response["banners"] = $banners;
} else {
    $response["banners"] = array(['error' => 'No banners found']);
}

echo dataOutput($response);
?>