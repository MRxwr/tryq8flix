import { registerPlugin } from '@capacitor/core';

interface StreamBrowserPlugin {
  openExternal(options: { url: string }): Promise<void>;
}

export const streamBrowser = registerPlugin<StreamBrowserPlugin>('StreamBrowser');
