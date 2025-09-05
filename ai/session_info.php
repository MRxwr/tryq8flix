<?php
session_start();
echo "Session ID: " . session_id() . "<br>";
echo "Session Name: " . session_name() . "<br>";
echo "Session Status: " . session_status() . "<br>";
echo "Session Save Path: " . session_save_path() . "<br>";

// Debug current session data
echo "<pre>";
print_r($_SESSION);
echo "</pre>";
?>
