# API Documentation & Findings

## **API Architecture Overview**

### **Main Entry Point** (`api/index.php`)
- RESTful API router using endpoint-based routing
- Returns JSON responses with UTF-8 encoding
- Authorization via Bearer token in HTTP headers
- Routes to view files based on `?endpoint=` parameter
- Requires `admin/includes/config.php` and `admin/includes/functions.php`

---

## **API Endpoints Analysis**

### **1. Banners API** (`api/views/apiBanners.php`)
- **Purpose**: Fetch active promotional banners
- **Query**: Selects `id, url, imageurl` from `banners` table where `status=0` and `hidden=0`
- **Response**: Array of banner objects or error

### **2. Firebase API** (`api/views/apiFirebase.php`)
- **Status**: Empty file (placeholder for Firebase integration)

### **3. Home API** (`api/views/apiHome.php`)
- **Purpose**: Multi-server content scraping platform
- **Actions**: 
  - `list`: Browse content from various servers
  - `view`: View specific content
- **Supported Servers** (8 total):
  1. **Wecima** - Arabic content streaming
  2. **EgyDead** - Egyptian content
  3. **TopCinema** - Cinema listings
  4. **Shahid** - Middle Eastern streaming service
  5. **Shahid Space** - Alternative Shahid source
  6. **ShahidwBs** - Another Shahid variant
  7. **MyCima** - Arabic movies/shows
  8. **TukTuk** - Additional content provider

- **Features**:
  - Search functionality with query encoding
  - Pagination support
  - Server-specific scraping functions
  - Token authentication required

### **4. Live API** (`api/views/apiLive.php`)
- **Purpose**: Live sports match streaming
- **Actions**:
  - `live`: Lists current matches with team info, logos, scores, league
  - `match`: Extracts live stream URLs from match pages
- **Functions**:
  - `searchMatches()`: Scrapes match listings from `$websiteLive`
  - `liveMatch()`: Finds iframe sources for streaming (checks 6 servers)
- **Filtering**: Excludes URLs containing 'wallplaster'
- **Returns**: Match metadata with streaming URLs

### **5. Main API** (`api/views/apiMain.php`)
- **Purpose**: Central server listing and banner management
- **Returns**:
  - Array of 8 server definitions (id, name)
  - Active banners from database with `title, endpoint, server, url, imageurl`

### **6. More API** (`api/views/apiMore.php`)
- **Purpose**: Detailed content listings for specific shows/movies
- **Action**: `list` (requires `href` parameter)
- **Server-specific functions**:
  - `wecimaListing()`, `egyDeadListing()`, `TopCenimaListings()`
  - `shahidMore()`, `shahidSpaceMore()`, `shahidwBsListing()`
  - `myCimaListings()`, `tuktukListings()`
- **Authentication**: Token required

### **7. News API** (`api/views/apiNews.php`)
- **Status**: Empty file (not implemented)

### **8. QA Categories API** (`api/views/apiQACategories.php`)
- **Purpose**: Quiz/Q&A category hierarchy
- **Database**: `qas_categories` table
- **Structure**: Hierarchical with parent-child relationships
- **Returns**: Main categories with nested subcategories
- **Fields**: `id, date, title, subTitle, image`

### **9. QA Questions API** (`api/views/apiQAQuestions.php`)
- **Purpose**: Retrieve quiz questions by category
- **Point Distribution**:
  - 2 questions × 10 points
  - 2 questions × 20 points
  - 1 question × 40 points
- **Question Types**: Multiple choice, True/False
- **Fields**: `id, type, question, correctAnswer, answer1-3, answerTrue/False, image, video, audio, points`
- **Random Selection**: Uses `ORDER BY RAND()`

### **10. QA Rooms API** (`api/views/apiQARooms.php`)
- **Purpose**: Multiplayer quiz room management
- **Room Types**: 
  1. Public game
  2. Private game  
  3. Group game
- **Features**:
  - Auto-joins user to existing public room
  - Creates new room if none exists
  - 6-character alphanumeric room codes
  - Stores members as JSON array
- **Authentication**: Token-based user validation

