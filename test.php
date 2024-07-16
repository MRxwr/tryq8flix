<?php

function fetchRawHtml($url, $proxies) {
    foreach ($proxies as $proxy) {
        echo "Trying proxy: $proxy\n";
        
        $ch = curl_init();
        
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36");
        
        // Set proxy
        curl_setopt($ch, CURLOPT_PROXY, $proxy);
        curl_setopt($ch, CURLOPT_PROXYTYPE, CURLPROXY_SOCKS5);
        
        // Extend timeout for proxy connection
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
        curl_setopt($ch, CURLOPT_TIMEOUT, 60);
        
        // Verbose output for debugging
        curl_setopt($ch, CURLOPT_VERBOSE, true);
        $verbose = fopen('php://temp', 'w+');
        curl_setopt($ch, CURLOPT_STDERR, $verbose);
        
        $response = curl_exec($ch);
        
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        
        if (curl_errno($ch)) {
            echo 'Curl error: ' . curl_error($ch) . "\n";
            rewind($verbose);
            $verboseLog = stream_get_contents($verbose);
            echo "Verbose information:\n", htmlspecialchars($verboseLog), "\n";
            curl_close($ch);
            
            // Wait for 5 seconds before trying the next proxy
            sleep(5);
            
            continue;  // Try next proxy
        }
        
        curl_close($ch);
        
        echo "HTTP Code: $httpCode\n";
        
        if ($httpCode == 200) {
            return $response;  // Successful, return the raw HTML
        }
        
        rewind($verbose);
        $verboseLog = stream_get_contents($verbose);
        echo "Verbose information:\n", htmlspecialchars($verboseLog), "\n";
        
        // Wait for 5 seconds before trying the next proxy
        sleep(5);
    }
    
    return false;  // All proxies failed
}

// List of proxies (replace these with working proxies)
$proxies = [
    "89.35.237.187:4145",  // The original SOCKS5 proxy
    "98.162.96.53:10663",  // Additional SOCKS5 proxy
    "162.241.12.242:32598", // Another SOCKS5 proxy
    // Add more proxies here
];

// Usage
$url = "https://shvip.cam/";//"https://egydead.space/home";

$rawHtml = fetchRawHtml($url, $proxies);

if ($rawHtml !== false) {
    echo "Successfully retrieved the raw HTML. Here's a preview of the first 500 characters:\n\n";
    echo substr($rawHtml, 0, 500) . "...\n";
    
    // Optionally, save the full HTML to a file
    //file_put_contents('rawhtml.txt', $rawHtml);
    echo "\nFull HTML content has been saved to 'rawhtml.txt'\n";
} else {
    echo "Failed to retrieve the raw HTML with all available proxies.";
}

?>
?>