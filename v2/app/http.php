<?php
declare(strict_types=1);

function allowedHttpHosts(): array
{
    $hosts=['api.themoviedb.org','image.tmdb.org','wecima.click','web2.topcinema.cam','y0vx70khe8u.animepec.online'];
    foreach (config('provider_urls') as $url) {
        $host=parse_url($url,PHP_URL_HOST); if ($host) $hosts[]=strtolower($host);
    }
    return array_values(array_unique(array_merge($hosts,config('extra_hosts'),config('embed_hosts'))));
}

function publicIp(string $ip): bool
{
    if (!filter_var($ip,FILTER_VALIDATE_IP,FILTER_FLAG_NO_PRIV_RANGE|FILTER_FLAG_NO_RES_RANGE)) return false;
    if (str_contains($ip,':')) {
        // Only globally routed IPv6 unicast; reject mapped/private transition addresses.
        $packed=inet_pton($ip);
        return $packed!==false && (ord($packed[0]) & 0xe0)===0x20 && !str_starts_with(strtolower($ip),'2001:') && !str_starts_with(strtolower($ip),'2002:');
    }
    return true;
}

function validateRemoteUrl(string $url, ?array $allowed = null): array
{
    if (strlen($url)>4096 || preg_match('/[\x00-\x20\x7f]/',$url)) throw new AppError('unsafe_url','Invalid remote URL.');
    $parts=parse_url($url);
    if (!$parts || !in_array($parts['scheme'] ?? '',['https','http'],true) || isset($parts['user']) || isset($parts['pass'])) throw new AppError('unsafe_url','Only HTTP(S) provider URLs are allowed.');
    $host=strtolower($parts['host'] ?? '');
    if (!in_array($host,$allowed ?? allowedHttpHosts(),true)) throw new AppError('host_denied','Provider host is not approved.',403);
    $port=$parts['port'] ?? ($parts['scheme']==='https' ? 443 : 80);
    if (!in_array($port,[80,443],true)) throw new AppError('port_denied','Remote port is not approved.',403);
    return [$host,$port,$parts];
}

function resolvePublicAddresses(string $host): array
{
    if (filter_var($host,FILTER_VALIDATE_IP)) $ips=[$host];
    else {
        $ips=[];
        foreach (dns_get_record($host,DNS_A|DNS_AAAA) ?: [] as $record) {
            if (isset($record['ip'])) $ips[]=$record['ip'];
            if (isset($record['ipv6'])) $ips[]=$record['ipv6'];
        }
    }
    if (!$ips) throw new AppError('dns_failure','Provider DNS lookup failed.',502);
    foreach ($ips as $ip) if (!publicIp($ip)) throw new AppError('private_destination','Private network destinations are forbidden.',403);
    return $ips;
}

function absoluteUrl(string $relative,string $base): string
{
    $relative=html_entity_decode(trim($relative),ENT_QUOTES|ENT_HTML5,'UTF-8');
    if (preg_match('#^[a-z][a-z0-9+.-]*:#i',$relative)) return $relative;
    $parts=parse_url($base);
    $origin=($parts['scheme'] ?? 'https').'://'.($parts['host'] ?? '').(isset($parts['port']) ? ':'.$parts['port'] : '');
    if (str_starts_with($relative,'//')) return ($parts['scheme'] ?? 'https').':'.$relative;
    if (str_starts_with($relative,'?')) return $origin.($parts['path'] ?? '/').$relative;
    $basePath=$parts['path'] ?? '/';
    $directory=str_ends_with($basePath,'/') ? rtrim($basePath,'/') : rtrim(str_replace('\\','/',dirname($basePath)),'/');
    $path=str_starts_with($relative,'/') ? $relative : $directory.'/'.$relative;
    $segments=[];
    foreach(explode('/',$path) as $segment) { if($segment==='..') array_pop($segments); elseif($segment!=='.') $segments[]=$segment; }
    return $origin.'/'.ltrim(implode('/',$segments),'/');
}

