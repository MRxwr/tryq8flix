<?php
if( isset($_GET["action"]) && $_GET["action"] == "version" ){
    $response = array(
        "version" => "1.0.0"
    );
    echo dataOutput($response);
}else{
    echo dataError(array("msg" => "Invalid action"));
}