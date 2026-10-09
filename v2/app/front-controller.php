<?php
declare(strict_types=1);
require __DIR__.'/bootstrap.php';
securityHeaders();
$routes=['Home'=>'bladeHome.php','Search'=>'bladeSearch.php','Category'=>'bladeSearch.php','More'=>'bladeMore.php','Servers'=>'bladeMore.php','Favorites'=>'bladeFavorites.php','History'=>'bladeHistory.php','Settings'=>'bladeSettings.php','Profile'=>'bladeSettings.php','Health'=>'bladeHealth.php','Login'=>'bladeLogin.php','Register'=>'bladeRegister.php','Forget'=>'bladeForget.php','Reset'=>'bladeReset.php','About'=>'bladeAbout.php','Policy'=>'bladePolicy.php','Terms'=>'bladeTerms.php'];
try {
    $view=textInput('v',30,'Home');
    if(!isset($routes[$view])) {http_response_code(404);$view='NotFound';}
    $user=currentUser();
    $public=['Login','Register','Forget','Reset','About','Policy','Terms','NotFound'];
    if(!$user && !in_array($view,$public,true)) {header('Location: '.basePath().'/index.php?v=Login');exit;}
    if($view==='Health') requireUser(true);
    if($user && in_array($view,['Login','Register'],true)) {header('Location: '.basePath().'/index.php?v=Home');exit;}
    require V2_ROOT.'/views/header.php';
    if($view==='NotFound') echo '<section class="empty-state"><h1>Page not found</h1><a class="button" href="index.php">Return home</a></section>';
    else require V2_ROOT.'/views/'.$routes[$view];
    require V2_ROOT.'/views/footer.php';
} catch(Throwable $error) {
    http_response_code($error instanceof AppError ? $error->status : 503);
    echo '<!doctype html><html lang="en"><meta charset="utf-8"><title>Q8Flix V2 setup</title><body><h1>Q8Flix V2</h1><p>'.e($error instanceof AppError ? $error->getMessage() : 'Initialize the isolated V2 database using php v2/bin/migrate.php.').'</p></body></html>';
    logEvent('page_failure',['class'=>get_class($error)]);
}
