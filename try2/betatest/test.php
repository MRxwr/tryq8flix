<?php
/*
$url = file_get_contents($_POST["elcURL"]);

$explode = explode('<div class="panel jumbo">',$url);
$explode = explode('<div class="row collapse medium-uncollapse">',$explode[1]);
$content = $explode[0];

$arabicTitle = explode('dir="rtl">',$content);
$arabicTitle = explode('</span>',$arabicTitle[1]);
$output['arabicTitle'] = $arabicTitle[0];

$englishTitle = explode('dir="ltr">',$content);
$englishTitle = explode('</span>',$englishTitle[1]);
$output['englishTitle'] = $englishTitle[0];

$poster = explode('<img src="',$content);
$poster = explode('" alt',$poster[1]);
$output['poster'] = $poster[0];

$countryTime = explode('<ul class="list-separator">',$content);
$countryTime = explode('<li></li>',$countryTime[1]);
$countryTime = $countryTime[0];

$country = explode('<li>',$countryTime);
$country = explode('">',$country[2]);
$country = explode('</a></li>',$country[1]);
$output['country'] = $country[0];

$time = explode('<li>',$countryTime);
$time = explode('</li>',$time[3]);
$time = explode(' ',$time[0]);
$output['time'] = $time[0] . " min";

$year = explode('release_year',$content);
$year = explode('">',$year[1]);
$year = explode('</a>',$year[1]);
$output['year'] = $year[0];

$details = explode('<p>',$content);
$details = explode('</p>',$details[1]);
$output['details'] = $details[0];

$staff = explode('<ul class="list-separator list-title">',$content);
$staff = explode('cast">',$staff[3]);
$actors = $staff[0];
$actors = explode('">',$actors);

$allActros = "";
for ( $i = 1 ; $i < sizeof($actors) ; $i++ ){
	$subActor = explode('</a>',$actors[$i]);
	$allActros = $subActor[0] . ", " . $allActros;
}
$output['actors'] = $allActros;

$output['genre'] = "Comedy, Drama, Ramadan";
*/
?>