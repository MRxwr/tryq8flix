<?php
function outputImage2($imageUrl) {
    $image = file_get_contents($imageUrl);
    header('Content-Type: image/jpeg');
    echo $image;
    var_dump("test");
}

outputImage2($_GET["url"]);
?>