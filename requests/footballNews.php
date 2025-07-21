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
var_dump($cards);
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
        echo "<div class='card mb-4 shadow-sm' style='background-color:#f8f9fa;border-radius:10px;'>";
        echo "<div class='card-body'>";
        echo "<div class='row'>";
        if($imgsrc) {
            echo "<div class='col-md-3 text-center'><img src='" . htmlspecialchars($imgsrc) . "' class='img-fluid rounded mb-2' style='max-width:180px;max-height:180px;object-fit:cover;'/></div>";
        }
        echo "<div class='col-md-9'>";
        echo "<a href='" . htmlspecialchars($link) . "' target='_blank' style='text-decoration:none;'><h4 class='card-title'>" . htmlspecialchars($headlinetext) . "</h4></a>";
        echo "<div class='mb-2'><span class='badge bg-secondary'>" . htmlspecialchars($tagtext) . "</span></div>";
        echo "<div class='mb-2 text-muted'><span>" . htmlspecialchars($timetext) . "</span> | <span>" . htmlspecialchars($datetext) . "</span></div>";
        echo "</div></div>";
        echo "</div></div>";
        echo "</div>";
    }
}

function getArticleBodyHtml($link){
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
        return $dom->saveHTML($content->item(0));
    }
    return '';
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