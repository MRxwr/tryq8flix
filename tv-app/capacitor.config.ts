import type { CapacitorConfig } from '@capacitor/cli';

const config: CapacitorConfig = {
  appId: 'com.tryq8flix.tv',
  appName: 'TryQ8Flix TV',
  webDir: 'dist',
  plugins: {
    // Native HTTP: the website sends no CORS headers, so requests from the WebView must go native.
    CapacitorHttp: {
      enabled: true,
    },
  },
};

export default config;
