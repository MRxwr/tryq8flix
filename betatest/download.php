<?php

$title = $_GET["title"] .".mp4" ;
$dl = $_GET["dl"];

header('Content-type: video/mp4');

// It will be called downloaded.pdf
header("Content-Disposition: attachment; filename=$title");

// The PDF source is in original.pdf
readfile("$dl");

?> 