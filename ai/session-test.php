<?php
// Start the session
session_start();

// Set a test value in the session
if (!isset($_SESSION['test_value'])) {
    $_SESSION['test_value'] = 1;
} else {
    $_SESSION['test_value']++;
}

// Display session info
echo "<h1>Session Test</h1>";
echo "<p>Session ID: " . session_id() . "</p>";
echo "<p>Session Name: " . session_name() . "</p>";
echo "<p>Session Path: " . session_save_path() . "</p>";
echo "<p>Session Counter: " . $_SESSION['test_value'] . "</p>";
echo "<p>Cookie Info: " . print_r($_COOKIE, true) . "</p>";

// Display all session data
echo "<h2>Session Data</h2>";
echo "<pre>";
print_r($_SESSION);
echo "</pre>";

// Link to reload the page
echo "<p><a href='session-test.php'>Reload Page</a> (counter should increase)</p>";
echo "<p><a href='text.php'>Go back to Chat</a></p>";
?>
