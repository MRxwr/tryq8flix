<?php
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
    echo "<h2>Title:</h2>";
    echo $title = "<p>" . str_replace("\\","",$title[0]) . "</p>";

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
    echo "<h2>Text Content:</h2>";
    foreach ($text_parts as $part) {
        echo "<p>" . $part . "</p>";
    }

    // Output the images
    echo "<h2>Images:</h2>";
    foreach ($image_sources as $src) {
        echo '<img src="' . $src . '" style="width: 600px; height: 400px;" /><br>';
    }
}

if( isset($_POST["articleLink"]) && !empty($_POST["articleLink"]) ){
    articleBody($_POST["articleLink"]);
}else{
    $msg = "something wrong happened, Please try again.";
    echo $msg;
}

?>