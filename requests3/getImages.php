<?php
function outputImage2($imageUrl) {
    //header('Content-Type: image/jpg');
    $image = curlCall($imageUrl);
    echo $image;
}
outputImage2($_GET["url"]);
?>