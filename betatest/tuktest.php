<?php
header('Content-type: application/json');
$url = "https://tuktukcinema.net/wp-admin/admin-ajax.php";

$contents = file_get_contents('https://tuktukcinema.net/series/%d8%a7%d9%84%d9%85%d9%88%d8%b3%d9%85-1-%d9%85%d8%b3%d9%84%d8%b3%d9%84-desperate-housewives-%d9%85%d8%aa%d8%b1%d8%ac%d9%85/');

$id = explode('data-id="',$contents);
$id = explode('"',$id[1]);
$id = $id[0];

$slug = explode('data-slug="',$contents);
$slug = explode('"',$slug[1]);
$slug = $slug[0];

$parent = explode('data-parent="',$contents);
$parent = explode('"',$parent[1]);
$parent = $parent[0];


//The data you want to send via POST
$fields = [
'action'   => "getTabsInsSeries",
'id' 		=> $id,
'slug'      => $slug,
'parent'    => $parent
];

//url-ify the data for the POST
$fields_string = http_build_query($fields);

//open connection
$ch = curl_init();

//set the url, number of POST vars, POST data
curl_setopt($ch,CURLOPT_URL, $url);
curl_setopt($ch,CURLOPT_POST, true);
curl_setopt($ch,CURLOPT_POSTFIELDS, $fields_string);

//So that curl_exec returns the contents of the cURL; rather than echoing it
curl_setopt($ch,CURLOPT_RETURNTRANSFER, true); 

//execute post
$result = curl_exec($ch);

$content = explode('getMoreByScroll',$result);
$content = explode('</section>',$content[1]);

//echo $content[0]; die();
$links = explode('href="',$content[0]);

for ( $i = 1; $i < sizeof($links) ; $i++ )
{
	$tukyuklink = explode('"',$links[$i]);
	//echo $tukyuklink[0];die();
	$tuktukLinks = $tukyuklink[0];
	$uptoboxLink = explode('uptobox.com/',file_get_contents($tuktukLinks."watch"));
	$uptoboxLink = explode('"',$uptoboxLink[1]);
	$uptoboxLinks[] = 'https://uptobox.com/' . $uptoboxLink[0];
}

print_r(json_encode(array_reverse($uptoboxLinks)));

/* action:'getTabsInsSeries',
id:$('.loadTabsInSeries').data('id'),
slug:$('.loadTabsInSeries').data('slug'),
parent:$('.loadTabsInSeries').data('parent'), */

?>