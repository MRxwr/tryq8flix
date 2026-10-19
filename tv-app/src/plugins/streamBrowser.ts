import { registerPlugin } from '@capacitor/core';

interface StreamBrowserPlugin {
  /** Opens Android native browser with video sniffing, redirect prompts and player. */
  openInternal(options: { url: string }): Promise<void>;
  openExternal(options: { url: string }): Promise<void>;
}

export const streamBrowser = registerPlugin<StreamBrowserPlugin>('StreamBrowser');
