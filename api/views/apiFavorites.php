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
    $existing = selectDBNew("favourites", [$userId, $_POST["server"], $_POST["link"]], "`userId` = ? AND `server` = ? AND `link` = ?", "");
    if( $existing ){
        echo dataError(array("msg" => "Already in favorites")); die();
    }

    $data = array(
        "userId" => $userId,
        "server" => $_POST["server"],
        "title" => $_POST["title"],
        "poster" => $_POST["poster"],
        "link" => $_POST["link"],
        "date" => date("Y-m-d H:i:s")
    );

    if( insertDB("favourites", $data) ){
        echo dataOutput(array("msg" => "Added to favorites"));
    } else {
        echo dataError(array("msg" => "Failed to add to favorites"));
    }

} elseif ( $_GET["action"] == "remove" ) {
    if( !isset($_POST["server"]) || empty($_POST["server"]) ) { echo dataError(array("msg" => "Server is required")); die(); }
    if( !isset($_POST["link"]) || empty($_POST["link"]) ) { echo dataError(array("msg" => "Link is required")); die(); }

    // We use selectDBNew logic for delete condition construction if needed, but deleteDB takes a string where clause.
    // Ideally we should use prepared statements for delete too, but deleteDB in sql.php doesn't seem to support it fully safely with params array like selectDBNew.
    // However, deleteDB does `str_replace([';', '"'], "", $where)` which offers basic protection.
    // Let's construct the where clause carefully.
    
    $server = $dbconnect->real_escape_string($_POST["server"]);
    $link = $dbconnect->real_escape_string($_POST["link"]);
    
    $where = "`userId` = '{$userId}' AND `server` = '{$server}' AND `link` = '{$link}'";
    
    if( deleteDB("favourites", $where) ){
        echo dataOutput(array("msg" => "Removed from favorites"));
    } else {
        echo dataError(array("msg" => "Failed to remove from favorites"));
    }

} elseif ( $_GET["action"] == "list" ) {
    $favorites = selectDBNew("favourites", [$userId], "`userId` = ?", "`id` DESC");
    if( $favorites ){
        echo dataOutput(array("favorites" => $favorites));
    } else {
        echo dataOutput(array("favorites" => []));
    }

} elseif ( $_GET["action"] == "check" ) {
    if( !isset($_POST["server"]) || empty($_POST["server"]) ) { echo dataError(array("msg" => "Server is required")); die(); }
    if( !isset($_POST["link"]) || empty($_POST["link"]) ) { echo dataError(array("msg" => "Link is required")); die(); }

    $existing = selectDBNew("favourites", [$userId, $_POST["server"], $_POST["link"]], "`userId` = ? AND `server` = ? AND `link` = ?", "");
    if( $existing ){
        echo dataOutput(array("isFavorite" => true));
    } else {
        echo dataOutput(array("isFavorite" => false));
    }
}
?>