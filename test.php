<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ScrapingBee Clone</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f5f5f5;
            margin: 0;
            padding: 20px;
        }

        .container {
            max-width: 600px;
            margin: 0 auto;
            background: #fff;
            padding: 20px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
        }

        h1 {
            text-align: center;
        }

        form {
            display: flex;
            flex-direction: column;
        }

        label, input {
            margin-bottom: 10px;
        }

        button {
            padding: 10px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        button:hover {
            background-color: #0056b3;
        }

        #result {
            margin-top: 20px;
            white-space: pre-wrap;
            background: #f0f0f0;
            padding: 10px;
            border-radius: 4px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>ScrapingBee Clone</h1>
        <form id="scrapeForm" method="post">
            <label for="url">Enter URL:</label>
            <input type="text" id="url" name="url" required>
            <button type="submit">Scrape</button>
        </form>
        <div id="result"><?php
            if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['url'])) {
                $url = filter_var($_POST['url'], FILTER_VALIDATE_URL);
                if ($url) {
                    $nodeScript = <<<EOT
const puppeteer = require('puppeteer');

(async () => {
    const browser = await puppeteer.launch();
    const page = await browser.newPage();
    await page.goto('$url', { waitUntil: 'networkidle2' });
    const content = await page.content();
    console.log(content);
    await browser.close();
})();
EOT;
                    file_put_contents('scrape.js', $nodeScript);
                    $output = shell_exec('node scrape.js 2>&1');
                    echo htmlspecialchars($output);
                    unlink('scrape.js');
                } else {
                    echo 'Error: Invalid URL.';
                }
            }
        ?></div>
    </div>
</body>
</html>


<?php
/*
require("admin/includes/config.php");
require("admin/includes/functions.php");
require('templates/simple_html_dom.php');





/*
echo file_get_contents("https://wallhaven.cc/search?q=one piece");

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
