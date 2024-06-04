<?php

require("../admin/includes/config.php");
require("../admin/includes/functions.php");
require("../templates/simple_html_dom.php");

if( isset($_GET["type"]) && $_GET["type"] == "playVideo" ){
    require("playVideo.php");
}elseif( isset($_GET["type"]) && $_GET["type"] == "more" ){
    require("more.php");
}elseif( isset($_GET["type"]) && $_GET["type"] == "register" ){
    require("register.php");
}elseif( isset($_GET["type"]) && $_GET["type"] == "login" ){
    require("login.php");
}elseif( isset($_GET["type"]) && $_GET["type"] == "forget" ){
    require("forget.php");
}elseif( isset($_GET["type"]) && $_GET["type"] == "profile" ){
    require("profile.php");
}elseif( isset($_GET["type"]) && $_GET["type"] == "contact" ){
    require("contact.php");
}elseif( isset($_GET["type"]) && $_GET["type"] == "request" ){
    require("request.php");
}elseif( isset($_GET["type"]) && $_GET["type"] == "likedVidoes" ){
    require("likedVidoes.php");
}elseif( isset($_GET["type"]) && $_GET["type"] == "disLikedVidoes" ){
    require("disLikedVidoes.php");
}elseif( isset($_GET["type"]) && $_GET["type"] == "history" ){
    require("history.php");
}elseif( isset($_GET["type"]) && $_GET["type"] == "myList" ){
    require("myList.php");
}elseif( isset($_GET["type"]) && $_GET["type"] == "home" ){
    require("home.php");
}elseif( isset($_GET["type"]) && $_GET["type"] == "search" ){
    require("search.php");
}elseif( isset($_GET["type"]) && $_GET["type"] == "loadMore" ){
    require("loadMore.php");
}elseif( isset($_GET["type"]) && $_GET["type"] == "logout" ){
    require("logout.php");
}elseif( isset($_GET["type"]) && $_GET["type"] == "live" ){
    require("live.php");
}elseif( isset($_GET["type"]) && $_GET["type"] == "loadMoreSearch" ){
    require("loadMoreSearch.php");
}

?>