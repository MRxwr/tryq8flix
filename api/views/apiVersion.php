<?php
if( isset($_GET["action"]) && $_GET["action"] == "version" ){
    $response = array(
        "version" => "1.0.0",
        "iosLink" => "https://itunes.apple.com/us/app/your-app-name/id1234567890?ls=1&mt=8",
        "androidLink" => "https://tryq8flix.com/app-release.apk"
    );
    echo dataOutput($response);
}else{
    echo dataError(array("msg" => "Invalid action"));
} 
?>