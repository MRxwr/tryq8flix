<?php
include('simple_html_dom.php');
$curl = curl_init($website2);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
curl_setopt($curl, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/58.0.3029.110 Safari/537.3');
curl_setopt($curl, CURLOPT_USERAGENT, 'Mozilla/5.0 (iPhone; CPU iPhone OS 16_6 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/16.6 Mobile/15E148 Safari/604.1');
curl_setopt($curl, CURLOPT_COOKIE, 'MicrosoftApplicationsTelemetryFirstLaunchTime=2023-12-30T12:31:00.279Z');
$html = curl_exec($curl);

$httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
echo "HTTP response code: " . $httpCode;

curl_close($curl);
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
    echo $shows = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
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