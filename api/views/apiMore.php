<?php 
if( isset($_GET["action"]) && !empty($_GET["action"]) ){
    if( empty($token) ){
        echo dataError(array("msg" => "token is required"));die();
    }
    if( $_GET["action"] == "list" ){
        if( !isset($_GET["href"]) || empty($_GET["href"]) ){
            echo dataError(array("msg" => "href is required"));die();
        }
        if( isset($_GET["server"]) && !empty($_GET["server"]) ){
            if( $_GET["server"] == 1 ){
                $data = wecimaListing($_GET["href"]);
            }elseif( $_GET["server"] == 2 ){
                $data = egyDeadListing($_GET["href"]);
            }elseif( $_GET["server"] == 3 ){
                $data = TopCenimaListings($_GET["href"]);
            }elseif( $_GET["server"] == 4 ){
                $data = shahidMore($_GET["href"]);
            }else{
                echo dataError(array("msg" => "Invalid Server"));die();
            }
            echo dataOutput($data);die();
        }
        $data = wecimaListing($_GET["href"]);
        echo dataOutput($data);die();
    }else{
        echo dataError(array("msg" => "404 action Not Found"));die();
    }    
}else{
    echo dataError(array("msg" => "404 action Not Found"));die();
}
?>