<?php
if (isset($_POST["id"]) && !empty($_POST["id"])) {
    $html = file_get_contents("{$_POST["id"]}");
    $dom = str_get_html($html);
    $data = [
        'shows' => []
    ];
    if ($dom) {
        foreach ($dom->find('.WatchServersList ul li') as $server) {
            $btn = $server->find('btn', 0);
            if ($btn) {
                $title = $btn->find('strong', 0)->plaintext;
                $dataUrl = $btn->getAttribute('data-url');
                $jsonData = [
                    'link' => str_replace(" ", "", $dataUrl)
                ];
                $data['shows'][] = $jsonData;
            }
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
            $url = $servers[$i]["link"];
            $serverDetails = json_encode($servers[$i]);
            $links .= "<div class='col-3 p-1'><a class='btn btn-secondary w-100' style='color:white' href='#' onclick='sendIdToIframe2(\"{$url}\")'>Serv-{$y}</a></div>";
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