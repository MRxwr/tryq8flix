#!/usr/bin/env node

// Simple test script for the Cloudflare scraper
const CloudflareScraper = require('./cloudflare-scraper.js');

async function test() {
    const url = process.argv[2] || 'https://shahid4u.free';
    
    console.log('Testing Cloudflare scraper with URL:', url);
    
    const scraper = new CloudflareScraper({
        headless: false, // Set to false for debugging
        timeout: 60000
    });
    
    try {
        const result = await scraper.scrape(url);
        
        if (result.success) {
            console.log('\n✅ SUCCESS!');
            console.log('URL:', result.url);
            console.log('Title:', result.title);
            console.log('Content length:', result.html.length);
            
            // Check for Cloudflare challenge in content
            if (result.html.includes('Just a moment') || result.html.includes('_cf_chl_opt')) {
                console.log('⚠️  WARNING: Content still contains Cloudflare challenge!');
            } else {
                console.log('✅ No Cloudflare challenge detected in content');
            }
            
            // Save to test file
            await scraper.saveToFile(result.html, 'test-output.html');
            
        } else {
            console.log('\n❌ FAILED!');
            console.log('Error:', result.error);
        }
        
    } catch (error) {
        console.error('Test failed:', error);
    }
}

test();
