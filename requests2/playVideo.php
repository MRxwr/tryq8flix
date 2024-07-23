<?php
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
                'link' => "{$_POST["id"]}watch/",
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
    $ajaxUrl = "{$website2}/wp-content/themes/movies2023/Ajaxat/Single/Server.php";
    $notListed = [];//[0,2,3,4];
    for ($i = 0; $i < sizeof($servers); $i++) {
        if ( $i == 1 ){
            //unset($servers[$i]["link"]);
            $url = makeRequest($ajaxUrl, $servers[$i], "{$_POST["id"]}watch/");
            $mainServer[] = $url;
        }
        if( !in_array($i, $notListed) ){
            $serverDetails = json_encode($servers[$i]);
            $links .= "<div class='col-3 p-1'><a class='btn btn-secondary w-100 playServer' style='color:white' href='#' id='{$serverDetails}'>Serv-{$y}</a></div>";
            $y++;
        }
    }
    $links .= "</div>";
    if (isset($mainServer) && sizeof($mainServer) > 0) {
        $videoTag = "{$links}<iframe id='frame' src='{$mainServer[0]}' style='width:100%;height:300px;margin-top: 30px;' frameborder='0' allow='accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture' allowfullscreen=''></iframe>";
        echo $videoTag;
    } else {
        echo "لا يوجد روابط متاحه للمشاهده حاليا، الرجاء المحاولة لاحقاً";
    }
}
?>