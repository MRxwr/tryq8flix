# YouTube Video Downloader API - No Dependencies Required

This is a **pure PHP** YouTube video downloader API that works **without requiring yt-dlp or any external dependencies** on your server. It uses web scraping and YouTube's public APIs to extract video information and generate download links.

## 🌟 Features

- ✅ **No external dependencies** - Pure PHP solution
- ✅ Extract video information (title, duration, thumbnail, uploader)
- ✅ Multiple quality options (720p, 480p, 360p, 240p)
- ✅ Clean JSON API responses
- ✅ Built-in error handling
- ✅ Works on shared hosting
- ✅ User-friendly test interface included

## 📋 Requirements

- PHP 7.0 or higher
- `allow_url_fopen` enabled in PHP (usually enabled by default)
- Web server (Apache/Nginx/shared hosting)

## 🚀 Installation

1. **Upload the files** to your server:
   - `api/views/apiYoutube.php`
   - `api/views/apiYoutubeDownload.php`
   - `youtube-downloader-test.html` (for testing)

2. **Ensure PHP settings** (usually already enabled):
   ```php
   allow_url_fopen = On
   ```

3. **Test the installation** by opening `youtube-downloader-test.html` in your browser.

## 🔧 API Endpoints

### 1. Get Video Information

**Endpoint:** `POST /api/?endpoint=Youtube&action=Youtube`

**Parameters:**
- `link` (required): YouTube video URL

**Example Request:**
```javascript
const formData = new FormData();
formData.append('link', 'https://www.youtube.com/watch?v=dQw4w9WgXcQ');

fetch('/api/?endpoint=Youtube&action=Youtube', {
    method: 'POST',
    body: formData
})
.then(response => response.json())
.then(data => console.log(data));
```

**Example Response:**
```json
{
    "error": false,
    "data": {
        "video_id": "dQw4w9WgXcQ",
        "title": "Rick Astley - Never Gonna Give You Up",
        "duration": "03:33",
        "thumbnail": "https://img.youtube.com/vi/dQw4w9WgXcQ/maxresdefault.jpg",
        "uploader": "Rick Astley",
        "view_count": 1234567890,
        "description": "Music video description...",
        "formats": [
            {
                "format_id": 22,
                "quality": "720p HD",
                "resolution": "1280x720",
                "filesize": "Unknown",
                "ext": "mp4",
                "download_url": "/api/?endpoint=YoutubeDownload&video_id=dQw4w9WgXcQ&itag=22"
            },
            {
                "format_id": 18,
                "quality": "480p",
                "resolution": "854x480",
                "filesize": "Unknown",
                "ext": "mp4",
                "download_url": "/api/?endpoint=YoutubeDownload&video_id=dQw4w9WgXcQ&itag=18"
            }
        ]
    }
}
```

### 2. Download Video

**Endpoint:** `GET /api/?endpoint=YoutubeDownload&video_id={VIDEO_ID}&itag={ITAG}`

**Parameters:**
- `video_id` (required): YouTube video ID (11 characters)
- `itag` (required): Format ID from the video information response
- `direct=1` (optional): Return direct URL instead of streaming

**Example:**
```
GET /api/?endpoint=YoutubeDownload&video_id=dQw4w9WgXcQ&itag=22
```

## 💻 Usage Examples

### PHP/cURL Example:
```php
<?php
// Get video information
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'https://yoursite.com/api/?endpoint=Youtube&action=Youtube');
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, 'link=https://www.youtube.com/watch?v=dQw4w9WgXcQ');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$response = curl_exec($ch);
curl_close($ch);

$data = json_decode($response, true);
if (!$data['error']) {
    echo "Video Title: " . $data['data']['title'] . "\n";
    foreach ($data['data']['formats'] as $format) {
        echo "Quality: " . $format['quality'] . " - Download: " . $format['download_url'] . "\n";
    }
}
?>
```

### JavaScript/AJAX Example:
```javascript
async function downloadYouTubeVideo(url) {
    try {
        // Get video info
        const formData = new FormData();
        formData.append('link', url);
        
        const response = await fetch('/api/?endpoint=Youtube&action=Youtube', {
            method: 'POST',
            body: formData
        });
        
        const result = await response.json();
        
        if (result.error) {
            console.error('Error:', result.msg);
            return;
        }
        
        console.log('Video Title:', result.data.title);
        
        // Show download options
        result.data.formats.forEach(format => {
            console.log(`${format.quality}: ${format.download_url}`);
        });
        
    } catch (error) {
        console.error('Request failed:', error);
    }
}

// Usage
downloadYouTubeVideo('https://www.youtube.com/watch?v=dQw4w9WgXcQ');
```

## 🔍 How It Works

The API uses multiple methods to extract video information:

1. **YouTube oEmbed API** - Gets basic video information
2. **Direct page scraping** - Extracts detailed video data from YouTube pages
3. **Player response parsing** - Attempts to extract streaming URLs when available

### Extraction Methods:

1. **Method 1**: Uses YouTube's official oEmbed API for basic info
2. **Method 2**: Scrapes the YouTube watch page for detailed information
3. **Method 3**: Fallback methods for download URLs (when available)

## ⚠️ Limitations

- **YouTube Protection**: YouTube actively prevents direct downloads, so some videos may not work
- **Quality Detection**: File sizes are often unknown until download
- **Rate Limiting**: YouTube may block requests if too many are made
- **Age-Restricted Content**: Cannot access age-restricted or private videos
- **Geographic Restrictions**: Some videos may not be available in all regions

## 🛠️ Troubleshooting

### Common Issues:

**1. "Failed to get video information"**
- Video may be private, age-restricted, or deleted
- Try a different video URL
- Check if the URL is a valid YouTube URL

**2. "Unable to generate download link"**
- YouTube's protection mechanisms are blocking access
- Try again later or with a different video
- Some videos cannot be downloaded due to restrictions

**3. Downloads not working**
- YouTube frequently changes their systems
- The video may have download restrictions
- Try different quality options

### PHP Configuration:
Make sure these settings are enabled in your `php.ini`:
```ini
allow_url_fopen = On
file_get_contents = enabled
curl = enabled (optional but recommended)
```

## 🧪 Testing

1. Open `youtube-downloader-test.html` in your browser
2. Enter a YouTube URL (try: `https://www.youtube.com/watch?v=dQw4w9WgXcQ`)
3. Click "Get Video Information"
4. Try downloading different quality options

## 🔒 Security Considerations

- **URL Validation**: The API validates YouTube URLs to prevent injection
- **Rate Limiting**: Consider implementing rate limiting for production use
- **User Agent**: Uses proper user agents to avoid blocking
- **Error Handling**: Comprehensive error handling prevents crashes

## 📝 Legal Disclaimer

This tool is for **educational purposes only**. Please:
- ✅ Respect YouTube's Terms of Service
- ✅ Only download videos you have permission to download
- ✅ Respect copyright laws and content creators' rights
- ✅ Use responsibly and ethically

## 🆘 Support

If you encounter issues:

1. Check that your PHP version is 7.0+
2. Verify `allow_url_fopen` is enabled
3. Test with different YouTube URLs
4. Check server error logs for detailed error messages

## 🔄 Updates

Since YouTube frequently changes their systems, this API may need periodic updates. The modular design makes it easy to add new extraction methods or modify existing ones.

---

**Note**: This solution doesn't require any external dependencies but may have limitations compared to dedicated tools like yt-dlp. It's designed to work on shared hosting and servers where you can't install additional software.
