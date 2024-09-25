<?php
if (isset($_POST["id"]) && !empty($_POST["id"])) {

    $curl = curl_init();
    curl_setopt_array($curl, array(
    CURLOPT_URL => "{$_POST["id"]}",
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => '',
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 0,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => 'POST',
    CURLOPT_POSTFIELDS => array('View' => '1'),
    ));
    $html = curl_exec($curl);
    curl_close($curl);
    $dom = str_get_html($html);
    $data = [
        'shows' => []
    ];
    if ($dom) {
        $serversList = $dom->find('.watchAreaMaster .serversList', 0);
        if ($serversList) {
            foreach ($serversList->find('li') as $server) {
                $link = $server->getAttribute('data-link');
                $jsonData = [
                    'link' => str_replace(" ", "", $link)
                ];
                $data['shows'][] = $jsonData;
            }
            $servers = json_encode($data['shows'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        } else {
            echo 'Error: Server list not found.';
            $servers = json_encode([], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        }
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
            $links .= "<div class='col-3 p-1'><a class='btn btn-secondary w-100' style='color:white' href='#' onclick='sendIdToIframe(\"{$url}\"); return false;'>Serv-{$y}</a></div>";
            $mainServer[] = $url;
            $y++;
            if( $i == 3 ){
                break;
            }
    }
    $links .= "</div>";
    if (isset($mainServer) && sizeof($mainServer) > 0) {
        $videoTag = "{$links}<iframe id='frame' src='{$mainServer[0]}' style='width:100%;height:300px;margin-top: 30px;overflow: hidden;' sandbox='allow-same-origin allow-scripts' allowFullScreen></iframe>"; 
        echo $videoTag;
    } else {
        echo "لا يوجد روابط متاحه للمشاهده حاليا، الرجاء المحاولة لاحقاً";
    }
}
?>