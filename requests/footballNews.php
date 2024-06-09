<?php
function searchNews($more){
    $curl = curl_init();
    curl_setopt_array($curl, array(
        CURLOPT_URL => "https://www.kooora.com/?n=0&&pg={$more}",
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
    $array_string = explode('news = new Array (', $response);
    $array_string = explode(');', $array_string[1]);
    $array_string = str_replace("\\","",$array_string[0]);
    preg_match_all('/"n=(\d+)"/', $array_string, $matches);
    // Extract the n values
    $n_values = $matches[1];
    // Print the extracted n values
    foreach ($n_values as $n) {
        articleBody("https://www.kooora.com/?n=$n");
    }
}

function articleBody($link){
    $curl = curl_init();
    curl_setopt_array($curl, array(
        CURLOPT_URL => $link,
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

    $title = explode('he_article_title = "', $body_utf8);
    $title = explode('";', $title[1]);
    echo "<div class='row p-0 m-3'><div class='col-sm-12 mb-3'><div class='row p-3' style='background-color:#a28c5a;border-radius: 10px;box-shadow: 0px 0px 3px 0px #3b3b3b;'><div class='col-sm-12 text-center'>";
    echo $title = "<h3>" . str_replace("\\","",$title[0]) . "</h3>";

    $image = explode('article_images = new Array (', $body_utf8);
    $image = explode('"', $image[1]);
    $image = explode('"', $image[1]);
    $image = str_replace("\\","",$image[0]);
    echo "<img src='$image' class='rounded' style='width: 250px;height: 250px;object-fit: cover;'/>";

    $body = explode('article_content = "', $body_utf8);
    $body = explode('";', $body[1]);
    $body = str_replace("\\","",$body[0]);

    // Load the HTML into a DOMDocument
    $dom = new DOMDocument;
    libxml_use_internal_errors(true);
    $dom->loadHTML('<?xml encoding="UTF-8">' . $body);
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
    echo "<p class='p-3'>";
    foreach ($text_parts as $part) {
        echo str_replace("<p>","",str_replace("</p>","",str_replace("<br>","",$part))) ;
    }
    echo "</p></div>";
    // Output the images
    foreach ($image_sources as $src) {
        echo '<div class="col-6 col-md-3 p-1 text-center"><img src="' . $src . '" style="width:150px;height:150px" class="rounded" /></div>';
    }
    echo "</div></div></div>";
}

$user = checkLogin();

if( !empty($user["id"]) ){
    $more = ( isset($_POST["more"]) && !empty($_POST["more"]) ) ? $_POST["more"] : 1 ;
    searchNews($more); 
    echo "<div class='col-md-12 loadMoreNewsBtn mb-3' style='text-align-last: center;' id='{$more}'><div class='btn btn-secondary w-75' >تابع</div></div>";
}else{
	echo "something wrong happened, Please try again.";
}
?>