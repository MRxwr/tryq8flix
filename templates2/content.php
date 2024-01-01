<?php
include('simple_html_dom.php');
//$html = file_get_contents($website2);
// use curl instead of file_get_contents
$curl = curl_init();
curl_setopt_array($curl, array(
  CURLOPT_URL => 'https://egydead.space/',
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => '',
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 0,
  CURLOPT_FOLLOWLOCATION => true,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => 'GET',
));
echo $html = curl_exec($curl);
curl_close($curl);
// Create a DOM object
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