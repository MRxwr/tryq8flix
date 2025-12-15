<?php
include_once ("includes/config.php");
include_once("includes/checksouthead.php");

$url = 'https://uptobox.com/api/upload';
$data = [
    'token' => 'c7592f3d7e8a2c6682fb51ebd2e9d96f6uvoo'
];

$curl = curl_init();
curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "GET");
curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
curl_setopt($curl, CURLOPT_URL, $url);

$result = curl_exec($curl);
curl_close($curl);

$result = explode('uploadLink":"',$result);
$result = explode('"',$result[1]);
$resultuptobox = str_replace("upload","remote",$result[0]);
 
error_reporting(E_ERROR | E_PARSE);
error_reporting(E_ALL ^ E_NOTICE);  
ini_set('max_execution_time', 1000000);

if ( isset( $_POST["videolinks"] ) AND $_POST["videolinks"] != "" )
{
	if ( isset($_POST["site"]))
	{
		$state = "";
		$valid = "";
		
		if ( $_POST["site"] == "egyAnime" )
		{
			$array = preg_replace('/\n+/', "\n", trim($_POST['videolinks']));
			$array = explode(PHP_EOL, $array);
			$newarray = array();
			$finallinks = array();
			$y = 0;
			
			while ( $y < sizeof($array) )
			{
				$array[$y] = trim(preg_replace('/\s+/', ' ', $array[$y]));
				$getcontents = file_get_contents($array[$y]);
				$explode = explode('href="https://uptobox.com/',$getcontents);
				$explode = explode('">',$explode[1]);
				$finallinks[] = "https://uptobox.com/" . $explode[0];

				$y = $y + 1;
			}
		}
		
		if ( $_POST["site"] == "shahid4uNew" )
		{
			$array = preg_replace('/\n+/', "\n", trim($_POST['videolinks']));
			$array = explode(PHP_EOL, $array);
			$i = 0;
			$finallinks = array();
			while ( $i < sizeof($array) )
			{
				$array[$i] = trim(preg_replace('/\s+/', ' ', $array[$i]."download/"));
				$curl = curl_init();
				curl_setopt_array($curl, array(
				  CURLOPT_URL => $array[$i],
				  CURLOPT_RETURNTRANSFER => true,
				  CURLOPT_ENCODING => '',
				  CURLOPT_MAXREDIRS => 10,
				  CURLOPT_TIMEOUT => 0,
				  CURLOPT_FOLLOWLOCATION => true,
				  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
				  CURLOPT_CUSTOMREQUEST => 'GET',
				));
				$getcontents = curl_exec($curl);
				curl_close($curl);
				var_dump($getcontents);die();
				$explode1 = explode('uptobox.com/',$getcontents);
				$explode2 = explode('"',$explode1[1]);
				$finallinks[] = "https://www.uptobox.com/" . $explode2[0];
				$i = $i + 1 ;
			}
		}
		
		if ( $_POST["site"] == "cimalight" )
		{
			$array = preg_replace('/\n+/', "\n", trim($_POST['videolinks']));
			$array = explode(PHP_EOL, $array);
			$newarray = array();
			$finallinks = array();
			$y = 0;
			
			while ( $y < sizeof($array) )
			{
				$array[$y] = trim(preg_replace('/\s+/', ' ', str_replace("watch","download",$array[$y])));
				$getcontents = file_get_contents($array[$y]);
				$explode = explode('href="https://uptobox.com/',$getcontents);
				$explode = explode('"',$explode[1]);
				$finallinks[] = "https://uptobox.com/" . $explode[0];

				$y = $y + 1;
			}
		}
		
		if ( $_POST["site"] == "cimahouse" )
		{
			$array = preg_replace('/\n+/', "\n", trim($_POST['videolinks']));
			$array = explode(PHP_EOL, $array);
			$newarray = array();
			$finallinks = array();
			$y = 0;
			
			while ( $y < sizeof($array) )
			{
				$array[$y] = trim(preg_replace('/\s+/', ' ', $array[$y]));
				$getcontents = file_get_contents($array[$y]);
				$explode = explode('uptobox.com/',$getcontents);
				$explode = explode('"',$explode[1]);
				$finallinks[] = "https://uptobox.com/" . $explode[0];
				$y = $y + 1;
			}
		}
		
		if ( $_POST["site"] == "egybest2" )
		{
			$array = preg_replace('/\n+/', "\n", trim($_POST['videolinks']));
			$array = explode(PHP_EOL, $array);
			$newarray = array();
			$finallinks = array();
			$y = 0;
			
			while ( $y < sizeof($array) )
			{
				$array[$y] = trim(preg_replace('/\s+/', ' ', $array[$y]));
				$getcontents = file_get_contents($array[$y]);
				$explode = explode('uptobox',$getcontents);
				$explode = explode('href="',$explode[1]);
				$explode1 = explode('"',$explode[1]);

				$getcontents = file_get_contents($explode1[0]);
				$explode1 = explode("https://uptobox.com/",$getcontents);
				$explode2 = explode('?',$explode1[1]);
				$finallinks[] = "https://uptobox.com/" . $explode2[0];

				$y = $y + 1;
			}
		}

		if ( $_POST["site"] == "asiatvdrama" )
		{
			$array = preg_replace('/\n+/', "\n", trim($_POST['videolinks']));
			$array = explode(PHP_EOL, $array);
			$newarray = array();
			$finallinks = array();
			$y = 0;
					
			while ( $y < sizeof($array) )
			{
				$array[$y] = trim(preg_replace('/\s+/', ' ', $array[$y]));
				$getcontents = file_get_contents($array[$y]);
				$explode1 = explode("https://uptostream.com/iframe/",$getcontents);
				$explode2 = explode('"',$explode1[1]);
				$finallinks[] = "https://uptobox.com/" . $explode2[0];
				$y = $y + 1;
			}
		}
		
		if ( $_POST["site"] == "animelek" )
		{
			$array = preg_replace('/\n+/', "\n", trim($_POST['videolinks']));
			$array = explode(PHP_EOL, $array);
			$newarray = array();
			$finallinks = array();
			$y = 0;
					
			while ( $y < sizeof($array) )
			{
				$array[$y] = trim(preg_replace('/\s+/', ' ', $array[$y]));
				$getcontents = file_get_contents($array[$y]);
				$explode1 = explode("https://uptobox.com/",$getcontents);
				$explode2 = explode('"',$explode1[1]);
				$finallinks[] = "https://uptobox.com/" . $explode2[0];
				$y = $y + 1;
			}
		}
		
		if ( $_POST["site"] == "4helallive" )
		{
			$array = preg_replace('/\n+/', "\n", trim($_POST['videolinks']));
			$array = explode(PHP_EOL, $array);
			$newarray = array();
			$i = 0;
			while ( $i < sizeof($array) )
			{
				$array[$i] = trim(preg_replace('/\s+/', ' ', $array[$i]));
				$contents = file_get_contents("$array[$i]");
				$contentsExplode1 = explode ( " data-post=", $contents);
				$contentsExplode2 = explode ( " data-server", $contentsExplode1[1]);
				$postid = $contentsExplode2[0];
				$url = "https://4helal.live/wp-content/themes/helalbest/Ajax/server.php";

				//The data you want to send via POST
				$fields = [
					'i'         => '7',
					'id'         => $postid
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
				$explode1 = explode ( "https://uptostream.com/iframe/" ,$result );
				if ( strstr ( $explode1[1] , "?D" ) !== false )
				{
					$explode2 = explode ( "?D", $explode1[1]);
				}
				else
				{
					$explode2 = explode ( "?d", $explode1[1]);
				}
				
				$finallinks[] = "https://uptobox.com/" . $explode2[0];
				$i = $i + 1 ;
			}
		}
		
		if ( $_POST["site"] == "4helallive7up" )
		{
			$array = preg_replace('/\n+/', "\n", trim($_POST['videolinks']));
			$array = explode(PHP_EOL, $array);
			$newarray = array();
			$i = 0;
			while ( $i < sizeof($array) )
			{
				$array[$i] = trim(preg_replace('/\s+/', ' ', $array[$i]));
				$contents = file_get_contents("$array[$i]");
				$contentsExplode1 = explode ( " data-post=", $contents);
				$contentsExplode2 = explode ( " data-server", $contentsExplode1[1]);
				$postid = $contentsExplode2[0];
				$url = "https://4helal.live/wp-content/themes/helalbest/Ajax/server.php";

				//The data you want to send via POST
				$fields = [
					'i'         => '8',
					'id'         => $postid
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
				$explode1 = explode ( "https://7-up.net/embed-" ,$result );
				if ( strstr ( $explode1[1] , ".html" ) !== false )
				{
					$explode2 = explode ( '"', $explode1[1]);
				}
				else
				{
					$explode2 = explode ( '"', $explode1[1]);
				}
				
				$url1 = "https://7-up.net/" . $explode2[0];
				$finallink7up = file_get_contents($url1);
				$explode7up = explode('"id" value="',$finallink7up);
				$explode7up = explode('"',$explode7up[1]);
				$code = $explode7up[0];
				//The data you want to send via POST
				$fields = [
					'op'         => 'download2',
					'id'         => $code,
					'rand'         => '',
					'referer'         => '',
					'method_free'         => '',
					'method_premium'         => ''
				];

				//url-ify the data for the POST
				$fields_string = http_build_query($fields);

				//open connection
				$ch = curl_init();

				//set the url, number of POST vars, POST data
				curl_setopt($ch,CURLOPT_URL, $url1);
				curl_setopt($ch,CURLOPT_POST, true);
				curl_setopt($ch,CURLOPT_POSTFIELDS, $fields_string);

				//So that curl_exec returns the contents of the cURL; rather than echoing it
				curl_setopt($ch,CURLOPT_RETURNTRANSFER, true); 

				//execute post
				$result = curl_exec($ch);
				$explode1 = explode ( 'id="direct_link"' ,$result );
				$explode1 = explode ( '<a href="' ,$explode1[1] );
				$explode1 = explode ( '"' ,$explode1[1] );
				if ( strstr ( $explode1[1] , ".html" ) !== false )
				{
					$explode2 = explode ( '"', $explode1[1]);
				}
				else
				{
					$explode2 = explode ( '"', $explode1[1]);
				}
				
				$finallinks[] = $explode1[0];
				
				$i = $i + 1 ;
			}
		}
		
		
		if ( $_POST["site"] == "xsanime" )
		{
			$array = preg_replace('/\n+/', "\n", trim($_POST['videolinks']));
			$array = explode(PHP_EOL, $array);
			$newarray = array();
			$finallinks = array();
			$y = 0;
					
			while ( $y < sizeof($array) )
			{
				$array[$y] = trim(preg_replace('/\s+/', ' ', $array[$y]));
				$getcontents = file_get_contents($array[$y]);
				$explode1 = explode("https://letsupload.co/",$getcontents);
				$explode2 = explode('" target',$explode1[1]);
				$finallinks[] = "https://letsupload.co/" . $explode2[0];
				$y = $y + 1;
			}
		}
		
		if ( $_POST["site"] == "dotrani" )
		{
			$array = preg_replace('/\n+/', "\n", trim($_POST['videolinks']));
			$array = explode(PHP_EOL, $array);
			$newarray = array();
			$finallinks = array();
			$y = 0;
					
			while ( $y < sizeof($array) )
			{
				$array[$y] = trim(preg_replace('/\s+/', ' ', $array[$y]));
				$getcontents = file_get_contents($array[$y]);
				$explode1 = explode("content='techpro: ",$getcontents);
				$explode2 = explode("' property",$explode1[1]);
				$finallinks[] = $explode2[0];
				$y = $y + 1;
			}
		}
		
		if ( $_POST["site"] == "filmydz" )
		{
			$array = preg_replace('/\n+/', "\n", trim($_POST['videolinks']));
			$array = explode(PHP_EOL, $array);
			$newarray = array();
			$finallinks = array();
			$y = 0;
					
			while ( $y < sizeof($array) )
			{
				$array[$y] = trim(preg_replace('/\s+/', ' ', $array[$y]));
				$getcontents = file_get_contents($array[$y]);
				$explode1 = explode("uptobox.com/",$getcontents);
				$explode2 = explode('"',$explode1[1]);
				$finallinks[] = "https://uptobox.com/" . $explode2[0];
				$y = $y + 1;
			}
		}
		
		if ( $_POST["site"] == "mycimaSeasons" )
		{
			$array = preg_replace('/\n+/', "\n", trim($_POST['videolinks']));
			$array = explode(PHP_EOL, $array);
			$newarray = array();
			$finallinks = array();
			$y = 0;
					
			while ( $y < sizeof($array) )
			{
				$array[$y] = trim(preg_replace('/\s+/', ' ', $array[$y]));
				$getcontents = file_get_contents($array[$y]);
				$explode1 = explode('selected="series"',$getcontents);
				$explode2 = explode('PostsScrollLoader',$explode1[1]);
				$videos = $explode2[0];
				$overflowcount = substr_count($videos,'href="');
				$explode = explode('href="',$videos);
					
				$i = 0;
				while ( $i < $overflowcount )
				{
					$explode3 = explode('"',$explode[$i]);
					$newarray[] = $explode3[0];
					$i = $i + 1 ;
				}

				$i = 0;
				while ( $i < sizeof($newarray) )
				{
					if ( strstr($newarray[$i],"https://") )
					{
						$getcontents = file_get_contents($newarray[$i]);
						$explode4 = explode('WatchServersList',$getcontents);
						$explode5 = explode('href="',$explode4[1]);
						$explode6 = explode('"',$explode5[1]);
						$finallinks[] = $explode6[0];
					}
					$i = $i + 1 ;
				}
				$y = $y + 1;
			}
		}
		
		if ( $_POST["site"] == "Mycima.co" )
		{
			$array = preg_replace('/\n+/', "\n", trim($_POST['videolinks']));
			$array = explode(PHP_EOL, $array);
			$i = 0;
			$finallinks = array();
			while ( $i < sizeof($array) )
			{
				$array[$i] = trim(preg_replace('/\s+/', ' ', $array[$i]));
				$getcontents = file_get_contents($array[$i]);
				$explode1 = explode("https://uptostream.com/iframe/",$getcontents);
				$explode2 = explode('?Key',$explode1[1]);
				$finallinks[] = "https://uptobox.com/" . $explode2[0];
				$i = $i + 1 ;
			}
		}
		
		if ( $_POST["site"] == "cima4useasons" )
		{
			$array = preg_replace('/\n+/', "\n", trim($_POST['videolinks']));
			$array = explode(PHP_EOL, $array);
			$newarray = array();
			$finallinks = array();
			$y = 0;
					
			while ( $y < sizeof($array) )
			{
				$array[$y] = trim(preg_replace('/\s+/', ' ', $array[$y]));
				$getcontents = file_get_contents($array[$y]);
				$explode1 = explode('class="blocks"',$getcontents);
				$explode2 = explode('class="pagination"',$explode1[1]);
				$videos = $explode2[0];
				$overflowcount = substr_count($videos,'href="https://cima4u.vip/');
				$explode = explode('href="https://cima4u.vip/',$videos);	
				
				$i = 0;
				while ( $i < $overflowcount )
				{
					$explode3 = explode('"',$explode[$i+1]);
					$newarray[] = "https://cima4u.vip/" . $explode3[0];
					$i = $i + 1 ;
				} 

				$i = 0;
				while ( $i < sizeof($newarray) )
				{
					$getcontents = file_get_contents($newarray[$i]);
					$explode1 = explode ("https://uptobox.com/" ,$getcontents);
					$explode2 = explode ("?", $explode1[1]);
					$finallinks[] = "https://uptobox.com/" . $explode2[0];
					$i = $i + 1 ;
				}
				$y = $y + 1;
			}
		}
		
		if ( $_POST["site"] == "cera.video" )
		{
			$array = preg_replace('/\n+/', "\n", trim($_POST['videolinks']));
			$array = explode(PHP_EOL, $array);
			$newarray = array();
			$i = 0;
			while ( $i < sizeof($array) )
			{
				$array[$i] = trim(preg_replace('/\s+/', ' ', $array[$i]));
				$contents = file_get_contents("$array[$i]");
				$contentsExplode1 = explode ( " data-post=", $contents);
				$contentsExplode2 = explode ( " data-server", $contentsExplode1[1]);
				$postid = $contentsExplode2[0];
				$url = "https://cera.video/wp-content/themes/helalbest/Ajax/server.php";

				//The data you want to send via POST
				$fields = [
					'i'         => '7',
					'id'         => $postid
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
				$explode1 = explode ( "https://uptostream.com/iframe/" ,$result );
				$explode2 = explode ( "?D", $explode1[1]);
				$finallinks[] = "https://uptobox.com/" . $explode2[0];
				$i = $i + 1 ;
			}
		}
		
		if ( $_POST["site"] == "cima4u.vip" )
		{
			$array = preg_replace('/\n+/', "\n", trim($_POST['videolinks']));
			$array = explode(PHP_EOL, $array);
			$newarray = array();
			$i = 0;
			while ( $i < sizeof($array) )
			{
				$array[$i] = trim(preg_replace('/\s+/', ' ', $array[$i]));
				$contents = file_get_contents("$array[$i]");
				$contentsExplode1 = explode ( " data-post=", $contents);
				$contentsExplode2 = explode ( " data-server", $contentsExplode1[1]);
				$postid = $contentsExplode2[0];
				$url = "https://cima4u.vip/wp-content/themes/helalbest/Ajax/server.php";

				//The data you want to send via POST
				$fields = [
					'i'         => '7',
					'id'         => $postid
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
				$explode1 = explode ( "https://uptostream.com/iframe/" ,$result );
				$explode2 = explode ( "?D", $explode1[1]);
				$finallinks[] = "https://uptobox.com/" . $explode2[0];
				$i = $i + 1 ;
			}
		}
		
		if ( $_POST["site"] == "almstbavideo" )
		{
			$array = preg_replace('/\n+/', "\n", trim($_POST['videolinks']));
			$array = explode(PHP_EOL, $array);
			$newarray = array();
			$finallinks = array();
			$y = 0;
					
			while ( $y < sizeof($array) )
			{
				$array[$y] = trim(preg_replace('/\s+/', ' ', $array[$y]));
				$getcontents = file_get_contents($array[$y]);
				$explode1 = explode('<!-- Aired Episodes -->',$getcontents);
				$explode2 = explode('<!-- Aired Episodes -->',$explode1[1]);
				$videos = $explode2[0];
				$overflowcount = substr_count($videos,'https://www.almstbatv.com/watch.php?vid=');
				$explode = explode('https://www.almstbatv.com/watch.php?vid=',$videos);
					
				$i = 0;
				while ( $i < $overflowcount )
				{
					$explode3 = explode('"',$explode[$i]);
					$newarray[] = "https://www.almstbatv.com/watch.php?vid=" . $explode3[0];
					$i = $i + 1 ;
				}

				$i = 0;
				while ( $i < sizeof($newarray) )
				{
					$getcontents = file_get_contents($newarray[$i]);
					$explode4 = explode('<iframe src="',$getcontents);
					$explode5 = explode('"',$explode4[1]);
					$finallinks[] = $explode5[0]  ;
					$i = $i + 1 ;
				}
				$y = $y + 1;
			}
		}
		
		if ( $_POST["site"] == "almstbaseasons" ){
			$array = preg_replace('/\n+/', "\n", trim($_POST['videolinks']));
			$array = explode(PHP_EOL, $array);
			$newarray = array();
			$finallinks = array();
			$y = 0;
					
			while ( $y < sizeof($array) )
			{
				$array[$y] = trim(preg_replace('/\s+/', ' ', $array[$y]));
				$getcontents = file_get_contents($array[$y]);
				$explode1 = explode('class="SeasonsEpisodesMain"',$getcontents);
				$explode2 = explode('class="AiredEPS"',$explode1[1]);
				$videos = $explode2[0];
				$overflowcount = substr_count($videos,'https://www.almstbatv.com/watch.php?vid=');
				$explode = explode('https://www.almstbatv.com/watch.php?vid=',$videos);
					
				$i = 0;
				while ( $i < $overflowcount ){
					$explode3 = explode('"',$explode[$i]);
					$newarray[] = "https://www.almstbatv.com/watch.php?vid=" . $explode3[0];
					$i = $i + 1 ;
				}

				$i = 0;
				while ( $i < sizeof($newarray) ){
					$getcontents = file_get_contents($newarray[$i]);
					$explode4 = explode('https://uptobox.com/',$getcontents);
					$explode5 = explode('" rel=',$explode4[1]);
					$finallinks[] = "https://uptobox.com/" . $explode5[0]  ;
					$i = $i + 1 ;
				}
				$y = $y + 1;
			}
		}
		
		if ( $_POST["site"] == "shofhaseasons" ){
			$array = preg_replace('/\n+/', "\n", trim($_POST['videolinks']));
			$array = explode(PHP_EOL, $array);
			$newarray = array();
			$finallinks = array();
			$y = 0;
					
			while ( $y < sizeof($array) )
			{
				$array[$y] = trim(preg_replace('/\s+/', ' ', $array[$y]));
				$getcontents = file_get_contents($array[$y]);
				$explode1 = explode('class="SeasonsEpisodesMain"',$getcontents);
				$explode2 = explode('class="AiredEPS"',$explode1[1]);
				$videos = $explode2[0];
				$overflowcount = substr_count($videos,'https://www.shofha.tv/watch.php?vid=');
				$explode = explode('https://www.shofha.tv/watch.php?vid=',$videos);

				$i = 1;
				while ( $i < sizeof($explode) ){
					$explode3 = explode('"',$explode[$i]);
					$newarray[] = "https://www.shofha.tv/watch.php?vid=" . $explode3[0];
					$i++ ;
				}
				
				$i = 0;
				while ( $i < sizeof($newarray) ){
					$getcontents = file_get_contents(str_replace("watch","view",$newarray[$i]));
					$explode4 = explode('uptobox.com/',$getcontents);
					$explode5 = explode('" rel=',$explode4[1]);
					$finallinks[] = "https://uptobox.com/" . $explode5[0]  ;
					$i = $i + 1 ;
				}
				$y = $y + 1;
			}
		}
		
		if ( $_POST["site"] == "familymoviez" )
		{
			$array = preg_replace('/\n+/', "\n", trim($_POST['videolinks']));
			$array = explode(PHP_EOL, $array);
			$newarray = array();
			$finallinks = array();
			$y = 0;
					
			while ( $y < sizeof($array) )
			{
				$array[$y] = trim(preg_replace('/\s+/', ' ', $array[$y]));
				$getcontents = file_get_contents($array[$y]);
				$overflowcount = substr_count($getcontents,'class="watch_aflam"');
				$explode = explode('class="watch_aflam"',$getcontents);
					
				$i = 0;
				while ( $i < ($overflowcount+1) )
				{
					$explode1 = explode('" href="',$explode[$i]);
					$explode1 = explode('"',$explode1[1]);
					$newarray[] = $explode1[0];
					$i = $i + 1 ;
				}

				$i = 0;
				while ( $i < sizeof($newarray) )
				{
					$getcontents = file_get_contents($newarray[$i]);
					$explode1 = explode('<a class="stor2" href="',$getcontents);
					$explode2 = explode('">UpToBox',$explode1[1]);
					$getcontents1 = file_get_contents($explode2[0]);
					$explode3 = explode('launch a new download',$getcontents1);
					$explode4 = explode('<a href="',$explode3[1]);
					$explode5 = explode('"',$explode4[1]);
					$finallinks[] = str_replace("uptostream", "uptobox", $explode5[0] ) ;
					$i = $i + 1 ;
				}
				$y = $y + 1;
			}
		}
		
		if ( $_GET["site"] == "tuktuk" )
		{
			$array = preg_replace('/\n+/', "\n", trim($_POST['videolinks']));
			$array = explode(PHP_EOL, $array);
			$newarray = array();
			$i = 0;
			while ( $i < sizeof($array) )
			{
				$array[$i] = trim(preg_replace('/\s+/', ' ', $array[$i]));
				$checkWatch = strstr($array[$i],"watch");
				if ( $checkWatch != "" )
				{
					$newarray[] = $array[$i];
					goto jump;
				}
				$newarray[] = $array[$i]."watch/";
				jump:
				$i = $i + 1 ;
			}
			$i = 0;
			$finallinks = array();
			while ( $i < sizeof($newarray) )
			{
				$newarray[$i] = trim(preg_replace('/\s+/', ' ', $newarray[$i]));
				$getcontents = file_get_contents($newarray[$i]);
				$explode1 = explode("https://uptobox.com/",$getcontents);
				$explode2 = explode('" class',$explode1[1]);
				$finallinks[] = "https://uptobox.com/" . $explode2[0];
				$i = $i + 1 ;
			}
		}
		
		if ( $_POST["site"] == "tukseasons" ){
			$array = preg_replace('/\n+/', "\n", trim($_POST['videolinks']));
			$array = explode(PHP_EOL, $array);
			$newarray = array();
			$finallinks = array();
			$y = 0;
			while ( $y < sizeof($array) ){
				$array[$y] = trim(preg_replace('/\s+/', ' ', $array[$y]));
				
				if(!empty($array[$y])){
					$contents = file_get_contents($array[$y]);
				}
				
				$id = explode(' data-id="',$contents);
				$id = explode('"',$id[1]);
				$id = $id[0];
				$slug = explode('data-slug="',$contents);
				$slug = explode('"',$slug[1]);
				$slug = $slug[0];
				$parent = explode('data-parent="',$contents);
				$parent = explode('"',$parent[1]);
				$parent = $parent[0];
				
				$url = "https://t.tuktukcinema.co/wp-content/themes/Elshaikh/Inc/Ajax/";
				//The data you want to send via POST
				$fields = [
				'action'   => "getTabsInsSeries",
				'id' 		=> $id,
				'slug'      => $slug,
				'parent'    => $parent
				];
				//url-ify the data for the POST
				$fields_string = http_build_query($fields);
				//set the url, number of POST vars, POST data
				$ch = curl_init();
				curl_setopt($ch,CURLOPT_URL, $url);
				curl_setopt($ch,CURLOPT_POST, true);
				curl_setopt($ch,CURLOPT_POSTFIELDS, $fields_string);
				//So that curl_exec returns the contents of the cURL; rather than echoing it
				curl_setopt($ch,CURLOPT_RETURNTRANSFER, true); 
				//execute post
				$result = curl_exec($ch);
				curl_close($ch);

				$content = explode('getMoreByScroll',$result); 
				$content = explode('</section>',$content[1]);
				$links = explode('href="',$content[0]);
				
				for ( $i = 1; $i < sizeof($links) ; $i++ ){
					$tukyuklink = explode('"',$links[$i]);
					$tuktukLinks = $tukyuklink[0]."watch/";
					$uptoboxLink = explode('uptobox.com/',file_get_contents($tuktukLinks));
					$uptoboxLink = explode('"',$uptoboxLink[1]);
					$finallinks[] = 'https://uptobox.com/' . $uptoboxLink[0];
				}
				$y++;
			}	
		}
		
		if ( $_POST["site"] == "kporama" )
		{
			$array = preg_replace('/\n+/', "\n", trim($_POST['videolinks']));
			$array = explode(PHP_EOL, $array);
			$i = 0;
			$finallinks = array();
			while ( $i < sizeof($array) )
			{
				$array[$i] = trim(preg_replace('/\s+/', ' ', $array[$i]));
				$getcontents = file_get_contents($array[$i]);
				$explode1 = explode("https://uptobox.com/",$getcontents);
				$explode2 = explode('" class',$explode1[1]);
				$finallinks[] = "https://uptobox.com/" . $explode2[0];
				$i = $i + 1 ;
			}
		}

		if ( $_POST["site"] == "shofha" )
		{
			$array = preg_replace('/\n+/', "\n", trim($_POST['videolinks']));
			$array = explode(PHP_EOL, $array);
			$i = 0;
			$finallinks = array();
			while ( $i < sizeof($array) )
			{
				$array[$i] = trim(preg_replace('/\s+/', ' ', $array[$i]));
				$getcontents = file_get_contents(str_replace("watch","view",$array[$i]));
				$explode1 = explode("https://uptobox.com/",$getcontents);
				$explode2 = explode('" rel',$explode1[1]);
				$finallinks[] = "https://uptobox.com/" . $explode2[0];
				$i = $i + 1 ;
			}
		}
		
		if ( $_POST["site"] == "arabseed" )
		{
			$array = preg_replace('/\n+/', "\n", trim($_POST['videolinks']));
			$array = explode(PHP_EOL, $array);
			$newarray = array();
			$i = 0;
			while ( $i < sizeof($array) )
			{
				$array[$i] = trim(preg_replace('/\s+/', ' ', $array[$i]));
				$getcontents = file_get_contents($array[$i]);
				$explode1 = explode("https://arabseed.net/watch/",$getcontents);
				$explode2 = explode('" class',$explode1[1]);
				$newarray[] = "https://arabseed.net/watch/" . $explode2[0];
				$i = $i + 1 ;
			}

			$i = 0; 
			$finallinks = array();
			while ( $i < sizeof($newarray) )
			{
				$newarray[$i] = trim(preg_replace('/\s+/', ' ', $newarray[$i]));
				$getcontents = file_get_contents($newarray[$i]);
				$explode1 = explode("https://uptostream.com/iframe/",$getcontents);
				$explode2 = explode('&',$explode1[1]);
				$finallinks[] = "https://uptobox.com/" . $explode2[0];
				$i = $i + 1 ;
			}
		}
		
		if ( $_POST["site"] == "cimaclubNew" )
		{
			$array = preg_replace('/\n+/', "\n", trim($_POST['videolinks']));
			$array = explode(PHP_EOL, $array);
			$newarray = array();
			$i = 0;
			while ( $i < sizeof($array) )
			{
				$array[$i] = trim(preg_replace('/\s+/', ' ', $array[$i]));
				$getcontents = file_get_contents($array[$i]);
				$explode1 = explode("https://w.cimaclub.com/watch/",$getcontents);
				$explode2 = explode('" class',$explode1[1]);
				$newarray[] = "https://w.cimaclub.com/watch/" . $explode2[0];
				$i = $i + 1 ;
			}

			$i = 0;
			$finallinks = array();
			while ( $i < sizeof($newarray) )
			{
				$newarray[$i] = trim(preg_replace('/\s+/', ' ', $newarray[$i]));
				$getcontents = file_get_contents($newarray[$i]);
				$explode1 = explode("https://uptobox.com/",$getcontents);
				$explode2 = explode('" class',$explode1[1]);
				$finallinks[] = "https://uptobox.com/" . $explode2[0];
				$i = $i + 1 ;
			}
		}
		
		if ( $_POST["site"] == "cimaclub" )
		{
			$array = preg_replace('/\n+/', "\n", trim($_POST['videolinks']));
			$array = explode(PHP_EOL, $array);
			$i = 0;
			$finallinks = array();
			while ( $i < sizeof($array) )
			{
				$array[$i] = trim(preg_replace('/\s+/', ' ', $array[$i]));
				$getcontents = file_get_contents($array[$i]);
				$explode1 = explode('<a href="http://www.keeload.com/',$getcontents);
				$explode2 = explode('" target',$explode1[1]);
				$finallinks[] = "https://www.keeload.com/" . $explode2[0];
				$i = $i + 1 ;
			}
		}
		
		if ( $_POST["site"] == "cimaclubCOM" )
		{
			$array = preg_replace('/\n+/', "\n", trim($_POST['videolinks']));
			$array = explode(PHP_EOL, $array);
			$newarray = array();
			$i = 0;
			while ( $i < sizeof($array) )
			{
				$array[$i] = trim(preg_replace('/\s+/', ' ', $array[$i]));
				$url = $array[$i];

				//The data you want to send via POST
				$fields = [
					'__VIEWSTATE '      => $state,
					'__EVENTVALIDATION' => $valid,
					'view'         => '1'
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
				var_dump($result = curl_exec($ch));
				$explode1 = explode("https://uptobox.com/",$result);
				$explode2 = explode('">',$explode1[1]);
				$newarray[] = "https://uptobox.com/" . $explode2[0];
				$i = $i + 1 ;
			} 
		}

		if ( $_POST["site"] == "movs4u" )
		{
			$array = preg_replace('/\n+/', "\n", trim($_POST['videolinks']));
			$array = explode(PHP_EOL, $array);
			$newarray = array();
			$i = 0;
			while ( $i < sizeof($array) )
			{
				$array[$i] = trim(preg_replace('/\s+/', ' ', $array[$i]));
				
				$fields = [
					'__VIEWSTATE '      => $state,
					'__EVENTVALIDATION' => $valid,
					'view'         => '1'
				];

				//url-ify the data for the POST
				$fields_string = http_build_query($fields);

				//open connection
				$ch = curl_init();

				//set the url, number of POST vars, POST data
				curl_setopt($ch,CURLOPT_URL, $array[$i]);
				curl_setopt($ch,CURLOPT_POST, true);
				curl_setopt($ch,CURLOPT_POSTFIELDS, $fields_string);

				//So that curl_exec returns the contents of the cURL; rather than echoing it
				curl_setopt($ch,CURLOPT_RETURNTRANSFER, true); 

				//execute post
				$getcontents = curl_exec($ch);
				$explode1 = explode("download_link?server=uptobox&id=",$getcontents);
				$explode2 = explode('" target',$explode1[1]);
				$newarray[] = "https://www.movs4u.cc/download_link?server=uptobox&id=" . $explode2[0];
				$i = $i + 1 ;
			}

			$i = 0;
			$finallinks = array();
			while ( $i < sizeof($newarray) )
			{
				$newarray[$i] = trim(preg_replace('/\s+/', ' ', $newarray[$i]));
				$getcontents = file_get_contents($newarray[$i]);
				$explode1 = explode("https://uptobox.com/",$getcontents);
				$explode2 = explode('" rel',$explode1[1]);
				$finallinks[] = "https://uptobox.com/" . $explode2[0];
				$i = $i + 1 ;
			}
		}
		
		if ( $_POST["site"] == "Shahid4u" )
		{
			$finallinks = array();
			$array = preg_replace('/\n+/', "\n", trim($_POST['videolinks']));
			$array = explode(PHP_EOL, $array);
			$i = 0;
			while ( $i < sizeof($array) )
			{
				$array[$i] = trim(preg_replace('/\s+/', ' ', $array[$i]));
				
				$curl = curl_init();
				curl_setopt_array($curl, array(
				CURLOPT_URL => $array[$i],
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_ENCODING => '',
				CURLOPT_MAXREDIRS => 10,
				CURLOPT_TIMEOUT => 0,
				CURLOPT_FOLLOWLOCATION => true,
				CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
				CURLOPT_CUSTOMREQUEST => 'GET',
				CURLOPT_HTTPHEADER => array(
				'X-CSRF-TOKEN: MT6Yd7MbmqKDtkkUxBTq918662gI8gNKK7qMiF0V',
				'x-requested-with: XMLHttpRequest',
				  ),
				));
				$postId = curl_exec($curl);
				curl_close($curl);
				
				//$postId = file_get_contents($array[$i]);
				//$postId = explode('post_id=',$postId);
				//$postId = explode('"',$postId[1]);
				preg_match('/_post_id=([0-9]+)/', $postId, $matches);
				$postId = $matches[1];

				$curl1 = curl_init();
				curl_setopt_array($curl1, array(
				  CURLOPT_URL => 'https://shahed4uu.name/ajaxCenter?_action=getdownloadlinks&postId='.$postId,
				  CURLOPT_RETURNTRANSFER => true,
				  CURLOPT_ENCODING => '',
				  CURLOPT_MAXREDIRS => 10,
				  CURLOPT_TIMEOUT => 0,
				  CURLOPT_FOLLOWLOCATION => true,
				  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
				  CURLOPT_CUSTOMREQUEST => 'GET',
				  CURLOPT_HTTPHEADER => array(
					'X-CSRF-TOKEN: MT6Yd7MbmqKDtkkUxBTq918662gI8gNKK7qMiF0V',
					'x-requested-with: XMLHttpRequest',
				  ),
				));
				$response = curl_exec($curl1);
				curl_close($curl1);
				//$response = explode('uptobox.com/',$response);
				//$response = explode('"',$response[1]);
				//$response = "https://uptobox.com/" . $response[0];
				$pattern = '/href="(https:\/\/uptobox\.com\/[a-zA-Z0-9]+)"/';
				preg_match($pattern, $response, $matches);
				$finallinks[] = $matches[1];
				$i++;
			}
		}

		if ( $_POST["site"] == "elmstba" )
		{
			$array = preg_replace('/\n+/', "\n", trim($_POST['videolinks']));
			$array = explode(PHP_EOL, $array);
			$i = 0;
			$finallinks = array();
			while ( $i < sizeof($array) )
			{
				$array[$i] = trim(preg_replace('/\s+/', ' ', $array[$i]));
				$getcontents = file_get_contents($array[$i]);
				$explode1 = explode("https://uptobox.com/",$getcontents);
				$explode2 = explode('" rel',$explode1[1]);
				$finallinks[] = "https://uptobox.com/" . $explode2[0];
				$i = $i + 1 ;
			}
		}
		
		// new method \\
		if ( $_POST["site"] == "myfilmey" ){
			$array = preg_replace('/\n+/', "\n", trim($_POST['videolinks']));
			$array = explode(PHP_EOL, $array);
			for( $i = 0; $i < sizeof($array); $i++ ){
				$array[$i] = trim(preg_replace('/\s+/', ' ', $array[$i]));
				$getcontents = file_get_contents($array[$i]);
				$pattern = '/href="(https:\/\/uptobox\.com\/[^"]+)"/';
				preg_match($pattern, $getcontents, $matches);
				$finallinks[] = $matches[1];
			}
		}
		
		if ( $_POST["site"] == "anime4up" )
		{
			$array = preg_replace('/\n+/', "\n", trim($_POST['videolinks']));
			$array = explode(PHP_EOL, $array);
			$i = 0;
			$finallinks = array();
			while ( $i < sizeof($array) )
			{
				$array[$i] = trim(preg_replace('/\s+/', ' ', $array[$i]));
				$getcontents = file_get_contents($array[$i]);
				$explode1 = explode("https://uptobox.com/",$getcontents);
				$size = sizeof($explode1);
				$explode2 = explode('">',$explode1[$size-1]);
				$finallinks[] = "https://uptobox.com/" . $explode2[0];
				$i = $i + 1 ;
			}
		}
		
		if ( $_POST["site"] == "faselHdME" )
		{
			$array = preg_replace('/\n+/', "\n", trim($_POST['videolinks']));
			$array = explode(PHP_EOL, $array);
			$i = 0;
			$finallinks = array();
			while ( $i < sizeof($array) )
			{
				$array[$i] = trim(preg_replace('/\s+/', ' ', $array[$i]));
				$getcontents = file_get_contents($array[$i]);
				$explode1 = explode("https://uptobox.com/",$getcontents);
				$explode2 = explode('?',$explode1[1]);
				$finallinks[] = "https://uptobox.com/" . $explode2[0];
				$i = $i + 1 ;
			}
		}
		
		if ( $_POST["site"] == "faselhd" )
		{
			$array = preg_replace('/\n+/', "\n", trim($_POST['videolinks']));
			$array = explode(PHP_EOL, $array);
			$newarray = array();
			$i = 0;
			while ( $i < sizeof($array) )
			{
				$array[$i] = trim(preg_replace('/\s+/', ' ', $array[$i]));
				$url = $array[$i];

				//The data you want to send via POST
				$fields = [
					'__VIEWSTATE '      => $state,
					'__EVENTVALIDATION' => $valid,
					'View'         => '1'
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
				$explode1 = explode("https://www.t7meel.to/file/",$result);
				$explode2 = explode('"',$explode1[1]);
				$newarray[] = "https://www.t7meel.to/file/" . $explode2[0];
				$i = $i + 1 ;
			}

			$i = 0;
			$finallinks = array();
			while ( $i < sizeof($newarray) )
			{
				$newarray[$i] = trim(preg_replace('/\s+/', ' ', $newarray[$i]));
				$url = $newarray[$i];

				//The data you want to send via POST
				$fields = [
					'__VIEWSTATE '      => $state,
					'__EVENTVALIDATION' => $valid,
					'View'         => '1'
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
				$result = file_get_contents($newarray[$i]);
				$explode1 = explode("https://7-up.net/",$result);
				$explode2 = explode('"',$explode1[1]);
				
				////////////////////////////////
				$url1 = "https://7-up.net/" . $explode2[0];
				$finallink7up = file_get_contents($url1);
				$explode7up = explode('"id" value="',$finallink7up);
				$explode7up = explode('"',$explode7up[1]);
				$code = $explode7up[0];
				//The data you want to send via POST
				$fields = [
					'op'         => 'download2',
					'id'         => $code,
					'rand'         => '',
					'referer'         => '',
					'method_free'         => '',
					'method_premium'         => ''
				];

				//url-ify the data for the POST
				$fields_string = http_build_query($fields);

				//open connection
				$ch = curl_init();

				//set the url, number of POST vars, POST data
				curl_setopt($ch,CURLOPT_URL, $url1);
				curl_setopt($ch,CURLOPT_POST, true);
				curl_setopt($ch,CURLOPT_POSTFIELDS, $fields_string);

				//So that curl_exec returns the contents of the cURL; rather than echoing it
				curl_setopt($ch,CURLOPT_RETURNTRANSFER, true); 

				//execute post
				$result = curl_exec($ch);
				$explode1 = explode ( 'id="direct_link"' ,$result );
				$explode1 = explode ( '<a href="' ,$explode1[1] );
				$explode1 = explode ( '"' ,$explode1[1] );
				if ( strstr ( $explode1[1] , ".html" ) !== false )
				{
					$explode2 = explode ( '"', $explode1[1]);
				}
				else
				{
					$explode2 = explode ( '"', $explode1[1]);
				}
				
				$finallinks[] = $explode1[0];
				////////////////////////////////
				
				$i = $i + 1 ;
			}
		}
		
		if ( $_POST["site"] == "4helal" )
		{
			$array = preg_replace('/\n+/', "\n", trim($_POST['videolinks']));
			$array = explode(PHP_EOL, $array);
			$i = 0;
			$finallinks = array();
			while ( $i < sizeof($array) )
			{
				$array[$i] = trim(preg_replace('/\s+/', ' ', $array[$i]));
				$getcontents = file_get_contents($array[$i]);
				$explode1 = explode("https://uptobox.com/",$getcontents);
				$explode2 = explode('">',$explode1[1]);
				$finallinks[] = "https://uptobox.com/" . $explode2[0];
				$i = $i + 1 ;
			}
		}
		
		if ( $_POST["site"] == "4helal1" )
		{
			$array = preg_replace('/\n+/', "\n", trim($_POST['videolinks']));
			$array = explode(PHP_EOL, $array);
			$i = 0;
			$newarray = array();
			while ( $i < sizeof($array) )
			{
				$array[$i] = trim(preg_replace('/\s+/', ' ', $array[$i]));
				$getcontents = file_get_contents($array[$i]);
				$explode1 = explode("https://vidbob.com/",$getcontents);
				$explode2 = explode('">',$explode1[1]);
				$newarray[] = "https://vidbob.com/" . $explode2[0];
				$i = $i + 1 ;
			}
			
			$i = 0;
			$newarray2 = array();
			while ( $i < sizeof($newarray) )
			{
				$newarray[$i] = trim(preg_replace('/\s+/', ' ', $newarray[$i]));
				$getcontents = file_get_contents($newarray[$i]);
				$explode1 = explode("Normal quality",$getcontents);
				$explode2 = explode("download_video('",$explode1[1]);
				$id = explode("',",$explode2[1]);
				$mode = explode("'",$id[1]);
				$hash = explode("'",$id[2]);
				$newarray2[] = "https://vidbob.com/dl?op=download_orig&id=$id[0]&mode=$mode[1]&hash=$hash[1]";
				$i = $i + 1 ;
			}
			
			$i = 0;
			$finallinks = array();
			while ( $i < sizeof($newarray2) )
			{
				$newarray2[$i] = trim(preg_replace('/\s+/', ' ', $newarray2[$i]));
				$getcontents = file_get_contents($newarray2[$i]);
				$explode1 = explode('padding:7px;">',$getcontents);
				$explode2 = explode('href="https://',$explode1[1]);
				$id = explode('">',$explode2[1]);
				$finallinks[] = "https://".$id[0];
				$i = $i + 1 ;
			}
		}
		
		if ( $_POST["site"] == "animetak" )
		{
			$array = preg_replace('/\n+/', "\n", trim($_POST['videolinks']));
			$array = explode(PHP_EOL, $array);
			$i = 0;
			$finallinks = array();
			while ( $i < sizeof($array) )
			{
				$array[$i] = trim(preg_replace('/\s+/', ' ', $array[$i]));
				$getcontents = file_get_contents($array[$i]);
				$explode1 = explode("https://uptobox.com/",$getcontents);
				$explode2 = explode('">',$explode1[1]);
				$finallinks[] = "https://uptobox.com/" . $explode2[0];
				$i = $i + 1 ;
			}
		}
		
		if ( $_POST["site"] == "shahiidanime" )
		{
			$array = preg_replace('/\n+/', "\n", trim($_POST['videolinks']));
			$array = explode(PHP_EOL, $array);
			$newarray = array();
			$i = 0;
			while ( $i < sizeof($array) )
			{
				$array[$i] = trim(preg_replace('/\s+/', ' ', $array[$i]));
				$url = $array[$i];

				//The data you want to send via POST
				$fields = [
					'__VIEWSTATE '      => $state,
					'__EVENTVALIDATION' => $valid,
					'View'         => '1'
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
				$explode1 = explode("https://shahiid-anime.net/?download=",$result);
				$explode2 = explode('"',$explode1[1]);
				$newarray[] = "https://shahiid-anime.net/?download=" . $explode2[0];
				$i = $i + 1 ;
			}

			$i = 0;
			$finallinks = array();
			while ( $i < sizeof($newarray) )
			{
				$newarray[$i] = trim(preg_replace('/\s+/', ' ', $newarray[$i]));
				$url = $newarray[$i];

				//The data you want to send via POST
				$fields = [
					'__VIEWSTATE '      => $state,
					'__EVENTVALIDATION' => $valid,
					'View'         => '1'
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
				$explode1 = explode("https://uptobox.com/",$result);
				$explode2 = explode('">',$explode1[1]);
				$finallinks[] = "https://uptobox.com/" . $explode2[0];
				$i = $i + 1 ;
			}
		}
		
		if ( $_POST["site"] == "EGYdead" ){
			$array = preg_replace('/\n+/', "\n", trim($_POST['videolinks']));
			$array = explode(PHP_EOL, $array);
			$i = 0;
			$finallinks = array();
			while ( $i < sizeof($array) ){
				$array[$i] = trim(preg_replace('/\s+/', ' ', $array[$i]));
				$curl = curl_init();
				curl_setopt_array($curl, array(
				  CURLOPT_URL => $array[$i],
				  CURLOPT_RETURNTRANSFER => true,
				  CURLOPT_ENCODING => '',
				  CURLOPT_MAXREDIRS => 10,
				  CURLOPT_TIMEOUT => 0,
				  CURLOPT_FOLLOWLOCATION => true,
				  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
				  CURLOPT_CUSTOMREQUEST => 'POST',
				  CURLOPT_POSTFIELDS => array('View' => '1'),
				));
				$response = curl_exec($curl);
				curl_close($curl);
				preg_match('/href="([^"]*uptobox\.fr[^"]*)"/', $response, $matches);
				$finallinks[] = $matches[1];
				$i = $i + 1 ;
			}
		}
		
		if ( $_POST["site"] == "EGYdeadSeasons" ){
			$array = preg_replace('/\n+/', "\n", trim($_POST['videolinks']));
			$array = explode(PHP_EOL, $array);
			$y = 0;
			$finallinks = array();
			for( $y = 0; $y < sizeof($array); $y++ ){
				$array[$y] = trim(preg_replace('/\s+/', ' ', $array[$y]));
				$response = file_get_contents($array[$y]);
				$dom = new DOMDocument();
				@$dom->loadHTML($response);
				$xpath = new DOMXPath($dom);
				$links = $xpath->query('//div[@class="EpsList"]//a');
				foreach ($links as $link) {
					$href = $link->getAttribute("href");
					$EpLinks[] = $href . PHP_EOL;
				}
				for( $i = 0; $i < sizeof($EpLinks); $i++ ){
					$EpLinks[$i] = trim(preg_replace('/\s+/', ' ', $EpLinks[$i]));
					$curl = curl_init();
					curl_setopt_array($curl, array(
					  CURLOPT_URL => $EpLinks[$i],
					  CURLOPT_RETURNTRANSFER => true,
					  CURLOPT_ENCODING => '',
					  CURLOPT_MAXREDIRS => 10,
					  CURLOPT_TIMEOUT => 0,
					  CURLOPT_FOLLOWLOCATION => true,
					  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
					  CURLOPT_CUSTOMREQUEST => 'POST',
					  CURLOPT_POSTFIELDS => array('View' => '1'),
					));
					$response = curl_exec($curl);
					curl_close($curl);
					preg_match('/href="([^"]*uptobox\.[^"]*)"/', $response, $matches);
					$finallinks[] = $matches[1];
				}
			}
		}
		
		if ( $_POST["site"] == "vidbom" )
		{
			$array = preg_replace('/\n+/', "\n", trim($_POST['videolinks']));
			$array = explode(PHP_EOL, $array);
			$i = 0;
			$finallinks = array();
			while ( $i < sizeof($array) )
			{
				$array[$i] = trim(preg_replace('/\s+/', ' ', $array[$i]));
				$getcontents = file_get_contents($array[$i]);
				$explode1 = explode('file:"',$getcontents);
				$explode2 = explode('",',$explode1[1]);
				$finallinks[] = $explode2[0];
				$i = $i + 1 ;
			}
		}
		
		if ( $_POST["site"] == "shof4u" )
		{
			$array = preg_replace('/\n+/', "\n", trim($_POST['videolinks']));
			$array = explode(PHP_EOL, $array);
			$i = 0;
			$finallinks = array();
			while ( $i < sizeof($array) )
			{
				$array[$i] = trim(preg_replace('/\s+/', ' ', $array[$i]));
				$getcontents = file_get_contents($array[$i]."?download=1");
				$explode1 = explode("https://uptobox.com/",$getcontents);
				$explode2 = explode('">',$explode1[1]);
				$finallinks[] = "https://uptobox.com/" . $explode2[0];
				$i = $i + 1 ;
			}
		}
		
		if ( $_POST["site"] == "ceera" )
		{
			$array = preg_replace('/\n+/', "\n", trim($_POST['videolinks']));
			$array = explode(PHP_EOL, $array);
			$i = 0;
			$finallinks = array();
			while ( $i < sizeof($array) )
			{
				$array[$i] = trim(preg_replace('/\s+/', ' ', $array[$i]));
				$getcontents = file_get_contents($array[$i]);
				$explode1 = explode("uptobox.com/",$getcontents);
				$explode2 = explode('"',$explode1[1]);
				$finallinks[] = "https://uptobox.com/" . $explode2[0];
				$i = $i + 1 ;
			}
		}

		if ( $_POST["site"] == "ceerakeeload" )
		{
			$array = preg_replace('/\n+/', "\n", trim($_POST['videolinks']));
			$array = explode(PHP_EOL, $array);
			$i = 0;
			$finallinks = array();
			while ( $i < sizeof($array) )
			{
				$array[$i] = trim(preg_replace('/\s+/', ' ', $array[$i]));
				$getcontents = file_get_contents($array[$i]);
				$explode1 = explode('<a href="https://www.keeload.com/',$getcontents);
				$explode2 = explode('"  target',$explode1[1]);
				$finallinks[] = "https://www.keeload.com/" . $explode2[0];
				$i = $i + 1 ;
			}
		}
		
		if ( $_POST["site"] == "ceeraseasons" )
		{
			$array = preg_replace('/\n+/', "\n", trim($_POST['videolinks']));
			$array = explode(PHP_EOL, $array);
			$newarray = array();
			$finallinks = array();
			$y = 0;
			
			while ( $y < sizeof($array) )
			{
				$array[$y] = trim(preg_replace('/\s+/', ' ', $array[$y]));
				$getcontents = file_get_contents($array[$y]);
				$overflowcount = substr_count($getcontents,'class="Overflow">');
				$explode = explode('class="Overflow">',$getcontents);
			
				$i = 0;
				while ( $i < $overflowcount+1 )
				{
					$explode1 = explode('<a href="',$explode[$i]);
					$explode1 = explode('"',$explode1[1]);
					$newarray[] = $explode1[0];
					$i = $i + 1 ;
				}

				$i = 0;
				while ( $i < sizeof($newarray) )
				{
					$getcontents = file_get_contents($newarray[$i]);
					$explode1 = explode("uptobox.com/",$getcontents);
					$explode2 = explode('"',$explode1[1]);
					$finallinks[] = "https://uptobox.com/" . $explode2[0];
					$i = $i + 1 ;
				}
				$y = $y + 1;
			}
		}
		
		if ( $_POST["site"] == "mycima" )
		{
			$array = preg_replace('/\n+/', "\n", trim($_POST['videolinks']));
			$array = explode(PHP_EOL, $array);
			$i = 0;
			$finallinks = array();
			while ( $i < sizeof($array) )
			{
				$array[$i] = trim(preg_replace('/\s+/', ' ', $array[$i]));
				$getcontents = file_get_contents($array[$i]);
				$explode1 = explode("https://www.ok.ru/",$getcontents);
				$explode2 = explode('?Key',$explode1[1]);
				$finallinks[] = "https://www.ok.ru/" . $explode2[0];
				$i = $i + 1 ;
			}
		}
		
		if ( $_POST["site"] == "lodynet" )
		{
			$array = preg_replace('/\n+/', "\n", trim($_POST['videolinks']));
			$array = explode(PHP_EOL, $array);
			$i = 0;
			$finallinks = array();
			while ( $i < sizeof($array) )
			{
				$array[$i] = trim(preg_replace('/\s+/', ' ', $array[$i]));
				$getcontents = file_get_contents($array[$i]);
				$explode1 = explode("https://uptobox.com/",$getcontents);
				$explode2 = explode('" target',$explode1[1]);
				$finallinks[] = "https://uptobox.com/" . $explode2[0];
				$i = $i + 1 ;
			}
		}
		
		if ( $_POST["site"] == "zimabdko" )
		{
			$array = preg_replace('/\n+/', "\n", trim($_POST['videolinks']));
			$array = explode(PHP_EOL, $array);
			$i = 0;
			$finallinks = array();
			while ( $i < sizeof($array) )
			{
				$array[$i] = trim(preg_replace('/\s+/', ' ', $array[$i]));
				$getcontents = file_get_contents($array[$i]);
				$explode1 = explode("https://uptobox.com/",$getcontents);
				$explode2 = explode('">',$explode1[1]);
				$finallinks[] = "https://uptobox.com/" . $explode2[0];
				$i = $i + 1 ;
			}
		}
		
		if ( $_POST["site"] == "zimabdkoru" )
		{
			$array = preg_replace('/\n+/', "\n", trim($_POST['videolinks']));
			$array = explode(PHP_EOL, $array);
			$i = 0;
			$finallinks = array();
			while ( $i < sizeof($array) )
			{
				$array[$i] = trim(preg_replace('/\s+/', ' ', $array[$i]));
				$getcontents = file_get_contents($array[$i]);
				$explode1 = explode('data-post="',$getcontents);
				$explode2 = explode('">',$explode1[1]);
				$getcontents = file_get_contents('https://www.zimabdko.com/wp-admin/admin-ajax.php?action=codecanal_ajax_request&serv=_server_movie_01&post='.$explode2[0]);
				$explode1 = explode('https://www.ok.ru/videoembed/',$getcontents);
				$explode2 = explode('"',$explode1[1]);
				$finallinks[] = "https://www.ok.ru/video/" . $explode2[0];
				$i = $i + 1 ;
			}
		}
		
		if ( $_POST["site"] == "e7na" )
		{
			
			$array = preg_replace('/\n+/', "\n", trim($_POST['videolinks']));
			$array = explode(PHP_EOL, $array);
			$i = 0;
			$finallinks = array();
			while ( $i < sizeof($array) )
			{
				$array[$i] = trim(preg_replace('/\s+/', ' ', $array[$i]));
				$url = $array[$i];

				//The data you want to send via POST
				$fields = [
					'__VIEWSTATE '      => $state,
					'__EVENTVALIDATION' => $valid,
					'wtchBtn'         => ''
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
			
				//$getcontents = file_get_contents($result);
				$explode1 = explode("https://uptobox.com/",$result);
				$explode2 = explode('" target',$explode1[1]);
				$finallinks[] = "https://uptobox.com/" . $explode2[0];
				$i = $i + 1 ;
			}
		}

		if ( $_POST["site"] == "mazagat" )
		{
			$array = preg_replace('/\n+/', "\n", trim($_POST['videolinks']));
			$array = explode(PHP_EOL, $array);
			$newarray = array();
			$i = 0;
			while ( $i < sizeof($array) )
			{
				$array[$i] = trim(preg_replace('/\s+/', ' ', $array[$i]));
				$newarray[$i] = str_replace("watch","see",$array[$i]);
				$i = $i + 1 ;
			}

			$i = 0;
			$finallinks = array();
			while ( $i < sizeof($newarray) )
			{
				$newarray[$i] = trim(preg_replace('/\s+/', ' ', $newarray[$i]));
				$getcontents = file_get_contents($newarray[$i]);
				$explode1 = explode("https://uptobox.com/",$getcontents);
				$explode2 = explode('" class',$explode1[1]);
				$finallinks[] = "https://uptobox.com/" . $explode2[0];
				$i = $i + 1 ;
			}
		}
		
	}
}


?>

<!DOCTYPE html>
<html>
<title>Links Scrapper - Q8Flix</title>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="icon" href="images/logo.png">
<link rel="stylesheet" href="css/style1.css?dasd">
<link rel='stylesheet' href='https://fonts.googleapis.com/css?family=Roboto'>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
<style>
		.deleteLink,.sizeInfo,.text,.progressInfo,.eta
		{
			display: none;
		}
		textarea
		{
			width: 100%;
			height: 300px;
		}
		button
		{
			-moz-box-shadow:inset 0px 1px 0px 0px #ffffff;
			-webkit-box-shadow:inset 0px 1px 0px 0px #ffffff;
			box-shadow:inset 0px 1px 0px 0px #ffffff;
			background:-webkit-gradient(linear, left top, left bottom, color-stop(0.05, darkred), color-stop(1, #dfdfdf));
			background:-moz-linear-gradient(top, darkred 5%, red 100%);
			background:-webkit-linear-gradient(top, darkred 5%, red 100%);
			background:-o-linear-gradient(top, darkred 5%, red 100%);
			background:-ms-linear-gradient(top, darkred 5%, red 100%);
			background:linear-gradient(to bottom, darkred 5%, red 100%);
			filter:progid:DXImageTransform.Microsoft.gradient(startColorstr='darkred', endColorstr='red',GradientType=0);
			background-color:darkred;
			-moz-border-radius:6px;
			-webkit-border-radius:6px;
			border-radius:6px;
			border:1px solid #2d2d2d;
			display:inline-block;
			cursor:pointer;
			color:palegoldenrod;
			font-family:Arial;
			font-size:15px;
			padding:6px 24px;
			text-decoration:none;
			text-shadow:0px 1px 0px #000000;
			width: 100%;
		}
		button:hover 
		{
			background:-webkit-gradient(linear, left top, left bottom, color-stop(0.05, #dfdfdf), color-stop(1, darkred));
			background:-moz-linear-gradient(top, red 5%, darkred 100%);
			background:-webkit-linear-gradient(top, red 5%, darkred 100%);
			background:-o-linear-gradient(top, red 5%, darkred 100%);
			background:-ms-linear-gradient(top, red 5%, darkred 100%);
			background:linear-gradient(to bottom, red 5%, darkred 100%);
			filter:progid:DXImageTransform.Microsoft.gradient(startColorstr='red', endColorstr='darkred',GradientType=0);
			background-color:red;
		}
		button:active 
		{
			position:relative;
			top:1px;
		}

		html,body,h1,h2,h3,h4,h5,h6 {font-family: "Roboto", sans-serif}
		.boxsizingBorder 
		{
			-webkit-box-sizing: border-box;
			-moz-box-sizing: border-box;
					box-sizing: border-box;
		}
			/* DivTable.com */
		.divTable{
			display: table;
			width: 100%;
		}
		.divTableRow {
			display: table-row;
		}
		.divTableHeading {
			background-color: #EEE;
			display: table-header-group;
		}
		.divTableCell, .divTableHead {
			border: 1px solid #999999;
			display: table-cell;
			padding: 3px 10px;
		}
		.divTableHeading {
			background-color: #EEE;
			display: table-header-group;
			font-weight: bold;
		}
		.divTableFoot {
			background-color: #EEE;
			display: table-footer-group;
			font-weight: bold;
		}
		.divTableBody {
			display: table-row-group;
		}
</style>

<body>
<!-- Page Container -->
<div class="w3-content" style="max-width:1300px">

  <!-- The Grid -->
  <div class="">
  <?php
include_once ("template/header.php");
?>
    <!-- Right Column -->
    <div class="w3-text-white" style="padding-top: 40px">
      <div class="w3-row-padding w3-padding-16 w3-center">
	<h3>Select a Website From Below: <h2 style="text-align: center;color: red"><?php if (isset($_GET["site"])){echo $_GET["site"] ;} ?></h2></h3>
<select name="site" style="width: 100%;" onchange="location = this.options[this.selectedIndex].value;">
	<option>...</option>
	<option value="getlinks.php?site=4helal1">4Helal Video</option>
	<option value="getlinks.php?site=4helal">4Helal</option>
	<option value="getlinks.php?site=4helallive">4Helal.LIVE UPtoBOX</option>
	<option value="getlinks.php?site=4helallive7up">4Helal.LIVE 7up</option>
	<option value="getlinks.php?site=anime4up">Anime4UP</option>
	<option value="getlinks.php?site=animelek">AnimeLek</option>
	<option value="getlinks.php?site=animetak">AnimeTAK</option>
	<option value="getlinks.php?site=arabseed">ArabSeed</option>
	<option value="getlinks.php?site=asiatvdrama">Asia.TV.Drama</option>
	<option value="getlinks.php?site=cimalight">CimaLight</option>
	<option value="getlinks.php?site=cimahouse">CimaHouse</option>
	<option value="getlinks.php?site=cera.video">Cera.Video</option>
	<option value="getlinks.php?site=ceera">CEERA</option>
	<option value="getlinks.php?site=ceerakeeload">CEERA Keeload</option>
	<option value="getlinks.php?site=ceeraseasons">CEERA SEASONS</option>
	<option value="getlinks.php?site=cimaclub">CimaClub</option>
	<option value="getlinks.php?site=cimaclubCOM">CimaClubCOM</option>
	<option value="getlinks.php?site=cimaclubNew">cimaclubNew</option>
	<option value="getlinks.php?site=cima4u.vip">Cima4u.vip</option>
	<option value="getlinks.php?site=cima4useasons">Cima4u.vip Seasons</option>
	<option value="getlinks.php?site=dotrani">DotRani Mega</option>
	<option value="getlinks.php?site=EGYdead">EGYDead</option>
	<option value="getlinks.php?site=EGYdeadSeasons">EGYDead Seasons</option>
	<option value="getlinks.php?site=elmstba">Elmstba</option>
	<option value="getlinks.php?site=almstbavideo">Elmstba Video</option>
	<option value="getlinks.php?site=almstbaseasons">Elmstba Seasons</option>
	<option value="getlinks.php?site=e7na">E7na</option>
	<option value="getlinks.php?site=filmydz">FilmyDZ</option>
	<option value="getlinks.php?site=familymoviez">FamilyMoviez</option>
	<option value="getlinks.php?site=faselhd">FASELhd</option>
	<option value="getlinks.php?site=faselHdME">FASELhd.ME</option>
	<option value="getlinks.php?site=tuktuk">TukTukCinema</option>
	<option value="getlinks.php?site=tukseasons">TukTukCinema Seasons</option>
	<option value="getlinks.php?site=kporama">KPOrama</option>
	<option value="getlinks.php?site=lodynet">Lodynet</option>
	<option value="getlinks.php?site=myfilmey">MyFilmey</option>
	<option value="getlinks.php?site=mazagat">Mazagat</option>
	<option value="getlinks.php?site=movs4u">Movs4U</option>
	<option value="getlinks.php?site=mycima">MyCima</option>
	<option value="getlinks.php?site=Mycima.co">MyCima.Co</option>
	<option value="getlinks.php?site=mycimaSeasons">MyCima Seasons</option>
	<option value="getlinks.php?site=shofha">Shofha</option>
	<option value="getlinks.php?site=shofhaseasons">Shofha Seasons</option>
	<option value="getlinks.php?site=shahiidanime">Shahid Anime</option>
	<option value="getlinks.php?site=shof4u">Shof4U</option>
	<option value="getlinks.php?site=Shahid4u">Shahid4U</option>
	<option value="getlinks.php?site=shahid4uNew">shahid4uNew</option>
	<option value="getlinks.php?site=vidbom">VidBom</option>
	<option value="getlinks.php?site=xsanime">XSanime</option>
	<option value="getlinks.php?site=zimabdko">Zimabdko UPtoBOX</option>
	<option value="getlinks.php?site=zimabdko">Zimabdko OK.RU</option>
</select>
	
<?php
if ( isset($_GET["site"]) AND !isset($finallinks) )
{

?>
<h3>Enter all links Below:	</h3>
<form method="post"	action="" enctype="multipart/form-data">
<textarea name="videolinks" style="width: 100%; height: 300px;"></textarea>
	<p></p>
	<input type="hidden" name="site" value="<?php if (isset($_GET["site"])){echo $_GET["site"] ;} ?>" />
<input type="submit" value="Get Links" style="width: 100%"/>
</form>

	<?php
}
	$i = 0 ;
	$displayedlinks = array();
	$uploadVar = "";
	
	if ( isset($_POST["videolinks"]) )
	{
		if ( isset($finallinks) )
		{
			if ( $_GET["site"] == "Mycima.co" OR $_GET["site"] = "mycimaSeasons" )
			{
				$finallinks = array_reverse($finallinks);
			}
			echo "<h3 style='text-align: center; color:red'>Your uptobox links are ready:</h3><textarea style='width:100%; height:300px'>";
			
			while ( $i < sizeof($finallinks) )
			{
				$emptylinks = explode("com/", $finallinks[$i]);
				$emptylinkslive = explode("cc/", $finallinks[$i]);
				$emptylinksco = explode("co/", $finallinks[$i]);
				$emptylinksmega = explode("mega.", $finallinks[$i]);
				$emptylinksxyz = explode("xyz", $finallinks[$i]);
				$emptylinksnet = explode("net", $finallinks[$i]);
				$emptylinkstv = explode("tv", $finallinks[$i]);
				$emptylinksru = explode("ru", $finallinks[$i]);
				$emptylinksme = explode("me", $finallinks[$i]);
				$emptylinksfr = explode("fr", $finallinks[$i]);
				if ( ($emptylinks[1] != "" AND !in_array($finallinks[$i],$displayedlinks)) OR ($emptylinkslive[1] != "" AND !in_array($finallinks[$i],$displayedlinks)) OR ($emptylinksco[1] != "" AND !in_array($finallinks[$i],$displayedlinks)) OR ($emptylinksmega[1] != "" AND !in_array($finallinks[$i],$displayedlinks)) OR ($emptylinksxyz[1] != "" AND !in_array($finallinks[$i],$displayedlinks)) OR ($emptylinksnet[1] != "" AND !in_array($finallinks[$i],$displayedlinks)) OR ($emptylinkstv[1] != "" AND !in_array($finallinks[$i],$displayedlinks)) OR ($emptylinksru[1] != "" AND !in_array($finallinks[$i],$displayedlinks)) OR ($emptylinksme[1] != "" AND !in_array($finallinks[$i],$displayedlinks)) OR ($emptylinksfr[1] != "" AND !in_array($finallinks[$i],$displayedlinks)) )
				{
					echo $finallinks[$i] . "\n";
					$displayedlinks[] = $finallinks[$i];
					$uploadVar = $finallinks[$i];
				}
				$i = $i + 1 ;
			}
			echo "</textarea>";
			?>
		  <h3 style='text-align: center; color:tan'>Move Links to Uptobox:</h3>
<div id="col" class='flex grow1 column'>
<div id='content-wrapper' class='content-wrapper-upload       '>
<div id='content' class='width-content' data-ui='{&quot;domain&quot;:&quot;uptobox.com&quot;,&quot;utbDomain&quot;:&quot;uptobox.com&quot;,&quot;utsDomain&quot;:&quot;uptostream.com&quot;,&quot;sendToUI&quot;:{&quot;domain&quot;:&quot;uptobox.com&quot;,&quot;utbDomain&quot;:&quot;uptobox.com&quot;,&quot;utsDomain&quot;:&quot;uptostream.com&quot;,&quot;domainCookie&quot;:&quot;.uptobox.com&quot;},&quot;uploadUrl&quot;:&quot;\/\/www110.uptobox.com\/remote?sess_id=NRbSbfcZc6h8BzuQzIT3&quot;,&quot;text&quot;:{&quot;upload_other_links&quot;:&quot;Upload other links&quot;,&quot;link_detected&quot;:&quot;link detected&quot;,&quot;links_detected&quot;:&quot;links detected&quot;,&quot;start_upload&quot;:&quot;Start upload&quot;,&quot;remote_upload_description&quot;:&quot;This feature allows you to upload files that are not located on your disk but from a different source. Paste one or multiple URLs in the fields below. Make sure to enter only one URL per line.&quot;,&quot;one_url_per_line&quot;:&quot;Enter one URL per line&quot;,&quot;error&quot;:&quot;An error prevented the upload to be done. Please try again later&quot;,&quot;download_link&quot;:&quot;Download link&quot;,&quot;delete_link&quot;:&quot;Delete link&quot;,&quot;all_time&quot;:&quot;All time&quot;,&quot;this_month&quot;:&quot;This month&quot;,&quot;this_year&quot;:&quot;This year&quot;,&quot;redirection_short&quot;:&quot;Redirection...&quot;,&quot;copy_to_clipboard&quot;:&quot;Copy to clipboard&quot;,&quot;copied_to_clipboard&quot;:&quot;Copied to clipboard&quot;,&quot;copied&quot;:&quot;Copied!&quot;,&quot;error_occured&quot;:&quot;An error occured&quot;,&quot;submit&quot;:&quot;Submit&quot;,&quot;click_here&quot;:&quot;Click here&quot;,&quot;invalid_format&quot;:&quot;Invalid format&quot;,&quot;loading&quot;:&quot;Loading&quot;,&quot;warning&quot;:&quot;Warning&quot;,&quot;more_details&quot;:&quot;More details&quot;,&quot;go_to_website&quot;:&quot;Go to website&quot;,&quot;empty&quot;:&quot;No data&quot;,&quot;time&quot;:{&quot;day&quot;:&quot;day&quot;,&quot;hour&quot;:&quot;hour&quot;,&quot;minute&quot;:&quot;minute&quot;,&quot;second&quot;:&quot;second&quot;,&quot;days&quot;:&quot;days&quot;,&quot;hours&quot;:&quot;hours&quot;,&quot;minutes&quot;:&quot;minutes&quot;,&quot;seconds&quot;:&quot;seconds&quot;},&quot;sizes&quot;:{&quot;perSecondNotation&quot;:&quot;ps&quot;,&quot;b&quot;:&quot;b&quot;,&quot;Kb&quot;:&quot;Kb&quot;,&quot;Mb&quot;:&quot;Mb&quot;,&quot;Gb&quot;:&quot;Gb&quot;,&quot;Tb&quot;:&quot;Tb&quot;,&quot;Pb&quot;:&quot;Pb&quot;,&quot;Kib&quot;:&quot;Kib&quot;,&quot;Mib&quot;:&quot;Mib&quot;,&quot;Gib&quot;:&quot;Gib&quot;,&quot;Tib&quot;:&quot;Tib&quot;,&quot;Pib&quot;:&quot;Pib&quot;,&quot;B&quot;:&quot;B&quot;,&quot;kB&quot;:&quot;kB&quot;,&quot;MB&quot;:&quot;MB&quot;,&quot;GB&quot;:&quot;GB&quot;,&quot;TB&quot;:&quot;TB&quot;,&quot;PB&quot;:&quot;PB&quot;,&quot;KiB&quot;:&quot;KiB&quot;,&quot;MiB&quot;:&quot;MiB&quot;,&quot;GiB&quot;:&quot;GiB&quot;,&quot;TiB&quot;:&quot;TiB&quot;,&quot;PiB&quot;:&quot;PiB&quot;},&quot;lang_upload_finished&quot;:&quot;Finished&quot;,&quot;lang_upload_ready_upload&quot;:&quot;Ready to upload&quot;,&quot;lang_upload_transcoding&quot;:&quot;Transcoding&quot;,&quot;lang_upload_waiting_transcode&quot;:&quot;Retrieving transcode data...&quot;,&quot;lang_upload_transcode_message&quot;:&quot;Your uploads just finished! The transcoding phase just started. You can close the tab if you want to.&quot;,&quot;lang_upload_wait_data&quot;:&quot;In queue&quot;,&quot;wait&quot;:&quot;Please wait &quot;,&quot;hours&quot;:&quot;hours&quot;,&quot;minutes&quot;:&quot;minutes&quot;,&quot;seconds&quot;:&quot;seconds&quot;,&quot;start&quot;:&quot;Start&quot;,&quot;cancel&quot;:&quot;Cancel&quot;}}'>
<div id='homepage'>

<div class='upload-container'>

<div class='tab-content local-upload block'>
<form id="fileupload" action="//www110.uptobox.com/upload?sess_id=n96TXKFvXzxkltYrWvow" method="POST" enctype="multipart/form-data">
<div id='localUpload'></div>
</form>
</div>
<div id='remote-tab' class='tab-content url-upload none'>
<div class='remote-url-content'></div>
</div>
</div>
</div>
</div>
</div>
</div>
		  <?php
		}
		else
		{
			echo "Could not find any link, Try again...";
		}
	}
	?>
    
  </div>
    </div>
<?php
include_once ("template/footer.php");
?>
    <!-- End Right Column -->
    </div>

<script type='text/javascript' src="https://uptobox.com/dist/uptobox-min.js?cacheKiller=1679927622"></script>
<script>
$( document ).ready(function() {
    $('#urls').val('<?php echo $uploadVar; ?>');
});
</script>
</body>
</html>
