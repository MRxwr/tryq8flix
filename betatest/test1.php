<?php
$url = file_get_contents("https://www.imdb.com/title/tt15130518/");
$explode = explode('application/ld+json">',$url);
$explode1 = explode('</script><meta property="og:url"',$explode[1]);
$string = $explode1[0];
/*
$string = '{"@context":"https://schema.org",
"@type":"TVSeries",
"url":"/title/tt15130518/",
"name":"The Walking Dead: Origins",
"image":"https://m.media-amazon.com/images/M/MV5BNWIyNThjYWQtM2Y5Yi00ZjNhLTgwZmQtMDUwZTkxMDY5MDI0XkEyXkFqcGdeQXVyOTI3NzkwNzQ@._V1_.jpg",
"genre":["Documentary"],
"actor":[
	{
		"@type":"Person",
		"url":"/name/nm1659348/",
		"name":"Lauren Cohan"
	},
	{
		"@type":"Person",
		"url":"/name/nm0564350/",
		"name":"Melissa McBride"
	},
	{
		"@type":"Person",
		"url":"/name/nm0604742/",
		"name":"Jeffrey Dean Morgan"
	}
	],
"creator":[{
	"@type":"Organization",
	"url":"/company/co0407837/"
}],
"datePublished":"2021-07-15",
"description":"Revisiting the stories of major characters, presented and narrated by the cast members who play them; taking a look back at key moments and events that affected them the most on their journeys through The Walking Dead Universe.",
"review":{
	"@type":"Review",
	"itemReviewed":{
		"@type":"CreativeWork",
		"url":"/title/tt15130518/"
	},
	"author":{
			"@type":"Person",
			"name":"marmrtdy"
			},
	"dateCreated":"2021-07-29",
	"inLanguage":"English",
	"name":"It&apos;s fun to watch old events with your favorite actors.",
	"reviewBody":"The idea of the series is great and I hope that it will have a second season and that they will add more and more characters.",
	"reviewRating":{
			"@type":"Rating",
			"worstRating":1,
			"bestRating":10,
			"ratingValue":10
		}
},
	"aggregateRating":{
		"@type":"AggregateRating",
		"ratingCount":33,
		"bestRating":10,
		"worstRating":1,
		"ratingValue":8.1
		}
}';
*/
$data = json_decode($string,true);
echo json_encode($data);
?>
