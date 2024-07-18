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
	CURLOPT_URL => "{$_POST["id"]}watch/",
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
	var_dump($html);
    $dom = str_get_html($html);
    $data = [
        'shows' => []
    ];
    if ($dom) {
        foreach ($dom->find('.server--item') as $server) {
            $link = $server->getAttribute('data-link');
            $title = $server->find('span', 0)->plaintext;
            $jsonData = [
                'url' => $link,
                'name' => $title,
            ];
            $data['shows'][] = $jsonData;
        }
        $servers = json_encode($data['shows'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    } else {
        echo 'Error: Invalid DOM object.';
        $servers = json_encode([], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }
    $servers = json_decode($servers, true);
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