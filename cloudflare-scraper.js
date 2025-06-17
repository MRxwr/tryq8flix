const puppeteer = require('puppeteer');
const fs = require('fs').promises;
const path = require('path');

/**
 * Cloudflare Challenge Bypasser using Puppeteer
 * Handles Cloudflare protection by waiting for challenges to complete
 */
class CloudflareScraper {
    constructor(options = {}) {
        this.options = {
            headless: true,
            timeout: 30000,
            waitUntil: 'networkidle2',
            userAgent: 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
            ...options
        };
    }

    async scrape(url) {
        let browser;
        let page;
        
        try {            console.log(`Starting browser...`);
            browser = await puppeteer.launch({
                headless: this.options.headless,
                args: [
                    '--no-sandbox',
                    '--disable-setuid-sandbox',
                    '--disable-dev-shm-usage',
                    '--disable-accelerated-2d-canvas',
                    '--no-first-run',
                    '--no-zygote',
                    '--disable-gpu',
                    '--disable-blink-features=AutomationControlled',
                    '--disable-features=VizDisplayCompositor',
                    '--disable-web-security',
                    '--disable-features=TranslateUI',
                    '--disable-ipc-flooding-protection'
                ],
                ignoreDefaultArgs: ['--enable-automation'],
                defaultViewport: null
            });

            page = await browser.newPage();
            
            // Remove webdriver property
            await page.evaluateOnNewDocument(() => {
                Object.defineProperty(navigator, 'webdriver', {
                    get: () => undefined,
                });
            });
            
            // Override the plugins property to use a custom getter
            await page.evaluateOnNewDocument(() => {
                Object.defineProperty(navigator, 'plugins', {
                    get: () => [1, 2, 3, 4, 5],
                });
            });
            
            // Override the languages property to use a custom getter
            await page.evaluateOnNewDocument(() => {
                Object.defineProperty(navigator, 'languages', {
                    get: () => ['en-US', 'en'],
                });
            });
            
            // Set user agent to appear more legitimate
            await page.setUserAgent(this.options.userAgent);
            
            // Set viewport
            await page.setViewport({ width: 1366, height: 768 });
            
            // Enable JavaScript and cookies (default in Puppeteer)
            await page.setJavaScriptEnabled(true);
            
            console.log(`Navigating to: ${url}`);
            
            // Navigate to the URL with extended timeout
            await page.goto(url, { 
                waitUntil: this.options.waitUntil,
                timeout: this.options.timeout 
            });

            console.log('Page loaded, checking for Cloudflare challenge...');
            
            // Wait for Cloudflare challenge to complete
            await this.waitForCloudflareChallenge(page);
            
            console.log('Cloudflare challenge completed (if any), extracting content...');
            
            // Extract the full HTML content
            const htmlContent = await page.content();
            
            return {
                success: true,
                html: htmlContent,
                url: page.url(),
                title: await page.title()
            };
            
        } catch (error) {
            console.error('Error during scraping:', error.message);
            return {
                success: false,
                error: error.message,
                html: null
            };
        } finally {
            if (page) await page.close();
            if (browser) await browser.close();
        }
    }    async waitForCloudflareChallenge(page) {
        try {
            // Check if we're on a Cloudflare challenge page
            const isCloudflare = await page.evaluate(() => {
                return document.title.includes('Just a moment') || 
                       document.body.innerHTML.includes('Checking your browser') ||
                       document.body.innerHTML.includes('cloudflare') ||
                       document.body.innerHTML.includes('_cf_chl_opt') ||
                       document.querySelector('.cf-browser-verification') !== null ||
                       document.querySelector('script[src*="challenge-platform"]') !== null;
            });

            if (isCloudflare) {
                console.log('Cloudflare challenge detected, waiting for completion...');
                
                // Wait longer for modern Cloudflare challenges
                const maxWaitTime = 30000; // 30 seconds
                let attempts = 0;
                const maxAttempts = 6;
                
                while (attempts < maxAttempts) {
                    attempts++;
                    console.log(`Challenge attempt ${attempts}/${maxAttempts}...`);
                    
                    try {
                        // Wait for the challenge to complete by checking for page changes
                        await Promise.race([
                            // Wait for navigation away from challenge page
                            page.waitForNavigation({ 
                                waitUntil: 'networkidle0', 
                                timeout: maxWaitTime / maxAttempts 
                            }),
                            
                            // Wait for title to change
                            page.waitForFunction(
                                () => {
                                    const title = document.title;
                                    const body = document.body.innerHTML;
                                    return !title.includes('Just a moment') && 
                                           !body.includes('Checking your browser') &&
                                           !body.includes('_cf_chl_opt') &&
                                           body.length > 5000; // Ensure we have real content
                                },
                                { timeout: maxWaitTime / maxAttempts }
                            ),
                            
                            // Static wait
                            new Promise(resolve => setTimeout(resolve, maxWaitTime / maxAttempts))
                        ]);
                        
                        // Check if challenge is really completed
                        const challengeCompleted = await page.evaluate(() => {
                            const title = document.title;
                            const body = document.body.innerHTML;
                            return !title.includes('Just a moment') && 
                                   !body.includes('Checking your browser') &&
                                   !body.includes('_cf_chl_opt') &&
                                   body.length > 5000;
                        });
                        
                        if (challengeCompleted) {
                            console.log('Cloudflare challenge completed successfully!');
                            break;
                        } else {
                            console.log(`Challenge not yet completed, attempt ${attempts}...`);
                            await page.waitForTimeout(2000);
                        }
                        
                    } catch (error) {
                        console.log(`Attempt ${attempts} failed: ${error.message}`);
                        if (attempts < maxAttempts) {
                            await page.waitForTimeout(3000);
                        }
                    }
                }
                
                // Final verification
                const finalCheck = await page.evaluate(() => {
                    return {
                        title: document.title,
                        bodyLength: document.body.innerHTML.length,
                        hasChallenge: document.body.innerHTML.includes('_cf_chl_opt')
                    };
                });
                
                console.log(`Final status: Title="${finalCheck.title}", BodyLength=${finalCheck.bodyLength}, HasChallenge=${finalCheck.hasChallenge}`);
                
            } else {
                console.log('No Cloudflare challenge detected');
            }
            
            // Final check - ensure body content is available
            await page.waitForSelector('body', { timeout: 5000 });
            
        } catch (error) {
            console.warn('Warning during Cloudflare challenge wait:', error.message);
            // Continue anyway as the challenge might have completed
        }
    }

