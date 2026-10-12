<?php
declare(strict_types=1);

function providers(): array
{
    $definitions=[
        1=>['Wecima','website3','wecima.php','scrapeWecima','wecimaListing','scrapeWecimaServers'],
        2=>['EgyDead','website4','egydead.php','scrapEgyDead','egyDeadListing','egyDeadServers'],
        3=>['TopCinema','website2','topcima.php','domTopCinema','TopCenimaListings','topCinemaServers'],
        4=>['Shahid source','website','shahid.php','searchShahidListing','shahidMore','shahidServers'],
        5=>['Shahid Space','website5','shahidSpace.php','searchShahidSpaceListing','shahidSpaceMore','shahidSpaceServers'],
        6=>['ShahidwBs','website6','shahidwbs.php','scrapeShahidwBs','shahidwBsListing','scrapeShahidwBsServers'],
        7=>['Top Cinema Zone','website7','mycima.php','myCimaHome','myCimaListings','myCimaServers'],
        8=>['TukTuk','website8','tuktuk.php','tuktukHome','tuktukListings','tuktukServers'],
        9=>['Qesset','website9','qesset.php','qessetHome','qessetListings','qessetServers'],
        10=>['Esq','website10','esq.php','esqHome','esqListings','esqServers'],
        11=>['Anime Slayer','website11','animeslayer.php','animeSlayerHome','animeSlayerListings','animeSlayerServers'],
        12=>['Anime Pec','website12','animePec.php','animePecHome','animePecListings','animePecServers'],
        13=>['TMDB Series',null,null,null,null,null],
        14=>['TMDB Movies',null,null,null,null,null],
        15=>['EgyDead LAT','website13','egydeadLat.php','scrapEgyDeadLAT','EgyDeadLATListing','EgyDeadLATServers'],
        16=>['Wit Anime','website14','witanime.php','witanimeHome','witanimeListings','witanimeServers'],
    ];
    $result=[];
    foreach($definitions as $id=>$def) {
        $url=$def[1] ? (config('provider_urls')[$def[1]] ?? '') : 'https://api.themoviedb.org/3/';
        $configured=$def[1] ? $url!=='' : config('tmdb_token')!=='';
        // Relay-based legacy providers are deliberately not used; use an authorized direct integration.
        $enabled=$configured && !in_array($id,[4,5],true);
        $result[$id]=['id'=>$id,'name'=>$def[0],'url'=>$url,'file'=>$def[2],'browse'=>$def[3],'details'=>$def[4],'streams'=>$def[5],'enabled'=>$enabled,'search'=>$id!==6,'kind'=>in_array($id,[13,14],true) ? 'metadata' : 'scraper','reason'=>in_array($id,[4,5],true) ? 'Legacy third-party relay removed; direct integration needed.' : ($configured ? '' : 'Configuration required.')];
    }
    return $result;
}

function provider(int $id): array
{
    $provider=providers()[$id] ?? null;
    if(!$provider) throw new AppError('invalid_provider','Unknown provider.');
    if(!$provider['enabled']) throw new AppError('provider_disabled',$provider['reason'] ?: 'Provider is disabled.',503);
    return $provider;
}

function providerBrowseUrl(array $provider,int $page,string $search): string
{
    $base=rtrim($provider['url'],'/'); $q=rawurlencode($search);
    return match($provider['id']) {
        1=>$search!=='' ? $base.'/filtering/?keywords='.$q : $base.($page>1 ? '/page/'.$page.'/' : '/'),
        2,7=>$search!=='' ? $base.'/page/'.$page.'/?s='.$q : $base.'/page/'.$page.'/',
        3=>$search!=='' ? $base.'/search/?query='.$q.'&type=all&offset='.$page : $base.'/recent/page/'.$page,
        6=>$base.'/',
        8=>$search!=='' ? $base.'/?s='.$q.'&page='.$page : $base.'/recent/page/'.$page.'/',
        9,10=>$search!=='' ? $base.'/search/'.$q : $base.'/'.($provider['id']===10 && $page===1 ? 'latest/' : 'page/'.$page.'/'),
        11=>$search!=='' ? $base.'/page/'.$page.'/?s='.$q : $base.'/anime/?page='.$page.'&order=update',
        12=>$search!=='' ? $base.'/?s='.$q.'&page='.$page : $base.'/last/page/'.$page.'/',
        15=>$search!=='' ? $base.'/?s='.$q.'&page='.$page : $base.'/?page='.$page,
        16=>$search!=='' ? $base.'/?search_param=animes&s='.$q : $base.'/episode/page/'.$page.'/',
        default=>$base.'/',
    };
}

function parserCall(array $provider,string $operation,string $href): array
{
    require_once V2_ROOT.'/try2/templates/simple_html_dom.php';
    require_once __DIR__.'/legacy-transport.php';
    require_once V2_ROOT.'/admin/includes/functions/'.$provider['file'];
    $callback='V2Legacy\\'.$provider[$operation];
    $get=$_GET; $post=$_POST; $server=$_SERVER;
    foreach(config('provider_urls') as $name=>$url) $GLOBALS[$name]=$url;
    $_SERVER['HTTP_HOST']=parse_url((string)config('origin'),PHP_URL_HOST);
    $_SERVER['HTTP_USER_AGENT']='Q8FlixV2/2.0';
    ob_start();
    try {
        set_error_handler(static function(int $severity,string $message,string $file,int $line): never {throw new ErrorException($message,0,$severity,$file,$line);});
        $result=$callback($href);
        if(is_string($result)) $result=json_decode($result,true,512,JSON_THROW_ON_ERROR);
        if(!is_array($result)) throw new AppError('parser_changed','Provider page did not match the expected layout.',502);
        return $result;
    } catch(AppError $error) {throw $error;}
    catch(Throwable $error) {logEvent('parser_failure',['provider'=>$provider['id'],'class'=>get_class($error)]); throw new AppError('parser_changed','Provider layout changed or returned incomplete data.',502);}
    finally {ob_end_clean(); restore_error_handler(); $_GET=$get; $_POST=$post; $_SERVER=$server;}
}

