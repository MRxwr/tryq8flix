<?php
function extractDomain($url) {
    $parsedUrl = parse_url($url);
    if ($parsedUrl && isset($parsedUrl['host'])) {
        return $parsedUrl['host'];
    } else {
        return false;
    }
}

function getIframeURL($url) {
    $curl = curl_init();
    curl_setopt_array($curl, array(
    CURLOPT_URL => 'https://web5.topcinema.world/wp-content/themes/movies2023/Ajaxat/Single/Server.php',
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => '',
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 0,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => 'POST',
    CURLOPT_POSTFIELDS => array('id' => $url["id"],'i' => $url["i"]),
    CURLOPT_HTTPHEADER => array(
        'X-Requested-With: XMLHttpRequest',
        'Referer: https://web5.topcinema.world/'
    ),
    ));
    $response = curl_exec($curl);
    curl_close($curl);
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
    var_dump($servers);die();
    $links = "<div class='row m-0'>";
    $counter = 0;
    $y = 1;
    $mainServer = [];
    for ($i = 0; $i < sizeof($servers); $i++) {
        $url = getIframeURL($servers[$i]);
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