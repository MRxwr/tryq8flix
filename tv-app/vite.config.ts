/// <reference types="vitest" />

import legacy from '@vitejs/plugin-legacy';
import react from '@vitejs/plugin-react';
import { defineConfig } from 'vite';

const LIVE_SITE = 'https://tryq8flix.com';

// https://vitejs.dev/config/
export default defineConfig({
  plugins: [
    react(),
    legacy(),
  ],
  server: {
    // Dev only: the browser calls same-origin paths and Vite forwards them to the live site.
    proxy: {
      '/api': { target: LIVE_SITE, changeOrigin: true, secure: true },
      '/videoPlayer.php': { target: LIVE_SITE, changeOrigin: true, secure: true },
    },
  },
  test: {
    globals: true,
    environment: 'jsdom',
    setupFiles: './src/setupTests.ts',
  },
});
