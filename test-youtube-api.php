<?php
// Simple diagnostic script to test the YouTube API
header("Content-Type: application/json; charset=UTF-8");

// Include the necessary files
try {
    require_once("admin/includes/config.php");
    require_once("admin/includes/functions.php");
    echo json_encode(array("status" => "success", "message" => "Dependencies loaded successfully"));
} catch (Exception $e) {
    echo json_encode(array("status" => "error", "message" => "Failed to load dependencies: " . $e->getMessage()));
}

// Test basic PHP functions
$tests = array();

// Test 1: Check if allow_url_fopen is enabled
$tests['allow_url_fopen'] = ini_get('allow_url_fopen') ? 'enabled' : 'disabled';

// Test 2: Check if curl is available  
$tests['curl_available'] = function_exists('curl_init') ? 'yes' : 'no';

// Test 3: Test basic HTTP request
try {
    $test_url = "https://www.youtube.com/oembed?url=https://www.youtube.com/watch?v=dQw4w9WgXcQ&format=json";
    $context = stream_context_create([
        'http' => [
            'timeout' => 10,
            'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36'
        ]
    ]);
    $response = @file_get_contents($test_url, false, $context);
    $tests['http_request'] = $response ? 'success' : 'failed';
    $tests['response_sample'] = $response ? substr($response, 0, 100) . '...' : 'no response';
} catch (Exception $e) {
    $tests['http_request'] = 'error: ' . $e->getMessage();
}

// Test 4: Check error reporting
$tests['error_reporting'] = error_reporting();
$tests['display_errors'] = ini_get('display_errors');

// Test 5: Check if functions exist
$required_functions = array('dataOutput', 'dataError', 'searchFile');
foreach ($required_functions as $func) {
    $tests["function_{$func}"] = function_exists($func) ? 'exists' : 'missing';
}

echo json_encode(array(
    "status" => "diagnostic_complete",
    "tests" => $tests,
    "php_version" => phpversion(),
    "server_software" => $_SERVER['SERVER_SOFTWARE'] ?? 'unknown'
), JSON_PRETTY_PRINT);
?>
