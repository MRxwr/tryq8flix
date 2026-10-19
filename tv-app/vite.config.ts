/// <reference types="vitest" />

import legacy from '@vitejs/plugin-legacy';
import react from '@vitejs/plugin-react';
import { defineConfig, loadEnv } from 'vite';

const LIVE_SITE = 'https://tryq8flix.com';

// The browser calls same-origin /api paths; these proxies forward them to the live site
// so the app can be tested locally (dev server and `vite preview`).
const proxy = {
  '/api': { target: LIVE_SITE, changeOrigin: true, secure: true },
  '/videoPlayer.php': { target: LIVE_SITE, changeOrigin: true, secure: true },
};

// https://vitejs.dev/config/
export default defineConfig(({ mode }) => {
  const env = loadEnv(mode, process.cwd(), '');

  return {
    // Default '/' is what the Android WebView needs. `npm run build:web` uses --mode tvweb,
    // which sets TV_BASE=/tv-app/ so the same app can be uploaded to https://tryq8flix.com/tv-app/.
    base: env.TV_BASE || '/',
    plugins: [
      react(),
      legacy(),
    ],
    server: { proxy },
    preview: { proxy },
    test: {
      globals: true,
      environment: 'jsdom',
      setupFiles: './src/setupTests.ts',
    },
  };
});
