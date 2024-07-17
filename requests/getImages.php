<?php
// Function to output the image
function outputImage($imageUrl) {
    $image = scrapePage($imageUrl);
    header('Content-Type: image/jpeg');
    echo $image;
}

// Get the image URL from the query parameter
$imageUrl = $_GET['url'];

// Call the outputImage function with the image URL
outputImage($imageUrl);
?>