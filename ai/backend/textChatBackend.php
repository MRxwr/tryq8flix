<?php
// Ensure proper session configuration
ini_set('session.cookie_lifetime', 86400); // 24 hours
ini_set('session.gc_maxlifetime', 86400); // 24 hours
ini_set('session.use_cookies', 1);
ini_set('session.use_only_cookies', 1);
ini_set('session.cookie_httponly', 1);

// Set a cookie to help with session persistence
$cookieName = 'POLLINATIONS_CHAT_SESSION';
if (!isset($_COOKIE[$cookieName])) {
    setcookie($cookieName, '1', time() + 86400, '/', '', false, false);
}

// Start session for chat history
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Your provided token
$token = '8x5QP4YGfNKsu8j-'; 

// Initialize chat history session variable if not exists
if (!isset($_SESSION['chat_history'])) {
    $_SESSION['chat_history'] = [];
}

// Include the models fetching logic
require_once("models.php");
$models = fetchModels($token);

// Include the rest of the chat logic
require_once("textChatCode.php");
