<?php
// Token validation
if( empty($token) ){
    echo dataError(array("msg" => "Unauthorized")); die();
}
$user = selectDB("users", "`keepalive` = '{$token}'");
if( !$user ){
    echo dataError(array("msg" => "Invalid token")); die();
}
$userId = $user[0]['id'];

if( $_GET["action"] == "add" ){
    if( !isset($_POST["server"]) || empty($_POST["server"]) ) { echo dataError(array("msg" => "Server is required")); die(); }
    if( !isset($_POST["title"]) || empty($_POST["title"]) ) { echo dataError(array("msg" => "Title is required")); die(); }
    if( !isset($_POST["poster"]) || empty($_POST["poster"]) ) { echo dataError(array("msg" => "Poster is required")); die(); }
    if( !isset($_POST["link"]) || empty($_POST["link"]) ) { echo dataError(array("msg" => "Link is required")); die(); }

    // Check if already exists
    // We check by link to avoid duplicates for the same episode
    $existing = selectDBNew("watchedvideos", [$userId, $_POST["server"], $_POST["link"]], "`userId` = ? AND `server` = ? AND `link` = ?", "");
    
    $data = array(
        "userId" => $userId,
        "server" => $_POST["server"],
        "title" => $_POST["title"],
        "poster" => $_POST["poster"],
        "link" => $_POST["link"],
        "date" => date("Y-m-d H:i:s")
    );

    if( $existing ){
        // Update the date
        if( updateDB("watchedvideos", ["date" => date("Y-m-d H:i:s")], "`id` = '{$existing[0]['id']}'") ){
            echo dataOutput(array("msg" => "History updated"));
        } else {
            echo dataError(array("msg" => "Failed to update history"));
        }
    } else {
        if( insertDB("watchedvideos", $data) ){
            echo dataOutput(array("msg" => "Added to history"));
        } else {
            echo dataError(array("msg" => "Failed to add to history"));
        }
    }

} elseif ( $_GET["action"] == "list" ) {
    if( isset($_GET["server"]) && !empty($_GET["server"]) ){
        $history = selectDBNew("watchedvideos", [$userId, $_GET["server"]], "`userId` = ? AND `server` = ?", "`date` DESC");
    } else {
        $history = selectDBNew("watchedvideos", [$userId], "`userId` = ?", "`date` DESC");
    }
    
    if( $history ){
        echo dataOutput(array("history" => $history));
    } else {
        echo dataOutput(array("history" => []));
    }

} elseif ( $_GET["action"] == "clear" ) {
    if( deleteDB("watchedvideos", "`userId` = '{$userId}'") ){
        echo dataOutput(array("msg" => "History cleared"));
    } else {
        echo dataError(array("msg" => "Failed to clear history"));
    }
}
?>