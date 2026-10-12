<?php
// Invalidate the client-side session. The keepalive token is cleared
// server-side by api/index.php?endpoint=User&action=logout when the app
// gets a chance; here we just drop the cookie and bounce to login.
setcookie("tryq8flix2", "", time() - 3600, "/");
header("Location: ?v=Login");
exit();
