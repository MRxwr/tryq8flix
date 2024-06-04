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
    $html = file_get_contents($_POST["id"]);
    // Extract server information using regular expressions
    $pattern = '/let servers = JSON\.parse\(\'(.*?)\'\);/';
    preg_match($pattern, $html, $matches);
    if (isset($matches[1])) {
        $serversData = json_decode($matches[1], true);
        // Output the extracted server information
        $server = json_encode($serversData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    } else {
        echo 'Error: Server information not found.';
    }
    $servers = json_decode($server,true);
	$links = "<div class='row m-0' >";
	$counter = 0;
	$notWanted = ["vembed.net","uqload.co","iioo.vadbam.net","emma.viidshar.com","uptostream.com"];
	for( $i = 0; $i < sizeof($servers); $i++ ){
		$domain = extractDomain($servers[$i]["url"]);
		if( !in_array(strtolower($domain),$notWanted) && isset($servers[$i]["url"]) && @!empty(file_get_contents($servers[$i]["url"])) ){
			$links .= "<div class='col-6 p-1'><a class='btn btn-secondary w-100' style='color:white' href='#' id='{$servers[$i]["url"]}' onclick='sendIdToIframe(\"{$servers[$i]["url"]}\"); return false;'>{$domain}</a></div>";
			$server = $servers[$i]["url"];
			if( $counter == 3 ){
				break;
			}else{
				$counter++;
			}
		}
	}
	$links .= "</div>";
    $videoTag = "{$links}<iframe id='frame' src='{$server}' style='width:100%;height:300px;margin-top: 30px;' sandbox='allow-same-origin allow-scripts'></iframe>";
    echo $videoTag;
}
?>
