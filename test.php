<?php
function scrapePage($url, $proxies) {
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
        curl_setopt($ch, CURLOPT_PROXYTYPE, CURLPROXY_SOCKS5);  // Changed to SOCKS5
        
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
            continue;  // Try next proxy
        }
        
        curl_close($ch);
        
        if ($httpCode == 200) {
            return $response;  // Successful, return the response
        }
        
        echo "HTTP Code: $httpCode\n";
        rewind($verbose);
        $verboseLog = stream_get_contents($verbose);
        echo "Verbose information:\n", htmlspecialchars($verboseLog), "\n";
    }
    
    return false;  // All proxies failed
}

function extractData($html) {
    // Use a simple regex to extract all <p> tag contents
    preg_match_all('/<p>(.*?)<\/p>/s', $html, $matches);
    
    return $html;//$matches[1] ?? [];
}

// List of proxies
$proxies = [
    "89.35.237.187:4145",  // The SOCKS5 proxy you provided
    // Add more proxies here in the same format, for example:
    // "IP_ADDRESS:PORT",
];

// Usage
$url = "https://shvip.cam/";//"https://egydead.space/home";

$html = scrapePage($url, $proxies);

if ($html !== false) {
    $data = extractData($html);
    echo $data;/*
    if (empty($data)) {
        echo "No <p> tags found. Here's a sample of the HTML:\n";
        echo substr($html, 0, 500) . "...\n";
    } else {
        echo "Scraped data:\n";
        foreach ($data as $item) {
            echo $item . "\n";
        }
    }
		*/
} else {
    echo "Failed to scrape the website with all available proxies.";
}

?>