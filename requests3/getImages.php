<?php
header('Content-Type: image/jpeg');
function outputImage2($imageUrl) {
    $image = file_get_contents($imageUrl);
    echo $image;
    var_dump("test");
}

outputImage2($_GET["url"]);
?>