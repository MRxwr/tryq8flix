<?php
function extractDomain($url) {
    $parsedUrl = parse_url($url);
    if ($parsedUrl && isset($parsedUrl['host'])) {
        return $parsedUrl['host'];
    } else {
        return false;
    }
}

if (isset($_POST["id"]) && !empty($_POST["id"])) {
    $html = file_get_contents("{$_POST["id"]}watch/");
	var_dump($html);
    $dom = new DOMDocument();
    @$dom->loadHTML($html);
    $xpath = new DOMXPath($dom);
    $servers = [];
    $serverItems = $xpath->query("//li[@class='server--item']");
    foreach ($serverItems as $item) {
        $servers[] = [
            "url" => $item->getAttribute("data-link"),
            "name" => $xpath->evaluate("string(./span)", $item)
        ];
    }
    $server = json_encode($servers, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    $servers = json_decode($server, true);
    $links = "<div class='row m-0'>";
    $counter = 0;
    $notWanted = ["vembed.net","uqload.co","uqload.com","iioo.vadbam.net","emma.viidshar.com","uptostream.com", "embedv.net", "fdewsdc.sbs","ok.ru", "doodstream.com"];
    $y = 1;
    $mainServer = [];
    for ($i = 0; $i < sizeof($servers); $i++) {
        $domain = extractDomain($servers[$i]["url"]);
        if (!in_array(strtolower($domain), $notWanted) && isset($servers[$i]["url"])) {
            $links .= "<div class='col-3 p-1'><a class='btn btn-secondary w-100' style='color:white' href='#' id='{$servers[$i]["url"]}' onclick='sendIdToIframe(\"{$servers[$i]["url"]}\"); return false;'>Serv-{$y}</a></div>";
            $mainServer[] = $servers[$i]["url"];
            $y++;
        }
    }
    $links .= "</div>";
    if (isset($mainServer) && sizeof($mainServer) > 0) {
        $videoTag = "{$links}<iframe id='frame' src='{$mainServer[0]}' style='width:100%;height:300px;margin-top: 30px;' sandbox='allow-same-origin allow-scripts' allowFullScreen></iframe>";
        echo $videoTag;
    } else {
        echo "لا يوجد روابط متاحه للمشاهده حاليا، الرجاء المحاولة لاحقاً";
    }
}
?>