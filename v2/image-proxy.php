<?php
require __DIR__.'/app/bootstrap.php';
securityHeaders();
try {
    $user=requireUser();rateLimit('images-'.$user['id'],120,60);
    $url=fixRemoteEncoding(textInput('url',4096));validateRemoteUrl($url);
    $key=hash('sha256',$url);$dir=V2_ROOT.'/storage/images';if(!is_dir($dir))mkdir($dir,0700,true);
    $file=$dir.'/'.$key;$expires=86400;
    if(!is_file($file) || filemtime($file)+$expires<time()) {
        $response=httpRequest($url,['max_bytes'=>5*1024*1024]);
        $mime=(new finfo(FILEINFO_MIME_TYPE))->buffer($response['body']);
        if(!in_array($mime,['image/jpeg','image/png','image/webp','image/gif'],true))throw new AppError('invalid_image','Only verified raster images are allowed.',415);
        $temp=$file.'.'.bin2hex(random_bytes(4));file_put_contents($temp,$response['body'],LOCK_EX);rename($temp,$file);
    }
    $mime=(new finfo(FILEINFO_MIME_TYPE))->file($file);header('Content-Type: '.$mime);header('Cache-Control: private, max-age=86400');readfile($file);
} catch(AppError $error){jsonResponse(null,$error->status,$error);}
catch(Throwable $error){logEvent('image_failure',['class'=>get_class($error)]);jsonResponse(null,502,new AppError('image_failure','Image could not be retrieved.',502));}
