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
	$videoUrl = $_POST["id"];
	if (substr($videoUrl, -7) !== '/watch/') {
		$videoUrl = rtrim($videoUrl, '/') . '/watch/';
	}
	$html = scrapePage($videoUrl);
	$htmlDom = str_get_html($html);
	$servers = [];
	$notWanted = ["vembed.net","uqload.co","uqload.com","iioo.vadbam.net","emma.viidshar.com","uptostream.com", "embedv.net", "fdewsdc.sbs","ok.ru", "doodstream.com"];
	foreach ($htmlDom->find('div.ServersList ul#watch li') as $li) {
		$url = $li->getAttribute('data-watch');
		$nameTag = $li->find('span#serverName', 0);
		$name = $nameTag ? $nameTag->plaintext : '';
		$domain = extractDomain($url);
		if ($url && !in_array(strtolower($domain), $notWanted)) {
			$servers[] = ["url" => $url, "name" => $name];
		}
	}
	$links = "<div class='row m-0' >";
	$y = 1;
	$mainServer = [];
	foreach ($servers as $server) {
		$links .= "<div class='col-3 p-1'><a class='btn btn-secondary w-100' style='color:white' href='#' id='{$server["url"]}' onclick='sendIdToIframe(\"{$server["url"]}\"); return false;'>Serv-{$y}</a></div>";
		$mainServer[] = $server["url"];
		$y++;
	}
	$links .= "</div>";
	if( sizeof($mainServer) > 0 ){
		$videoTag = "{$links}<iframe id='frame' src='{$mainServer[0]}' style='width:100%;height:300px;margin-top: 30px;'  allowFullScreen></iframe>";
		echo $videoTag;
	}else{
		echo "لا يوجد روابط متاحه للمشاهده حاليا، الرجاء المحاولة لاحقاً";
	}
}
?>
