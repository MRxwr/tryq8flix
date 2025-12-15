# Functions Documentation

This document provides a detailed reference for all helper functions located in `admin/includes/functions/`. These functions handle database operations, web scraping for various streaming servers, user authentication, and general utilities.

---

## **1. General Utilities** (`general.php`)

Core utility functions used throughout the application.

- **`direction($valEn, $valAr)`**
  - **Purpose**: Returns English or Arabic text based on the global `$directionHTML` variable (RTL/LTR support).
  
- **`dataOutput($data)`**
  - **Purpose**: Formats a successful API response as a JSON string.
  - **Structure**: `{"ok": true, "error": "0", "status": "successful", "data": ...}`

- **`dataError($data)`**
  - **Purpose**: Formats an error API response as a JSON string.
  - **Structure**: `{"ok": false, "error": "1", "status": "Error", "data": ...}`

- **`checkLogin()`**
  - **Purpose**: Validates the `tryq8flix2` cookie against the database.
  - **Returns**: User profile array if valid, or empty profile if invalid. Reloads page if cookie exists but user is not found.

- **`extractUptoboxId($url)`**
  - **Purpose**: Extracts the file ID from an Uptobox URL.

- **`validateInput($input)`**
  - **Purpose**: Sanitizes input strings.
  - **Checks**: Blocks SQL injection keywords (SELECT, INSERT, etc.) and special characters (`;`, `"`, `'`).

- **`randomLetter()`**
  - **Purpose**: Returns a single random alphanumeric character.

- **`scrapePage($url)`**
  - **Purpose**: Performs a cURL request to fetch a webpage content.
  - **Features**: Follows redirects, ignores SSL verification, sets User-Agent.

---

## **2. Database Operations** (`sql.php`)

Functions for interacting with the MySQL database.

- **`selectDB($table, $where)`**
  - **Purpose**: Basic SELECT query.
  - **Parameters**: Table name, WHERE clause string.
  - **Returns**: Array of associative arrays or 0 on failure.

- **`selectDBNew($table, $placeHolders, $where, $order)`**
  - **Purpose**: Secure SELECT query using prepared statements.
  - **Parameters**: Table name, array of values for binding, WHERE clause with `?` placeholders, ORDER BY clause.

- **`selectDataDB($select, $table, $where)`**
  - **Purpose**: SELECT specific columns.
  - **Parameters**: Columns to select, table name, WHERE clause.

- **`selectDB2($select, $table, $where)`**
  - **Purpose**: Secure SELECT specific columns using prepared statements.
  - **Parameters**: Columns to select, table name, WHERE clause.

---

## **3. Notification Services** (`notification.php`)

- **`sendMail($data)`**
  - **Purpose**: Sends emails using an external API (`createid.link`).
  - **Parameters**: Array containing `site`, `subject`, `body`, `to`.

---

## **4. Server-Specific Scraping Functions**

These functions are specialized for scraping content from specific streaming websites.

### **EgyDead** (`egydead.php`)
- **`scrapEgyDead($url)`**: Scrapes movie/show listings. Handles category pages and search results.
- **`extractSeasonUrlEgyDead($html)`**: Helper to extract season URLs from HTML.
- **`egyDeadListing($url)`**: Scrapes seasons and episodes for a specific show.
- **`egyDeadServers($url)`**: Scrapes available watch servers via POST request.
- **`outputData3($shows)`**: Generates HTML grid for displaying shows (UI component).

### **MyCima** (`mycima.php`)
- **`myCimaHome($url)`**: Scrapes home page or search results. Extracts posters, titles, and descriptions.
- **`myCimaListings($url)`**: Scrapes seasons and episodes.
- **`myCimaServers($url)`**: Scrapes watch servers. Resolves iframe sources.

### **Shahid** (`shahid.php`)
- **`searchShahidListing($url)`**: Scrapes listings from Shahid. Extracts background images from inline styles.
- **`shahidMore($url)`**: Scrapes seasons and episodes.
- **`shahidServers($url)`**: Extracts server data from a JavaScript variable (`let servers = ...`).
- **`outputData($shows)`**: Generates HTML grid for displaying shows.

### **Shahid Space** (`shahidSpace.php`)
- **`searchShahidSpaceListing($url)`**: Scrapes listings. Uses `image-proxy.php` for images.
- **`shahidSpaceMore($url)`**: Scrapes seasons and episodes. Includes custom sorting logic for numerical ordering.
- **`shahidSpaceServers($url)`**: Scrapes servers, filtering out unwanted domains.
- **`outputData5($shows)`**: Generates HTML grid for displaying shows.

### **ShahidwBs** (`shahidwbs.php`)
- **`scrapeShahidwBs($url)`**: Scrapes listings from the new theme structure.
- **`shahidwBsListing($url)`**: Scrapes seasons and episodes.
- **`scrapeShahidwBsServers($url)`**: Scrapes servers.
- **`outputData6($shows)`**: Generates HTML grid for displaying shows.

### **TopCinema** (`topcima.php`)
- **`domTopCinema($url)`**: Scrapes listings from TopCinema.
- **`TopCenimaListings($url)`**: Scrapes seasons and episodes.
- **`topCinemaServers($url)`**: Scrapes servers. Includes logic to fetch real video links via AJAX for specific server IDs.

### **TukTuk Cinema** (`tuktuk.php`)
- **`tuktukHome($url)`**: Scrapes listings. Extracts genres and images.
- **`tuktukListings($url)`**: Scrapes seasons and episodes.
- **`tuktukServers($url)`**: Scrapes servers. Decodes base64-encoded server links (reversed string logic).

### **Wecima** (`wecima.php`)
- **`scrapeWecima($url)`**: Scrapes listings. Extracts images from `data-src` or inline styles.
- **`scrapeWecimaSearch($query)`**: Performs a POST-based search and scrapes the results.
- **`wecimaListing($url)`**: Scrapes seasons and episodes. Handles AJAX loading for episodes within seasons.
- **`scrapeWecimaServers($url)`**: Scrapes servers. Decodes base64-encoded links (custom obfuscation removal).
- **`outputData2($shows)`**: Generates HTML grid for displaying shows.

---

## **Common Patterns in Scraping Functions**
- **DOM Parsing**: Most functions use `str_get_html` (Simple HTML DOM Parser) to traverse the HTML structure.
- **Image Proxying**: Several functions route images through `image-proxy.php` to avoid hotlinking issues or mixed content warnings.
- **AJAX Handling**: Some scrapers (like Wecima and TopCinema) perform additional HTTP requests to fetch dynamic content like episodes or server links.
- **Data Normalization**: All scrapers aim to return a standardized array structure (href, image, title, category, episode, etc.) for the API to consume.
