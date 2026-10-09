<?php
namespace V2Legacy;

final class Request
{
    public array $options=[];
    public ?array $response=null;
    public function __construct(?string $url) {if($url!==null) $this->options[CURLOPT_URL]=$url;}
}
function curl_init(?string $url=null): Request {return new Request($url);}
function curl_setopt(Request $request,int $option,mixed $value): bool {$request->options[$option]=$value;return true;}
function curl_setopt_array(Request $request,array $options): bool {$request->options=array_replace($request->options,$options);return true;}
function curl_exec(Request $request): string
{
    $options=$request->options;
    $url=(string)($options[CURLOPT_URL] ?? '');
    // Remove the old public-app proxy dependency, fetching the approved origin directly.
    if(str_starts_with($url,'https://tryq8flix.com/video-proxy.php?url=')) {
        $url=substr($url,strlen('https://tryq8flix.com/video-proxy.php?url='));
    }
    $headers=$options[CURLOPT_HTTPHEADER] ?? [];
    if(isset($options[CURLOPT_REFERER])) $headers[]='Referer: '.$options[CURLOPT_REFERER];
    $post=isset($options[CURLOPT_POSTFIELDS]) || !empty($options[CURLOPT_POST]) || ($options[CURLOPT_CUSTOMREQUEST] ?? '')==='POST';
    $request->response=\httpRequest(\fixRemoteEncoding($url),['method'=>$post ? 'POST' : 'GET','body'=>$options[CURLOPT_POSTFIELDS] ?? null,'headers'=>$headers]);
    return $request->response['body'];
}
function curl_close(Request $request): void {}
function curl_getinfo(Request $request,?int $option=null): mixed
{
    return match($option) {CURLINFO_HTTP_CODE=>$request->response['status'] ?? 0,CURLINFO_CONTENT_TYPE=>$request->response['headers']['content-type'] ?? '',CURLINFO_EFFECTIVE_URL=>$request->response['url'] ?? '',default=>[]};
}
function curl_errno(Request $request): int {return 0;}
function file_get_contents(string $url): string
{
    if(str_starts_with($url,'https://tryq8flix.com/video-proxy.php?url=')) $url=substr($url,strlen('https://tryq8flix.com/video-proxy.php?url='));
    return \httpRequest(\fixRemoteEncoding($url))['body'];
}
function str_get_html(string $html): \simple_html_dom
{
    $dom=\str_get_html($html);
    if(!$dom) throw new \AppError('empty_document','Provider returned an empty or oversized page.',502);
    return $dom;
}
function curlCall(string $url): string {return file_get_contents($url);}
function curlPost(string $url,mixed $post=[]): string {return \httpRequest(\fixRemoteEncoding($url),['method'=>'POST','body'=>$post,'headers'=>['X-Requested-With: XMLHttpRequest','Referer: '.$url]])['body'];}
function extractLink(string $html): string
{
    $dom=str_get_html($html);$iframe=$dom->find('iframe',0);$link=$iframe ? $iframe->src : '';$dom->clear();return $link;
}
