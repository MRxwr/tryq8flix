<?php
include('simple_html_dom.php');
$options = array(
    'http' => array(
        'header' => "User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/58.0.3029.110 Safari/537.3",
    ),
);
$context = stream_context_create($options);
$html = file_get_contents($website2, false, $context);// Create a DOM object
$dom = str_get_html($html);

// Check if the DOM object is valid
if ($dom) {
    $data = [
        'shows' => []
    ];

    foreach($dom->find('em, li.movieItem') as $element) {
        if (!$found && $element->tag == 'em' && $element->innertext == 'المضاف حديثا') {
            $found = true; // Set flag when target <em> tag is found
            continue;
        }
        if ($found && $element->tag == 'li') {
            // Extract data from the movie item
            $a = $element->find('a', 0);
            $href = $a->href;
            $title = $a->find('h1.BottomTitle', 0)->innertext;
            $category = $a->find('span.cat_name', 0)->innertext;
            $episode = $a->find('span.number_episode em', 0)->innertext;
            $image = $a->find('img', 0)->src;
            // Add the movie data to the array
            $jsonData = array(
                'href' => $href,
                'title' => $title,
                'category' => $category,
                'episode' => $episode,
                'image' => $image,
                'description' => $title
            );
        }
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