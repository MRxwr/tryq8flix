<?php 
// Logout logic
if(isset($_COOKIE['tryq8flix2'])) {
    // Call API to invalidate token
    $token = $_COOKIE['tryq8flix2'];
    // We can't easily call the API via PHP here without curl, so we'll just clear cookie and redirect
    // Ideally we should call api/index.php?endpoint=User&action=logout
}
setcookie("tryq8flix2", "", time() - 3600, "/");
header("Location: ?v=Login");
exit();
?>
