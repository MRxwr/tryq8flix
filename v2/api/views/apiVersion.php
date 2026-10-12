<?php
if( isset($_GET["action"]) && $_GET["action"] == "version" ){
    $response = array(
        "version" => "1.0.4",
        "iosLink" => "itms-beta://",
        "androidLink" => "https://tryq8flix.com/app-release.apk",
        "windowsLink" => "https://tryq8flix.com/q8flix.exe",
    );
    echo dataOutput($response);
}else{
    echo dataError(array("msg" => "Invalid action"));
} 
?>