<?php
if (isset($_GET["action"]) && !empty($_GET["action"])) {
    if (empty($token)) {
        echo dataError(array("msg" => "token is required"));
        die();
    }
    if ($_GET["action"] == "list") {
        $data = array();
        echo dataOutput($data);
        die();
    } elseif ($_GET["action"] == "view") {
        if (isset($_GET["server"]) && !empty($_GET["server"])) {
            if ($_GET["server"] == 1) {
                $url = $website3;
                if (isset($_GET["search"]) && !empty($_GET["search"])) {
                    // Use POST-based search
                    $url .= "filtering/?keywords=" . urlencode($_GET["search"]);
                }
                if (isset($_GET["page"]) && !empty($_GET["page"]) && (!isset($_GET["search"]) || empty($_GET["search"]))) {
                    $url .= "/page/{$_GET["page"]}";
                }
                $data = scrapeWecima("{$url}");
                $data = json_decode($data, true);
                echo dataOutput($data);
                die();
            } elseif ($_GET["server"] == 2) {
                $url = $website4;
                //strip last /
                $url = rtrim($url, '/');
                if (isset($_GET["page"]) && !empty($_GET["page"]) && (!isset($_GET["search"]) || empty($_GET["search"]))) {
                    $url .= "/?page={$_GET["page"]}/";
                }
                if (isset($_GET["search"]) && !empty($_GET["search"]) && (!isset($_GET["page"]) || empty($_GET["page"]))) {
                    $_GET["search"] = urlencode($_GET["search"]);;
                    $url .= "/?s={$_GET["search"]}";
                }
                if (isset($_GET["page"]) && !empty($_GET["page"]) && isset($_GET["search"]) && !empty($_GET["search"])) {
                    $_GET["search"] = urlencode($_GET["search"]);
                    $url .= "/page/{$_GET["page"]}/?s={$_GET["search"]}";
                }
                $data = scrapEgyDead("{$url}");
                echo dataOutput(array("shows" => $data));
                die();
            } elseif ($_GET["server"] == 3) {
                $url = $website2;
                $_GET["page"] = (isset($_GET["page"]) && !empty($_GET["page"])) ? (int)$_GET["page"] + 0 : 1;
                if (isset($_GET["search"]) && !empty($_GET["search"])) {
                    $_GET["search"] = str_replace(" ", "+", $_GET["search"]);
                    $url .= "/search/?query={$_GET["search"]}&type=all";
                } else {
                    $url .= "/recent";
                }
                if ((!isset($_GET["search"]) || empty($_GET["search"]))) {
                    $url .= "/page/{$_GET["page"]}";
                } elseif ((isset($_GET["search"]) && !empty($_GET["search"]))) {
                    $url .= "&offset={$_GET["page"]}";
                }
                $data = domTopCinema("{$url}");
                echo dataOutput(array("shows" => $data));
                die();
            } elseif ($_GET["server"] == 4) {
                $url = $website;
                if (isset($_GET["search"]) && !empty($_GET["search"]) && (!isset($_GET["page"]) || empty($_GET["page"]))) {
                    $_GET["search"] = str_replace(" ", "+", $_GET["search"]);
                    $url .= "search?s={$_GET["search"]}";
                }
                if (isset($_GET["page"]) && !empty($_GET["page"]) && (!isset($_GET["search"]) || empty($_GET["search"]))) {
                    $url .= "?page={$_GET["page"]}";
                } elseif (isset($_GET["page"]) && !empty($_GET["page"]) && (isset($_GET["search"]) && !empty($_GET["search"]))) {
                    $_GET["search"] = str_replace(" ", "+", $_GET["search"]);
                    $url .= "search?s={$_GET["search"]}&page={$_GET["page"]}";
                }
                $data = searchShahidListing($url);
                echo dataOutput(array("shows" => $data));
                die();
            } elseif ($_GET["server"] == 5) {
                $url = $website5;
                $_GET["search"] = (isset($_GET["search"]) && !empty($_GET["search"])) ? urlencode($_GET["search"]) : '';
                if (isset($_GET["search"]) && !empty($_GET["search"]) && (!isset($_GET["page"]) || empty($_GET["page"]))) {
                    $_GET["search"] = str_replace(" ", "+", $_GET["search"]);
                    $url .= "?s={$_GET["search"]}";
                }
                if (isset($_GET["page"]) && !empty($_GET["page"]) && (!isset($_GET["search"]) || empty($_GET["search"]))) {
                    $url .= "last/page/{$_GET["page"]}";
                } elseif (isset($_GET["page"]) && !empty($_GET["page"]) && (isset($_GET["search"]) && !empty($_GET["search"]))) {
                    $_GET["search"] = str_replace(" ", "+", $_GET["search"]);
                    $url .= "?s={$_GET["search"]}&page={$_GET["page"]}";
                }
                $data = searchShahidSpaceListing($url);
                echo dataOutput(array("shows" => $data));
                die();
            } elseif ($_GET["server"] == 6) {
                $url = $website6;
                $data = scrapeShahidwBs($url);
                $data = json_decode($data, true);
                echo dataOutput(array("shows" => $data["shows"]));
                die();
            } elseif ($_GET["server"] == 7) {
                $url = $website7;
                if (isset($_GET["search"]) && !empty($_GET["search"])) {
                    // urlencode to handle Arabic characters and spaces
                    $_GET["search"] = urlencode($_GET["search"]);
                }
                if (isset($_GET["page"]) && !empty($_GET["page"]) && (!isset($_GET["search"]) || empty($_GET["search"]))) {
                    $url .= "/page/{$_GET["page"]}";
                } elseif (isset($_GET["page"]) && !empty($_GET["page"]) && (isset($_GET["search"]) && !empty($_GET["search"]))) {
                    $url .= "?s={$_GET["search"]}&page={$_GET["page"]}";
                }
                $data = myCimaHome($url);
                echo dataOutput(array("shows" => $data));
                die();
            } elseif ($_GET["server"] == 8) {
                $url = $website8;
                if (isset($_GET["search"]) && !empty($_GET["search"])) {
                    // urlencode to handle Arabic characters and spaces
                    $_GET["search"] = urlencode($_GET["search"]);
                }
                if (isset($_GET["page"]) && !empty($_GET["page"]) && (!isset($_GET["search"]) || empty($_GET["search"]))) {
                    $url .= "recent/page/{$_GET["page"]}";
                } elseif (isset($_GET["page"]) && !empty($_GET["page"]) && (isset($_GET["search"]) && !empty($_GET["search"]))) {
                    $url .= "?s={$_GET["search"]}&page={$_GET["page"]}";
                }
                $data = tuktukHome($url);
                echo dataOutput(array("shows" => $data));
                die();
            } elseif ($_GET["server"] == 9) {
                $url = $website9;
                if (isset($_GET["search"]) && !empty($_GET["search"])) {
                    // urlencode to handle Arabic characters and spaces
                    $_GET["search"] = urlencode($_GET["search"]);
                }
                if (isset($_GET["page"]) && !empty($_GET["page"]) && (!isset($_GET["search"]) || empty($_GET["search"]))) {
                    $url .= "page/{$_GET["page"]}";
                } elseif (isset($_GET["page"]) && !empty($_GET["page"]) && (isset($_GET["search"]) && !empty($_GET["search"]))) {
                    $url .= "search/{$_GET["search"]}";
                }
                $data = qessetHome($url);
                echo dataOutput(array("shows" => $data));
                die();
            } elseif ($_GET["server"] == 10) {
                $url = $website10;
                if (isset($_GET["search"]) && !empty($_GET["search"])) {
                    // urlencode to handle Arabic characters and spaces
                    $_GET["search"] = urlencode($_GET["search"]);
                }
                if (isset($_GET["page"]) && !empty($_GET["page"]) && (!isset($_GET["search"]) || empty($_GET["search"]))) {
                    $url .= "page/{$_GET["page"]}";
                } elseif (isset($_GET["page"]) && !empty($_GET["page"]) && (isset($_GET["search"]) && !empty($_GET["search"]))) {
                    $url .= "search/{$_GET["search"]}";
                }
                if ((!isset($_GET["search"]) || empty($_GET["search"])) && (!isset($_GET["page"]) || empty($_GET["page"]))) {
                    $url .= "latest/";
                }
                $data = esqHome($url);
                echo dataOutput(array("shows" => $data));
                die();
            } elseif ($_GET["server"] == 11) {
                $url = $website11;
                if (isset($_GET["search"]) && !empty($_GET["search"])) {
                    // urlencode to handle Arabic characters and spaces
                    $_GET["search"] = urlencode($_GET["search"]);
                }
                if (isset($_GET["page"]) && !empty($_GET["page"]) && (!isset($_GET["search"]) || empty($_GET["search"]))) {
                    $url .= "anime/?page={$_GET["page"]}&order=update";
                } elseif (isset($_GET["page"]) && !empty($_GET["page"]) && (isset($_GET["search"]) && !empty($_GET["search"]))) {
                    $url .= "page/{$_GET["page"]}/?s={$_GET["search"]}";
                }
                if ((!isset($_GET["search"]) || empty($_GET["search"])) && (!isset($_GET["page"]) || empty($_GET["page"]))) {
                    $url .= "anime/?order=update";
                }
                $data = animeSlayerHome($url);
                echo dataOutput(array("shows" => $data));
                die();
            } elseif ($_GET["server"] == 12) {
                $url = $website12;
                if (isset($_GET["search"]) && !empty($_GET["search"])) {
                    // urlencode to handle Arabic characters and spaces
                    $_GET["search"] = urlencode($_GET["search"]);
                }
                if (isset($_GET["page"]) && !empty($_GET["page"]) && (!isset($_GET["search"]) || empty($_GET["search"]))) {
                    $url .= "last/page/{$_GET["page"]}/";
                } elseif (isset($_GET["page"]) && !empty($_GET["page"]) && (isset($_GET["search"]) && !empty($_GET["search"]))) {
                    $url .= "?s={$_GET["search"]}&page={$_GET["page"]}/";
                }
                if ((!isset($_GET["search"]) || empty($_GET["search"])) && (!isset($_GET["page"]) || empty($_GET["page"]))) {
                    $url .= "last/";
                }
                $data = animePecHome($url);
                echo dataOutput(array("shows" => $data));
                die();
            }elseif ( $_GET["server"] == 13){
                $url = "";
                if ( (isset($_GET["page"]) && !empty($_GET["page"])) && (!isset($_GET["search"]) || empty($_GET["search"]))) {
                    $url .= "?page={$_GET["page"]}";
                } elseif ( (!isset($_GET["page"]) || empty($_GET["page"])) && (isset($_GET["search"]) && !empty($_GET["search"]))) {
                    $url .= "?search={$_GET["search"]}";
                }elseif ( ( isset($_GET["page"]) && !empty($_GET["page"])) && (isset($_GET["search"]) && !empty($_GET["search"]))){
                    $url .= "?search={$_GET["search"]}&page={$_GET["page"]}";
                }
                $data = tvdbTvShowsHome($url);
                echo dataOutput(array("shows" => json_decode($data, true)["shows"]));
                die();
            }elseif ( $_GET["server"] == 14){
                $url = "";
                if ( (isset($_GET["page"]) && !empty($_GET["page"])) && (!isset($_GET["search"]) || empty($_GET["search"]))) {
                    $url .= "?page={$_GET["page"]}";
                } elseif ( (!isset($_GET["page"]) || empty($_GET["page"])) && (isset($_GET["search"]) && !empty($_GET["search"]))) {
                    $url .= "?search={$_GET["search"]}";
                }elseif ( ( isset($_GET["page"]) && !empty($_GET["page"])) && (isset($_GET["search"]) && !empty($_GET["search"]))){
                    $url .= "?search={$_GET["search"]}&page={$_GET["page"]}";
                }
                $data = tvdbMoviesHome($url);
                echo dataOutput(array("shows" => json_decode($data, true)["shows"]));
                die();
            } elseif ($_GET["server"] == 15) {
                $url = $website13;
                //strip last /
                $url = rtrim($url, '/');
                if (isset($_GET["page"]) && !empty($_GET["page"]) && (!isset($_GET["search"]) || empty($_GET["search"]))) {
                    $url .= "/?page={$_GET["page"]}/";
                }
                if (isset($_GET["search"]) && !empty($_GET["search"]) && (!isset($_GET["page"]) || empty($_GET["page"]))) {
                    $_GET["search"] = urlencode($_GET["search"]);;
                    $url .= "/?s={$_GET["search"]}";
                }
                if (isset($_GET["page"]) && !empty($_GET["page"]) && isset($_GET["search"]) && !empty($_GET["search"])) {
                    $_GET["search"] = urlencode($_GET["search"]);
                    $url .= "/page/{$_GET["page"]}/?s={$_GET["search"]}";
                }
                $data = scrapEgyDeadLAT("{$url}");
                echo dataOutput(array("shows" => $data));
                die();
            }elseif ($_GET["server"] == 16) {
                $url = $website14;
                //strip last /
                $url = rtrim($url, '/');
                if (isset($_GET["page"]) && !empty($_GET["page"]) && (!isset($_GET["search"]) || empty($_GET["search"]))) {
                    $url .= "/episode/page/{$_GET["page"]}/";
                }
                if (isset($_GET["search"]) && !empty($_GET["search"]) && (!isset($_GET["page"]) || empty($_GET["page"]))) {
                    $_GET["search"] = urlencode($_GET["search"]);;
                    $url .= "?search_param=animes&s={$_GET["search"]}";
                }
                $data = witanimeHome("{$url}");
                echo dataOutput(array("shows" => $data));
                die();
            } else {
                echo dataError(array("msg" => "Invalid Server"));
                die();
            }
        } else {
            echo dataError(array("msg" => "server is required"));
            die();
        }
    } else {
        echo dataError(array("msg" => "404 action Not Found"));
        die();
    }
} else {
    echo dataError(array("msg" => "404 action Not Found"));
    die();
}
