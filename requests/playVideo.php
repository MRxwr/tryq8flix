<?php
function extractDomain($url) {
    $parsedUrl = parse_url($url);
    
    // Check if the URL is valid and contains the host
    if ($parsedUrl && isset($parsedUrl['host'])) {
        return $parsedUrl['host'];
    } else {
        // Handle invalid URLs or those without a host
        return false;
    }
}
 
if( isset($_POST["id"]) && !empty($_POST["id"]) ){
    // get episode link \\
    //$html = file_get_contents($_POST["id"]);
	$curl = curl_init();
	curl_setopt_array($curl, array(
	  CURLOPT_URL => "https://app.scrapingbee.com/api/v1/?api_key=IN9YLTOE0MBVC5BV5GASF63BEE472R7CRTLX4N77FWZBTNZL4L3XNANQ4XMZFDN82Z6IVRQ4BAVH8GR6&url=". urlencode("{$_POST["id"]}"),
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
    // Extract server information using regular expressions
    $pattern = '/let servers = JSON\.parse\(\'(.*?)\'\);/';
    preg_match($pattern, $html, $matches);
    if (isset($matches[1])) {
        $serversData = json_decode($matches[1], true);
        // Output the extracted server information
        $server = json_encode($serversData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    } else {
        echo 'Error: Server information not found.';
		$server = json_encode(array());
    }
    $servers = json_decode($server,true);
	$links = "<div class='row m-0' >";
	$counter = 0;
	$notWanted = ["vembed.net","uqload.co","uqload.com","iioo.vadbam.net","emma.viidshar.com","uptostream.com", "embedv.net", "fdewsdc.sbs","ok.ru", "doodstream.com"];
	$y = 1;
	for( $i = 0; $i < sizeof($servers); $i++ ){
		$domain = extractDomain($servers[$i]["url"]);
		if( !in_array(strtolower($domain),$notWanted) && isset($servers[$i]["url"]) ){
			$links .= "<div class='col-3 p-1'><a class='btn btn-secondary w-100' style='color:white' href='#' id='{$servers[$i]["url"]}' onclick='sendIdToIframe(\"{$servers[$i]["url"]}\"); return false;'>Serv-{$y}</a></div>";
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
