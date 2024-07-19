<?php
function extractDomain($url) {
    $parsedUrl = parse_url($url);
    if ($parsedUrl && isset($parsedUrl['host'])) {
        return $parsedUrl['host'];
    } else {
        return false;
    }
}

function getIframeURL($url, $link) {
    GLOBAL $website2;
    $postData = array(
        'id' => $url["id"],
        'i' => $url["i"]
    );
    $headers = array(
        "Referer: " . str_replace("web","web5",str_replace("cam","world",$link)),
        'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:128.0) Gecko/20100101 Firefox/128.0',
        'X-Requested-With: XMLHttpRequest',
    );
    //var_dump($postData); var_dump($headers); var_dump($link); var_dump($website2 . "/wp-content/themes/movies2023/Ajaxat/Single/Server.php\n");
    $curl = curl_init();
    curl_setopt_array($curl, array(
    CURLOPT_URL => "{$website2}/wp-content/themes/movies2023/Ajaxat/Single/Server.php",
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => '',
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 0,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => 'POST',
    CURLOPT_POSTFIELDS => $postData,
    CURLOPT_HTTPHEADER => $headers,
    ));
    $response = curl_exec($curl);
    curl_close($curl);
    var_dump($response);die();
    $output = explode('src="', $response);
    $output = explode('"', $output[1]);
    return $output[0];
}

if (isset($_POST["id"]) && !empty($_POST["id"])) {
    $html = curlCall("{$_POST["id"]}watch/");
    $dom = str_get_html($html);
    $data = [
        'shows' => []
    ];
    if ($dom) {
        foreach ($dom->find('.server--item') as $server) {
            $id = $server->getAttribute('data-id');
            $i = $server->getAttribute('data-server');
            $jsonData = [
                'id' => $id,
                'i' => $i,
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
    $y = 1;
    $mainServer = [];
    for ($i = 0; $i < sizeof($servers); $i++) {
        $url = getIframeURL($servers[$i], "{$_POST["id"]}watch/");
        $links .= "<div class='col-3 p-1'><a class='btn btn-secondary w-100' style='color:white' href='#' id='{$url}' onclick='sendIdToIframe(\"{$url}\"); return false;'>Serv-{$y}</a></div>";
        $mainServer[] = $url;
        $y++;
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