function tmdb(string $path,array $params=[]): array
{
    if(config('tmdb_token')==='') throw new AppError('metadata_unconfigured','TMDB credentials are not configured.',503);
    $params=['language'=>config('language')] + $params;
    return httpJson('https://api.themoviedb.org/3/'.ltrim($path,'/').'?'.http_build_query($params),['Authorization: Bearer '.config('tmdb_token'),'Accept: application/json']);
}

function tmdbBrowse(int $providerId,int $page,string $search): array
{
    $type=$providerId===13 ? 'tv' : 'movie';
    $result=tmdb($search!=='' ? 'search/'.$type : 'trending/'.$type.'/day',['page'=>$page]+($search!=='' ? ['query'=>$search,'include_adult'=>'false'] : []));
    $items=[];
    foreach($result['results'] ?? [] as $item) {
        if(!empty($item['adult'])) continue;
        $items[]=['href'=>(string)$item['id'],'title'=>$item['title'] ?? $item['name'] ?? '', 'original_title'=>$item['original_title'] ?? $item['original_name'] ?? '', 'image'=>tmdbImage($item['poster_path'] ?? null),'backdrop'=>tmdbImage($item['backdrop_path'] ?? null,'w1280'),'overview'=>$item['overview'] ?? '', 'year'=>(int)substr($item['release_date'] ?? $item['first_air_date'] ?? '',0,4) ?: null,'rating'=>$item['vote_average'] ?? null,'genres'=>$item['genre_ids'] ?? [],'language'=>$item['original_language'] ?? '', 'media_type'=>$type==='tv' ? 'tv' : 'movie'];
    }
    return ['shows'=>$items,'has_more'=>$page<(int)($result['total_pages'] ?? 1)];
}

function tmdbImage(?string $path,string $size='w500'): string
{
    return $path ? 'https://image.tmdb.org/t/p/'.$size.$path : '';
}

function tmdbDetails(int $providerId,string $href): array
{
    if(!preg_match('#^(\d+)(?:/season/(\d+))?$#D',$href,$parts)) throw new AppError('invalid_id','Invalid metadata identifier.');
    $id=$parts[1]; $type=$providerId===13 ? 'tv' : 'movie'; $data=tmdb($type.'/'.$id);
    $result=['title'=>$data['title'] ?? $data['name'] ?? '', 'media_type'=>$type==='tv' ? 'tv' : 'movie','overview'=>$data['overview'] ?? '', 'backdrop'=>tmdbImage($data['backdrop_path'] ?? null,'w1280'),'poster'=>tmdbImage($data['poster_path'] ?? null),'release_date'=>$data['release_date'] ?? $data['first_air_date'] ?? '', 'runtime'=>$data['runtime'] ?? null,'rating'=>$data['vote_average'] ?? null,'genres'=>$data['genres'] ?? [],'seasons'=>[],'episodes'=>[]];
    if($type==='tv') {
        foreach($data['seasons'] ?? [] as $season) $result['seasons'][]=['link'=>$id.'/season/'.$season['season_number'],'title'=>$season['name'],'season_number'=>(int)$season['season_number'],'poster'=>tmdbImage($season['poster_path'] ?? null)];
        // Select the first regular season, not an unaired latest season.
        $number=isset($parts[2]) ? (int)$parts[2] : (int)($result['seasons'][0]['season_number'] ?? 1);
        if(!isset($parts[2])) foreach($result['seasons'] as $season) if($season['season_number']>0) {$number=$season['season_number'];break;}
        $season=tmdb('tv/'.$id.'/season/'.$number);
        foreach($season['episodes'] ?? [] as $episode) $result['episodes'][]=['link'=>$id.'/'.$number.'/'.$episode['episode_number'],'title'=>$episode['name'] ?: 'Episode '.$episode['episode_number'],'season_number'=>$number,'episode_number'=>(int)$episode['episode_number'],'poster'=>tmdbImage($episode['still_path'] ?? null),'air_date'=>$episode['air_date'] ?? null,'released'=>!empty($episode['air_date']) && $episode['air_date']<=gmdate('Y-m-d')];
    }
    return $result;
}

function tmdbStreams(int $providerId,string $href): array
{
    if($providerId===14) {
        if(!ctype_digit($href)) throw new AppError('invalid_id','Invalid movie identifier.');
        $path='movie/'.$href;
    } else {
        if(!preg_match('#^\d+/\d+/\d+$#D',$href)) throw new AppError('invalid_id','Choose an episode first.');
        $path='tv/'.$href;
    }
    // Retains the existing configured embed choices; their availability is not inferred from metadata.
    return [['name'=>'VidKing','link'=>'https://www.vidking.net/embed/'.$path],['name'=>'Videasy','link'=>'https://player.videasy.net/'.$path],['name'=>'Vidcore','link'=>'https://vidcore.net/'.$path]];
}
