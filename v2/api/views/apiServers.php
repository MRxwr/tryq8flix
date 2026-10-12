<?php
if( isset($_GET["action"]) && !empty($_GET["action"]) ){
    if( empty($token) ){
        echo dataError(array("msg" => "token is required"));die();
    }
    if( $_GET["action"] == "list" ){
        if( !isset($_GET["href"]) || empty($_GET["href"]) ){
            echo dataError(array("msg" => "href is required"));die();
        }
        $url = $_GET["href"];
        if( isset($_GET["server"]) && !empty($_GET["server"]) ){
            if( $_GET["server"] == 1 ){
                $data = scrapeWecimaServers($url);
            }elseif( $_GET["server"] == 2 ){
                $data = egyDeadServers($url);
            }elseif( $_GET["server"] == 3 ){
                $data = topCinemaServers($url);
            }elseif( $_GET["server"] == 4 ){
                $data = shahidServers($url);
            }elseif( $_GET["server"] == 5 ){
                $data = shahidSpaceServers($url);
            }elseif( $_GET["server"] == 6 ){
                $data = scrapeShahidwBsServers($url);
            }elseif( $_GET["server"] == 7 ){
                $data = myCimaServers($url);
            }elseif( $_GET["server"] == 8 ){
                $data = tuktukServers($url);
            }elseif( $_GET["server"] == 9 ){
                $data = qessetServers($url);
            }elseif( $_GET["server"] == 10 ){
                $data = esqServers($url);
            }elseif( $_GET["server"] == 11 ){
                $data = animeSlayerServers($url);
            }elseif( $_GET["server"] == 12 ){
                $data = animePecServers($url);
            }elseif( $_GET["server"] == 13 ){
                $data = tvdbTvShowsServers($url);
            }elseif( $_GET["server"] == 14 ){
                $data = tvdbMoviesServers($url);
            }elseif( $_GET["server"] == 15 ){
                $data = EgyDeadLATServers($url);
            }elseif( $_GET["server"] == 16 ){
                $data = witanimeServers($url);
            }else{
                echo dataError(array("msg" => "Invalid Server"));die();
            }
        }else{
            echo dataError(array("msg" => "server is required"));die();
        }
        echo dataOutput($data);die();
    }elseif( $_GET["action"] == "view" ){
        
    }else{
        echo dataError(array("msg" => "404 action Not Found"));die();
    }
}else{
    echo dataError(array("msg" => "404 action Not Found"));die();
}
?>