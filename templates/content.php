<?php
include('simple_html_dom.php');
$html = file_get_contents($website);
// Create a DOM object
$dom = str_get_html($html);

// Check if the DOM object is valid
if ($dom) {
    $data = [
        'shows' => []
    ];

    // Loop through each show
    foreach ($dom->find('.shows-container .show-card') as $show) {
        // Extract background-image URL from style attribute
        $style = $show->style;
        preg_match('/\burl\s*\(\s*[\'"]?(.*?)[\'"]?\s*\)/', $style, $matches);
        $imageUrl = isset($matches[1]) ? $matches[1] : '';

        $jsonData = [
            'href' => $show->href,
            'image' => trim($imageUrl),
            'episode' => $show->find('.ep', 0)->plaintext,
            'category' => $show->find('.categ', 0)->plaintext,
            'title' => $show->find('.title', 0)->plaintext,
            'description' => trim(preg_replace('/\s+/', ' ', $show->find('.description', 0)->plaintext)),
        ];

        // Add the JSON data to the array
        $data['shows'][] = $jsonData;
    }

    // Output the JSON array
    $shows = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
} else {
    echo 'Error: Invalid DOM object.';
}

$shows = ( isset($shows) && !empty($shows) ) ? json_decode($shows,true) : array() ;
$shows = $shows["shows"];
// Clean up the DOM object
$dom->clear();
unset($dom);

?>
<div id="content">

</div>