### **11. Servers API** (`api/views/apiServers.php`)
- **Purpose**: Extract server/stream links from content pages
- **Action**: `list` (requires `href` parameter)
- **Server Functions**:
  - `scrapeWecimaServers()`, `egyDeadServers()`, `topCinemaServers()`
  - `shahidServers()`, `shahidSpaceServers()`, `scrapeShahidwBsServers()`
  - `myCimaServers()`, `tuktukServers()`

### **12. Settings API** (`api/views/apiSettings.php`)
- **Purpose**: App configuration and social links
- **Returns**: `about, terms, policy, whatsapp, instagram, twitter, tiktok`
- **Source**: `settings` table

### **13. Submit Room API** (`api/views/apiSubmitRoom.php`)
- **Purpose**: Save completed quiz room data
- **Required Fields**: `code, type, roomData`
- **Authentication**: Token validation required
- **Inserts data into** `qas_rooms` table

### **14. User API** (`api/views/apiUser.php`)
- **Purpose**: Complete user authentication system
- **Actions**:
  1. **login**: Username/password auth, generates token
  2. **register**: New user creation with email validation
  3. **logout**: Clears keepalive token
  4. **delete**: Soft delete (sets status=1)
  5. **forget**: Password reset via email
  6. **change**: Update password for logged-in users
  7. **appFire**: Token validation for Firebase

- **Security**:
  - SHA1 password hashing
  - MD5 token generation
  - Case-insensitive username lookup
  - Email uniqueness validation
  - Firebase token storage

### **15. Version API** (`api/views/apiVersion.php`)
- **Purpose**: App version management and download links
- **Current Version**: 1.0.4
- **Platform Links**:
  - iOS: TestFlight beta
  - Android: Direct APK download
  - Windows: EXE installer

### **16. Video Player API** (`api/views/apiVideoPlayer.php`)
- **Purpose**: Extract direct video stream URLs
- **Process**:
  1. Fetches page with proper referer header
  2. Extracts JWPlayer source using regex
  3. Crops URL at `.m3u8` extension
- **Returns**: Direct M3U8 playlist URL

---

## **Helper Functions Analysis** (`admin/includes/functions/`)

### **General Functions** (`general.php`)
- **`direction($valEn, $valAr)`**: Handles RTL/LTR text switching.
- **`dataOutput($data)`**: Formats successful JSON responses.
- **`dataError($data)`**: Formats error JSON responses.
- **`checkLogin()`**: Validates `tryq8flix2` cookie and retrieves user profile.
- **`extractUptoboxId($url)`**: Parses Uptobox URLs.
- **`validateInput($input)`**: Sanitizes input against SQL injection keywords and special characters.
- **`randomLetter()`**: Generates a single random alphanumeric character.
- **`scrapePage($url)`**: cURL wrapper for scraping external pages (references `$scrappingBeeToken`).

### **SQL Functions** (`sql.php`)
- **`selectDB($table, $where)`**: Basic SELECT query with `WHERE` clause.
- **`selectDBNew($table, $placeHolders, $where, $order)`**: Prepared statement version of SELECT for better security.
- **`selectDataDB($select, $table, $where)`**: SELECT specific columns.
- **`selectDB2($select, $table, $where)`**: Prepared statement version for selecting specific columns.

### **Notification Functions** (`notification.php`)
- **`sendMail($data)`**: Sends emails via external API (`createid.link/api/v1/send/notify`).

---

## **Key Technical Patterns**

### **Authentication Flow**
```
HTTP_AUTHORIZATION header → Bearer token → User validation via keepalive field
```

### **Response Format**
- Success: `dataOutput($data)` 
- Error: `dataError($error)`

### **Common Dependencies**
- Database functions: `selectDB()`, `selectDB2()`, `insertDB()`, `updateDB()`
- Web scraping: `curlCall()`, `str_get_html()` (simple_html_dom)
- Various scraping functions for each content provider

### **Database Tables Used**
- `banners`, `settings`, `users`
- `qas_categories`, `qas` (questions), `qas_rooms`

---

## **Security Considerations**
⚠️ **Potential Issues Identified**:
- SHA1 password hashing (deprecated, use bcrypt/Argon2)
- Direct SQL queries without full parameterization visible in some older functions
- No rate limiting mechanisms apparent
- Token stored in plain text (`keepalive` field)
- No CSRF protection visible
