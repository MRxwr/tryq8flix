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
    //$html = file_get_contents("{$_POST["id"]}watch/");
	$curl = curl_init();
	curl_setopt_array($curl, array(
	CURLOPT_URL => 'https://tuktukcima.art/%d9%85%d8%b4%d8%a7%d9%87%d8%af%d8%a9-%d9%81%d9%8a%d9%84%d9%85-%d8%b4%d9%88%d8%ac%d8%b1-%d8%af%d8%a7%d8%af%d9%8a-2023/watch/',
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
	var_dump("$html");
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