function httpRequest(string $url,array $options=[]): array
{
    $allowed=$options['allowed_hosts'] ?? allowedHttpHosts();
    $deadline=$options['deadline'] ?? ($GLOBALS['providerDeadline'] ?? microtime(true)+(int)config('http_budget'));
    $method=$options['method'] ?? 'GET'; $post=$options['body'] ?? null;
    $headers=$options['headers'] ?? []; $initialHost=parse_url($url,PHP_URL_HOST);
    for($redirect=0;$redirect<=4;$redirect++) {
        [$host,$port]=validateRemoteUrl($url,$allowed);
        $remaining=$deadline-microtime(true);
        if($remaining<=0) throw new AppError('provider_timeout','Provider request deadline exceeded.',504);
        $ips=resolvePublicAddresses($host);
        $body=''; $responseHeaders=[]; $tooLarge=false;
        $maxBytes=$options['max_bytes'] ?? 4*1024*1024;
        $safeHeaders=[];
        foreach($headers as $header) {
            if(preg_match('/[\r\n]/',$header)) throw new AppError('invalid_header','Invalid provider header.');
            if($host!==$initialHost && preg_match('/^(authorization|cookie):/i',$header)) continue;
            $safeHeaders[]=$header;
        }
        $curl=curl_init($url);
        curl_setopt_array($curl,[CURLOPT_RETURNTRANSFER=>false,CURLOPT_FOLLOWLOCATION=>false,CURLOPT_PROTOCOLS=>CURLPROTO_HTTP|CURLPROTO_HTTPS,CURLOPT_SSL_VERIFYPEER=>true,CURLOPT_SSL_VERIFYHOST=>2,CURLOPT_CONNECTTIMEOUT_MS=>(int)(min(5,$remaining)*1000),CURLOPT_TIMEOUT_MS=>(int)(min((int)config('http_timeout'),$remaining)*1000),CURLOPT_USERAGENT=>'Q8FlixV2/2.0 (catalog aggregator)',CURLOPT_ENCODING=>'',CURLOPT_HTTPHEADER=>$safeHeaders,CURLOPT_PROXY=>'',CURLOPT_RESOLVE=>[$host.':'.$port.':'.(str_contains($ips[0],':') ? '['.$ips[0].']' : $ips[0])],CURLOPT_HEADERFUNCTION=>static function($curl,$line) use (&$responseHeaders) { if(str_starts_with($line,'HTTP/')) $responseHeaders=[]; elseif(str_contains($line,':')) {[$key,$value]=explode(':',$line,2); $responseHeaders[strtolower(trim($key))]=trim($value);} return strlen($line);},CURLOPT_WRITEFUNCTION=>static function($curl,$chunk) use (&$body,&$tooLarge,$maxBytes) {if(strlen($body)+strlen($chunk)>$maxBytes){$tooLarge=true;return 0;} $body.=$chunk; return strlen($chunk);}]);
        if($method==='HEAD') curl_setopt($curl,CURLOPT_NOBODY,true);
        elseif($method==='POST') {curl_setopt($curl,CURLOPT_POST,true); curl_setopt($curl,CURLOPT_POSTFIELDS,is_array($post) ? http_build_query($post) : $post);}
        $ok=curl_exec($curl); $status=(int)curl_getinfo($curl,CURLINFO_HTTP_CODE); $errno=curl_errno($curl); curl_close($curl);
        if($tooLarge) throw new AppError('response_too_large','Provider response exceeded the size limit.',502);
        if($ok===false) throw new AppError($errno===CURLE_OPERATION_TIMEDOUT ? 'provider_timeout' : 'network_error','Could not connect securely to the provider.',$errno===CURLE_OPERATION_TIMEDOUT ? 504 : 502);
        if(in_array($status,[301,302,303,307,308],true)) {
            if(empty($responseHeaders['location'])) throw new AppError('bad_redirect','Provider returned an invalid redirect.',502);
            $url=absoluteUrl($responseHeaders['location'],$url);
            if($status===303 || (($status===301 || $status===302) && $method==='POST')) {$method='GET';$post=null;}
            continue;
        }
        if(in_array($status,[429,502,503,504],true) && empty($options['retried']) && $method==='GET') {
            $delay=ctype_digit($responseHeaders['retry-after'] ?? '') ? (int)$responseHeaders['retry-after'] : 1;
            if($delay<=2 && microtime(true)+$delay+1<$deadline) {sleep($delay); return httpRequest($url,array_replace($options,['retried'=>true,'deadline'=>$deadline]));}
        }
        if($status<200 || $status>=300) throw new AppError('upstream_http_'.$status,'Provider returned an error response.',502);
        return ['body'=>$body,'headers'=>$responseHeaders,'status'=>$status,'url'=>$url];
    }
    throw new AppError('redirect_limit','Too many provider redirects.',502);
}

function httpJson(string $url,array $headers=[]): array
{
    $response=httpRequest($url,['headers'=>$headers]);
    try {$json=json_decode($response['body'],true,512,JSON_THROW_ON_ERROR);} catch(JsonException) {throw new AppError('invalid_json','Provider returned invalid JSON.',502);}
    if(!is_array($json)) throw new AppError('invalid_json','Provider returned an invalid document.',502);
    return $json;
}
