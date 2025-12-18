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
            }elseif( $_GET["server"] == 5 ){
                $data = shahidSpaceMore($_GET["href"]);
            }elseif( $_GET["server"] == 6 ){
                $data = shahidwBsListing($_GET["href"]);
            }elseif( $_GET["server"] == 7 ){
                $data = myCimaListings($_GET["href"]);
            }elseif( $_GET["server"] == 8 ){
                $data = tuktukListings($_GET["href"]);
            }elseif( $_GET["server"] == 9 ){
                $data = qessetListings($_GET["href"]);
            }elseif( $_GET["server"] == 10 ){
                $data = esqListings($_GET["href"]);
            }elseif( $_GET["server"] == 11 ){
                $data = animeSlayerListings($_GET["href"]);
            }elseif( $_GET["server"] == 12 ){
                $data = anime4upListings($_GET["href"]);
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