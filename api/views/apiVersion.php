<?php
if( isset($_GET["action"]) && $_GET["action"] == "version" ){
    $response = array(
        "version" => "0.2.2",
        "iosLink" => "https://itunes.apple.com/us/app/your-app-name/id1234567890?ls=1&mt=8",
        "androidLink" => "https://play.google.com/store/apps/details?id=com.your.app.name"
    );
    echo dataOutput($response);
}else{
    echo dataError(array("msg" => "Invalid action"));
} 
?>