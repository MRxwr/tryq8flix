<?php
function searchNews($more){
    $url = $more == 1 ? "https://www.kooora.com/أخبار" : "https://www.kooora.com/أخبار/{$more}";
    $curl = curl_init();
    curl_setopt_array($curl, array(
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'GET',
        CURLOPT_HEADER => false,
    ));
    $response = curl_exec($curl);
    curl_close($curl);
    $dom = new DOMDocument;
    libxml_use_internal_errors(true);
    $dom->loadHTML('<?xml encoding="UTF-8">' . $response);
    libxml_clear_errors();
    $xpath = new DOMXPath($dom);
    $cards = $xpath->query("//*[contains(@class, 'fco-card')]");
    foreach ($cards as $card) {
        // Get post link
        $a = $xpath->query(".//a", $card);
        $href = '';
        if ($a->length && $a->item(0)->nodeType === XML_ELEMENT_NODE) {
            $element = $a->item(0);
            if ($element instanceof DOMElement) {
                $href = $element->getAttribute('href');
            }
        }
        $link = $href ? (strpos($href, 'http') === 0 ? $href : 'https://www.kooora.com' . $href) : '';
        // Get image
        $img = $xpath->query(".//*[contains(@class, 'fco-image__image')]", $card);
        $imgsrc = '';
        if ($img->length && $img->item(0)->nodeType === XML_ELEMENT_NODE) {
            $element = $img->item(0);
            if ($element instanceof DOMElement) {
                $imgsrc = $element->getAttribute('src');
            }
        }
        // Get tag text
        $tag = $xpath->query(".//*[contains(@class, 'fco-tag-text')]", $card);
        $tagtext = $tag->length ? $tag->item(0)->textContent : '';
        // Get headline
        $headline = $xpath->query(".//*[contains(@class, 'fco-card__headline-text')]", $card);
        $headlinetext = $headline->length ? $headline->item(0)->textContent : '';
        // Get time
        $time = $xpath->query(".//*[contains(@class, 'fco-card__info--time')]", $card);
        $timetext = $time->length ? $time->item(0)->textContent : '';
        // Get date
        $date = $xpath->query(".//*[contains(@class, 'fco-card__info--date')]", $card);
        $datetext = $date->length ? $date->item(0)->textContent : '';
        // Output card
        echo "<div class='row p-0 m-3'><div class='col-sm-12 mb-3'><div class='row p-3' style='background-color:#a28c5a;border-radius: 10px;box-shadow: 0px 0px 3px 0px #3b3b3b;'><div class='col-sm-12 text-center'>";
        echo "<a href='$link' target='_blank'><h2>" . htmlspecialchars($headlinetext) . "</h2></a>";
        if($imgsrc) echo "<img src='$imgsrc' class='rounded' style='width: 250px;height: 250px;object-fit: cover;'/>";
        echo "<div class='mt-2'><span class='badge bg-secondary'>" . htmlspecialchars($tagtext) . "</span></div>";
        echo "<div class='mt-2'><span>" . htmlspecialchars($timetext) . "</span> | <span>" . htmlspecialchars($datetext) . "</span></div>";
        echo "</div></div></div></div>";
        // Optionally, fetch and show details
        if($link) articleBody($link);
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
        CURLOPT_HEADER => false,
    ));
    $response = curl_exec($curl);
    curl_close($curl);
    $dom = new DOMDocument;
    libxml_use_internal_errors(true);
    $dom->loadHTML('<?xml encoding="UTF-8">' . $response);
    libxml_clear_errors();
    $xpath = new DOMXPath($dom);
    $content = $xpath->query("//*[contains(@class, 'article_article__content__VfjFz')]");
    if($content->length){
        $html = $dom->saveHTML($content->item(0));
        echo "<div class='row p-0 m-3'><div class='col-sm-12 mb-3'><div class='row p-3' style='background-color:#f5f5f5;border-radius: 10px;box-shadow: 0px 0px 3px 0px #3b3b3b;'><div class='col-sm-12 text-center'>";
        echo $html;
        echo "</div></div></div></div>";
    }
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