<?php
require("admin/includes/config.php");
require("admin/includes/functions.php");
require('templates/simple_html_dom.php');


$curl = curl_init();
curl_setopt_array($curl, array(
    CURLOPT_URL => "https://www.kooora.com/?n=1332536&pg=1&o=n",
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => '',
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 0,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => 'GET',
    CURLOPT_HEADER => true, // Include headers in the output
));
$response = curl_exec($curl);
if ($response === false) {
    echo "cURL Error: " . curl_error($curl);
    curl_close($curl);
    exit;
}
$header_size = curl_getinfo($curl, CURLINFO_HEADER_SIZE);
curl_close($curl);
$headers = substr($response, 0, $header_size);
$body = substr($response, $header_size);
$encoding = 'windows-1256'; // Default to windows-1256 if not specified
if (preg_match('/Content-Type:\s*text\/html;\s*charset=([\w-]+)/i', $headers, $matches)) {
    $encoding = $matches[1];
}
$body_utf8 = iconv($encoding, 'UTF-8//IGNORE', $body);
$body_utf8 = explode('article_content = "', $body_utf8);
$body_utf8 = explode('";', $body_utf8[1]);
$body_utf8 = str_replace("\\","",$body_utf8[0]);

// Load the HTML into a DOMDocument
$dom = new DOMDocument;
libxml_use_internal_errors(true);
$dom->loadHTML('<?xml encoding="UTF-8">' . $body_utf8);
libxml_clear_errors();

// Create arrays to hold text and image sources
$text_parts = [];
$image_sources = [];

// Loop through each <p> tag
foreach ($dom->getElementsByTagName('p') as $p) {
    // Extract text content
    $text_content = '';
    foreach ($p->childNodes as $node) {
        if ($node->nodeType == XML_TEXT_NODE || $node->nodeType == XML_ELEMENT_NODE && $node->tagName == 'br') {
            $text_content .= $dom->saveHTML($node);
        }
    }
    $text_parts[] = $text_content;

    // Extract images
    foreach ($p->getElementsByTagName('img') as $img) {
        $image_sources[] = $img->getAttribute('src');
    }
}

// Output the text parts
echo "<h2>Text Content:</h2>";
foreach ($text_parts as $part) {
    echo "<p>" . $part . "</p>";
}

// Output the images
echo "<h2>Images:</h2>";
foreach ($image_sources as $src) {
    echo '<img src="' . $src . '" style="width: 600px; height: 400px;" /><br>';
}

//echo file_get_contents("https://wallhaven.cc/search?q=one piece");

/*
function searchShahid(){
	
	$curl = curl_init();
	curl_setopt_array($curl, array(
	  CURLOPT_URL => "https://web5.topcinema.world/recent/",
	  CURLOPT_RETURNTRANSFER => true,
	  CURLOPT_ENCODING => '',
	  CURLOPT_MAXREDIRS => 10,
	  CURLOPT_TIMEOUT => 0,
	  CURLOPT_FOLLOWLOCATION => true,
	  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
	  CURLOPT_CUSTOMREQUEST => 'GET',
	));
	$html = curl_exec($curl);
	curl_close($curl);
	
	//$html = file_get_contents(getWebsite());
	// Create a DOM object
	$dom = str_get_html($html);
	// Check if the DOM object is valid
	if ($dom) {
		$data = [
			'shows' => []
		];
		// Loop through each show
		foreach ($dom->find('.Small--Box') as $show) {
			// Extract background-image URL from style attribute
			$style = $show->style;
			preg_match('/\burl\s*\(\s*[\'"]?(.*?)[\'"]?\s*\)/', $style, $matches);
			$imageUrl = isset($matches[1]) ? $matches[1] : '';
			$title = $show->find('.recent--block', 0)->title;
			$url = $show->find('.recent--block', 0)->href;
			$poster = $show->find('img', 0)->getAttribute('data-src');
			$genre = $show->find('.liList li', 0)->plaintext;
			$resolution = $show->find('.liList li', 1)->plaintext;
			$jsonData = [
				'title' => $title,
				'url' => $url,
				'poster' => $poster,
				'genre' => $genre,
				'resolution' => $resolution
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
	var_dump($shows);die();
	return $shows = $shows["shows"];
	// Clean up the DOM object
	$dom->clear();
	unset($dom);
}

$shows = searchShahid();
outputData($shows); 
/*
if( isset($_POST["type"]) && !empty($_POST["type"]) ){ 
	$user = checkLogin();
	if ( !empty($user["id"]) ){
		$shows = searchShahid();
		outputData($shows); 
	}else{
		$msg = "Please login first.";
		echo $msg;
	}
}else{
    $msg = "something wrong happened, Please try again.";
    echo $msg;
}

/*
$clientId = '386563124e58e6c';
$imageUrl = 'https://createkuwait.com/images/15-03-2316788848811678884882.png';

// API endpoint for image upload
$apiEndpoint = 'https://api.imgur.com/3/image';

// cURL setup
$ch = curl_init();

// Set cURL options
curl_setopt($ch, CURLOPT_URL, $apiEndpoint);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    'Authorization: Client-ID ' . $clientId,
    'Content-Type: application/x-www-form-urlencoded',
));

// Set the image URL as the POST data
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query(array('image' => $imageUrl)));

// Execute cURL session
$response = curl_exec($ch);

// Close cURL session
curl_close($ch);

// Decode the JSON response
$result = json_decode($response, true);

// Check if the upload was successful
if ($result && isset($result['data']['link'])) {
    echo 'Image uploaded successfully! Imgur link: ' . $result['data']['link'];
} else {
    echo 'Image upload failed. Error: ' . print_r($result, true);
}
/*
// pull from instagram 

$curl = curl_init();

curl_setopt_array($curl, [
	CURLOPT_URL => "https://instagram-scraper-20231.p.rapidapi.com/postdetail/C0l5_X8tlk-",
	CURLOPT_RETURNTRANSFER => true,
	CURLOPT_ENCODING => "",
	CURLOPT_MAXREDIRS => 10,
	CURLOPT_TIMEOUT => 30,
	CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
	CURLOPT_CUSTOMREQUEST => "GET",
	CURLOPT_HTTPHEADER => [
		"X-RapidAPI-Host: instagram-scraper-20231.p.rapidapi.com",
		"X-RapidAPI-Key: e1e4eb21b9msh475ffb7cfdf9329p1350c2jsnbd8b9595ad67"
	],
]);

$response = curl_exec($curl);
$err = curl_error($curl);

curl_close($curl);

if ($err) {
	echo "cURL Error #:" . $err;
} else {
	echo $response;
}
*/
?>
