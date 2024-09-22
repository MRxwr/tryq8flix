<?php
function extractDomain($url) {
    $parsedUrl = parse_url($url);
    if ($parsedUrl && isset($parsedUrl['host'])) {
        return $parsedUrl['host'];
    } else {
        return false;
    }
}
 
if( isset($_POST["id"]) && !empty($_POST["id"]) ){
	$html = scrapePage("{$_POST["id"]}");
    $pattern = '/let servers\s*=\s*JSON\.parse\(\'(.*?)\'\);/s';
    preg_match($pattern, $html, $matches);
    if (isset($matches[1])) {
        $serversData = json_decode($matches[1], true);
        $server = json_encode($serversData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    } else {
        echo 'Error: Server information not found.';
		$server = json_encode(array());
    }
    $servers = json_decode($server,true);
	$links = "<div class='row m-0' >";
	$counter = 0;
	$notWanted = [];//["vembed.net","uqload.co","uqload.com","iioo.vadbam.net","emma.viidshar.com","uptostream.com", "embedv.net", "fdewsdc.sbs","ok.ru", "doodstream.com"];
	$y = 1;
	for( $i = 0; $i < sizeof($servers); $i++ ){
		$domain = extractDomain($servers[$i]["url"]);
		if( !in_array(strtolower($domain),$notWanted) && isset($servers[$i]["url"]) ){
			$links .= "<div class='col-3 p-1'><a class='btn btn-secondary w-100' style='color:white' href='#' id='{$servers[$i]["url"]}' onclick='sendIdToIframe2(\"{$servers[$i]["url"]}\"); return false;'>Serv-{$y}</a></div>";
			$server = $servers[$i]["url"];
			$mainServer[] = $servers[$i]["url"]; 
			$y++;
		}
	}
	$links .= "</div>";
	if( isset($mainServer) && sizeof($mainServer) > 0){
		$videoTag = "{$links}<iframe id='frame' src='{$mainServer[0]}' style='width:100%;height:300px;margin-top: 30px;' sandbox='allow-same-origin allow-scripts' allowFullScreen></iframe>";
		echo $videoTag;
	}else{
		echo "لا يوجد روابط متاحه للمشاهده حاليا، الرجاء المحاولة لاحقاً";
	}
    
}
?>
