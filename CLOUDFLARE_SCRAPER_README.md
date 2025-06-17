# Cloudflare Scraper with Puppeteer

This Node.js script uses Puppeteer to bypass Cloudflare challenges and extract HTML content from protected websites.

## Installation

1. Make sure you have Node.js installed
2. Install dependencies:
```bash
npm install
```

## Usage

### Basic usage:
```bash
node cloudflare-scraper.js https://example.com
```

### Without URL (uses default):
```bash
node cloudflare-scraper.js
```

## Features

- ✅ Launches headless browser (Puppeteer)
- ✅ Handles Cloudflare challenges automatically
- ✅ Waits for networkidle2 or page content availability
- ✅ Extracts full HTML content after challenge completion
- ✅ Saves content to timestamped HTML file
- ✅ Comprehensive error handling
- ✅ Command-line URL support
- ✅ JavaScript and cookies enabled by default
- ✅ Async/await implementation

## How it works

1. Launches a headless Chrome browser
2. Sets realistic user agent and viewport
3. Navigates to the target URL
4. Detects Cloudflare challenge pages
5. Waits for challenge completion using multiple strategies
6. Extracts and saves the final HTML content

## Configuration

You can modify the scraper options in the script:

```javascript
const scraper = new CloudflareScraper({
    headless: true,        // Set to false for debugging
    timeout: 45000,        // Request timeout in milliseconds
    waitUntil: 'networkidle2'  // Wait condition
});
```

## Error Handling

The script includes comprehensive error handling for:
- Network timeouts
- Cloudflare challenge failures
- Browser launch issues
- File saving errors

## Output

The script will:
1. Print progress messages to console
2. Save HTML content to a timestamped file
3. Display content preview and statistics
4. Return appropriate exit codes for automation

## Integration with PHP

You can call this script from your PHP application:

```php
function scrapeWithCloudflare($url) {
    $command = "node cloudflare-scraper.js " . escapeshellarg($url);
    $output = shell_exec($command);
    
    // Parse the saved file or use the output
    return $output;
}
```
