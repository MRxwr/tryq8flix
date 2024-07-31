<?php
function outputImage2($imageUrl) {
    header('Content-Type: image/jpeg');
    $image = file_get_contents($imageUrl);
    echo $image;
}
outputImage2($_GET["url"]);
?>