<?php
function outputImage($imageUrl) {
    $image = file_get_contents($imageUrl);
    header('Content-Type: image/jpeg');
    echo $image;
}

outputImage($_GET["url"]);
?>