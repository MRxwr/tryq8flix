<?php
declare(strict_types=1);
require_once __DIR__.'/providers.php';

function fixRemoteEncoding(string $url): string
{
    return preg_replace_callback('/[^\x21-\x7e]/',static fn($match)=>rawurlencode($match[0]),$url);
}
function normalizedTitle(string $title): string
{
    $title=mb_strtolower(html_entity_decode(strip_tags($title),ENT_QUOTES|ENT_HTML5,'UTF-8'));
    $title=strtr($title,['أ'=>'ا','إ'=>'ا','آ'=>'ا','ى'=>'ي','٠'=>'0','١'=>'1','٢'=>'2','٣'=>'3','٤'=>'4','٥'=>'5','٦'=>'6','٧'=>'7','٨'=>'8','٩'=>'9']);
    $title=preg_replace('/[\x{064B}-\x{065F}\x{0640}]/u','',$title);
    return trim(preg_replace('/[^\p{L}\p{N}]+/u',' ',$title));
}
function cleanText(mixed $text,int $length=255): string {return mb_substr(trim(html_entity_decode(strip_tags((string)$text),ENT_QUOTES|ENT_HTML5,'UTF-8')),0,$length);}
function imageUrl(string $url): string
{
    if($url==='') return '';
    if(str_contains($url,'image-proxy.php?') || str_contains($url,'video-proxy.php?')) {
        parse_str(parse_url($url,PHP_URL_QUERY) ?: '',$params);$url=$params['url'] ?? '';
    }
    $url=fixRemoteEncoding($url);
    $parts=parse_url($url);
    if(($parts['scheme'] ?? '')!=='https' || isset($parts['user']) || isset($parts['pass'])) return '';
    return $url;
}
function normalizeItem(array $item,int $providerId): array
{
    $title=cleanText($item['title'] ?? '');
    $href=(string)($item['href'] ?? '');
    $type=$item['media_type'] ?? ($providerId===13 ? 'tv' : ($providerId===14 ? 'movie' : 'unknown'));
    if(!in_array($type,['movie','tv','unknown'],true)) $type='unknown';
    $year=$item['year'] ?? null;
    if(!$year && preg_match('/\b(19\d{2}|20\d{2})\b/u',normalizedTitle((string)($item['description'] ?? '').' '.$title),$match)) $year=(int)$match[1];
    if(in_array($providerId,[13,14],true)) $id=hash('sha256','tmdb:'.$type.':'.$href);
    else $id=hash('sha256','source:'.$providerId.':'.$href);
    // Never merge uncertain names automatically: remakes, translations, and similarly named shows differ.
    return ['id'=>$id,'media_type'=>$type,'external_id'=>in_array($providerId,[13,14],true) ? $href : null,'title'=>$title,'original_title'=>cleanText($item['original_title'] ?? ''),'normalized_title'=>normalizedTitle($title),'year'=>$year,'poster'=>imageUrl((string)($item['image'] ?? '')),'backdrop'=>imageUrl((string)($item['backdrop'] ?? '')),'overview'=>cleanText($item['overview'] ?? '',20000),'rating'=>is_numeric($item['rating'] ?? null) ? (float)$item['rating'] : null,'genres'=>$item['genres'] ?? [],'language'=>cleanText($item['language'] ?? '',16),'source'=>['id'=>hash('sha256',$providerId.':'.$href),'provider_id'=>$providerId,'href'=>$href,'label'=>providers()[$providerId]['name']]];
}
function storeItem(array $item): void
{
    if($item['title']==='' || $item['source']['href']==='') return;
    query('INSERT INTO titles(id,media_type,external_id,title,original_title,normalized_title,year,poster,backdrop,overview,rating,genres,language) VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?) ON DUPLICATE KEY UPDATE title=VALUES(title),original_title=VALUES(original_title),normalized_title=VALUES(normalized_title),year=VALUES(year),poster=VALUES(poster),backdrop=VALUES(backdrop),overview=VALUES(overview),rating=VALUES(rating),genres=VALUES(genres),language=VALUES(language),updated_at=UTC_TIMESTAMP()',[$item['id'],$item['media_type'],$item['external_id'],$item['title'],$item['original_title'],$item['normalized_title'],$item['year'],$item['poster'],$item['backdrop'],$item['overview'],$item['rating'],json_encode($item['genres'],JSON_UNESCAPED_UNICODE),$item['language']]);
    query('INSERT INTO sources(id,title_id,provider_id,href,label) VALUES(?,?,?,?,?) ON DUPLICATE KEY UPDATE label=VALUES(label)',[$item['source']['id'],$item['id'],$item['source']['provider_id'],$item['source']['href'],$item['source']['label']]);
    $canonical=query('SELECT title_id FROM sources WHERE id=?',[$item['source']['id']])->fetchColumn();
    if($canonical!==$item['id']) query('DELETE FROM titles WHERE id=? AND NOT EXISTS(SELECT 1 FROM sources WHERE title_id=?)',[$item['id'],$item['id']]);
}
function hydrateTitle(array $row): array
{
    $row['genres']=json_decode($row['genres'],true) ?: [];
    $row['sources']=query('SELECT id,provider_id,label,href FROM sources WHERE title_id=? ORDER BY provider_id',[$row['id']])->fetchAll();
    return $row;
}
function titleById(string $id): array
{
    if(!preg_match('/^[a-f0-9]{64}$/D',$id)) throw new AppError('invalid_title','Invalid title identifier.');
    $title=query('SELECT * FROM titles WHERE id=?',[$id])->fetch();
    if(!$title) throw new AppError('not_found','Title not found.',404);
    return hydrateTitle($title);
}
function sourceById(string $id): array
{
    $source=query('SELECT * FROM sources WHERE id=?',[$id])->fetch();
    if(!$source) throw new AppError('not_found','Source not found.',404);
    return $source;
}
function assertSourceHref(array $source,string $href): void
{
    $provider=provider((int)$source['provider_id']);
    if(in_array((int)$source['provider_id'],[13,14],true)) {
        if(!preg_match('#^\d+(?:/season/\d+|/\d+/\d+)?$#D',$href) || explode('/',$href)[0]!==$source['href']) throw new AppError('invalid_episode','Episode does not belong to this title.');
    } else {
        $host=parse_url($provider['url'],PHP_URL_HOST);
        validateRemoteUrl(fixRemoteEncoding($href),[$host]);
        if($href!==$source['href']) {
            $known=false;
            foreach(query('SELECT payload FROM cache WHERE stale_until>UTC_TIMESTAMP()')->fetchAll() as $cached) {
                $data=json_decode($cached['payload'],true);
                if(($data['source_id'] ?? '')!==$source['id']) continue;
                foreach(array_merge($data['episodes'] ?? [],$data['seasons'] ?? []) as $entry) if(($entry['link'] ?? '')===$href) $known=true;
            }
            if(!$known) throw new AppError('invalid_episode','Load this title’s episode list before selecting an episode.');
        }
    }
}
function cachedProvider(int $id,string $operation,array $args,bool $force=false): array
{
    $provider=provider($id); $key=hash('sha256',$id.':'.$operation.':'.json_encode($args));
    $cache=query('SELECT * FROM cache WHERE cache_key=?',[$key])->fetch();
    if(!$force && $cache && strtotime($cache['expires_at'].' UTC')>time()) return ['data'=>json_decode($cache['payload'],true),'cache'=>'fresh'];
    if(!$force && $cache && strtotime($cache['stale_until'].' UTC')>time()) {
        query('INSERT IGNORE INTO refresh_jobs(cache_key,provider_id,operation,args) VALUES(?,?,?,?)',[$key,$id,$operation,json_encode($args)]);
        return ['data'=>json_decode($cache['payload'],true),'cache'=>'stale'];
    }
    $lockName='q8v2:'.substr($key,0,48);
    if(!(int)query('SELECT GET_LOCK(?,0)',[$lockName])->fetchColumn()) {
        if($cache) return ['data'=>json_decode($cache['payload'],true),'cache'=>'refreshing'];
        throw new AppError('refresh_busy','This provider is refreshing. Please retry shortly.',503);
    }
    $started=microtime(true); $GLOBALS['providerDeadline']=$started+(int)config('http_budget');
    try {
        if($operation==='browse') {
            $page=(int)$args['page']; $search=(string)$args['search'];
            if($search!=='' && !$provider['search']) throw new AppError('search_unsupported','This provider does not support search.');
            if(in_array($id,[13,14],true)) $raw=tmdbBrowse($id,$page,$search);
            else {
                $raw=parserCall($provider,'browse',providerBrowseUrl($provider,$page,$search));
                $raw=['shows'=>$raw['shows'] ?? $raw,'has_more'=>!empty($raw) && !in_array($id,[6,9,10,16],true)];
                if($search==='' && empty($raw['shows'])) throw new AppError('parser_changed','Provider returned no catalog items; its layout may have changed.',502);
            }
            $items=[];
            foreach($raw['shows'] as $entry) if(is_array($entry)) {
                if(!in_array($id,[13,14],true)) $entry['href']=absoluteUrl((string)($entry['href'] ?? ''),$provider['url']);
                $item=normalizeItem($entry,$id);
                if($item['title']!=='') {storeItem($item);$canonical=query('SELECT title_id FROM sources WHERE id=?',[$item['source']['id']])->fetchColumn();$items[$canonical]=titleById($canonical);}
            }
            $data=['shows'=>array_values($items),'has_more'=>$raw['has_more'],'next_page'=>$raw['has_more'] ? $page+1 : null];
        } else {
            $href=(string)$args['href'];
            $raw=in_array($id,[13,14],true) ? ($operation==='details' ? tmdbDetails($id,$href) : tmdbStreams($id,$href)) : parserCall($provider,$operation,$href);
            if($operation==='details') {
                $data=normalizeDetails($raw,$provider,$href); $data['source_id']=$args['source_id'];
            } else {
                $data=['streams'=>normalizeStreams($raw,$href)];
                if(!$data['streams']) throw new AppError('no_streams','No approved playable sources were found.',502);
            }
        }
        $ttl=$operation==='browse' ? ($args['search']!=='' ? 600 : 1200) : ($operation==='streams' ? 120 : 3600);
        query('INSERT INTO cache(cache_key,payload,expires_at,stale_until) VALUES(?,?,?,?) ON DUPLICATE KEY UPDATE payload=VALUES(payload),expires_at=VALUES(expires_at),stale_until=VALUES(stale_until),updated_at=UTC_TIMESTAMP()',[$key,json_encode($data,JSON_UNESCAPED_UNICODE|JSON_INVALID_UTF8_SUBSTITUTE),gmdate('Y-m-d H:i:s',time()+$ttl),gmdate('Y-m-d H:i:s',time()+$ttl+($operation==='streams' ? 0 : 86400))]);
        query("INSERT INTO provider_health(provider_id,state,latency_ms,last_success) VALUES(?,'healthy',?,UTC_TIMESTAMP()) ON DUPLICATE KEY UPDATE state='healthy',error_code=NULL,latency_ms=VALUES(latency_ms),last_success=UTC_TIMESTAMP(),checked_at=UTC_TIMESTAMP()",[$id,(int)((microtime(true)-$started)*1000)]);
        return ['data'=>$data,'cache'=>'miss'];
    } catch(AppError $error) {
        query("INSERT INTO provider_health(provider_id,state,error_code,latency_ms) VALUES(?,'degraded',?,?) ON DUPLICATE KEY UPDATE state='degraded',error_code=VALUES(error_code),latency_ms=VALUES(latency_ms),checked_at=UTC_TIMESTAMP()",[$id,$error->errorCode,(int)((microtime(true)-$started)*1000)]);
        throw $error;
    } finally {query('SELECT RELEASE_LOCK(?)',[$lockName]);unset($GLOBALS['providerDeadline']);}
}
function normalizeDetails(array $raw,array $provider,string $href): array
{
    $data=$raw;
    foreach(['seasons','episodes'] as $kind) {
        $data[$kind]=[];
        foreach($raw[$kind] ?? [] as $entry) {
            $link=(string)($entry['link'] ?? ''); if($link==='') continue;
            if($provider['kind']==='scraper') $link=absoluteUrl($link,$href);
            $entry['link']=$link;$entry['title']=cleanText($entry['title'] ?? '');
            foreach(['season_number','episode_number'] as $number) $entry[$number]=isset($entry[$number]) && is_numeric($entry[$number]) ? (int)$entry[$number] : null;
            $entry['poster']=imageUrl((string)($entry['poster'] ?? ''));
            $data[$kind][]=$entry;
        }
        usort($data[$kind],static fn($a,$b)=>[$a['season_number'] ?? 0,$a['episode_number'] ?? 0]<=>[$b['season_number'] ?? 0,$b['episode_number'] ?? 0]);
    }
    $data['media_type']=$raw['media_type'] ?? ($data['seasons'] || $data['episodes'] ? 'tv' : 'unknown');
    // An empty list is not evidence of a movie or of successful parsing.
    $data['retrieval_status']=$data['media_type']==='unknown' ? 'unclassified' : 'complete';
    return $data;
}
function normalizeStreams(array $raw,string $base): array
{
    $streams=[];
    foreach($raw as $index=>$entry) {
        $url=absoluteUrl((string)($entry['link'] ?? ''),$base);
        if(empty($entry['link'])) continue;
        try {validateRemoteUrl(fixRemoteEncoding($url));} catch(AppError) {continue;}
        if(isset($streams[$url])) continue;
        $path=parse_url($url,PHP_URL_PATH) ?: '';
        $type=preg_match('/\.m3u8$/i',$path) ? 'hls' : (preg_match('/\.mp4$/i',$path) ? 'mp4' : 'embed');
        $streams[$url]=['name'=>cleanText($entry['name'] ?? $entry['title'] ?? 'Source '.($index+1)),'link'=>$url,'type'=>$type,'availability'=>'unverified'];
    }
    return array_values($streams);
}
function catalogSearch(string $search,int $page,array $filters=[]): array
{
    $where=[];$params=[];
    if($search!=='') {$where[]='(normalized_title LIKE ? OR original_title LIKE ?)';$q='%'.strtr(normalizedTitle($search),['\\'=>'\\\\','%'=>'\\%','_'=>'\\_']).'%';$params[]=$q;$params[]=$q;}
    foreach(['media_type','year','language'] as $column) if(!empty($filters[$column])) {$where[]=$column.'=?';$params[]=$filters[$column];}
    if(!empty($filters['genre'])) {$where[]='genres LIKE ?';$params[]='%'.strtr($filters['genre'],['%'=>'\\%','_'=>'\\_']).'%';}
    if(!empty($filters['provider'])) {$where[]='EXISTS(SELECT 1 FROM sources s WHERE s.title_id=titles.id AND s.provider_id=?)';$params[]=(int)$filters['provider'];}
    $sql='SELECT * FROM titles'.($where ? ' WHERE '.implode(' AND ',$where) : '').' ORDER BY updated_at DESC,id LIMIT 25 OFFSET '.(($page-1)*24);
    $rows=query($sql,$params)->fetchAll();$more=count($rows)>24;
    return ['shows'=>array_map('hydrateTitle',array_slice($rows,0,24)),'has_more'=>$more,'next_page'=>$more ? $page+1 : null];
}
function itemIdentity(array $source,string $href,?int $season,?int $episode): string
{
    if($season!==null && $episode!==null) return hash('sha256',$source['title_id'].':s'.$season.':e'.$episode);
    return $href===$source['href'] ? hash('sha256',$source['title_id'].':movie') : hash('sha256',$source['title_id'].':'.$href);
}