    async saveToFile(content, filename = null) {
        if (!filename) {
            const timestamp = new Date().toISOString().replace(/[:.]/g, '-');
            filename = `scraped-content-${timestamp}.html`;
        }
        
        const filepath = path.resolve(filename);
        await fs.writeFile(filepath, content, 'utf8');
        console.log(`Content saved to: ${filepath}`);
        return filepath;
    }
}

// Main execution function
async function main() {
    // Get URL from command line arguments or use default
    const targetUrl = process.argv[2] || 'https://example.com';
    
    console.log(`Cloudflare Scraper Starting...`);
    console.log(`Target URL: ${targetUrl}`);
    
    const scraper = new CloudflareScraper({
        headless: true,
        timeout: 45000
    });
    
    try {
        const result = await scraper.scrape(targetUrl);
        
        if (result.success) {
            console.log(`\n✅ Successfully scraped: ${result.url}`);
            console.log(`📄 Page title: ${result.title}`);
            console.log(`📊 Content length: ${result.html.length} characters`);
            
            // Save to file
            const filename = await scraper.saveToFile(result.html);
            
            // Print first 500 characters of content
            console.log('\n📝 Content preview:');
            console.log('=' .repeat(50));
            console.log(result.html.substring(0, 500) + '...');
            console.log('=' .repeat(50));
            
        } else {
            console.error(`\n❌ Scraping failed: ${result.error}`);
            process.exit(1);
        }
        
    } catch (error) {
        console.error(`\n💥 Unexpected error: ${error.message}`);
        process.exit(1);
    }
}

// Handle unhandled promise rejections
process.on('unhandledRejection', (reason, promise) => {
    console.error('Unhandled Rejection at:', promise, 'reason:', reason);
    process.exit(1);
});

// Run the script if called directly
if (require.main === module) {
    main();
}

module.exports = CloudflareScraper;
